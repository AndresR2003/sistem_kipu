<?php

namespace App\Models;

use CodeIgniter\Model;

class ColaboradorModel extends Model
{
    protected $table            = 'admin_usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'username', 'email', 'password', 'nombre', 'rol', 'activo',
        'ultimo_acceso', 'foto', 'id_departamento',
        'telefono', 'puesto', 'fecha_nacimiento', 'fecha_contratacion',
        'idioma', 'preferencias_notificacion',
    ];

    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'username' => 'required|max_length[50]|is_unique[admin_usuarios.username,id,{id}]',
        'email'    => 'required|valid_email|max_length[100]|is_unique[admin_usuarios.email,id,{id}]',
        'nombre'   => 'required|max_length[100]',
        'rol'      => 'required|in_list[superadmin,admin,empleado,soporte,vendedor,tecnico]',
    ];

    public function ObtenerTodos(): array
    {
        return $this->select('admin_usuarios.*,departamentos.descripcion as dep_des')
                    ->join('departamentos', 'departamentos.id = admin_usuarios.id_departamento', 'left')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    public function ObtenerPorId(int $id): ?array
    {
        return $this->find($id);
    }

    public function Guardar(array $datos): bool
    {
        if (!empty($datos['id'])) {
            $id = (int) $datos['id'];
            unset($datos['id']);
            if (empty($datos['password'])) {
                unset($datos['password']);
            } else {
                $datos['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
            }
            $datos['id'] = $id;
            $ok = $this->update($id, $datos);
            unset($datos['id']);
            return $ok;
        }

        if (!empty($datos['password'])) {
            $datos['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }
        return $this->insert($datos) ? true : false;
    }

    public function Eliminar(int $id): bool
    {
        return $this->delete($id);
    }
}
