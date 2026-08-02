<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartamentoModel extends Model
{
    protected $table      = 'departamentos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['descripcion'];

    public function ObtenerTodos(): array
    {
        return $this->orderBy('descripcion', 'ASC')->findAll();
    }
}
