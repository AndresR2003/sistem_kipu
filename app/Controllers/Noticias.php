<?php

namespace App\Controllers;

class Noticias extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'noticias']),
            'titulo'     => 'Noticias - Litio',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . filemtime(FCPATH . 'js/publicaciones.js') . '"></script>',
        ]);
    }
}
