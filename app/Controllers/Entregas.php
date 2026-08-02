<?php

namespace App\Controllers;

use App\Models\EntregaModel;

class Entregas extends BaseController
{
    protected EntregaModel $model;

    public function __construct()
    {
        $this->model = new EntregaModel();
    }

    public function index(): string
    {
        $pageScripts = '<script src="' . base_url('js/entregas.js') . '?v=' . filemtime(FCPATH . 'js/entregas.js') . '"></script>';

        return view('layout', [
            'contenido'   => view('entregas'),
            'titulo'      => 'Entregas / Pases de turno - Litio',
            'pageScripts' => $pageScripts,
        ]);
    }

    public function listar(): \CodeIgniter\HTTP\Response
    {
        $fecha = $this->request->getGet('fecha') ?? date('Y-m-d');
        $tareas = $this->model->ObtenerActivas($fecha);
        $registros = $this->model->ObtenerRegistrosDeHoy($fecha);

        $porTarea = [];
        foreach ($registros as $r) {
            $porTarea[$r['entrega_id']][] = $r;
        }

        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        foreach ($tareas as &$t) {
            $t['hecho_por_mi'] = false;
            $t['hecho_por'] = [];
            if (isset($porTarea[$t['id']])) {
                foreach ($porTarea[$t['id']] as $r) {
                    $t['hecho_por'][] = [
                        'nombre' => $r['usuario_nombre'] ?? 'Desconocido',
                        'hora'   => $r['completado_at'],
                        'mio'    => ((int) $r['usuario_id']) === $usuarioId,
                    ];
                    if (((int) $r['usuario_id']) === $usuarioId) {
                        $t['hecho_por_mi'] = true;
                    }
                }
            }
        }

        return $this->response->setJSON(['fecha' => $fecha, 'tareas' => $tareas]);
    }

    public function listarAdmin(): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON($this->model->ObtenerTodas());
    }

    public function registros(): \CodeIgniter\HTTP\Response
    {
        $inicio = $this->request->getGet('inicio') ?? '';
        $fin = $this->request->getGet('fin') ?? '';
        return $this->response->setJSON($this->model->ObtenerRegistros($inicio, $fin));
    }

    public function guardar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['titulo']) || empty($json['fecha_inicio'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El titulo y la fecha de inicio son obligatorios.',
            ]);
        }

        $datos = [
            'id'            => $json['id'] ?? null,
            'titulo'        => $json['titulo'],
            'descripcion'   => $json['descripcion'] ?? '',
            'repetir_diario'=> !empty($json['repetir_diario']) ? 1 : 0,
            'fecha_inicio'  => $json['fecha_inicio'],
            'fecha_fin'     => $json['fecha_fin'] ?? null,
            'publicado'     => !empty($json['publicado']) ? 1 : 0,
            'created_by'    => session()->get('usuario_id') ?? session()->get('admin_id'),
        ];

        if (empty($datos['id'])) {
            unset($datos['id']);
        }

        if ($this->model->Guardar($datos)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tarea guardada.',
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
        $ok = $this->model->Eliminar($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Tarea eliminada.' : 'Error al eliminar.',
        ]);
    }

    public function publicar(int $id): \CodeIgniter\HTTP\Response
    {
        $tarea = $this->model->ObtenerPorId($id);
        if (!$tarea) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no encontrada.']);
        }
        $nuevo = $tarea['publicado'] ? 0 : 1;
        $ok = $this->model->TogglePublicado($id, $nuevo);
        return $this->response->setJSON([
            'success' => $ok,
            'publicado' => (bool) $nuevo,
            'message' => $ok ? ($nuevo ? 'Tarea publicada.' : 'Tarea despublicada.') : 'Error.',
        ]);
    }

    public function completar(int $id): \CodeIgniter\HTTP\Response
    {
        $tarea = $this->model->ObtenerPorId($id);
        if (!$tarea || !$tarea['publicado']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no disponible.']);
        }

        $fecha = $this->request->getGet('fecha') ?? date('Y-m-d');
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));

        $resultado = $this->model->RegistrarEjecucion($id, $usuarioId, $fecha);
        return $this->response->setJSON($resultado);
    }

    public function eliminarRegistro(int $id): \CodeIgniter\HTTP\Response
    {
        $ok = $this->model->EliminarRegistro($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Registro eliminado.' : 'Error al eliminar.',
        ]);
    }
}
