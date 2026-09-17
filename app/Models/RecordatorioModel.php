<?php

namespace App\Models;

use CodeIgniter\Model;

class RecordatorioModel extends Model
{
    protected $table            = 'recordatorios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'titulo', 'descripcion', 'fecha', 'prioridad', 'completado', 'usuario_id', 'tipo',
        'origen_id', 'origen_tipo', 'seccion',
    ];

    protected $validationRules = [
        'titulo'    => 'required|max_length[255]',
        'fecha'     => 'permit_empty|valid_date',
        'prioridad' => 'permit_empty|in_list[baja,media,alta]',
        'tipo'      => 'permit_empty|in_list[recordatorio,marcador]',
    ];

    protected $validationMessages = [
        'titulo' => ['required' => 'El titulo del recordatorio es obligatorio.'],
        'fecha'  => ['required' => 'La fecha es obligatoria.', 'valid_date' => 'La fecha no es valida.'],
    ];

    public function ObtenerTodos(string $tipo = 'recordatorio', ?int $usuarioId = null): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * FROM recordatorios WHERE tipo = ? AND (usuario_id = ? OR usuario_id IS NULL) ORDER BY fecha ASC";
        $query = $db->query($sql, [$tipo, $usuarioId]);
        return $query->getResultArray();
    }

    public function ObtenerTodosConOrigen(string $tipo = 'recordatorio', ?int $usuarioId = null): array
    {
        $data = $this->ObtenerTodos($tipo, $usuarioId);

        $db  = \Config\Database::connect();
        $ids = ['borrador' => [], 'tarea' => [], 'entrega' => []];

        foreach ($data as $item) {
            $ot = $item['origen_tipo'] ?: 'borrador';
            if (!empty($item['origen_id']) && isset($ids[$ot])) {
                $ids[$ot][] = (int) $item['origen_id'];
            }
        }

        $origenes = [];
        $consultas = [
            'borrador' => 'SELECT b.id, b.titulo, b.contenido, b.updated_at, b.created_at,
                                  u.nombre AS autor_nombre, u.rol AS autor_rol, u.foto AS autor_foto
                           FROM borradores b
                           LEFT JOIN admin_usuarios u ON u.id = b.usuario_id
                           WHERE b.id IN (%s)',
            'tarea'    => 'SELECT t.id, t.titulo, t.descripcion AS contenido, t.updated_at, t.created_at,
                                  u.nombre AS autor_nombre, u.rol AS autor_rol, u.foto AS autor_foto
                           FROM tareas t
                           LEFT JOIN admin_usuarios u ON u.id = t.created_by
                           WHERE t.id IN (%s)',
            'entrega'  => 'SELECT e.id, e.titulo, e.descripcion AS contenido, e.updated_at, e.created_at,
                                  u.nombre AS autor_nombre, u.rol AS autor_rol, u.foto AS autor_foto
                           FROM entregas e
                           LEFT JOIN admin_usuarios u ON u.id = e.created_by
                           WHERE e.id IN (%s)',
        ];

        foreach ($consultas as $tipoOrigen => $plantilla) {
            if (empty($ids[$tipoOrigen])) {
                continue;
            }
            $marcadores = implode(',', array_fill(0, count($ids[$tipoOrigen]), '?'));
            $rows = $db->query(sprintf($plantilla, $marcadores), $ids[$tipoOrigen])->getResultArray();
            foreach ($rows as $row) {
                $origenes[$tipoOrigen][(int) $row['id']] = $row;
            }
        }

        foreach ($data as &$item) {
            $ot  = $item['origen_tipo'] ?: 'borrador';
            $oid = (int) ($item['origen_id'] ?? 0);

            if (($item['seccion'] ?? '') === 'tareas_diarias') {
                $item['seccion'] = 'tareas';
            }

            $item['origen_titulo']    = null;
            $item['origen_contenido'] = null;
            $item['origen_meta']      = null;
            $item['origen_fecha']     = null;
            $item['origen_hora']      = null;
            $item['autor_nombre']     = null;
            $item['autor_foto']       = null;
            $item['autor_rol']        = null;
            $item['autor_rol_legible'] = null;
            $item['comentarios_count'] = 0;

            $origen = $origenes[$ot][$oid] ?? null;
            if ($origen) {
                $meta = $origen['updated_at'] ?: ($origen['created_at'] ?? null);
                $item['origen_titulo']     = $origen['titulo'] ?? null;
                $item['origen_contenido']  = $origen['contenido'] ?? null;
                $item['origen_meta']       = $meta;
                $item['origen_fecha']      = $meta ? fecha_publicacion($meta) : null;
                $item['origen_hora']       = $meta ? hora_publicacion($meta) : null;
                $item['autor_nombre']      = $origen['autor_nombre'] ?? null;
                $item['autor_foto']        = $origen['autor_foto'] ?? null;
                $item['autor_rol']         = $origen['autor_rol'] ?? null;
                $item['autor_rol_legible'] = rol_legible($origen['autor_rol'] ?? null);
            }

            if ($oid > 0) {
                if ($ot === 'entrega') {
                    $item['comentarios_count'] = (int) $db->table('comentarios')->where('entrega_id', $oid)->countAllResults();
                } elseif ($ot === 'tarea') {
                    $item['comentarios_count'] = (int) $db->table('comentarios')->where('tarea_id', $oid)->countAllResults();
                } else {
                    $item['comentarios_count'] = (int) $db->table('comentarios')->where('borrador_id', $oid)->countAllResults();
                }
            }
        }

        return $data;
    }

    public function ObtenerPorId(int $id): ?array
    {
        return $this->find($id);
    }

    public function ContarPendientes(string $tipo = 'recordatorio', ?int $usuarioId = null): int
    {
        $db = \Config\Database::connect();
        $sql = "SELECT COUNT(*) AS total FROM recordatorios WHERE tipo = ? AND completado = 0 AND (usuario_id = ? OR usuario_id IS NULL)";
        $query = $db->query($sql, [$tipo, $usuarioId]);
        $row = $query->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    public function ContarTodos(string $tipo = 'recordatorio', ?int $usuarioId = null): int
    {
        $db = \Config\Database::connect();
        $sql = "SELECT COUNT(*) AS total FROM recordatorios WHERE tipo = ? AND (usuario_id = ? OR usuario_id IS NULL)";
        $query = $db->query($sql, [$tipo, $usuarioId]);
        $row = $query->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    public function Guardar(array $datos): bool
    {
        if (!isset($datos['tipo'])) {
            $datos['tipo'] = 'recordatorio';
        }
        if (!empty($datos['id'])) {
            $id = $datos['id'];
            unset($datos['id']);
            return $this->update($id, $datos);
        }
        return $this->insert($datos) ? true : false;
    }

    public function Eliminar(int $id): bool
    {
        return $this->delete($id);
    }
}
