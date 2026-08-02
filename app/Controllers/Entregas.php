<?php

namespace App\Controllers;

class Entregas extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo_seccion' => 'Entregas / Pases de turno',
            'icono'          => 'bi bi-arrow-left-right',
            'descripcion'    => 'Gestion de entregas y pases de turno.',
        ];

        return view('layout', [
            'contenido'  => view('seccion', $data),
            'titulo'     => 'Entregas - Litio',
            'pageScripts' => '',
        ]);
    }
}
