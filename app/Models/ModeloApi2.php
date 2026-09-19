<?php

namespace App\Models;

use CodeIgniter\Model;

class ModeloApi2 extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // 🔐 LOGIN
    public function login(string $usuario, string $clave)
    {
        $builder = $this->db->table('admin_usuarios');

        $user = $builder->select("
                id, username, email, nombre, rol, activo,
                telefono, puesto, id_departamento, password
            ")
            ->groupStart()
                ->where('username', $usuario)
                ->orWhere('email', $usuario)
            ->groupEnd()
            ->where('activo', 1)
            ->get()
            ->getRowArray();

        if (!$user) return false;

        if (password_verify($clave, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return false;
    }

    public function get_data_usuario($id)
    {
        $builder = $this->db->table('admin_usuarios');

        $builder->select("
            id, username, email, nombre, rol, telefono,
            puesto, id_departamento, direccion, observacion
        ");

        $builder->where('activo', 1);
        $builder->where('id', $id);

        $query = $builder->get();
        if ($query->getNumRows() > 0) {
            return $query->getResultArray();
        } else {
            return false;
        }
    }


    public function get_gestores_campo()
    {
        $builder = $this->db->table('admin_usuarios');
        $builder->select("
            id, nombre, username, email, rol,
            telefono,
            puesto, id_departamento
        ");

        $builder->where('activo', 1);
        $builder->whereNotIn('rol', ['admin', 'superadmin']);
        $builder->orderBy('nombre', 'ASC');
        $query = $builder->get();
        return $query->getNumRows() > 0 ? $query->getResult() : false;
    }





    public function insertData2($tabla, $data)
    {
        $this->db->table($tabla)->insert($data);
        return $this->db->insertID(); // 🔥 CLAVE
    }

    // 🔧 INSERT GENERAL (como CI3)
    public function insertData($tabla, $data)
    {
        return $this->db->table($tabla)->insert($data);
    }

    // 🔧 UPDATE GENERAL
    public function updateData($tabla, $data, $campo, $valor)
    {
        return $this->db->table($tabla)
                        ->where($campo, $valor)
                        ->update($data);
    }

    // 🔧 SELECT GENERAL
    public function getData($tabla, $where = [])
    {
        return $this->db->table($tabla)
                        ->where($where)
                        ->get()
                        ->getResultArray();
    }

    public function retornarcampo($tabla, $campo, $where = [])
    {
        $query = $this->db->table($tabla)
            ->select($campo)
            ->where($where)
            ->limit(1)
            ->get();

        if ($query->getNumRows() > 0) {
            return $query->getRow()->$campo;
        }

        return 'error';
    }

    public function eliminar($tabla, $campo, $valor)
    {
        return $this->db->table($tabla)
                        ->where($campo, $valor)
                        ->delete();
    }

    public function recorrido_index($table, $id, $campo)
    {
        $builder = $this->db->table($table);
        $query = $builder->where($campo, $id)->get();

        if ($query->getNumRows() > 0) {
            return $query->getResult(); // 👈 CI4
        } else {
            return []; // 👈 mejor retornar array vacío
        }
    }

    public function insertBatchData($tabla, $data)
    {
        return $this->db->table($tabla)->insertBatch($data);
    }

    public function retornarcampo_ultimo($id, $campo, $tabla, $retorno)
    {
        $builder = $this->db->table($tabla);

        $query = $builder
            ->where($campo, $id)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();

        $row = $query->getRow();

        return $row ? $row->$retorno : null;
    }

    public function retornarcampo_($id_cliente, $campo_cliente, $id_campania, $campo_campania, $tabla, $retorno)
    {
        $builder = $this->db->table($tabla);

        $query = $builder
            ->select($retorno)
            ->where($campo_cliente, $id_cliente)
            ->where($campo_campania, $id_campania)
            ->get();

        $rows = $query->getResult();

        if (!empty($rows)) {
            $ids = array_map(function($row) use ($retorno) {
                return $row->$retorno;
            }, $rows);

            return implode(',', $ids);
        }

        return '';
    }


}
