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
            'titulo'      => 'Configuración - Kipucloud',
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

        if (array_key_exists('menu_disabled', $json)) {
            if ((session('admin_rol') ?? '') !== 'superadmin') {
                unset($json['menu_disabled']);
            } else {
                $json['menu_disabled'] = $this->sanitizarMenu($json['menu_disabled']);
            }
        }

        if ($this->model->Guardar($json)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cambios guardados correctamente.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar.',
        ]);
    }

    private function sanitizarMenu($valor): string
    {
        $lista = is_array($valor) ? $valor : json_decode((string) $valor, true);
        if (!is_array($lista)) {
            $lista = [];
        }

        return json_encode(array_values(array_intersect($lista, menu_keys())));
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
