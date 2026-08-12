<?php

namespace App\Controllers;

use App\Models\ConfiguracionVisualModel;

class Configuracion extends BaseController
{
    protected ConfiguracionVisualModel $model;

    public function __construct()
    {
        $this->model = new ConfiguracionVisualModel();
    }

    public function index(): string
    {
        $pageScripts = '<script src="' . base_url('js/configuracion.js') . '?v=' . filemtime(FCPATH . 'js/configuracion.js') . '"></script>';

        return view('layout', [
            'contenido'   => view('configuracion'),
            'titulo'      => 'Configuracion - Kipucloud',
            'pageScripts' => $pageScripts,
        ]);
    }

    public function obtener(): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON($this->model->Obtener());
    }

    public function guardar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos invalidos.',
            ]);
        }

        if ($this->model->Guardar($json)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Colores guardados correctamente.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar.',
        ]);
    }

    public function subirLogo()
    {
        $file = $this->request->getFile('logo');
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

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solo se permiten JPG, PNG, WebP o SVG.',
            ]);
        }

        $ext = $file->getExtension();
        $nombre = 'marca_' . time() . '.' . $ext;

        $uploadPath = FCPATH . 'uploads/marca';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $file->move($uploadPath, $nombre);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Logo subido.',
            'logo'    => 'uploads/marca/' . $nombre,
        ]);
    }
}
