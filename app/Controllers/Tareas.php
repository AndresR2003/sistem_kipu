<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\ComentarioModel;
use App\Models\DepartamentoModel;

class Tareas extends BaseController
{
    protected TareaModel $model;

    public function __construct()
    {
        $this->model = new TareaModel();
    }

    // ─── Vista principal ───

    public function index(): string
    {
        $pageScripts = '<script src="' . base_url('js/tareas.js') . '?v=' . time() . '"></script>';

        return view('layout', [
            'contenido'   => view('tareas'),
            'titulo'      => 'Tareas - Kipucloud',
            'pageScripts' => $pageScripts,
        ]);
    }

    // ─── API: Listar tareas ───

    public function listar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true) ?? [];
        $modo = $json['modo'] ?? 'mis_tareas';
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $rol = session()->get('admin_rol') ?? 'empleado';
        $soloMisTareas = ($modo === 'mis_tareas' || !in_array($rol, ['admin', 'superadmin'], true));

        $tareas = $this->model->ObtenerPorDepartamento($usuarioId, $soloMisTareas);
        $pendientes = $this->model->ContarPendientesPorDepartamento($usuarioId, $soloMisTareas);

        $db = \Config\Database::connect();
        $departamentos = $db->table('departamentos')
                            ->orderBy('descripcion', 'ASC')
                            ->get()
                            ->getResultArray();

        $porDept = [];
        foreach ($tareas as $t) {
            $deptId = $t['departamento_id'] ?? 0;
            $porDept[$deptId][] = $t;
        }

        $resultado = [];
        foreach ($departamentos as $d) {
            $deptId = $d['id'];
            if (isset($porDept[$deptId]) && count($porDept[$deptId]) > 0) {
                $resultado[] = [
                    'id'        => $deptId,
                    'nombre'    => $d['descripcion'],
                    'pendientes'=> $pendientes[$deptId] ?? 0,
                    'tareas'    => $porDept[$deptId],
                ];
            }
        }

        return $this->response->setJSON([
            'success'      => true,
            'departamentos'=> $resultado,
            'total'        => count($tareas),
        ]);
    }

    // ─── API: Obtener tarea ───

    public function obtener(int $id): \CodeIgniter\HTTP\Response
    {
        $tarea = $this->model->ObtenerDetallada($id);
        if (!$tarea) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no encontrada.']);
        }

        $asignaciones = $this->model->ObtenerAsignaciones($id);
        $tarea['asignaciones'] = $asignaciones;

        return $this->response->setJSON(['success' => true, 'data' => $tarea]);
    }

    // ─── API: Guardar tarea ───

    public function guardar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['titulo'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El titulo es obligatorio.',
            ]);
        }

        $rol = session()->get('admin_rol') ?? 'empleado';
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tienes permiso para crear tareas.',
            ]);
        }

        $fechaLimite = $json['fecha_limite'] ?? null;
        if (!empty($fechaLimite)) {
            $fechaLimite = str_replace('T', ' ', $fechaLimite) . ':00';
        }

        $datos = [
            'titulo'            => $json['titulo'],
            'descripcion'       => $json['descripcion'] ?? '',
            'prioridad'         => $json['prioridad'] ?? 'media',
            'fecha_limite'      => $fechaLimite,
            'modalidad'         => $json['modalidad'] ?? 'single_completes_all',
            'departamento_id'   => !empty($json['departamento_id']) ? (int) $json['departamento_id'] : null,
            'destinatario_tipo' => $json['destinatario_tipo'] ?? 'todos',
            'destinatario_id'   => !empty($json['destinatario_id']) ? (int) $json['destinatario_id'] : null,
            'created_by'        => (int) (session()->get('usuario_id') ?? session()->get('admin_id')),
            'publicado'         => !empty($json['publicado']) ? 1 : 0,
        ];

        if (!empty($json['id'])) {
            $datos['id'] = $json['id'];
        }

        if ($this->model->Guardar($datos)) {
            $tareaId = $json['id'] ?? $this->model->insertID();

            if (($json['modalidad'] ?? '') === 'all_must_complete' && !empty($json['asignados'])) {
                $this->model->Asignar($tareaId, $json['asignados']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => empty($json['id']) ? 'Tarea creada.' : 'Tarea actualizada.',
            ]);
        }

        $errors = $this->model->errors();
        return $this->response->setJSON([
            'success' => false,
            'message' => !empty($errors) ? implode(', ', $errors) : 'Error al guardar.',
        ]);
    }

    // ─── API: Eliminar tarea ───

    public function eliminar(int $id): \CodeIgniter\HTTP\Response
    {
        $rol = session()->get('admin_rol') ?? 'empleado';
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos.']);
        }

        $ok = $this->model->Eliminar($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Tarea eliminada.' : 'Error al eliminar.',
        ]);
    }

    // ─── API: Publicar / Despublicar ───

    public function publicar(int $id): \CodeIgniter\HTTP\Response
    {
        $ok = $this->model->TogglePublicado($id, 1);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Tarea publicada.' : 'Error al publicar.',
        ]);
    }

    public function despublicar(int $id): \CodeIgniter\HTTP\Response
    {
        $ok = $this->model->TogglePublicado($id, 0);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Tarea despublicada.' : 'Error.',
        ]);
    }

    // ─── API: Completar / Descompletar ───

    public function completar(int $id): \CodeIgniter\HTTP\Response
    {
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $tarea = $this->model->find($id);

        if (!$tarea || !$tarea['publicado']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no disponible.']);
        }

        if ($tarea['modalidad'] === 'single_completes_all') {
            $resultado = $this->model->CompletarSingle($id, $usuarioId);
        } else {
            $resultado = $this->model->CompletarAll($id, $usuarioId);
        }

        return $this->response->setJSON($resultado);
    }

    public function descompletar(int $id): \CodeIgniter\HTTP\Response
    {
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $tarea = $this->model->find($id);

        if (!$tarea) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tarea no encontrada.']);
        }

        if ($tarea['modalidad'] === 'single_completes_all') {
            $resultado = $this->model->DescompletarSingle($id, $usuarioId);
        } else {
            $resultado = $this->model->DescompletarAll($id, $usuarioId);
        }

        return $this->response->setJSON($resultado);
    }

    // ─── API: Asignaciones ───

    public function asignar(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['tarea_id']) || empty($json['usuarios'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos.',
            ]);
        }

        $rol = session()->get('admin_rol') ?? 'empleado';
        if (!in_array($rol, ['admin', 'superadmin'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sin permisos.']);
        }

        $resultado = $this->model->Asignar((int) $json['tarea_id'], $json['usuarios']);
        return $this->response->setJSON($resultado);
    }

    public function listarAsignaciones(int $id): \CodeIgniter\HTTP\Response
    {
        $asignaciones = $this->model->ObtenerAsignaciones($id);
        return $this->response->setJSON(['success' => true, 'data' => $asignaciones]);
    }

    // ─── API: Comentarios ───

    public function listarComentarios(int $id): \CodeIgniter\HTTP\Response
    {
        $model = new ComentarioModel();
        $comentarios = $model->select('comentarios.*, admin_usuarios.nombre as autor_nombre, admin_usuarios.foto as autor_foto')
                             ->join('admin_usuarios', 'admin_usuarios.id = comentarios.usuario_id', 'left')
                             ->where('comentarios.tarea_id', $id)
                             ->orderBy('comentarios.created_at', 'ASC')
                             ->findAll();

        return $this->response->setJSON(['success' => true, 'data' => $comentarios]);
    }

    public function guardarComentario(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['tarea_id']) || empty($json['comentario'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos.',
            ]);
        }

        $model = new ComentarioModel();
        $ok = $model->Guardar([
            'tarea_id'   => (int) $json['tarea_id'],
            'usuario_id' => session()->get('usuario_id') ?? session()->get('admin_id'),
            'comentario' => $json['comentario'],
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Comentario agregado.' : 'Error al guardar.',
        ]);
    }

    // ─── API: Departamentos ───

    public function departamentos(): \CodeIgniter\HTTP\Response
    {
        $model = new DepartamentoModel();
        return $this->response->setJSON(['success' => true, 'data' => $model->ObtenerTodos()]);
    }
}
