<?php

namespace App\Controllers;

use App\Models\ColaboradorModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('layout', [
            'contenido'   => view('perfil'),
            'titulo'      => 'Mi Perfil - Litio',
            'pageScripts' => '<script src="' . base_url('js/perfil.js') . '?v=' . filemtime(FCPATH . 'js/perfil.js') . '"></script>',
        ]);
    }

    public function obtener()
    {
        $id   = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $model = new ColaboradorModel();
        $data  = $model->ObtenerPorId($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'No encontrado'])->setStatusCode(404);
        }
        unset($data['password']);
        return $this->response->setJSON($data);
    }

    public function guardar()
    {
        $id  = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $json = $this->request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos invalidos.']);
        }

        $model = new ColaboradorModel();
        $datos = [];

        if (!empty($json['nombre'])) {
            $datos['nombre'] = $json['nombre'];
        }
        if (!empty($json['email'])) {
            $datos['email'] = $json['email'];
        }
        if (!empty($json['password'])) {
            $datos['password'] = $json['password'];
        }

        if (empty($datos)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin datos para actualizar.']);
        }

        try {
            $ok = $model->Guardar(array_merge($datos, ['id' => $id]));
            if ($ok) {
                if (!empty($datos['nombre'])) {
                    session()->set('admin_nombre', $datos['nombre']);
                }
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perfil actualizado.',
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar.']);
    }

    public function subirFoto()
    {
        $id = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));

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
        $nombre = 'perfil_' . $id . '_' . time() . '.' . $ext;

        $uploadPath = FCPATH . 'uploads/perfil';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $file->move($uploadPath, $nombre);

        $model = new ColaboradorModel();
        $model->update($id, ['foto' => 'uploads/perfil/' . $nombre]);
        session()->set('admin_foto', 'uploads/perfil/' . $nombre);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Foto actualizada.',
            'foto'    => base_url('uploads/perfil/' . $nombre),
        ]);
    }
}
