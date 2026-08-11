<?php

namespace App\Controllers;

class Soporte extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo_seccion' => 'Soporte',
            'icono'          => 'bi bi-question-circle-fill',
            'descripcion'    => 'Centro de ayuda y soporte tecnico.',
        ];

        return view('layout', [
            'contenido'  => view('seccion', $data),
            'titulo'     => 'Soporte - Kipucloud',
            'pageScripts' => '',
        ]);
    }
}
