<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has('admin_id')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesion para acceder');
        }

        $rol = session('admin_rol') ?? '';
        if ($rol !== 'superadmin') {
            $ruta = trim((string) $request->getUri()->getPath(), '/');
            if ($ruta !== '' && in_array($ruta, menu_oculto(), true)) {
                return redirect()->to('/dashboard');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
