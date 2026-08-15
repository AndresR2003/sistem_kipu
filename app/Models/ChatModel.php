<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table         = 'chat_mensajes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'usuario_id', 'mensaje', 'archivo_nombre', 'archivo_ruta',
        'archivo_mime', 'archivo_tamano', 'creado_en',
    ];

    public function obtenerRecientes(int $desde = 0, int $limite = 80): array
    {
        $builder = $this->select('chat_mensajes.*, admin_usuarios.nombre AS usuario_nombre, admin_usuarios.foto AS usuario_foto')
            ->join('admin_usuarios', 'admin_usuarios.id = chat_mensajes.usuario_id', 'inner')
            ->where('admin_usuarios.activo', 1)
            ->orderBy('chat_mensajes.id', 'DESC')
            ->limit($limite);

        if ($desde > 0) {
            $builder->where('chat_mensajes.id >', $desde);
        }

        return array_reverse($builder->findAll());
    }

    public function obtenerPorId(int $id): ?array
    {
        return $this->where('id', $id)->first();
    }
}
