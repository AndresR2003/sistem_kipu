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
        $builder = $this->select('chat_mensajes.*, admin_usuarios.nombre AS usuario_nombre, admin_usuarios.foto AS usuario_foto, CASE WHEN EXISTS (SELECT 1 FROM chat_lecturas WHERE chat_lecturas.mensaje_id = chat_mensajes.id AND chat_lecturas.usuario_id != chat_mensajes.usuario_id) THEN 1 ELSE 0 END AS leido')
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

    public function marcarComoLeidos(int $usuarioId, ?int $destinatarioId = null): void
    {
        if ($destinatarioId === null) {
            $sql = 'INSERT IGNORE INTO chat_lecturas (mensaje_id, usuario_id, leido_en) '
                . 'SELECT id, ?, NOW() FROM chat_mensajes WHERE destinatario_id IS NULL AND usuario_id != ?';
            $this->db->query($sql, [$usuarioId, $usuarioId]);
            return;
        }

        $sql = 'INSERT IGNORE INTO chat_lecturas (mensaje_id, usuario_id, leido_en) '
            . 'SELECT id, ?, NOW() FROM chat_mensajes WHERE usuario_id != ? AND destinatario_id = ?';
        $this->db->query($sql, [$usuarioId, $usuarioId, $usuarioId]);
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

    public function obtenerConversaciones(int $usuarioId): array
    {
        $conversaciones = [];
        $grupal = $this->where('destinatario_id', null)
            ->orderBy('id', 'DESC')
            ->first();
        $ultimoGrupalMensaje = $grupal
            ? ($grupal['mensaje'] ?: ($grupal['archivo_nombre'] ? 'Archivo adjunto' : 'Aún no hay mensajes'))
            : 'Aún no hay mensajes';

        $conversaciones[] = [
            'tipo'           => 'grupo',
            'usuario_id'     => null,
            'nombre'         => 'Chat grupal',
            'foto'           => null,
            'ultimo_mensaje' => $ultimoGrupalMensaje,
            'ultimo_en'      => $grupal['creado_en'] ?? null,
            'ultimo_id'      => (int) ($grupal['id'] ?? 0),
            'ultimo_usuario_id' => (int) ($grupal['usuario_id'] ?? 0),
        ];

        foreach ($this->obtenerUsuarios($usuarioId) as $usuario) {
            $ultimo = $this->groupStart()
                ->groupStart()
                    ->where('usuario_id', $usuarioId)
                    ->where('destinatario_id', $usuario['id'])
                ->groupEnd()
                ->orGroupStart()
                    ->where('usuario_id', $usuario['id'])
                    ->where('destinatario_id', $usuarioId)
                ->groupEnd()
            ->groupEnd()
                ->orderBy('id', 'DESC')
                ->first();

            $conversaciones[] = [
                'tipo'           => 'individual',
                'usuario_id'     => (int) $usuario['id'],
                'nombre'         => $usuario['nombre'],
                'foto'           => $usuario['foto'],
                'ultimo_mensaje' => $ultimo ? ($ultimo['mensaje'] ?: 'Archivo adjunto') : 'Iniciar conversación',
                'ultimo_en'      => $ultimo['creado_en'] ?? null,
                'ultimo_id'      => (int) ($ultimo['id'] ?? 0),
                'ultimo_usuario_id' => (int) ($ultimo['usuario_id'] ?? 0),
            ];
        }

        return $conversaciones;
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
