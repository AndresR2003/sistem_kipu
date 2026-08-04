<?php

namespace App\Controllers;

use App\Models\RecordatorioModel;

class Recordatorio extends BaseController
{
    protected RecordatorioModel $model;

    public function __construct()
    {
        $this->model = new RecordatorioModel();
    }

    public function index(): string
    {
        $pageScripts = '<script src="' . base_url('js/recordatorio.js') . '?v=' . filemtime(FCPATH . 'js/recordatorio.js') . '"></script>';

        return view('layout', [
            'contenido'   => view('recordatorios'),
            'titulo'      => 'Recordatorio - Litio',
            'pageScripts' => $pageScripts,
        ]);
    }

    public function listar(): \CodeIgniter\HTTP\Response
    {
        $tipo = $this->request->getGet('tipo') ?? 'recordatorio';
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $data = $this->model->ObtenerTodos($tipo, $usuarioId);

        $db = \Config\Database::connect();
        foreach ($data as &$item) {
            $item['comentarios_count'] = 0;
            if (!empty($item['origen_id'])) {
                if ($item['origen_tipo'] === 'entrega') {
                    $cnt = $db->table('comentarios')->where('entrega_id', (int) $item['origen_id'])->countAllResults();
                } else {
                    $cnt = $db->table('comentarios')->where('borrador_id', (int) $item['origen_id'])->countAllResults();
                }
                $item['comentarios_count'] = (int) $cnt;
            }
        }

        return $this->response->setJSON($data);
    }

    public function obtener(int $id): \CodeIgniter\HTTP\Response
    {
        $data = $this->model->ObtenerPorId($id);
        if (!$data) {
            return $this->response->setJSON(['success' => false, 'message' => 'No encontrado.']);
        }
        return $this->response->setJSON($data);
    }

    public function guardar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['titulo'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El titulo es obligatorio.',
            ]);
        }

        $tipo = $json['tipo'] ?? 'recordatorio';

        $datos = [
            'id'          => $json['id'] ?? null,
            'titulo'      => $json['titulo'],
            'descripcion' => $json['descripcion'] ?? '',
            'fecha'       => $json['fecha'] ?? null,
            'prioridad'   => $json['prioridad'] ?? 'media',
            'tipo'        => $tipo,
            'origen_id'   => !empty($json['origen_id']) ? (int) $json['origen_id'] : null,
            'origen_tipo' => $json['origen_tipo'] ?? null,
            'seccion'     => $json['seccion'] ?? null,
            'usuario_id'  => (int) (session()->get('usuario_id') ?? session()->get('admin_id')),
        ];

        if (empty($datos['id'])) {
            unset($datos['id']);
        }

        if ($this->model->Guardar($datos)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $tipo === 'marcador' ? 'Marcador guardado.' : 'Recordatorio guardado.',
            ]);
        }

        $errors = $this->model->errors();
        return $this->response->setJSON([
            'success' => false,
            'message' => !empty($errors) ? implode(', ', $errors) : 'Error al guardar.',
        ]);
    }

    public function eliminar(int $id): \CodeIgniter\HTTP\Response
    {
        $item = $this->model->ObtenerPorId($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'No encontrado.']);
        }
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        if ((int) $item['usuario_id'] !== $usuarioId) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes permiso.']);
        }
        if ($this->model->Eliminar($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Eliminado.',
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al eliminar.',
        ]);
    }

    public function completar(int $id): \CodeIgniter\HTTP\Response
    {
        $item = $this->model->ObtenerPorId($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'No encontrado.']);
        }
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        if ((int) $item['usuario_id'] !== $usuarioId) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes permiso.']);
        }
        $json = $this->request->getJSON(true);
        $completado = ($json['completado'] ?? 0) ? 1 : 0;

        if ($this->model->update($id, ['completado' => $completado])) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false]);
    }
}
