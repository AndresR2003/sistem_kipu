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
        'borrador_id', 'entrega_id', 'tarea_id', 'usuario_id', 'comentario', 'archivos',
    ];

    protected $afterFind = ['decodificarArchivos'];

    protected function decodificarArchivos(array $data)
    {
        if (!array_key_exists('data', $data) || !is_array($data['data'])) {
            return $data;
        }

        if (!empty($data['singleton'])) {
            $data['data'] = $this->normalizarArchivosFila($data['data']);
        } else {
            foreach ($data['data'] as &$fila) {
                if (is_array($fila)) {
                    $fila = $this->normalizarArchivosFila($fila);
                }
            }
            unset($fila);
        }

        return $data;
    }

    private function normalizarArchivosFila(array $fila): array
    {
        if (!array_key_exists('archivos', $fila)) {
            return $fila;
        }

        if (empty($fila['archivos'])) {
            $fila['archivos'] = [];
            return $fila;
        }

        if (is_string($fila['archivos'])) {
            $decodificado = json_decode($fila['archivos'], true);
            $fila['archivos'] = is_array($decodificado) ? $decodificado : [];
        }

        return $fila;
    }

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

    public function ObtenerPorTarea(int $tareaId): array
    {
        return $this->select('comentarios.*, admin_usuarios.nombre as autor_nombre, admin_usuarios.foto as autor_foto')
                    ->join('admin_usuarios', 'admin_usuarios.id = comentarios.usuario_id', 'left')
                    ->where('comentarios.tarea_id', $tareaId)
                    ->orderBy('comentarios.created_at', 'ASC')
                    ->findAll();
    }

    public function EnriquecerLikes(array $comentarios): array
    {
        if (empty($comentarios)) {
            return $comentarios;
        }
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $ids = array_column($comentarios, 'id');
        $interaccion = new \App\Models\InteraccionModel();
        $likes = $interaccion->ContarLikesPorComentarios($ids);
        $meGusta = $interaccion->MeGustaComentarios($ids, $usuarioId);
        foreach ($comentarios as &$c) {
            $c['likes_count'] = $likes[$c['id']] ?? 0;
            $c['me_gusta']    = !empty($meGusta[$c['id']]);
        }
        return $comentarios;
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
