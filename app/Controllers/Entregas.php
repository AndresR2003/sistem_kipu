<?php

namespace App\Controllers;

use App\Models\DepartamentoModel;
use App\Models\PaseTurnoModel;
use App\Models\TareaModel;

class Entregas extends BaseController
{
    protected PaseTurnoModel $model;

    public function __construct()
    {
        $this->model = new PaseTurnoModel();
    }

    private function usuarioId(): int
    {
        return (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
    }

    private function esAdmin(): bool
    {
        return in_array(session()->get('admin_rol') ?? 'empleado', ['admin', 'superadmin'], true);
    }

    private function negar(): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON(['success' => false, 'message' => 'No tienes permisos para realizar esta accion.']);
    }

    public function index()
    {
        $pageScripts = '<script src="' . base_url('js/pases.js') . '?v=' . filemtime(FCPATH . 'js/pases.js') . '"></script>';

        return view('layout', [
            'contenido'   => view('entregas', ['esAdmin' => $this->esAdmin()]),
            'titulo'      => 'Pases de turno - Kipucloud',
            'pageScripts' => $pageScripts,
        ]);
    }

    // ─── Catálogo de turnos ───

    public function turnos(): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON(['success' => true, 'data' => $this->model->Turnos(false)]);
    }

    public function guardarTurno(): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $json = $this->request->getJSON(true);
        if (!$json || trim($json['nombre'] ?? '') === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'El nombre del turno es obligatorio.']);
        }

        $ok = $this->model->GuardarTurno(
            trim($json['nombre']),
            trim($json['descripcion'] ?? '') ?: null,
            (int) ($json['orden'] ?? 0),
            !empty($json['activo']) ? 1 : 0,
            !empty($json['id']) ? (int) $json['id'] : null
        );

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Turno guardado.' : 'Error al guardar el turno.',
        ]);
    }

    public function eliminarTurno(int $id): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $ok = $this->model->EliminarTurno($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Turno eliminado.' : 'Error al eliminar el turno.',
        ]);
    }

    // ─── Pases de turno ───

    public function listar(): \CodeIgniter\HTTP\Response
    {
        $estado = $this->request->getGet('estado') ?? '';
        return $this->response->setJSON([
            'success' => true,
            'data'    => $this->model->ListarPases($estado),
        ]);
    }

    public function obtener(int $id): \CodeIgniter\HTTP\Response
    {
        $pase = $this->model->ObtenerPase($id);
        if (!$pase) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pase de turno no encontrado.']);
        }
        return $this->response->setJSON([
            'success' => true,
            'data'    => $pase,
        ]);
    }

    public function guardar(): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $json = $this->request->getJSON(true);
        if (!$json || empty($json['de_turno_id']) || empty($json['a_turno_id']) || empty($json['fecha'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Debes indicar los turnos (de y para) y la fecha.',
            ]);
        }

        $id = $this->model->GuardarPase([
            'titulo'      => trim($json['titulo'] ?? '') ?: null,
            'de_turno_id' => (int) $json['de_turno_id'],
            'a_turno_id'  => (int) $json['a_turno_id'],
            'fecha'       => $json['fecha'],
            'estado'      => 'abierto',
            'creado_por'  => $this->usuarioId(),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pase de turno creado.',
            'id'      => $id,
        ]);
    }

    public function cerrar(int $id): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $ok = $this->model->CerrarPase($id, $this->usuarioId());
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Pase de turno cerrado.' : 'Error al cerrar.',
        ]);
    }

    public function reabrir(int $id): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $ok = $this->model->ReabrirPase($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Pase de turno reabierto.' : 'Error al reabrir.',
        ]);
    }

    public function eliminar(int $id): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $ok = $this->model->EliminarPase($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Pase de turno eliminado.' : 'Error al eliminar.',
        ]);
    }

    // ─── Puntos del pase ───

    public function puntos(int $id): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON([
            'success' => true,
            'data'    => $this->model->ObtenerPuntos($id),
        ]);
    }

    public function guardarPunto(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);
        if (!$json || empty($json['pase_id']) || trim($json['contenido'] ?? '') === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El contenido del punto es obligatorio.',
            ]);
        }

        $pase = $this->model->ObtenerPase((int) $json['pase_id']);
        if (!$pase) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pase de turno no encontrado.']);
        }

        $datos = [
            'pase_id'    => (int) $json['pase_id'],
            'area_id'    => !empty($json['area_id']) ? (int) $json['area_id'] : null,
            'contenido'  => trim($json['contenido']),
            'creado_por' => $this->usuarioId(),
        ];

        $puntoId = $this->model->GuardarPunto($datos, !empty($json['id']) ? (int) $json['id'] : null);
        return $this->response->setJSON([
            'success' => true,
            'message' => empty($json['id']) ? 'Punto agregado.' : 'Punto actualizado.',
            'id'      => $puntoId,
        ]);
    }

    public function cambiarEstadoPunto(int $id): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true) ?? [];
        $estado = $json['estado'] ?? '';
        if (!in_array($estado, ['pendiente', 'revisado', 'completado'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estado invalido.']);
        }
        $ok = $this->model->CambiarEstadoPunto($id, $estado, $this->usuarioId());
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Estado actualizado.' : 'Error al actualizar.',
        ]);
    }

    public function eliminarPunto(int $id): \CodeIgniter\HTTP\Response
    {
        $punto = $this->model->ObtenerPunto($id);
        if (!$punto) {
            return $this->response->setJSON(['success' => false, 'message' => 'Punto no encontrado.']);
        }

        $esAutor = ((int) $punto['creado_por']) === $this->usuarioId();
        if (!$this->esAdmin() && !$esAutor) {
            return $this->negar();
        }

        $ok = $this->model->EliminarPunto($id);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Punto eliminado.' : 'Error al eliminar.',
        ]);
    }

    // ─── Convertir punto en tarea ───

    public function convertirEnTarea(int $puntoId): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $json = $this->request->getJSON(true);
        $punto = $this->model->ObtenerPunto($puntoId);
        if (!$punto) {
            return $this->response->setJSON(['success' => false, 'message' => 'Punto no encontrado.']);
        }

        if (!$json || trim($json['titulo'] ?? '') === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El titulo de la tarea es obligatorio.',
            ]);
        }

        $tareaModel = new TareaModel();
        $departamentos = !empty($json['departamentos']) ? array_map('intval', (array) $json['departamentos']) : [];
        $asignados     = !empty($json['asignados']) ? array_map('intval', (array) $json['asignados']) : [];

        $fechaLimite = $json['fecha_limite'] ?? null;
        if (!empty($fechaLimite)) {
            $fechaLimite = str_replace('T', ' ', $fechaLimite) . ':00';
        }

        $datos = [
            'titulo'            => trim($json['titulo']),
            'descripcion'       => trim($json['descripcion'] ?? ''),
            'prioridad'         => $json['prioridad'] ?? 'media',
            'fecha_limite'      => $fechaLimite,
            'modalidad'         => $json['modalidad'] ?? 'single_completes_all',
            'departamento_id'   => !empty($departamentos) ? $departamentos[0] : null,
            'destinatario_tipo' => 'multiple',
            'destinatario_id'   => null,
            'created_by'        => $this->usuarioId(),
            'publicado'         => !empty($json['publicado']) ? 1 : 0,
        ];

        if ($tareaModel->Guardar($datos)) {
            $tareaId = (int) $tareaModel->insertID();
            $tareaModel->GuardarDepartamentos($tareaId, $departamentos);
            $tareaModel->GuardarUsuarios($tareaId, $asignados);
            $this->model->VincularTarea($puntoId, $tareaId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tarea creada y vinculada al punto.',
                'tarea_id' => $tareaId,
            ]);
        }

        $errors = $tareaModel->errors();
        return $this->response->setJSON([
            'success' => false,
            'message' => !empty($errors) ? implode(', ', $errors) : 'Error al crear la tarea.',
        ]);
    }

    public function desvincularTarea(int $puntoId): \CodeIgniter\HTTP\Response
    {
        if (!$this->esAdmin()) {
            return $this->negar();
        }
        $ok = $this->model->DesvincularTarea($puntoId);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Vinculo con la tarea eliminado.' : 'Error.',
        ]);
    }

    // ─── Comentarios por punto ───

    public function listarComentarios(int $puntoId): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON([
            'success' => true,
            'data'    => $this->model->ListarComentarios($puntoId),
        ]);
    }

    public function guardarComentario(): \CodeIgniter\HTTP\Response
    {
        $json = $this->request->getJSON(true);
        if (!$json || empty($json['punto_id']) || trim($json['comentario'] ?? '') === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El comentario es obligatorio.',
            ]);
        }

        $ok = $this->model->GuardarComentario((int) $json['punto_id'], $this->usuarioId(), trim($json['comentario']));
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Comentario agregado.' : 'Error al guardar.',
        ]);
    }

    // ─── Utilidades (areas y usuarios) ───

    public function areas(): \CodeIgniter\HTTP\Response
    {
        $deptoModel = new DepartamentoModel();
        return $this->response->setJSON([
            'success' => true,
            'data'    => $deptoModel->ObtenerTodos(),
        ]);
    }

    public function usuarios(): \CodeIgniter\HTTP\Response
    {
        $db = \Config\Database::connect();
        $usuarios = $db->table('admin_usuarios')->where('activo', 1)->orderBy('nombre', 'ASC')->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'data' => $usuarios]);
    }
}