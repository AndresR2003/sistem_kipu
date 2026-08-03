<?php

namespace App\Controllers;

use App\Models\PagoModel;
use App\Models\EntregaModel;
use App\Models\BorradorModel;
use App\Models\RecordatorioModel;
use App\Models\MarcadorModel;
use App\Models\EventoModel;
use App\Models\ColaboradorModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $pagoModel = new PagoModel();
        $pagoModel->GenerarDeudasAutomaticas();

        $hoy = date('Y-m-d');
        $usuarioId = (int) session()->get('usuario_id');
        $rol = session()->get('admin_rol') ?? 'empleado';
        $departamentoId = session()->get('id_departamento') ? (int) session()->get('id_departamento') : null;

        $entregaModel = new EntregaModel();
        $borradorModel = new BorradorModel();
        $recordatorioModel = new RecordatorioModel();
        $marcadorModel = new MarcadorModel();
        $eventoModel = new EventoModel();
        $colaboradorModel = new ColaboradorModel();

        $stats = [
            'tareas_hoy'      => $entregaModel->ContarActivas($hoy, $usuarioId, $departamentoId, $rol),
            'tareas_done'     => $entregaModel->ContarRegistradasHoy($hoy),
            'noticias'        => $borradorModel->ContarPublicados('noticias', $usuarioId, $departamentoId, $rol),
            'recordatorios'   => $recordatorioModel->ContarPendientes('recordatorio', $usuarioId),
            'marcadores'      => $marcadorModel->ContarTodos(),
            'eventos'         => $eventoModel->ContarProximos($hoy),
            'colaboradores'   => $colaboradorModel->countAllResults(),
        ];

        $eventos = $eventoModel->ObtenerProximos($hoy, 5);
        $noticias = $borradorModel->ObtenerPublicados('noticias', $usuarioId, $departamentoId, $rol);
        $noticias = array_slice($noticias, 0, 5);

        $data = [
            'titulo'    => 'Dashboard',
            'stats'     => $stats,
            'eventos'   => $eventos,
            'noticias'  => $noticias,
        ];

        return view('layout', [
            'contenido'  => view('dashboard', $data),
            'titulo'     => 'Dashboard - Litio',
            'pageScripts' => '',
        ]);
    }
}
