<?php

namespace App\Controllers;

class Manual extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'manual']),
            'titulo'     => 'Manual - Litio',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . filemtime(FCPATH . 'js/publicaciones.js') . '"></script>',
        ]);
    }
}
