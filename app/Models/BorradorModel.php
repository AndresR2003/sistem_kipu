<?php

namespace App\Models;

use CodeIgniter\Model;

class BorradorModel extends Model
{
    protected $table      = 'borradores';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'titulo', 'contenido', 'etiqueta', 'fijado', 'usuario_id',
        'seccion_destino', 'destinatario_tipo', 'destinatario_id', 'publicado', 'completado', 'anuncio',
    ];

    protected $validationRules = [
        'titulo' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'titulo' => ['required' => 'El titulo es obligatorio.'],
    ];

    public function ObtenerTodos(): array
    {
        return $this->orderBy('fijado DESC, updated_at DESC')->findAll();
    }

    public function ObtenerPorId(int $id): ?array
    {
        return $this->find($id);
    }

    public function ObtenerPublicados(string $seccion, ?int $usuarioId = null, ?int $departamentoId = null, string $rol = 'empleado', ?int $limite = null): array
    {
        $this->select('borradores.*, u.nombre AS usuario_nombre');
        $this->join('admin_usuarios u', 'u.id = borradores.usuario_id', 'left');
        $this->where('publicado', 1)->where('seccion_destino', $seccion);

        // Admin y superadmin ven todas las publicaciones
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            $this->groupStart();
            $this->where('destinatario_tipo', 'todos');
            if ($usuarioId) {
                $this->orWhere('destinatario_tipo', 'usuarios')->where('destinatario_id', $usuarioId);
            }
            if ($departamentoId) {
                $this->orWhere('destinatario_tipo', 'departamento')->where('destinatario_id', $departamentoId);
            }
            $this->groupEnd();
        }

        if ($limite !== null) {
            $this->limit($limite);
        }

        return $this->orderBy('fijado DESC, updated_at DESC')->findAll();
    }

    public function ContarPublicados(string $seccion, ?int $usuarioId = null, ?int $departamentoId = null, string $rol = 'empleado'): int
    {
        $this->where('publicado', 1)->where('seccion_destino', $seccion);

        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            $this->groupStart();
            $this->where('destinatario_tipo', 'todos');
            if ($usuarioId) {
                $this->orWhere('destinatario_tipo', 'usuarios')->where('destinatario_id', $usuarioId);
            }
            if ($departamentoId) {
                $this->orWhere('destinatario_tipo', 'departamento')->where('destinatario_id', $departamentoId);
            }
            $this->groupEnd();
        }

        return (int) $this->countAllResults();
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

    public function Publicar(int $id, string $seccion, string $tipo, ?int $destinatarioId, int $anuncio = 0): bool
    {
        return $this->update($id, [
            'seccion_destino'    => $seccion,
            'destinatario_tipo'  => $tipo,
            'destinatario_id'    => $destinatarioId,
            'publicado'          => 1,
            'anuncio'            => $anuncio ? 1 : 0,
        ]);
    }

    public function Despublicar(int $id): bool
    {
        return $this->update($id, [
            'publicado' => 0,
        ]);
    }

    public function ObtenerUltimoAnuncio(): ?array
    {
        $db = \Config\Database::connect();
        $row = $db->table('borradores')
            ->where('anuncio', 1)
            ->where('publicado', 1)
            ->orderBy('updated_at', 'DESC')
            ->get(1)
            ->getRowArray();
        return $row ?: null;
    }

    public function ToggleCompletado(int $id, int $completado): bool
    {
        return $this->update($id, ['completado' => $completado]);
    }
}
