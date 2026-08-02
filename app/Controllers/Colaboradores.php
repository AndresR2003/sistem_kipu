<?php

namespace App\Controllers;

use App\Models\ColaboradorModel;
use CodeIgniter\API\ResponseTrait;

class Colaboradores extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('layout', [
            'contenido'   => view('colaboradores'),
            'titulo'      => 'Personal - Litio',
            'pageScripts' => '<script src="' . base_url('js/colaboradores.js') . '?v=' . filemtime(FCPATH . 'js/colaboradores.js') . '"></script>',
        ]);
    }

    public function listar()
    {
        $model = new ColaboradorModel();
        return $this->response->setJSON($model->ObtenerTodos());
    }

    public function obtener(int $id)
    {
        $model = new ColaboradorModel();
        $data  = $model->ObtenerPorId($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'No encontrado'])->setStatusCode(404);
        }
        unset($data['password']);
        return $this->response->setJSON($data);
    }

    public function departamentos()
    {
        $db = \Config\Database::connect();
        $data = $db->table('departamentos')->orderBy('descripcion', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    public function guardar()
    {
        $json = $this->request->getJSON(true);
        if (!$json || empty($json['username']) || empty($json['email']) || empty($json['nombre'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan campos obligatorios (username, email, nombre).',
            ]);
        }

        $model = new ColaboradorModel();

        $datos = [
            'username'           => $json['username'],
            'email'              => $json['email'],
            'nombre'             => $json['nombre'],
            'rol'                => $json['rol'] ?? 'empleado',
            'activo'             => isset($json['activo']) ? (int) $json['activo'] : 1,
            'id_departamento'    => !empty($json['id_departamento']) ? (int) $json['id_departamento'] : null,
            'telefono'           => $json['telefono'] ?? null,
            'puesto'             => $json['puesto'] ?? null,
            'fecha_nacimiento'   => $json['fecha_nacimiento'] ?? null,
            'fecha_contratacion' => $json['fecha_contratacion'] ?? null,
        ];

        if (!empty($json['password'])) {
            $datos['password'] = $json['password'];
        }

        if (!empty($json['id'])) {
            $datos['id'] = (int) $json['id'];
        }

        try {
            $ok = $model->Guardar($datos);
            if ($ok) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => empty($json['id']) ? 'Colaborador creado.' : 'Colaborador actualizado.',
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar.',
        ]);
    }

    public function eliminar(int $id)
    {
        if ($id === (int) session()->get('usuario_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No puedes eliminarte a ti mismo.',
            ]);
        }

        $model = new ColaboradorModel();
        $ok    = $model->Eliminar($id);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Colaborador eliminado.' : 'Error al eliminar.',
        ]);
    }
}
