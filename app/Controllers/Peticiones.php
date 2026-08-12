<?php

namespace App\Controllers;

class Peticiones extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo_seccion' => 'Peticiones de huéspedes',
            'icono'          => 'bi bi-chat-dots-fill',
            'descripcion'    => 'Gestión de peticiones y solicitudes de huéspedes.',
        ];

        return view('layout', [
            'contenido'  => view('seccion', $data),
            'titulo'     => 'Peticiones - Kipucloud',
            'pageScripts' => '',
        ]);
    }
}
