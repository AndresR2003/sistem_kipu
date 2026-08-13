<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Api extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    private function RespuestaJson(bool $success, string $message, $data = null): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    // =====================================================
    // API - USUARIOS
    // =====================================================

    public function listarUsuarios()
    {
        $usuarios = $this->usuarioModel->orderBy('nombre', 'ASC')->findAll();
        return $this->RespuestaJson(true, 'Usuarios listados', $usuarios);
    }

    public function guardarUsuario()
    {
        $rules = [
            'nombre' => 'required|max_length[100]',
            'activo' => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->RespuestaJson(false, 'Error de validacion', $this->validator->getErrors());
        }

        $datos = [
            'nombre'   => $this->request->getPost('nombre'),
            'telefono' => $this->request->getPost('telefono'),
            'activo'   => $this->request->getPost('activo'),
        ];

        $id = $this->request->getPost('id');

        if ($id) {
            $datos['id'] = $id;
        }

        if ($this->usuarioModel->GuardarUsuario($datos)) {
            $mensaje = $id ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente';
            return $this->RespuestaJson(true, $mensaje);
        }

        $errores = $this->usuarioModel->errors();
        return $this->RespuestaJson(false, 'Error al guardar usuario', $errores);
    }

    public function eliminarUsuario(int $idUsuario)
    {
        if ($this->usuarioModel->EliminarUsuario($idUsuario)) {
            return $this->RespuestaJson(true, 'Usuario eliminado correctamente');
        }

        return $this->RespuestaJson(false, 'Error al eliminar usuario');
    }

    // =====================================================
    // API - ESTADISTICAS
    // =====================================================

    public function estadisticas()
    {
        return $this->RespuestaJson(true, 'Estadisticas', [
            'totalUsuarios' => $this->usuarioModel->countAllResults(),
        ]);
    }

    // =====================================================
    // API - NOTIFICACIONES
    // =====================================================

    public function notificaciones()
    {
        $cache = service('cache');
        $anuncio = $cache->get('ultimo_anuncio');
        if ($anuncio === null) {
            $anuncio = (new \App\Models\BorradorModel())->ObtenerUltimoAnuncio();
            $cache->save('ultimo_anuncio', $anuncio, 60);
        }

        return $this->RespuestaJson(true, 'Notificaciones', [
            'anuncio'    => $anuncio,
            'hayPendientes' => !empty($anuncio),
        ]);
    }
}
