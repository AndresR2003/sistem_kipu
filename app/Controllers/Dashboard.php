<?php

namespace App\Controllers;

use App\Models\BorradorModel;
use App\Models\PaseTurnoModel;
use App\Models\RecordatorioModel;
use App\Models\EventoModel;
use App\Models\ColaboradorModel;
use App\Models\TareaModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $hoy = date('Y-m-d');
        $usuarioId = (int) session()->get('usuario_id');
        $rol = session()->get('admin_rol') ?? 'empleado';
        $departamentoId = session()->get('id_departamento') ? (int) session()->get('id_departamento') : null;

        $cacheKey = 'dashboard_data_' . $rol . '_' . $usuarioId . '_' . $departamentoId . '_' . $hoy;
        $cache = service('cache');
        $data = $cache->get($cacheKey);

        if ($data === null) {
            $paseModel = new PaseTurnoModel();
            $tareaModel = new TareaModel();
            $borradorModel = new BorradorModel();
            $recordatorioModel = new RecordatorioModel();
            $eventoModel = new EventoModel();
            $colaboradorModel = new ColaboradorModel();

            $db = \Config\Database::connect();
            $tareasHoy = (int) $db->table('tareas')->where('publicado', 1)->where('completada', 0)->countAllResults();
            $tareasDone = (int) $db->table('tareas')->where('publicado', 1)->where('completada', 1)->countAllResults();
            $tareasVencidas = (int) $db->table('tareas')
                ->where('publicado', 1)
                ->where('completada', 0)
                ->where('fecha_limite IS NOT NULL')
                ->where('fecha_limite <', $hoy . ' 00:00:00')
                ->countAllResults();

            $stats = [
                'tareas_hoy'       => $tareasHoy,
                'tareas_done'      => $tareasDone,
                'noticias'         => $borradorModel->ContarPublicados('noticias', $usuarioId, $departamentoId, $rol),
                'recordatorios'    => $recordatorioModel->ContarPendientes('recordatorio', $usuarioId),
                'marcadores'       => $recordatorioModel->ContarTodos('marcador', $usuarioId),
                'eventos'          => $eventoModel->ContarProximos($hoy),
                'colaboradores'    => $colaboradorModel->countAllResults(),
                'tareas_vencidas'  => $tareasVencidas,
                'pases_pendientes' => $paseModel->ContarAbiertos(),
            ];

            $eventos = $eventoModel->ObtenerProximos($hoy, 5);
            $noticias = $borradorModel->ObtenerPublicados('noticias', $usuarioId, $departamentoId, $rol, 5);

            $data = [
                'titulo'    => 'Dashboard',
                'stats'     => $stats,
                'eventos'   => $eventos,
                'noticias'  => $noticias,
            ];

            $cache->save($cacheKey, $data, 60);
        }

        return view('layout', [
            'contenido'  => view('dashboard', $data),
            'titulo'     => 'Dashboard - Kipucloud',
            'pageScripts' => '',
        ]);
    }
}
