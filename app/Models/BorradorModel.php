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
        $db = \Config\Database::connect();

        $sql = "SELECT b.*, u.nombre AS usuario_nombre
                FROM borradores b
                LEFT JOIN admin_usuarios u ON u.id = b.usuario_id
                WHERE b.publicado = 1 AND b.seccion_destino = ?";

        $params = [$seccion];

        // Admin y superadmin ven todas las publicaciones
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            $sql .= " AND (
                (NOT EXISTS (SELECT 1 FROM borrador_departamentos bd WHERE bd.borrador_id = b.id)
                 AND NOT EXISTS (SELECT 1 FROM borrador_usuarios bu WHERE bu.borrador_id = b.id))
                OR (? IS NOT NULL AND EXISTS (SELECT 1 FROM borrador_departamentos bd WHERE bd.borrador_id = b.id AND bd.departamento_id = ?))
                OR (? IS NOT NULL AND EXISTS (SELECT 1 FROM borrador_usuarios bu WHERE bu.borrador_id = b.id AND bu.usuario_id = ?))
            )";
            $params[] = $departamentoId;
            $params[] = $departamentoId;
            $params[] = $usuarioId;
            $params[] = $usuarioId;
        }

        $sql .= " ORDER BY b.fijado DESC, b.updated_at DESC";

        if ($limite !== null) {
            $sql .= " LIMIT " . (int) $limite;
        }

        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }

    public function ContarPublicados(string $seccion, ?int $usuarioId = null, ?int $departamentoId = null, string $rol = 'empleado'): int
    {
        $db = \Config\Database::connect();

        $sql = "SELECT COUNT(*) AS total
                FROM borradores b
                WHERE b.publicado = 1 AND b.seccion_destino = ?";

        $params = [$seccion];

        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            $sql .= " AND (
                (NOT EXISTS (SELECT 1 FROM borrador_departamentos bd WHERE bd.borrador_id = b.id)
                 AND NOT EXISTS (SELECT 1 FROM borrador_usuarios bu WHERE bu.borrador_id = b.id))
                OR (? IS NOT NULL AND EXISTS (SELECT 1 FROM borrador_departamentos bd WHERE bd.borrador_id = b.id AND bd.departamento_id = ?))
                OR (? IS NOT NULL AND EXISTS (SELECT 1 FROM borrador_usuarios bu WHERE bu.borrador_id = b.id AND bu.usuario_id = ?))
            )";
            $params[] = $departamentoId;
            $params[] = $departamentoId;
            $params[] = $usuarioId;
            $params[] = $usuarioId;
        }

        $query = $db->query($sql, $params);
        return (int) ($query->getRowArray()['total'] ?? 0);
    }

    public function GuardarDepartamentos(int $borradorId, array $departamentoIds): void
    {
        $db = \Config\Database::connect();
        $db->table('borrador_departamentos')->where('borrador_id', $borradorId)->delete();

        foreach (array_unique(array_filter($departamentoIds)) as $deptId) {
            $db->table('borrador_departamentos')->insert([
                'borrador_id'    => $borradorId,
                'departamento_id' => (int) $deptId,
            ]);
        }
    }

    public function GuardarUsuarios(int $borradorId, array $usuarioIds): void
    {
        $db = \Config\Database::connect();
        $db->table('borrador_usuarios')->where('borrador_id', $borradorId)->delete();

        foreach (array_unique(array_filter($usuarioIds)) as $uid) {
            $db->table('borrador_usuarios')->insert([
                'borrador_id' => $borradorId,
                'usuario_id'  => (int) $uid,
            ]);
        }
    }

    public function ObtenerDepartamentos(int $borradorId): array
    {
        $db = \Config\Database::connect();
        return $db->table('borrador_departamentos')
            ->where('borrador_id', $borradorId)
            ->get()
            ->getResultArray();
    }

    public function ObtenerUsuarios(int $borradorId): array
    {
        $db = \Config\Database::connect();
        return $db->table('borrador_usuarios')
            ->where('borrador_id', $borradorId)
            ->get()
            ->getResultArray();
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
