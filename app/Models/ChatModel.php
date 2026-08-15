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
        'usuario_id', 'destinatario_id', 'mensaje', 'archivo_nombre', 'archivo_ruta',
        'archivo_mime', 'archivo_tamano', 'creado_en',
    ];

    public function obtenerRecientes(int $usuarioId, ?int $destinatarioId = null, int $desde = 0, int $limite = 80): array
    {
        $builder = $this->select('chat_mensajes.*, admin_usuarios.nombre AS usuario_nombre, admin_usuarios.foto AS usuario_foto')
            ->join('admin_usuarios', 'admin_usuarios.id = chat_mensajes.usuario_id', 'inner')
            ->where('admin_usuarios.activo', 1);

        if ($destinatarioId === null) {
            $builder->where('chat_mensajes.destinatario_id', null);
        } else {
            $builder->groupStart()
                ->groupStart()
                    ->where('chat_mensajes.usuario_id', $usuarioId)
                    ->where('chat_mensajes.destinatario_id', $destinatarioId)
                ->groupEnd()
                ->orGroupStart()
                    ->where('chat_mensajes.usuario_id', $destinatarioId)
                    ->where('chat_mensajes.destinatario_id', $usuarioId)
                ->groupEnd()
            ->groupEnd();
        }

        if ($desde > 0) {
            $builder->where('chat_mensajes.id >', $desde);
        }

        return array_reverse($builder->orderBy('chat_mensajes.id', 'DESC')->limit($limite)->findAll());
    }

    public function obtenerUsuarios(int $usuarioId): array
    {
        return $this->db->table('admin_usuarios')
            ->select('id, nombre, username, foto, rol')
            ->where('activo', 1)
            ->where('id !=', $usuarioId)
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function obtenerPorId(int $id, ?int $usuarioId = null): ?array
    {
        $builder = $this->where('chat_mensajes.id', $id);
        if ($usuarioId !== null) {
            $builder->groupStart()
                ->where('chat_mensajes.destinatario_id', null)
                ->orGroupStart()
                    ->where('chat_mensajes.usuario_id', $usuarioId)
                    ->orWhere('chat_mensajes.destinatario_id', $usuarioId)
                ->groupEnd()
            ->groupEnd();
        }
        return $builder->first();
    }
}
