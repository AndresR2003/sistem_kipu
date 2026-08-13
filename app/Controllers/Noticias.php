<?php

namespace App\Controllers;

class Noticias extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'noticias']),
            'titulo'     => 'Noticias - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . time() . '"></script>',
        ]);
    }
}
