<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table      = 'tareas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'titulo', 'descripcion', 'prioridad', 'fecha_limite',
        'modalidad', 'departamento_id', 'destinatario_tipo', 'destinatario_id',
        'created_by', 'publicado', 'completada', 'completada_por', 'completada_at',
    ];

    protected $validationRules = [
        'titulo'       => 'required|max_length[255]',
        'modalidad'    => 'required|in_list[single_completes_all,all_must_complete]',
        'departamento_id' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'titulo'    => ['required' => 'El titulo de la tarea es obligatorio.'],
        'modalidad' => ['required' => 'Debes seleccionar una modalidad.'],
    ];

    // ─── Listar tareas por departamento (vista employee) ───

    public function ObtenerPorDepartamento(int $usuarioId, bool $soloMisTareas = true): array
    {
        $db = \Config\Database::connect();

        $sql = "SELECT t.*,
                       u.nombre AS creador_nombre,
                       CASE WHEN t.completada_por IS NOT NULL THEN cu.nombre ELSE NULL END AS completada_por_nombre,
                       (SELECT COUNT(*) FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id) AS total_asignados,
                       (SELECT COUNT(*) FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id AND ta.completado = 1) AS total_completados,
                       (SELECT ta2.completado FROM tarea_asignaciones ta2 WHERE ta2.tarea_id = t.id AND ta2.usuario_id = ?) AS mi_asignacion,
                       (SELECT ta3.completado_at FROM tarea_asignaciones ta3 WHERE ta3.tarea_id = t.id AND ta3.usuario_id = ?) AS mi_completado_at,
                       (SELECT COUNT(*) FROM comentarios cc WHERE cc.tarea_id = t.id) AS comentarios_count,
                       (SELECT GROUP_CONCAT(td.departamento_id) FROM tarea_departamentos td WHERE td.tarea_id = t.id) AS departamentos_ids,
                       (SELECT GROUP_CONCAT(d.descripcion SEPARATOR ', ') FROM tarea_departamentos td INNER JOIN departamentos d ON d.id = td.departamento_id WHERE td.tarea_id = t.id) AS departamentos_nombres
                FROM tareas t
                LEFT JOIN admin_usuarios u ON u.id = t.created_by
                LEFT JOIN admin_usuarios cu ON cu.id = t.completada_por
                WHERE t.publicado = 1";

        $params = [$usuarioId, $usuarioId];

        if ($soloMisTareas) {
            $sql .= " AND (
                (NOT EXISTS (SELECT 1 FROM tarea_departamentos td WHERE td.tarea_id = t.id)
                 AND NOT EXISTS (SELECT 1 FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id))
                OR EXISTS (SELECT 1 FROM tarea_departamentos td INNER JOIN admin_usuarios au ON au.id_departamento = td.departamento_id WHERE td.tarea_id = t.id AND au.id = ?)
                OR EXISTS (SELECT 1 FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id AND ta.usuario_id = ?)
                OR (t.destinatario_tipo = 'usuarios' AND t.destinatario_id = ?)
                OR (t.destinatario_tipo = 'departamento' AND t.destinatario_id = (SELECT id_departamento FROM admin_usuarios WHERE id = ?))
            )";
            $params[] = $usuarioId;
            $params[] = $usuarioId;
            $params[] = $usuarioId;
            $params[] = $usuarioId;
        }

        $sql .= " ORDER BY t.fecha_limite ASC, FIELD(t.prioridad, 'alta', 'media', 'baja') ASC";

        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }

    // ─── Listar todas las tareas (admin) ───

    public function ObtenerTodas(): array
    {
        $db = \Config\Database::connect();

        $sql = "SELECT t.*,
                       u.nombre AS creador_nombre,
                       CASE WHEN t.completada_por IS NOT NULL THEN cu.nombre ELSE NULL END AS completada_por_nombre,
                       (SELECT COUNT(*) FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id) AS total_asignados,
                       (SELECT COUNT(*) FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id AND ta.completado = 1) AS total_completados,
                       (SELECT COUNT(*) FROM comentarios cc WHERE cc.tarea_id = t.id) AS comentarios_count,
                       (SELECT GROUP_CONCAT(td.departamento_id) FROM tarea_departamentos td WHERE td.tarea_id = t.id) AS departamentos_ids,
                       (SELECT GROUP_CONCAT(d.descripcion SEPARATOR ', ') FROM tarea_departamentos td INNER JOIN departamentos d ON d.id = td.departamento_id WHERE td.tarea_id = t.id) AS departamentos_nombres
                FROM tareas t
                LEFT JOIN admin_usuarios u ON u.id = t.created_by
                LEFT JOIN admin_usuarios cu ON cu.id = t.completada_por
                ORDER BY t.created_at DESC";

        $query = $db->query($sql);
        return $query->getResultArray();
    }

    // ─── Obtener tarea detallada ───

    public function ObtenerDetallada(int $id): ?array
    {
        $db = \Config\Database::connect();

        $sql = "SELECT t.*,
                       u.nombre AS creador_nombre,
                       CASE WHEN t.completada_por IS NOT NULL THEN cu.nombre ELSE NULL END AS completada_por_nombre,
                       (SELECT GROUP_CONCAT(td.departamento_id) FROM tarea_departamentos td WHERE td.tarea_id = t.id) AS departamentos_ids
                FROM tareas t
                LEFT JOIN admin_usuarios u ON u.id = t.created_by
                LEFT JOIN admin_usuarios cu ON cu.id = t.completada_por
                WHERE t.id = ?";

        $query = $db->query($sql, [$id]);
        return $query->getRowArray();
    }

    // ─── Contar pendientes por departamento ───

    public function ContarPendientesPorDepartamento(int $usuarioId, bool $soloMisTareas = true): array
    {
        $db = \Config\Database::connect();

        $sql = "SELECT td.departamento_id, COUNT(DISTINCT t.id) AS pendientes
                FROM tareas t
                INNER JOIN tarea_departamentos td ON td.tarea_id = t.id
                WHERE t.publicado = 1 AND t.completada = 0";

        $params = [];

        if ($soloMisTareas) {
            $sql .= " AND (
                (NOT EXISTS (SELECT 1 FROM tarea_departamentos td WHERE td.tarea_id = t.id)
                 AND NOT EXISTS (SELECT 1 FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id))
                OR EXISTS (SELECT 1 FROM tarea_departamentos td INNER JOIN admin_usuarios au ON au.id_departamento = td.departamento_id WHERE td.tarea_id = t.id AND au.id = ?)
                OR EXISTS (SELECT 1 FROM tarea_asignaciones ta WHERE ta.tarea_id = t.id AND ta.usuario_id = ?)
                OR (t.destinatario_tipo = 'usuarios' AND t.destinatario_id = ?)
                OR (t.destinatario_tipo = 'departamento' AND t.destinatario_id = (SELECT id_departamento FROM admin_usuarios WHERE id = ?))
            )";
            $params[] = $usuarioId;
            $params[] = $usuarioId;
            $params[] = $usuarioId;
            $params[] = $usuarioId;
        }

        $sql .= " GROUP BY td.departamento_id";

        $query = $db->query($sql, $params);
        $result = [];
        foreach ($query->getResultArray() as $row) {
            $result[$row['departamento_id']] = (int) $row['pendientes'];
        }
        return $result;
    }

    // ─── Completar tarea (single_completes_all) ───

    public function CompletarSingle(int $tareaId, int $usuarioId): array
    {
        $db = \Config\Database::connect();
        $fechaHora = date('Y-m-d H:i:s');

        $tarea = $this->find($tareaId);
        if (!$tarea) {
            return ['success' => false, 'message' => 'Tarea no encontrada.'];
        }

        if ($tarea['completada'] == 1) {
            return ['success' => false, 'message' => 'Esta tarea ya fue completada.'];
        }

        $this->update($tareaId, [
            'completada'     => 1,
            'completada_por' => $usuarioId,
            'completada_at'  => $fechaHora,
        ]);

        return [
            'success'    => true,
            'message'    => 'Tarea completada.',
            'completado' => $usuarioId,
            'fecha'      => $fechaHora,
        ];
    }

    // ─── Completar tarea (all_must_complete) ───

    public function CompletarAll(int $tareaId, int $usuarioId): array
    {
        $db = \Config\Database::connect();
        $fechaHora = date('Y-m-d H:i:s');

        $tarea = $this->find($tareaId);
        if (!$tarea) {
            return ['success' => false, 'message' => 'Tarea no encontrada.'];
        }

        if ($tarea['completada'] == 1) {
            return ['success' => false, 'message' => 'Esta tarea ya fue completada por todos.'];
        }

        $existe = $db->query(
            "SELECT id, completado FROM tarea_asignaciones WHERE tarea_id = ? AND usuario_id = ?",
            [$tareaId, $usuarioId]
        )->getRowArray();

        if ($existe && $existe['completado'] == 1) {
            return ['success' => false, 'message' => 'Ya completaste esta tarea.'];
        }

        if ($existe) {
            $db->query(
                "UPDATE tarea_asignaciones SET completado = 1, completado_at = ? WHERE id = ?",
                [$fechaHora, $existe['id']]
            );
        } else {
            $db->query(
                "INSERT INTO tarea_asignaciones (tarea_id, usuario_id, completado, completado_at) VALUES (?, ?, 1, ?)",
                [$tareaId, $usuarioId, $fechaHora]
            );
        }

        $totalAsignados = $db->query(
            "SELECT COUNT(*) AS total FROM tarea_asignaciones WHERE tarea_id = ?",
            [$tareaId]
        )->getRowArray()['total'];

        $totalCompletados = $db->query(
            "SELECT COUNT(*) AS total FROM tarea_asignaciones WHERE tarea_id = ? AND completado = 1",
            [$tareaId]
        )->getRowArray()['total'];

        if ($totalAsignados > 0 && $totalCompletados >= $totalAsignados) {
            $this->update($tareaId, [
                'completada'    => 1,
                'completada_at' => $fechaHora,
            ]);
        }

        return [
            'success'         => true,
            'message'         => 'Tu parte fue registrada.',
            'total'           => $totalAsignados,
            'completados'     => $totalCompletados,
            'todas_completas' => ($totalAsignados > 0 && $totalCompletados >= $totalAsignados),
        ];
    }

    // ─── Descompletar ───

    public function DescompletarSingle(int $tareaId, int $usuarioId): array
    {
        $tarea = $this->find($tareaId);
        if (!$tarea) {
            return ['success' => false, 'message' => 'Tarea no encontrada.'];
        }

        if ($tarea['completada_por'] != $usuarioId) {
            return ['success' => false, 'message' => 'Solo quien completó la tarea puede revertirla.'];
        }

        $this->update($tareaId, [
            'completada'     => 0,
            'completada_por' => null,
            'completada_at'  => null,
        ]);

        return ['success' => true, 'message' => 'Tarea marcada como pendiente.'];
    }

    public function DescompletarAll(int $tareaId, int $usuarioId): array
    {
        $db = \Config\Database::connect();

        $tarea = $this->find($tareaId);
        if (!$tarea) {
            return ['success' => false, 'message' => 'Tarea no encontrada.'];
        }

        $asignacion = $db->query(
            "SELECT id FROM tarea_asignaciones WHERE tarea_id = ? AND usuario_id = ? AND completado = 1",
            [$tareaId, $usuarioId]
        )->getRowArray();

        if (!$asignacion) {
            return ['success' => false, 'message' => 'No tienes esta tarea como completada.'];
        }

        $db->query(
            "UPDATE tarea_asignaciones SET completado = 0, completado_at = NULL WHERE id = ?",
            [$asignacion['id']]
        );

        if ($tarea['completada'] == 1) {
            $this->update($tareaId, ['completada' => 0]);
        }

        return ['success' => true, 'message' => 'Tu completado fue revertido.'];
    }

    // ─── Asignaciones ───

    public function Asignar(int $tareaId, array $usuarioIds): array
    {
        $db = \Config\Database::connect();

        $tarea = $this->find($tareaId);
        if (!$tarea) {
            return ['success' => false, 'message' => 'Tarea no encontrada.'];
        }

        $db->query("DELETE FROM tarea_asignaciones WHERE tarea_id = ?", [$tareaId]);

        foreach ($usuarioIds as $uid) {
            $db->query(
                "INSERT INTO tarea_asignaciones (tarea_id, usuario_id, completado) VALUES (?, ?, 0)",
                [$tareaId, $uid]
            );
        }

        if ($tarea['completada'] == 1) {
            $this->update($tareaId, ['completada' => 0]);
        }

        return ['success' => true, 'message' => count($usuarioIds) . ' usuario(s) asignado(s).'];
    }

    public function GuardarDepartamentos(int $tareaId, array $departamentoIds): void
    {
        $db = \Config\Database::connect();
        $db->table('tarea_departamentos')->where('tarea_id', $tareaId)->delete();

        foreach (array_unique(array_filter($departamentoIds)) as $deptId) {
            $db->table('tarea_departamentos')->insert([
                'tarea_id'       => $tareaId,
                'departamento_id' => (int) $deptId,
            ]);
        }
    }

    public function GuardarUsuarios(int $tareaId, array $usuarioIds): void
    {
        $db = \Config\Database::connect();
        $db->table('tarea_asignaciones')->where('tarea_id', $tareaId)->delete();

        foreach (array_unique(array_filter($usuarioIds)) as $uid) {
            $db->table('tarea_asignaciones')->insert([
                'tarea_id'   => $tareaId,
                'usuario_id' => (int) $uid,
                'completado' => 0,
            ]);
        }
    }

    public function ObtenerAsignaciones(int $tareaId): array
    {
        $db = \Config\Database::connect();

        $sql = "SELECT ta.*, u.nombre AS usuario_nombre
                FROM tarea_asignaciones ta
                INNER JOIN admin_usuarios u ON u.id = ta.usuario_id
                WHERE ta.tarea_id = ?
                ORDER BY u.nombre ASC";

        $query = $db->query($sql, [$tareaId]);
        return $query->getResultArray();
    }

    // ─── Guardar (crear/editar) ───

    public function Guardar(array $datos): bool
    {
        if (!empty($datos['id'])) {
            $id = $datos['id'];
            unset($datos['id']);
            return $this->update($id, $datos) ? true : false;
        }
        return $this->insert($datos) ? true : false;
    }

    // ─── Eliminar ───

    public function Eliminar(int $id): bool
    {
        return $this->delete($id);
    }

    // ─── Toggle publicado ───

    public function TogglePublicado(int $id, int $publicado): bool
    {
        return $this->update($id, ['publicado' => $publicado]);
    }
}
