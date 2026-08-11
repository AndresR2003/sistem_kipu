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
}
