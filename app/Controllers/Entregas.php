<?php

namespace App\Controllers;

use App\Models\ComentarioModel;
use App\Models\DepartamentoModel;
use App\Models\EntregaModel;

class Entregas extends BaseController
{
    protected EntregaModel $model;

    public function __construct()
    {
        $this->model = new EntregaModel();
    }

    public function index()
    {
        $rol = session()->get('admin_rol') ?? 'empleado';
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            return redirect()->to(site_url('tareas'));
        }

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
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $rol = session()->get('admin_rol') ?? 'empleado';
        $departamentoId = (int) (session()->get('id_departamento') ?? 0);
        $tareas = $this->model->ObtenerActivas($fecha, $usuarioId, $departamentoId ?: null, $rol);
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

        $ids = array_column($tareas, 'id');
        $counts = empty($ids) ? [] : (new ComentarioModel())->ContarPorEntregas($ids);
        foreach ($tareas as &$t) {
            $t['comentarios_count'] = $counts[$t['id']] ?? 0;
        }

        return $this->response->setJSON(['fecha' => $fecha, 'tareas' => $tareas]);
    }

    public function listarAdmin(): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON($this->model->ObtenerTodasConDestinatario());
    }

    public function destinatarios(): \CodeIgniter\HTTP\Response
    {
        $db = \Config\Database::connect();
        $usuarios = $db->table('admin_usuarios')->where('activo', 1)->orderBy('nombre', 'ASC')->get()->getResultArray();
        $deptoModel = new DepartamentoModel();
        $deptos = $deptoModel->ObtenerTodos();

        return $this->response->setJSON([
            'usuarios'      => $usuarios,
            'departamentos' => $deptos,
        ]);
    }

    public function obtener(int $id): \CodeIgniter\HTTP\Response
    {
        $tarea = $this->model->ObtenerPorId($id);
        if (!$tarea) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no encontrada.']);
        }
        return $this->response->setJSON($tarea);
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
            'destinatario_tipo' => $json['destinatario_tipo'] ?? 'todos',
            'destinatario_id'   => !empty($json['destinatario_id']) ? (int) $json['destinatario_id'] : null,
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

        $json = $this->request->getJSON(true) ?? [];
        $tipo = $json['destinatario_tipo'] ?? 'todos';
        $destId = !empty($json['destinatario_id']) ? (int) $json['destinatario_id'] : null;

        $ok = $this->model->PublicarCon($id, $tipo, $destId);
        return $this->response->setJSON([
            'success' => $ok,
            'publicado' => (bool) $ok,
            'message' => $ok ? 'Tarea publicada.' : 'Error al publicar.',
        ]);
    }

    public function despublicar(int $id): \CodeIgniter\HTTP\Response
    {
        $tarea = $this->model->ObtenerPorId($id);
        if (!$tarea || !$tarea['publicado']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La tarea no esta publicada.',
            ]);
        }

        $ok = $this->model->TogglePublicado($id, 0);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Tarea despublicada.' : 'Error al despublicar.',
        ]);
    }

    public function completar(int $id): \CodeIgniter\HTTP\Response
    {
        $tarea = $this->model->ObtenerPorId($id);
        if (!$tarea || !$tarea['publicado']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no disponible.']);
        }

        $json = $this->request->getJSON(true) ?? [];
        $fecha = $json['fecha'] ?? date('Y-m-d');
        $completado = !empty($json['completado']) ? 1 : 0;
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $rol = session()->get('admin_rol') ?? 'empleado';
        $departamentoId = (int) (session()->get('id_departamento') ?? 0);

        $activas = $this->model->ObtenerActivas($fecha, $usuarioId, $departamentoId ?: null, $rol);
        $visible = false;
        foreach ($activas as $a) {
            if ((int) $a['id'] === $id) {
                $visible = true;
                break;
            }
        }
        if (!$visible) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes asignada esta tarea.']);
        }

        $resultado = $this->model->RegistrarEjecucion($id, $usuarioId, $fecha, $completado);
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

    public function listarComentarios(int $id): \CodeIgniter\HTTP\Response
    {
        $model = new ComentarioModel();
        return $this->response->setJSON($model->ObtenerPorEntrega($id));
    }

    public function guardarComentario(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['entrega_id']) || empty($json['comentario'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos.',
            ]);
        }

        $model = new ComentarioModel();
        $ok    = $model->Guardar([
            'entrega_id'  => (int) $json['entrega_id'],
            'usuario_id'  => session()->get('usuario_id') ?? session()->get('admin_id'),
            'comentario'  => $json['comentario'],
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Comentario agregado.' : 'Error al guardar.',
        ]);
    }
}
