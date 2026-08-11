<?php

namespace App\Controllers;

use App\Models\RecordatorioModel;

class Marcadores extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'   => view('marcadores'),
            'titulo'      => 'Marcadores - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/marcadores.js') . '?v=' . filemtime(FCPATH . 'js/marcadores.js') . '"></script>',
        ]);
    }

    public function listar(): \CodeIgniter\HTTP\Response
    {
        $model = new RecordatorioModel();
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $data  = $model->ObtenerTodos('marcador', $usuarioId);
        return $this->response->setJSON($data);
    }

    public function eliminar(int $id): \CodeIgniter\HTTP\Response
    {
        $model = new RecordatorioModel();
        $item = $model->ObtenerPorId($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'No encontrado.']);
        }
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        if ((int) $item['usuario_id'] !== $usuarioId) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes permiso.']);
        }
        if ($model->Eliminar($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Marcador eliminado.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Error al eliminar.']);
    }
}
