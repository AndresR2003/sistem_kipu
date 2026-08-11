<?php

namespace App\Controllers;

class Tareas extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'tareas']),
            'titulo'     => 'Tareas - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . filemtime(FCPATH . 'js/publicaciones.js') . '"></script>',
        ]);
    }
}
