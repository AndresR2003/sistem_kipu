<?php

namespace App\Models;

use CodeIgniter\Model;

class PaseTurnoModel extends Model
{
    protected $table      = 'pases_turno';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'titulo', 'de_turno_id', 'a_turno_id', 'fecha',
        'estado', 'creado_por', 'cerrado_por', 'cerrado_at',
    ];

    // ─── Catálogo de turnos ───

    public function Turnos(bool $soloActivos = true): array
    {
        $db = \Config\Database::connect();
        $q = $db->table('turnos');
        if ($soloActivos) {
            $q->where('activo', 1);
        }
        return $q->orderBy('orden ASC, nombre ASC')->get()->getResultArray();
    }

    public function GuardarTurno(string $nombre, ?string $descripcion, int $orden, int $activo, ?int $id = null): bool
    {
        $db = \Config\Database::connect();
        $datos = [
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'orden'       => $orden,
            'activo'      => $activo ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        if ($id) {
            return (bool) $db->table('turnos')->where('id', $id)->update($datos);
        }
        $datos['created_at'] = date('Y-m-d H:i:s');
        return (bool) $db->table('turnos')->insert($datos);
    }

    public function EliminarTurno(int $id): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('turnos')->where('id', $id)->delete();
    }

    // ─── Pases de turno ───

    public function ListarPases(string $estado = ''): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT p.id, p.titulo, p.fecha, p.estado, p.cerrado_at,
                       t1.nombre AS de_turno, t1.id AS de_turno_id,
                       t2.nombre AS a_turno, t2.id AS a_turno_id,
                       u.nombre AS creador_nombre,
                       (SELECT COUNT(*) FROM pase_puntos pp WHERE pp.pase_id = p.id) AS total_puntos,
                       (SELECT COUNT(*) FROM pase_puntos pp WHERE pp.pase_id = p.id AND pp.estado != 'pendiente') AS puntos_avanzados,
                       (SELECT COUNT(*) FROM pase_puntos pp WHERE pp.pase_id = p.id AND pp.estado = 'pendiente') AS puntos_pendientes
                FROM pases_turno p
                LEFT JOIN turnos t1 ON t1.id = p.de_turno_id
                LEFT JOIN turnos t2 ON t2.id = p.a_turno_id
                LEFT JOIN admin_usuarios u ON u.id = p.creado_por
                WHERE 1 = 1";

        $params = [];
        if ($estado !== '') {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY p.fecha DESC, p.id DESC";
        return $db->query($sql, $params)->getResultArray();
    }

    public function ContarAbiertos(): int
    {
        $db = \Config\Database::connect();
        return (int) $db->table('pases_turno')->where('estado', 'abierto')->countAllResults();
    }

    public function ObtenerPase(int $id): ?array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT p.*, t1.nombre AS de_turno, t2.nombre AS a_turno,
                       u.nombre AS creador_nombre, cu.nombre AS cerrado_por_nombre
                FROM pases_turno p
                LEFT JOIN turnos t1 ON t1.id = p.de_turno_id
                LEFT JOIN turnos t2 ON t2.id = p.a_turno_id
                LEFT JOIN admin_usuarios u ON u.id = p.creado_por
                LEFT JOIN admin_usuarios cu ON cu.id = p.cerrado_por
                WHERE p.id = ?";
        return $db->query($sql, [$id])->getRowArray();
    }

    public function GuardarPase(array $datos): int
    {
        $db = \Config\Database::connect();
        $datos['created_at'] = date('Y-m-d H:i:s');
        $db->table('pases_turno')->insert($datos);
        return (int) $db->insertID();
    }

    public function CerrarPase(int $id, int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pases_turno')
            ->where('id', $id)
            ->update([
                'estado'      => 'cerrado',
                'cerrado_por' => $usuarioId,
                'cerrado_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
    }

    public function ReabrirPase(int $id): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pases_turno')
            ->where('id', $id)
            ->update([
                'estado'      => 'abierto',
                'cerrado_por' => null,
                'cerrado_at'  => null,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
    }

    public function EliminarPase(int $id): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pases_turno')->where('id', $id)->delete();
    }

    // ─── Puntos del pase ───

    public function ObtenerPuntos(int $paseId): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT pp.*,
                       COALESCE(d.descripcion, 'General') AS area_nombre,
                       cu.nombre AS creador_nombre,
                       au.nombre AS actualizado_nombre,
                       tu.titulo AS tarea_titulo
                FROM pase_puntos pp
                LEFT JOIN departamentos d ON d.id = pp.area_id
                LEFT JOIN admin_usuarios cu ON cu.id = pp.creado_por
                LEFT JOIN admin_usuarios au ON au.id = pp.actualizado_por
                LEFT JOIN tareas tu ON tu.id = pp.tarea_id
                WHERE pp.pase_id = ?
                ORDER BY COALESCE(d.descripcion, 'ZZZZ') ASC, pp.id ASC";
        return $db->query($sql, [$paseId])->getResultArray();
    }

    public function GuardarPunto(array $datos, ?int $id = null): int
    {
        $db = \Config\Database::connect();
        $ahora = date('Y-m-d H:i:s');
        if ($id) {
            $datos['actualizado_por'] = $datos['creado_por'];
            $datos['updated_at'] = $ahora;
            $db->table('pase_puntos')->where('id', $id)->update($datos);
            return (int) $id;
        }
        $datos['created_at'] = $ahora;
        $db->table('pase_puntos')->insert($datos);
        return (int) $db->insertID();
    }

    public function CambiarEstadoPunto(int $id, string $estado, int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pase_puntos')
            ->where('id', $id)
            ->update([
                'estado'          => $estado,
                'actualizado_por' => $usuarioId,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
    }

    public function VincularTarea(int $puntoId, int $tareaId): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pase_puntos')->where('id', $puntoId)->update(['tarea_id' => $tareaId]);
    }

    public function DesvincularTarea(int $puntoId): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pase_puntos')->where('id', $puntoId)->update(['tarea_id' => null]);
    }

    public function EliminarPunto(int $id): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pase_puntos')->where('id', $id)->delete();
    }

    public function ObtenerPunto(int $id): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('pase_puntos')->where('id', $id)->get()->getRowArray();
    }

    // ─── Comentarios por punto ───

    public function ListarComentarios(int $puntoId): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT c.*, u.nombre AS autor_nombre
                FROM pase_punto_comentarios c
                LEFT JOIN admin_usuarios u ON u.id = c.usuario_id
                WHERE c.punto_id = ?
                ORDER BY c.created_at ASC";
        return $db->query($sql, [$puntoId])->getResultArray();
    }

    public function GuardarComentario(int $puntoId, int $usuarioId, string $comentario): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->table('pase_punto_comentarios')->insert([
            'punto_id'   => $puntoId,
            'usuario_id' => $usuarioId,
            'comentario' => $comentario,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}