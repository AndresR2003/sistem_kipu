<?php

namespace App\Controllers;

class Reparaciones extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo_seccion' => 'Reparaciones',
            'icono'          => 'bi bi-tools',
            'descripcion'    => 'Registro de reparaciones y mantenimiento.',
        ];

        return view('layout', [
            'contenido'  => view('seccion', $data),
            'titulo'     => 'Reparaciones - Litio',
            'pageScripts' => '',
        ]);
    }
}
