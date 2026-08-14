<?php

namespace App\Controllers;

class Landing extends BaseController
{
    public function index(): string
    {
        $cfgMarca = [];
        $marcaActiva = false;
        $marcaNombre = 'Kipucloud';
        $marcaLigo = '';
        try {
            $cfgMarca = model('App\Models\ConfiguracionVisualModel')->Obtener();
            $marcaActiva = !empty($cfgMarca['marca_activa']);
            $marcaNombre = ($marcaActiva && !empty($cfgMarca['marca_nombre'])) ? $cfgMarca['marca_nombre'] : 'Kipucloud';
            $marcaLigo = ($marcaActiva && !empty($cfgMarca['marca_logo'])) ? base_url($cfgMarca['marca_logo']) : '';
        } catch (\Throwable $e) {
        }

        $logueado = session()->has('admin_id');

        return view('landing', [
            'titulo'      => $marcaNombre . ' - Gestion Empresarial',
            'marcaNombre' => $marcaNombre,
            'marcaLigo'   => $marcaLigo,
            'logueado'    => $logueado,
        ]);
    }
}
