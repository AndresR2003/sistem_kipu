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
