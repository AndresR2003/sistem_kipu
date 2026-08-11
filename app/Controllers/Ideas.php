<?php

namespace App\Controllers;

class Ideas extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'ideas']),
            'titulo'     => 'Ideas - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . filemtime(FCPATH . 'js/publicaciones.js') . '"></script>',
        ]);
    }
}
