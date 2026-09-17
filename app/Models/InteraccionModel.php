<?php

namespace App\Models;

use CodeIgniter\Model;

class InteraccionModel extends Model
{
    protected $table      = 'publicacion_likes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    // ─── Likes de publicaciones ───

    public function ToggleLikePublicacion(int $publicacionId, int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        $existente = $db->table('publicacion_likes')
                        ->where('publicacion_id', $publicacionId)
                        ->where('usuario_id', $usuarioId)
                        ->get()->getRow();
        if ($existente) {
            return $db->table('publicacion_likes')
                      ->where('publicacion_id', $publicacionId)
                      ->where('usuario_id', $usuarioId)
                      ->delete();
        }
        return (bool) $db->table('publicacion_likes')->insert([
            'publicacion_id' => $publicacionId,
            'usuario_id'     => $usuarioId,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function ContarLikesPorPublicaciones(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('publicacion_likes')
                    ->select('publicacion_id, COUNT(*) as total')
                    ->whereIn('publicacion_id', $ids)
                    ->groupBy('publicacion_id')
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['publicacion_id']] = (int) $f['total'];
        }
        return $resultado;
    }

    public function MeGustaPublicaciones(array $ids, int $usuarioId): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('publicacion_likes')
                    ->select('publicacion_id')
                    ->whereIn('publicacion_id', $ids)
                    ->where('usuario_id', $usuarioId)
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['publicacion_id']] = true;
        }
        return $resultado;
    }

    public function ObtenerLikesPublicacion(int $publicacionId): array
    {
        $db = \Config\Database::connect();
        return $db->query(
            "SELECT u.id, u.nombre, u.foto, u.rol, pl.created_at
             FROM publicacion_likes pl
             LEFT JOIN admin_usuarios u ON u.id = pl.usuario_id
             WHERE pl.publicacion_id = ?
             ORDER BY pl.created_at DESC",
            [$publicacionId]
        )->getResultArray();
    }

    // ─── Vistos de publicaciones ───

    public function MarcarVisto(int $publicacionId, int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        return (bool) $db->query(
            "INSERT IGNORE INTO publicacion_vistos (publicacion_id, usuario_id, visto_en)
             VALUES (?, ?, NOW())",
            [$publicacionId, $usuarioId]
        );
    }

    public function MarcarVistos(array $ids, int $usuarioId): void
    {
        if (empty($ids)) {
            return;
        }
        $db = \Config\Database::connect();
        $sql = "INSERT IGNORE INTO publicacion_vistos (publicacion_id, usuario_id, visto_en) VALUES ";
        $valores = [];
        $params = [];
        foreach ($ids as $pid) {
            $valores[] = "(?, ?, NOW())";
            $params[] = (int) $pid;
            $params[] = $usuarioId;
        }
        $sql .= implode(', ', $valores);
        $db->query($sql, $params);
    }

    public function ContarVistosPorPublicaciones(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('publicacion_vistos')
                    ->select('publicacion_id, COUNT(*) as total')
                    ->whereIn('publicacion_id', $ids)
                    ->groupBy('publicacion_id')
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['publicacion_id']] = (int) $f['total'];
        }
        return $resultado;
    }

    public function VistoPorPublicaciones(array $ids, int $usuarioId): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('publicacion_vistos')
                    ->select('publicacion_id')
                    ->whereIn('publicacion_id', $ids)
                    ->where('usuario_id', $usuarioId)
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['publicacion_id']] = true;
        }
        return $resultado;
    }

    public function ObtenerVistosPublicacion(int $publicacionId): array
    {
        $db = \Config\Database::connect();
        return $db->query(
            "SELECT u.id, u.nombre, u.foto, u.rol, pv.visto_en
             FROM publicacion_vistos pv
             LEFT JOIN admin_usuarios u ON u.id = pv.usuario_id
             WHERE pv.publicacion_id = ?
             ORDER BY pv.visto_en DESC",
            [$publicacionId]
        )->getResultArray();
    }

    // ─── Likes de comentarios (tabla comentarios) ───

    public function ToggleLikeComentario(int $comentarioId, int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        $existente = $db->table('comentario_likes')
                        ->where('comentario_id', $comentarioId)
                        ->where('usuario_id', $usuarioId)
                        ->get()->getRow();
        if ($existente) {
            return $db->table('comentario_likes')
                      ->where('comentario_id', $comentarioId)
                      ->where('usuario_id', $usuarioId)
                      ->delete();
        }
        return (bool) $db->table('comentario_likes')->insert([
            'comentario_id' => $comentarioId,
            'usuario_id'    => $usuarioId,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function ContarLikesPorComentarios(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('comentario_likes')
                    ->select('comentario_id, COUNT(*) as total')
                    ->whereIn('comentario_id', $ids)
                    ->groupBy('comentario_id')
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['comentario_id']] = (int) $f['total'];
        }
        return $resultado;
    }

    public function MeGustaComentarios(array $ids, int $usuarioId): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('comentario_likes')
                    ->select('comentario_id')
                    ->whereIn('comentario_id', $ids)
                    ->where('usuario_id', $usuarioId)
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['comentario_id']] = true;
        }
        return $resultado;
    }

    // ─── Likes de comentarios de pases (pase_punto_comentarios) ───

    public function ToggleLikePaseComentario(int $comentarioId, int $usuarioId): bool
    {
        $db = \Config\Database::connect();
        $existente = $db->table('pase_comentario_likes')
                        ->where('comentario_id', $comentarioId)
                        ->where('usuario_id', $usuarioId)
                        ->get()->getRow();
        if ($existente) {
            return $db->table('pase_comentario_likes')
                      ->where('comentario_id', $comentarioId)
                      ->where('usuario_id', $usuarioId)
                      ->delete();
        }
        return (bool) $db->table('pase_comentario_likes')->insert([
            'comentario_id' => $comentarioId,
            'usuario_id'    => $usuarioId,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function ContarLikesPorPaseComentarios(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('pase_comentario_likes')
                    ->select('comentario_id, COUNT(*) as total')
                    ->whereIn('comentario_id', $ids)
                    ->groupBy('comentario_id')
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['comentario_id']] = (int) $f['total'];
        }
        return $resultado;
    }

    public function MeGustaPaseComentarios(array $ids, int $usuarioId): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = \Config\Database::connect();
        $filas = $db->table('pase_comentario_likes')
                    ->select('comentario_id')
                    ->whereIn('comentario_id', $ids)
                    ->where('usuario_id', $usuarioId)
                    ->get()->getResultArray();
        $resultado = [];
        foreach ($filas as $f) {
            $resultado[$f['comentario_id']] = true;
        }
        return $resultado;
    }
}