<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo de Eventos
 * Maneja todas las operaciones CRUD de la tabla eventos para el calendario
 */
class EventoModel extends Model
{
    protected $table            = 'eventos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Fechas automaticas
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Campos permitidos para asignacion masiva
    protected $allowedFields = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'color',
        'usuario_id',
    ];

    // Reglas de validacion
    protected $validationRules = [
        'titulo'       => 'required|max_length[255]',
        'fecha_inicio' => 'required|valid_date',
        'fecha_fin'    => 'permit_empty|valid_date',
        'color'        => 'permit_empty|max_length[7]',
        'usuario_id'   => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'titulo' => [
            'required' => 'El titulo del evento es obligatorio.',
        ],
        'fecha_inicio' => [
            'required'   => 'La fecha de inicio es obligatoria.',
            'valid_date' => 'La fecha de inicio no es valida.',
        ],
    ];

    /**
     * Obtener todos los eventos en un rango de fechas
     */
    public function ObtenerPorRango(string $inicio, string $fin): array
    {
        return $this->where('fecha_inicio >=', $inicio)
                     ->where('fecha_inicio <=', $fin)
                     ->orWhere('fecha_fin >=', $inicio)
                     ->where('fecha_fin <=', $fin)
                     ->orderBy('fecha_inicio', 'ASC')
                     ->findAll();
    }

    /**
     * Obtener todos los eventos activos
     */
    public function ObtenerTodos(): array
    {
        return $this->orderBy('fecha_inicio', 'DESC')->findAll();
    }

    /**
     * Obtener eventos visibles para un usuario en un rango.
     * El creador y los invitados (por usuario o por departamento) lo ven.
     * Los administradores ven todos.
     */
    public function ObtenerVisibles(int $usuarioId, ?int $departamentoId, bool $esAdmin, string $inicio, string $fin): array
    {
        if ($esAdmin) {
            return $this->ObtenerPorRango($inicio, $fin);
        }

        $db = \Config\Database::connect();
        $subDept = $db->table('evento_invitados_departamentos')->select('evento_id')->where('departamento_id', $departamentoId);
        $subUsr  = $db->table('evento_invitados_usuarios')->select('evento_id')->where('usuario_id', $usuarioId);

        return $db->table('eventos e')
                  ->select('e.*')
                  ->where('e.fecha_inicio >=', $inicio)
                  ->where('e.fecha_inicio <=', $fin)
                  ->groupStart()
                      ->where('e.usuario_id', $usuarioId)
                      ->orWhereIn('e.id', $subUsr, true)
                      ->orWhereIn('e.id', $subDept, true)
                  ->groupEnd()
                  ->orderBy('e.fecha_inicio', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Contar eventos a partir de una fecha (hoy en adelante)
     */
    public function ContarProximos(string $fecha): int
    {
        return (int) $this->where('fecha_inicio >=', $fecha)
                         ->countAllResults();
    }

    /**
     * Obtener los proximos eventos (limite)
     */
    public function ObtenerProximos(string $fecha, int $limite = 5): array
    {
        return $this->where('fecha_inicio >=', $fecha)
                    ->orderBy('fecha_inicio', 'ASC')
                    ->limit($limite)
                    ->findAll();
    }

    /**
     * Obtener evento por ID
     */
    public function ObtenerPorId(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Guardar evento (crear o actualizar)
     */
    public function GuardarEvento(array $datos): bool
    {
        // Si se esta actualizando
        if (!empty($datos['id'])) {
            $id = $datos['id'];
            unset($datos['id']);
            return $this->update($id, $datos);
        }

        // Crear nuevo
        return $this->insert($datos);
    }

    /**
     * Eliminar evento por ID
     */
    public function EliminarEvento(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Guardar invitados (departamentos y usuarios) de un evento
     * Patron delete-all + reinsert (igual que tareas/borradores)
     */
    public function GuardarInvitados(int $eventoId, array $departamentoIds, array $usuarioIds): void
    {
        $db = \Config\Database::connect();

        $db->table('evento_invitados_departamentos')->where('evento_id', $eventoId)->delete();
        foreach (array_unique(array_filter(array_map('intval', $departamentoIds))) as $did) {
            $db->table('evento_invitados_departamentos')->insert([
                'evento_id'       => $eventoId,
                'departamento_id' => $did,
            ]);
        }

        $db->table('evento_invitados_usuarios')->where('evento_id', $eventoId)->delete();
        foreach (array_unique(array_filter(array_map('intval', $usuarioIds))) as $uid) {
            $db->table('evento_invitados_usuarios')->insert([
                'evento_id'  => $eventoId,
                'usuario_id' => $uid,
            ]);
        }
    }

    /**
     * Obtener datos del creador (nombre y rol legible)
     */
    public function ObtenerCreador(?int $usuarioId): ?array
    {
        if (!$usuarioId) {
            return null;
        }

        $db = \Config\Database::connect();
        return $db->table('admin_usuarios')
                  ->select('id, nombre, foto, rol')
                  ->where('id', $usuarioId)
                  ->get()
                  ->getRowArray();
    }

    /**
     * Obtener invitados de un evento (departamentos y usuarios)
     */
    public function ObtenerInvitados(int $eventoId): array
    {
        $db = \Config\Database::connect();

        $departamentos = $db->table('evento_invitados_departamentos')
                            ->select('d.id, d.descripcion')
                            ->join('departamentos d', 'd.id = evento_invitados_departamentos.departamento_id', 'inner')
                            ->where('evento_invitados_departamentos.evento_id', $eventoId)
                            ->orderBy('d.descripcion', 'ASC')
                            ->get()
                            ->getResultArray();

        $usuarios = $db->table('evento_invitados_usuarios')
                       ->select('u.id, u.nombre')
                       ->join('admin_usuarios u', 'u.id = evento_invitados_usuarios.usuario_id', 'inner')
                       ->where('evento_invitados_usuarios.evento_id', $eventoId)
                       ->orderBy('u.nombre', 'ASC')
                       ->get()
                       ->getResultArray();

        return [
            'departamentos' => $departamentos,
            'usuarios'      => $usuarios,
        ];
    }
}
