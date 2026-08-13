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
        'borrador_id', 'entrega_id', 'tarea_id', 'usuario_id', 'comentario',
    ];

    public function ObtenerPorBorrador(int $borradorId): array
    {
        return $this->select('comentarios.*, admin_usuarios.nombre as autor_nombre, admin_usuarios.foto as autor_foto')
                    ->join('admin_usuarios', 'admin_usuarios.id = comentarios.usuario_id', 'left')
                    ->where('comentarios.borrador_id', $borradorId)
                    ->orderBy('comentarios.created_at', 'ASC')
                    ->findAll();
    }

    public function ObtenerPorEntrega(int $entregaId): array
    {
        return $this->select('comentarios.*, admin_usuarios.nombre as autor_nombre, admin_usuarios.foto as autor_foto')
                    ->join('admin_usuarios', 'admin_usuarios.id = comentarios.usuario_id', 'left')
                    ->where('comentarios.entrega_id', $entregaId)
                    ->orderBy('comentarios.created_at', 'ASC')
                    ->findAll();
    }

    public function ContarPorEntregas(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $db = \Config\Database::connect();
        $filas = $db->table('comentarios')
                    ->select('entrega_id, COUNT(*) as total')
                    ->whereIn('entrega_id', $ids)
                    ->groupBy('entrega_id')
                    ->get()
                    ->getResultArray();

        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['entrega_id']] = (int) $f['total'];
        }
        return $resultado;
    }

    public function ContarPorBorradores(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $db = \Config\Database::connect();
        $filas = $db->table('comentarios')
                    ->select('borrador_id, COUNT(*) as total')
                    ->whereIn('borrador_id', $ids)
                    ->groupBy('borrador_id')
                    ->get()
                    ->getResultArray();

        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['borrador_id']] = (int) $f['total'];
        }
        return $resultado;
    }

    public function Guardar(array $datos): bool
    {
        return $this->insert($datos) ? true : false;
    }
}
