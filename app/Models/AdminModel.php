<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
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
    ];

    protected $validationRules = [
        'username' => 'required|max_length[50]|is_unique[admin_usuarios.username,id,{id}]',
        'email'    => 'required|valid_email|max_length[100]|is_unique[admin_usuarios.email,id,{id}]',
        'nombre'   => 'required|max_length[100]',
        'rol'      => 'required|in_list[superadmin,admin,empleado,soporte,vendedor,tecnico]',
    ];

    public function login(string $username, string $password): ?array
    {
        $usuario = $this->where('username', $username)
                        ->where('activo', 1)
                        ->first();

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            return null;
        }

        $this->update($usuario['id'], ['ultimo_acceso' => date('Y-m-d H:i:s')]);

        return $usuario;
    }
}
