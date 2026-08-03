<?php

namespace App\Models;

use CodeIgniter\Model;

class EntregaModel extends Model
{
    protected $table      = 'entregas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'titulo', 'descripcion', 'repetir_diario', 'fecha_inicio', 'fecha_fin',
        'publicado', 'created_by', 'destinatario_tipo', 'destinatario_id',
    ];

    protected $validationRules = [
        'titulo'       => 'required|max_length[255]',
        'fecha_inicio' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'titulo'       => ['required' => 'El titulo de la tarea es obligatorio.'],
        'fecha_inicio' => ['required' => 'La fecha de inicio es obligatoria.'],
    ];

    public function ObtenerTodas(): array
    {
        return $this->orderBy('created_at DESC')->findAll();
    }

    public function ObtenerTodasConDestinatario(): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT e.*,
                       CASE
                           WHEN e.destinatario_tipo = 'todos' THEN 'Todos'
                           WHEN e.destinatario_tipo = 'usuarios' THEN u.nombre
                           WHEN e.destinatario_tipo = 'departamento' THEN d.descripcion
                       END AS destinatario_nombre,
                       CASE
                           WHEN e.destinatario_tipo = 'todos' THEN 'Todos'
                           WHEN e.destinatario_tipo = 'usuarios' THEN 'Usuario'
                           WHEN e.destinatario_tipo = 'departamento' THEN 'Departamento'
                       END AS destinatario_tipo_nombre
                FROM entregas e
                LEFT JOIN admin_usuarios u ON e.destinatario_tipo = 'usuarios' AND u.id = e.destinatario_id
                LEFT JOIN departamentos d ON e.destinatario_tipo = 'departamento' AND d.id = e.destinatario_id
                ORDER BY e.created_at DESC";
        $query = $db->query($sql);
        return $query->getResultArray();
    }

    public function ObtenerPorId(int $id): ?array
    {
        return $this->find($id);
    }

    public function ObtenerActivas(string $fecha, ?int $usuarioId = null, ?int $departamentoId = null, string $rol = 'empleado'): array
    {
        $this->where('publicado', 1);
        $this->where('fecha_inicio <=', $fecha);
        $this->groupStart();
        $this->where('fecha_fin IS NULL');
        $this->orWhere('fecha_fin >=', $fecha);
        $this->groupEnd();

        // Admin y superadmin ven todas las tareas
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            $this->groupStart();
            $this->where('destinatario_tipo', 'todos');
            if ($usuarioId) {
                $this->orWhere('destinatario_tipo', 'usuarios')->where('destinatario_id', $usuarioId);
            }
            if ($departamentoId) {
                $this->orWhere('destinatario_tipo', 'departamento')->where('destinatario_id', $departamentoId);
            }
            $this->groupEnd();
        }

        return $this->orderBy('repetir_diario DESC, id ASC')->findAll();
    }

    public function ContarActivas(string $fecha, ?int $usuarioId = null, ?int $departamentoId = null, string $rol = 'empleado'): int
    {
        $this->where('publicado', 1);
        $this->where('fecha_inicio <=', $fecha);
        $this->groupStart();
        $this->where('fecha_fin IS NULL');
        $this->orWhere('fecha_fin >=', $fecha);
        $this->groupEnd();

        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            $this->groupStart();
            $this->where('destinatario_tipo', 'todos');
            if ($usuarioId) {
                $this->orWhere('destinatario_tipo', 'usuarios')->where('destinatario_id', $usuarioId);
            }
            if ($departamentoId) {
                $this->orWhere('destinatario_tipo', 'departamento')->where('destinatario_id', $departamentoId);
            }
            $this->groupEnd();
        }

        return (int) $this->countAllResults();
    }

    public function ContarRegistradasHoy(string $fecha): int
    {
        $db = \Config\Database::connect();
        return (int) $db->table('entrega_registros')->where('fecha', $fecha)->countAllResults();
    }

    public function Guardar(array $datos): bool
    {
        if (!empty($datos['id'])) {
            $id = $datos['id'];
            unset($datos['id']);
            return $this->update($id, $datos) ? true : false;
        }
        return $this->insert($datos) ? true : false;
    }

    public function Eliminar(int $id): bool
    {
        return $this->delete($id);
    }

    public function TogglePublicado(int $id, int $publicado): bool
    {
        return $this->update($id, ['publicado' => $publicado]);
    }

    public function PublicarCon(int $id, string $tipo, ?int $destinatarioId): bool
    {
        return $this->update($id, [
            'publicado'          => 1,
            'destinatario_tipo'  => $tipo,
            'destinatario_id'    => $destinatarioId,
        ]);
    }

    public function ObtenerRegistros(string $fechaInicio = '', string $fechaFin = ''): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT r.id, r.entrega_id, r.usuario_id, r.fecha, r.completado_at,
                       e.titulo,
                       u.nombre AS usuario_nombre
                FROM entrega_registros r
                INNER JOIN entregas e ON e.id = r.entrega_id
                LEFT JOIN admin_usuarios u ON u.id = r.usuario_id
                WHERE 1 = 1";
        $params = [];
        if ($fechaInicio !== '') {
            $sql .= " AND r.fecha >= ?";
            $params[] = $fechaInicio;
        }
        if ($fechaFin !== '') {
            $sql .= " AND r.fecha <= ?";
            $params[] = $fechaFin;
        }
        $sql .= " ORDER BY r.fecha DESC, r.completado_at DESC";
        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }

    public function ObtenerRegistrosDeHoy(string $fecha): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT r.entrega_id, r.usuario_id, r.fecha, r.completado_at,
                       u.nombre AS usuario_nombre
                FROM entrega_registros r
                LEFT JOIN admin_usuarios u ON u.id = r.usuario_id
                WHERE r.fecha = ?
                ORDER BY r.completado_at ASC";
        $query = $db->query($sql, [$fecha]);
        return $query->getResultArray();
    }

    public function RegistrarEjecucion(int $entregaId, int $usuarioId, string $fecha, int $completado = 1): array
    {
        $db = \Config\Database::connect();

        if ($completado === 0) {
            $ok = $db->query(
                "DELETE FROM entrega_registros WHERE entrega_id = ? AND usuario_id = ? AND fecha = ?",
                [$entregaId, $usuarioId, $fecha]
            );
            return [
                'success' => true,
                'message' => 'Tarea desmarcada.',
            ];
        }

        $fechaHora = date('Y-m-d H:i:s');

        $existe = $db->query(
            "SELECT id FROM entrega_registros WHERE entrega_id = ? AND usuario_id = ? AND fecha = ?",
            [$entregaId, $usuarioId, $fecha]
        )->getRowArray();

        if ($existe) {
            return ['success' => false, 'message' => 'Ya registraste esta tarea hoy.'];
        }

        $db->query(
            "INSERT INTO entrega_registros (entrega_id, usuario_id, fecha, completado_at) VALUES (?, ?, ?, ?)",
            [$entregaId, $usuarioId, $fecha, $fechaHora]
        );

        return ['success' => true, 'message' => 'Tarea registrada.'];
    }

    public function EliminarRegistro(int $id): bool
    {
        $db = \Config\Database::connect();
        return $db->query("DELETE FROM entrega_registros WHERE id = ?", [$id]);
    }
}
