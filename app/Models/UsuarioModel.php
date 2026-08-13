<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo de Usuarios
 * Maneja todas las operaciones CRUD y consultas de la tabla usuarios
 */
class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Campos permitidos para asignacion masiva
    protected $allowedFields = [
        'nombre',
        'telefono',
        'token',
        'activo',
    ];

    // Reglas de validacion
    protected $validationRules = [
        'nombre'  => 'required|max_length[100]',
        'token'   => 'required|max_length[64]|is_unique[usuarios.token,id,{id}]',
        'activo'  => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio.',
        ],
        'token' => [
            'required'  => 'El token es obligatorio.',
            'is_unique' => 'Este token ya esta en uso.',
        ],
    ];

    /**
     * Obtener todos los usuarios activos
     */
    public function ObtenerActivos(): array
    {
        return $this->where('activo', 1)
                     ->orderBy('nombre', 'ASC')
                     ->findAll();
    }

    /**
     * Obtener todos los usuarios (admin)
     */
    public function ObtenerTodos(): array
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }

    /**
     * Buscar usuario por token
     */
    public function BuscarPorToken(string $token): ?array
    {
        return $this->where('token', $token)
                     ->where('activo', 1)
                     ->first();
    }

    /**
     * Buscar usuario por ID
     */
    public function BuscarPorId(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Generar token unico de 64 caracteres
     */
    public function GenerarToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Contar usuarios activos
     */
    public function ContarActivos(): int
    {
        return $this->where('activo', 1)->countAllResults();
    }

    /**
     * Guardar usuario (crear o actualizar)
     */
    public function GuardarUsuario(array $datos): bool
    {
        // Si se esta creando, generar token
        if (empty($datos['id'])) {
            $datos['token'] = $this->GenerarToken();
            return $this->insert($datos);
        }

        // Actualizar
        $id = $datos['id'];
        unset($datos['id']);
        unset($datos['token']); // No cambiar token al editar
        return $this->update($id, $datos);
    }

    /**
     * Eliminar usuario (desactivar)
     */
    public function EliminarUsuario(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Verificar si un token existe y esta activo
     */
    public function TokenValido(string $token): bool
    {
        return $this->where('token', $token)
                     ->where('activo', 1)
                     ->countAllResults() > 0;
    }
}
