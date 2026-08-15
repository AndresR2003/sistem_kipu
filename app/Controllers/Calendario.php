<?php

namespace App\Controllers;

use App\Models\EventoModel;
use App\Models\DepartamentoModel;

class Calendario extends BaseController
{
    protected EventoModel $eventoModel;

    public function __construct()
    {
        $this->eventoModel = new EventoModel();
    }

    public function index(): string
    {
        $data = [
            'titulo_seccion' => 'Calendario',
            'icono'          => 'bi bi-calendar-fill',
            'descripcion'    => 'Gestiona tus eventos y fechas importantes.',
        ];

        $pageScripts = '<script src="' . base_url('assets/js/fullcalendar.global.min.js') . '"></script>'
                     . '<script src="' . base_url('assets/js/sweetalert2.min.js') . '"></script>'
                     . '<script src="' . base_url('js/calendario.js') . '?v=' . filemtime(FCPATH . 'js/calendario.js') . '"></script>';

        return view('layout', [
            'contenido'   => view('calendario', $data),
            'titulo'      => 'Calendario - Kipucloud',
            'pageScripts' => $pageScripts,
        ]);
    }

    public function listar(): \CodeIgniter\HTTP\Response
    {
        $inicio = $this->request->getGet('start') ?? date('Y-m-01');
        $fin    = $this->request->getGet('end') ?? date('Y-m-t');

        $eventos = $this->eventoModel->ObtenerPorRango($inicio, $fin);

        $data = array_map(function ($e) {
            return [
                'id'          => (int) $e['id'],
                'title'       => $e['titulo'],
                'start'       => $e['fecha_inicio'],
                'end'         => $e['fecha_fin'],
                'color'       => $e['color'] ?? '#4669FA',
                'description' => $e['descripcion'] ?? '',
                'usuario_id'  => $e['usuario_id'] ? (int) $e['usuario_id'] : null,
                'invitados'   => $this->eventoModel->ObtenerInvitados((int) $e['id']),
            ];
        }, $eventos);

        return $this->response->setJSON($data);
    }

    public function destinatarios(): \CodeIgniter\HTTP\Response
    {
        $db = \Config\Database::connect();

        $usuarios = $db->table('admin_usuarios')
                       ->where('activo', 1)
                       ->orderBy('nombre', 'ASC')
                       ->get()
                       ->getResultArray();

        $deptoModel = new DepartamentoModel();
        $deptos = $deptoModel->ObtenerTodos();

        return $this->response->setJSON([
            'usuarios'      => $usuarios,
            'departamentos' => $deptos,
        ]);
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
            'id'           => $json['id'] ?? null,
            'titulo'       => $json['titulo'],
            'descripcion'  => $json['descripcion'] ?? '',
            'fecha_inicio' => $json['fecha_inicio'],
            'fecha_fin'    => $json['fecha_fin'] ?? null,
            'color'        => $json['color'] ?? '#4669FA',
            'usuario_id'   => $json['usuario_id'] ?? null,
        ];

        if (empty($datos['id'])) {
            unset($datos['id']);
        }
        if (empty($datos['fecha_fin'])) {
            $datos['fecha_fin'] = null;
        }
        if (empty($datos['usuario_id'])) {
            $datos['usuario_id'] = null;
        }

        if (!$this->eventoModel->GuardarEvento($datos)) {
            $errors = $this->eventoModel->errors();
            return $this->response->setJSON([
                'success' => false,
                'message' => !empty($errors) ? implode(', ', $errors) : 'Error al guardar el evento.',
            ]);
        }

        $eventoId = $datos['id'] ?? $this->eventoModel->getInsertID();
        $evento   = $this->eventoModel->ObtenerPorId($eventoId);

        $this->eventoModel->GuardarInvitados(
            (int) $eventoId,
            $json['departamentos_invitados'] ?? [],
            $json['usuarios_invitados'] ?? []
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Evento guardado correctamente.',
            'data'    => [
                'id'          => (int) $evento['id'],
                'title'       => $evento['titulo'],
                'start'       => $evento['fecha_inicio'],
                'end'         => $evento['fecha_fin'],
                'color'       => $evento['color'] ?? '#4669FA',
                'description' => $evento['descripcion'] ?? '',
                'invitados'   => $this->eventoModel->ObtenerInvitados((int) $eventoId),
            ],
        ]);
    }

    public function eliminar(int $id): \CodeIgniter\HTTP\Response
    {
        $evento = $this->eventoModel->ObtenerPorId($id);
        if (!$evento) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Evento no encontrado.',
            ]);
        }

        if (!$this->eventoModel->EliminarEvento($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar el evento.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Evento eliminado correctamente.',
        ]);
    }
}
