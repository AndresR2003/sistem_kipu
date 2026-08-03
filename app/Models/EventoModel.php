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
}
