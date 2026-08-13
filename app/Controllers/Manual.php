<?php

namespace App\Controllers;

class Manual extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'manual']),
            'titulo'     => 'Manual - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . time() . '"></script>',
        ]);
    }
}
