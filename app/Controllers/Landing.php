<?php

namespace App\Controllers;

class Landing extends BaseController
{
    public function index(): string
    {
        $marcaNombre = 'kipucloud';
        $marcaLigo = base_url('assets/img/kipucloud-logo.svg');

        $logueado = session()->has('admin_id');

        return view('landing', [
            'titulo'      => 'kipucloud - Gestion Empresarial',
            'marcaNombre' => $marcaNombre,
            'marcaLigo'   => $marcaLigo,
            'logueado'    => $logueado,
        ]);
    }
}
