<?php

namespace App\Models;

use CodeIgniter\Model;

class MarcadorModel extends Model
{
    protected $table      = 'marcadores';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'titulo', 'contenido', 'url', 'usuario_id',
    ];

    public function ObtenerTodos(): array
    {
        return $this->orderBy('updated_at DESC')->findAll();
    }

    public function ObtenerPorId(int $id): ?array
    {
        return $this->find($id);
    }

    public function Guardar(array $datos): bool
    {
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
