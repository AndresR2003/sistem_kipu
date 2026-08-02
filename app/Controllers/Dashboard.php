<?php

namespace App\Controllers;

use App\Models\PagoModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $pagoModel = new PagoModel();
        $pagoModel->GenerarDeudasAutomaticas();

        $data = [
            'titulo'    => 'Dashboard',
        ];

        return view('layout', [
            'contenido'  => view('dashboard', $data),
            'titulo'     => 'Dashboard - Litio',
            'pageScripts' => '',
        ]);
    }
}
