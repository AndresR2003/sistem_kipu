<?php

namespace App\Controllers;

class Soporte extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('soporte'),
            'titulo'     => 'Soporte - KipuCloud',
            'pageScripts' => '',
        ]);
    }
}
