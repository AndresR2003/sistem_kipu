<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PagoModel;

/**
 * Controlador Pagos
 * Gestion de pagos e historial desde el admin
 */
class Pagos extends BaseController
{
    protected PagoModel $pagoModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->pagoModel = new PagoModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Pagina de gestion de pagos
     */
    public function index(): string
    {
        $this->pagoModel->GenerarDeudasAutomaticas();

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $data = [
            'titulo' => 'Gestion de Pagos',
            'meses'  => $meses,
        ];

        $pageScripts = '<script src="' . base_url('assets/js/xlsx.full.min.js') . '"></script>'
                     . '<script src="' . base_url('assets/js/jspdf.umd.min.js') . '"></script>'
                     . '<script src="' . base_url('assets/js/jspdf.plugin.autotable.min.js') . '"></script>'
                     . '<script src="' . base_url('js/pagos_admin.js') . '?v=' . filemtime(FCPATH . 'js/pagos_admin.js') . '"></script>';

        return view('layout', [
            'contenido'  => view('pagos', $data),
            'titulo'     => 'Pagos - Kipucloud',
            'pageScripts' => $pageScripts,
        ]);
    }

    /**
     * Historial de un usuario especifico
     */
    public function historial(int $idUsuario): string
    {
        $this->pagoModel->GenerarDeudasAutomaticas();

        $usuario = $this->usuarioModel->BuscarPorId($idUsuario);
        if (!$usuario) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $historial = $this->pagoModel->HistorialUsuario($idUsuario);

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        // Agregar nombre del mes
        foreach ($historial as &$item) {
            $item['mes_nombre'] = $meses[$item['mes']] ?? '';
        }
        unset($item);

        $totalAdeudado = $this->pagoModel->TotalAdeudado($idUsuario);

        $pagados = 0;
        $pendientes = 0;
        foreach ($historial as $item) {
            if ($item['estado'] === 'PAGADO') $pagados++;
            else $pendientes++;
        }

        $data = [
            'titulo'         => 'Historial de ' . $usuario['nombre'],
            'usuario'        => $usuario,
            'historial'      => $historial,
            'totalAdeudado'  => $totalAdeudado,
            'meses'          => $meses,
        ];

        $pageScripts = '<script>'
            . '$(document).ready(function() {'
            . '$("#totalPagados").text("' . $pagados . '");'
            . '$("#totalPendientes").text("' . $pendientes . '");'
            . '$("#tablaHistorial").DataTable({'
            . 'language: { search: "Buscar:", lengthMenu: "Mostrar _MENU_ registros", info: "Mostrando _START_ a _END_ de _TOTAL_ registros", paginate: { previous: "Anterior", next: "Siguiente" }, zeroRecords: "No hay registros" },'
            . 'pageLength: 12, order: [[1, "desc"], [0, "desc"]]'
            . '});'
            . '});'
            . 'function verComprobanteHistorial(idPago) {'
            . '$.ajax({ url: BASE_URL + "/api/ver-comprobante/" + idPago, type: "POST", dataType: "json",'
            . 'success: function(response) {'
            . 'if (response.success) { var data = response.data;'
            . '$("#compHistUsuario").text(data.nombre);'
            . '$("#compHistMes").text(data.mes + " " + data.anio + " - " + data.estado);'
            . '$("#compHistImagen").attr("src", data.imagen);'
            . 'if (data.observacion) { $("#compHistObservacion").html(\'<i class="bi bi-exclamation-triangle"></i> Motivo rechazo: \' + escapeHtml(data.observacion)).show(); }'
            . 'else { $("#compHistObservacion").hide(); }'
            . '$("#modalComprobanteHistorial").modal("show");'
            . '} else { Swal.fire("Error", response.message, "error"); } } });'
            . '}'
            . 'function escapeHtml(text) { if (!text) return ""; var div = document.createElement("div"); div.appendChild(document.createTextNode(text)); return div.innerHTML; }'
            . '</script>';

        return view('layout', [
            'contenido' => view('historial', $data),
            'titulo'    => 'Historial - ' . $usuario['nombre'],
            'pageScripts' => $pageScripts,
        ]);
    }
}
