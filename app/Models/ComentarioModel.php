<?php

namespace App\Models;

use CodeIgniter\Model;

class ComentarioModel extends Model
{
    protected $table      = 'comentarios';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $allowedFields = [
        'borrador_id', 'usuario_id', 'comentario',
    ];

    public function ObtenerPorBorrador(int $borradorId): array
    {
        return $this->select('comentarios.*, admin_usuarios.nombre as autor_nombre')
                    ->join('admin_usuarios', 'admin_usuarios.id = comentarios.usuario_id', 'left')
                    ->where('comentarios.borrador_id', $borradorId)
                    ->orderBy('comentarios.created_at', 'ASC')
                    ->findAll();
    }

    public function Guardar(array $datos): bool
    {
        return $this->insert($datos) ? true : false;
    }
}
