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
            'titulo'      => 'Personal - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/colaboradores.js') . '?v=' . filemtime(FCPATH . 'js/colaboradores.js') . '"></script>',
        ]);
    }

    private function esAdmin(): bool
    {
        return in_array(session()->get('admin_rol') ?? 'empleado', ['admin', 'superadmin'], true);
    }

    public function listar()
    {
        $model = new ColaboradorModel();
        $data  = $model->ObtenerTodos();
        foreach ($data as &$c) {
            $c['puede_gestionar'] = $this->esAdmin();
        }
        return $this->response->setJSON($data);
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
        if (!$this->esAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solo el administrador puede crear o editar colaboradores.',
            ]);
        }

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
                $id = $model->getInsertID() ?: ((int) ($json['id'] ?? 0));
                return $this->response->setJSON([
                    'success' => true,
                    'id'      => $id,
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
        if (!$this->esAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solo el administrador puede eliminar colaboradores.',
            ]);
        }

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

    public function subirFoto(int $id)
    {
        if (!$this->esAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solo el administrador puede subir fotos.',
            ]);
        }

        $model = new ColaboradorModel();
        $colab = $model->ObtenerPorId($id);
        if (!$colab) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Colaborador no encontrado.',
            ]);
        }

        $file = $this->request->getFile('foto');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se recibio una imagen valida.',
            ]);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La imagen debe ser menor a 2MB.',
            ]);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solo se permiten JPG, PNG, WebP o GIF.',
            ]);
        }

        $ext = $file->getExtension();
        $nombre = 'colab_' . $id . '_' . time() . '.' . $ext;

        $uploadPath = FCPATH . 'uploads/perfil';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $file->move($uploadPath, $nombre);

        $model->update($id, ['foto' => 'uploads/perfil/' . $nombre]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Foto actualizada.',
            'foto'    => base_url('uploads/perfil/' . $nombre),
        ]);
    }
}
