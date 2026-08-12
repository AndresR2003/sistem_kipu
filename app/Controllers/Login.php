<?php

namespace App\Controllers;

use App\Models\AdminModel;

class Login extends BaseController
{
    public function index()
    {
        if (session()->has('admin_id')) {
            return redirect()->to('/dashboard');
        }
        return view('login');
    }

    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (!$username || !$password) {
            return redirect()->back()->withInput()->with('error', 'Ingresa usuario y contraseña');
        }

        $adminModel = new AdminModel();
        $usuario = $adminModel->login($username, $password);

        if (!$usuario) {
            return redirect()->back()->withInput()->with('error', 'Credenciales invalidas');
        }

        session()->set([
            'admin_id'         => $usuario['id'],
            'usuario_id'       => $usuario['id'],
            'admin_nombre'     => $usuario['nombre'],
            'admin_username'   => $usuario['username'],
            'admin_rol'        => $usuario['rol'],
            'admin_foto'       => $usuario['foto'] ?? null,
            'id_departamento'  => $usuario['id_departamento'] ?? null,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente');
    }
}
