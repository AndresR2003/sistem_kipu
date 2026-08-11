<?php

namespace App\Controllers;

class Peticiones extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo_seccion' => 'Peticiones de huespedes',
            'icono'          => 'bi bi-chat-dots-fill',
            'descripcion'    => 'Gestion de peticiones y solicitudes de huespedes.',
        ];

        return view('layout', [
            'contenido'  => view('seccion', $data),
            'titulo'     => 'Peticiones - Kipucloud',
            'pageScripts' => '',
        ]);
    }
}
