<?php

namespace App\Controllers;

use App\Models\ConfiguracionVisualModel;

class Configuracion extends BaseController
{
    protected ConfiguracionVisualModel $model;

    public function __construct()
    {
        $this->model = new ConfiguracionVisualModel();
    }

    public function index(): string
    {
        $pageScripts = '<script src="' . base_url('js/configuracion.js') . '?v=' . filemtime(FCPATH . 'js/configuracion.js') . '"></script>';

        $usuarios = [];
        try {
            $usuarios = (new \App\Models\ColaboradorModel())
                ->select('id, nombre, username, rol')
                ->orderBy('nombre', 'ASC')
                ->findAll();
        } catch (\Throwable $e) {
            $usuarios = [];
        }

        return view('layout', [
            'contenido'    => view('configuracion', [
                'menuRoles'    => menu_roles(),
                'menuUsuarios' => $usuarios,
            ]),
            'titulo'       => 'Configuración - Kipucloud',
            'pageScripts'  => $pageScripts,
        ]);
    }

    public function obtener(): \CodeIgniter\HTTP\Response
    {
        $data = $this->model->Obtener();
        $data['menu_permisos'] = menu_config();

        return $this->response->setJSON($data);
    }

    public function guardar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos invalidos.',
            ]);
        }

        if (array_key_exists('menu_disabled', $json)) {
            if ((session('admin_rol') ?? '') !== 'superadmin') {
                unset($json['menu_disabled']);
            } else {
                $json['menu_disabled'] = $this->sanitizarMenu($json['menu_disabled']);
            }
        }

        if ($this->model->Guardar($json)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cambios guardados correctamente.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar.',
        ]);
    }

    private function sanitizarMenu($valor): string
    {
        $data = is_array($valor) ? $valor : json_decode((string) $valor, true);
        if (!is_array($data)) {
            $data = [];
        }

        $keysValidas  = menu_keys();
        $rolesValidos = array_keys(menu_roles());
        $idsValidos   = $this->usuariosValidos();

        $out = [];
        foreach ($data as $key => $conf) {
            if (!in_array($key, $keysValidas, true) || !is_array($conf)) {
                continue;
            }

            $roles = array_values(array_intersect(
                array_map('strval', (array) ($conf['roles'] ?? [])),
                $rolesValidos
            ));

            $usuarios = array_values(array_intersect(
                array_map('intval', (array) ($conf['usuarios'] ?? [])),
                $idsValidos
            ));

            if ($roles || $usuarios) {
                $out[$key] = ['roles' => $roles, 'usuarios' => $usuarios];
            }
        }

        return json_encode($out, JSON_UNESCAPED_UNICODE);
    }

    private function usuariosValidos(): array
    {
        try {
            $rows = \Config\Database::connect()
                ->table('admin_usuarios')
                ->select('id')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }

        return array_map('intval', array_column($rows, 'id'));
    }

    public function subirLogo()
    {
        $file = $this->request->getFile('logo');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se recibio una imagen valida.',
            ]);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La imagen debe ser menor a 2MB.',
            ]);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solo se permiten JPG, PNG, WebP o SVG.',
            ]);
        }

        $ext = $file->getExtension();
        $nombre = 'marca_' . time() . '.' . $ext;

        $uploadPath = FCPATH . 'uploads/marca';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $file->move($uploadPath, $nombre);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Logo subido.',
            'logo'    => 'uploads/marca/' . $nombre,
        ]);
    }
}
