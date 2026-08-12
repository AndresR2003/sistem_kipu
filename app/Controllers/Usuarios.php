<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PagoModel;

/**
 * Controlador Usuarios
 * CRUD de usuarios desde el admin
 */
class Usuarios extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Pagina de gestion de usuarios
     */
    public function index(): string
    {
        $data = [
            'titulo' => 'Gestión de Usuarios',
        ];

        $pageScripts = '<script src="' . base_url('js/usuarios.js') . '?v=' . filemtime(FCPATH . 'js/usuarios.js') . '"></script>';

        return view('layout', [
            'contenido'  => view('usuarios', $data),
            'titulo'     => 'Usuarios - Kipucloud',
            'pageScripts' => $pageScripts,
        ]);
    }
}
