<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PagoModel;

/**
 * Controlador API
 * Maneja todas las peticiones AJAX y devuelve respuestas JSON
 * Tambien maneja la vista publica del usuario por token
 */
class Api extends BaseController
{
    protected UsuarioModel $usuarioModel;
    protected PagoModel $pagoModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->pagoModel = new PagoModel();
    }

    /**
     * Respuesta JSON estandar
     */
    private function RespuestaJson(bool $success, string $message, $data = null): \CodeIgniter\HTTP\Response
    {
        return $this->response->setJSON([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    // =====================================================
    // VISTA PUBLICA DEL USUARIO (acceso por token)
    // =====================================================

    /**
     * Vista publica del usuario - accede desde /pago/{token}
     * Genera deudas automaticas y muestra estado del usuario
     */
    public function vistaPago(string $token)
    {
        // Validar token
        $usuario = $this->usuarioModel->BuscarPorToken($token);
        if (!$usuario) {
            return view('errors/html/error_404');
        }

        // Generar deudas automaticas
        $this->pagoModel->GenerarDeudasAutomaticas();

        // Obtener datos del usuario
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');
        $mesNombre = $this->ObtenerNombreMes($mesActual);

        $pagoActual = $this->pagoModel->EstadoMesActual($usuario['id']);
        $mesesAdeudados = $this->pagoModel->MesesAdeudados($usuario['id']);
        $totalAdeudado = $this->pagoModel->TotalAdeudado($usuario['id']);
        $yaPago = $this->pagoModel->YaPagoMesActual($usuario['id']);
        $ultimoPago = $this->pagoModel->UltimoPagoUsuario($usuario['id']);

        // Historial completo (todos los meses, incluyendo pagados)
        $historial = $this->pagoModel->HistorialUsuario($usuario['id']);

        // Preparar meses adeudados con nombre
        $mesesConNombre = [];
        foreach ($mesesAdeudados as $m) {
            $mesesConNombre[] = [
                'id'        => $m['id'],
                'mes'       => $m['mes'],
                'anio'      => $m['anio'],
                'monto'     => $m['monto'],
                'estado'    => $m['estado'],
                'mes_nombre' => $this->ObtenerNombreMes($m['mes']),
            ];
        }

        // Historial con nombre de mes
        $historialConNombre = [];
        foreach ($historial as $h) {
            $historialConNombre[] = [
                'id'         => $h['id'],
                'mes'        => $h['mes'],
                'anio'       => $h['anio'],
                'monto'      => $h['monto'],
                'estado'     => $h['estado'],
                'captura'    => $h['captura'],
                'observacion'=> $h['observacion'] ?? '',
                'fecha_envio'=> $h['fecha_envio'] ?? '',
                'mes_nombre' => $this->ObtenerNombreMes($h['mes']),
            ];
        }

        $datos = [
            'usuario'          => $usuario,
            'mesActual'        => $mesActual,
            'anioActual'       => $anioActual,
            'mesNombre'        => $mesNombre,
            'pagoActual'       => $pagoActual,
            'mesesAdeudados'   => $mesesConNombre,
            'totalAdeudado'    => $totalAdeudado,
            'yaPago'           => $yaPago,
            'ultimoPago'       => $ultimoPago,
            'token'            => $token,
            'historial'        => $historialConNombre,
        ];

        return view('subir_pago', $datos);
    }

    /**
     * Obtener nombre del mes en espanol
     */
    private function ObtenerNombreMes(int $mes): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $meses[$mes] ?? '';
    }

    // =====================================================
    // API - ESTADISTICAS DEL DASHBOARD
    // =====================================================

    /**
     * Obtener estadisticas para el dashboard (con filtros opcionales)
     */
    public function estadisticas()
    {
        $idUsuario = $this->request->getPost('id_usuario') ? (int) $this->request->getPost('id_usuario') : null;
        $mes = $this->request->getPost('mes') ? (int) $this->request->getPost('mes') : null;
        $anio = $this->request->getPost('anio') ? (int) $this->request->getPost('anio') : null;
        $estado = $this->request->getPost('estado') ?: null;

        $data = $this->pagoModel->EstadisticasConFiltros($idUsuario, $mes, $anio, $estado);

        return $this->RespuestaJson(true, 'Estadisticas obtenidas', $data);
    }

    // =====================================================
    // API - USUARIOS
    // =====================================================

    /**
     * Listar usuarios para DataTables
     */
    public function listarUsuarios()
    {
        $usuarios = $this->usuarioModel->ObtenerTodos();

        // Agregar estado de pago actual para cada usuario
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        foreach ($usuarios as &$usuario) {
            $pago = $this->pagoModel->where('id_usuario', $usuario['id'])
                                     ->where('mes', $mesActual)
                                     ->where('anio', $anioActual)
                                     ->first();
            $usuario['estado_pago'] = $pago ? $pago['estado'] : 'SIN REGISTRO';
            $usuario['token_url'] = site_url('pago/' . $usuario['token']);
        }

        return $this->RespuestaJson(true, 'Usuarios listados', $usuarios);
    }

    /**
     * Guardar usuario (crear o editar)
     */
    public function guardarUsuario()
    {
        $rules = [
            'nombre'   => 'required|max_length[100]',
            'telefono' => 'permit_empty|max_length[20]',
            'monto'    => 'required|decimal',
            'activo'   => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return $this->RespuestaJson(false, 'Error de validacion', $this->validator->getErrors());
        }

        $datos = [
            'nombre'   => $this->request->getPost('nombre'),
            'telefono' => $this->request->getPost('telefono'),
            'monto'    => $this->request->getPost('monto'),
            'activo'   => $this->request->getPost('activo'),
        ];

        $id = $this->request->getPost('id');

        if ($id) {
            $datos['id'] = $id;
        }

        if ($this->usuarioModel->GuardarUsuario($datos)) {
            $mensaje = $id ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente';
            return $this->RespuestaJson(true, $mensaje);
        }

        $errores = $this->usuarioModel->errors();
        return $this->RespuestaJson(false, 'Error al guardar usuario', $errores);
    }

    /**
     * Eliminar usuario
     */
    public function eliminarUsuario(int $idUsuario)
    {
        if ($this->usuarioModel->EliminarUsuario($idUsuario)) {
            return $this->RespuestaJson(true, 'Usuario eliminado correctamente');
        }

        return $this->RespuestaJson(false, 'Error al eliminar usuario');
    }

    // =====================================================
    // API - PAGOS
    // =====================================================

    /**
     * Listar pagos para DataTables (con filtros opcionales)
     */
    public function listarPagos()
    {
        $this->pagoModel->GenerarDeudasAutomaticas();

        $idUsuario = $this->request->getPost('id_usuario') ? (int) $this->request->getPost('id_usuario') : null;
        $mes = $this->request->getPost('mes') ? (int) $this->request->getPost('mes') : null;
        $anio = $this->request->getPost('anio') ? (int) $this->request->getPost('anio') : null;
        $estado = $this->request->getPost('estado') ?: null;

        if ($idUsuario !== null || $mes !== null || $anio !== null || $estado !== null) {
            $pagos = $this->pagoModel->ConFiltros($idUsuario, $mes, $anio, $estado);
        } else {
            $pagos = $this->pagoModel->ParaDataTable();
        }

        return $this->RespuestaJson(true, 'Pagos listados', $pagos);
    }

    /**
     * Guardar pago manual
     */
    public function guardarPago()
    {
        $rules = [
            'id_usuario' => 'required|integer',
            'mes'        => 'required|integer|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
            'anio'       => 'required|integer',
            'monto'      => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return $this->RespuestaJson(false, 'Error de validacion', $this->validator->getErrors());
        }

        // Verificar si ya existe
        $existe = $this->pagoModel->where('id_usuario', $this->request->getPost('id_usuario'))
                                   ->where('mes', $this->request->getPost('mes'))
                                   ->where('anio', $this->request->getPost('anio'))
                                   ->first();

        if ($existe) {
            return $this->RespuestaJson(false, 'Ya existe un registro para este mes');
        }

        $datos = [
            'id_usuario' => $this->request->getPost('id_usuario'),
            'mes'        => $this->request->getPost('mes'),
            'anio'       => $this->request->getPost('anio'),
            'monto'      => $this->request->getPost('monto'),
            'estado'     => 'NO_PAGADO',
        ];

        if ($this->pagoModel->insert($datos)) {
            return $this->RespuestaJson(true, 'Pago registrado correctamente');
        }

        $errores = $this->pagoModel->errors();
        return $this->RespuestaJson(false, 'Error al registrar pago', $errores);
    }

    /**
     * Aprobar un pago
     */
    public function aprobarPago(int $idPago)
    {
        $pago = $this->pagoModel->find($idPago);
        if (!$pago) {
            return $this->RespuestaJson(false, 'Pago no encontrado');
        }

        if ($pago['estado'] === 'PAGADO') {
            return $this->RespuestaJson(false, 'Este pago ya fue aprobado');
        }

        if ($this->pagoModel->AprobarPago($idPago)) {
            $usuario = $this->usuarioModel->BuscarPorId($pago['id_usuario']);
            $nombreMes = $this->ObtenerNombreMes($pago['mes']);
            return $this->RespuestaJson(
                true,
                "Pago de {$usuario['nombre']} - {$nombreMes} {$pago['anio']} aprobado correctamente"
            );
        }

        return $this->RespuestaJson(false, 'Error al aprobar pago');
    }

    /**
     * Rechazar un pago
     */
    public function rechazarPago(int $idPago)
    {
        $pago = $this->pagoModel->find($idPago);
        if (!$pago) {
            return $this->RespuestaJson(false, 'Pago no encontrado');
        }

        $observacion = $this->request->getPost('observacion') ?? '';

        if ($this->pagoModel->RechazarPago($idPago, $observacion)) {
            $usuario = $this->usuarioModel->BuscarPorId($pago['id_usuario']);
            $nombreMes = $this->ObtenerNombreMes($pago['mes']);
            return $this->RespuestaJson(
                true,
                "Pago de {$usuario['nombre']} - {$nombreMes} {$pago['anio']} rechazado"
            );
        }

        return $this->RespuestaJson(false, 'Error al rechazar pago');
    }

    /**
     * Eliminar un registro de pago
     */
    public function eliminarPago(int $idPago)
    {
        if ($this->pagoModel->EliminarPago($idPago)) {
            return $this->RespuestaJson(true, 'Pago eliminado correctamente');
        }

        return $this->RespuestaJson(false, 'Error al eliminar pago');
    }

    // =====================================================
    // API - COMPROBANTES
    // =====================================================

    /**
     * Subir comprobante de pago
     */
    public function subirComprobante()
    {
        // Validar que se envio un archivo
        $file = $this->request->getFile('comprobante');
        if (!$file || !$file->isValid()) {
            return $this->RespuestaJson(false, 'No se envio ningun archivo');
        }

        // Validar que no haya error
        if (!$file->isValid()) {
            return $this->RespuestaJson(false, 'Error al subir el archivo: ' . $file->getErrorString());
        }

        // Extensiones permitidas
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($file->getExtension());

        if (!in_array($extension, $extensionesPermitidas)) {
            return $this->RespuestaJson(
                false,
                'Extension no permitida. Solo se aceptan: ' . implode(', ', $extensionesPermitidas)
            );
        }

        // Validar tamaño maximo (5MB)
        $tamanoMaximo = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $tamanoMaximo) {
            return $this->RespuestaJson(false, 'El archivo excede el tamaño maximo de 5MB');
        }

        // Validar MIME type
        $mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getClientMimeType(), $mimePermitidos)) {
            return $this->RespuestaJson(false, 'Tipo de archivo no permitido');
        }

        // Obtener datos del formulario
        $idUsuario = (int) $this->request->getPost('id_usuario');
        $mes = (int) $this->request->getPost('mes');
        $anio = (int) $this->request->getPost('anio');
        $token = $this->request->getPost('token');

        // Validar que el token coincida con el usuario
        $usuarioToken = $this->usuarioModel->BuscarPorToken($token);
        if (!$usuarioToken || $usuarioToken['id'] != $idUsuario) {
            return $this->RespuestaJson(false, 'Token invalido');
        }

        // Generar nombre unico para el archivo
        $nombreArchivo = 'pago_' . $idUsuario . '_' . $mes . '_' . $anio . '_' . time() . '.' . $extension;

        // Asegurar que el directorio existe en public/
        $directorio = FCPATH . 'uploads/pagos';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        if ($file->move($directorio, $nombreArchivo)) {
            $rutaRelativa = 'uploads/pagos/' . $nombreArchivo;

            // Verificar si ya existe un registro para este mes
            $pagoExistente = $this->pagoModel->where('id_usuario', $idUsuario)
                                              ->where('mes', $mes)
                                              ->where('anio', $anio)
                                              ->first();

            if ($pagoExistente) {
                // Eliminar imagen anterior si existe
                if ($pagoExistente['captura']) {
                    $imagenAnterior = FCPATH . $pagoExistente['captura'];
                    if (file_exists($imagenAnterior)) {
                        unlink($imagenAnterior);
                    }
                }

                // Actualizar registro existente
                $this->pagoModel->update($pagoExistente['id'], [
                    'estado'     => 'PENDIENTE',
                    'captura'    => $rutaRelativa,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            } else {
                // Crear nuevo registro - buscar monto del usuario
                $usuario = $this->usuarioModel->find($idUsuario);
                $montoUsuario = $usuario['monto'] ?? 12.00;
                $this->pagoModel->insert([
                    'id_usuario' => $idUsuario,
                    'mes'        => $mes,
                    'anio'       => $anio,
                    'monto'      => $montoUsuario,
                    'estado'     => 'PENDIENTE',
                    'captura'    => $rutaRelativa,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            }

            return $this->RespuestaJson(true, 'Comprobante subido correctamente', [
                'imagen' => $rutaRelativa,
            ]);
        }

        return $this->RespuestaJson(false, 'Error al guardar el archivo');
    }

    /**
     * Ver comprobante de un pago
     */
    public function verComprobante(int $idPago)
    {
        $pago = $this->pagoModel->find($idPago);
        if (!$pago) {
            return $this->RespuestaJson(false, 'Pago no encontrado');
        }

        if (!$pago['captura']) {
            return $this->RespuestaJson(false, 'No hay comprobante adjunto');
        }

        $rutaCompleta = FCPATH . $pago['captura'];

        if (!file_exists($rutaCompleta)) {
            return $this->RespuestaJson(false, 'Archivo de comprobante no encontrado');
        }

        $usuario = $this->usuarioModel->BuscarPorId($pago['id_usuario']);
        $nombreMes = $this->ObtenerNombreMes($pago['mes']);

        return $this->RespuestaJson(true, 'Comprobante encontrado', [
            'imagen'     => base_url($pago['captura']),
            'nombre'     => $usuario['nombre'] ?? '',
            'mes'        => $nombreMes,
            'anio'       => $pago['anio'],
            'estado'     => $pago['estado'],
            'observacion' => $pago['observacion'] ?? '',
        ]);
    }

    // =====================================================
    // API - HISTORIAL
    // =====================================================

    /**
     * Obtener historial de un usuario
     */
    public function historialUsuario(int $idUsuario)
    {
        $this->pagoModel->GenerarDeudasAutomaticas();

        $usuario = $this->usuarioModel->BuscarPorId($idUsuario);
        if (!$usuario) {
            return $this->RespuestaJson(false, 'Usuario no encontrado');
        }

        $historial = $this->pagoModel->HistorialUsuario($idUsuario);

        // Agregar nombre del mes
        foreach ($historial as &$item) {
            $item['mes_nombre'] = $this->ObtenerNombreMes($item['mes']);
        }

        $totalAdeudado = $this->pagoModel->TotalAdeudado($idUsuario);

        return $this->RespuestaJson(true, 'Historial obtenido', [
            'usuario'       => $usuario,
            'historial'     => $historial,
            'totalAdeudado' => $totalAdeudado,
        ]);
    }

    /**
     * Datos del usuario por token (para vista publica via AJAX)
     */
    public function datosUsuarioToken(string $token)
    {
        $usuario = $this->usuarioModel->BuscarPorToken($token);
        if (!$usuario) {
            return $this->RespuestaJson(false, 'Token invalido');
        }

        $this->pagoModel->GenerarDeudasAutomaticas();

        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        $pagoActual = $this->pagoModel->EstadoMesActual($usuario['id']);
        $mesesAdeudados = $this->pagoModel->MesesAdeudados($usuario['id']);
        $totalAdeudado = $this->pagoModel->TotalAdeudado($usuario['id']);
        $yaPago = $this->pagoModel->YaPagoMesActual($usuario['id']);

        $mesesConNombre = [];
        foreach ($mesesAdeudados as $m) {
            $mesesConNombre[] = [
                'id'         => $m['id'],
                'mes'        => $m['mes'],
                'anio'       => $m['anio'],
                'monto'      => $m['monto'],
                'estado'     => $m['estado'],
                'mes_nombre' => $this->ObtenerNombreMes($m['mes']),
            ];
        }

        return $this->RespuestaJson(true, 'Datos obtenidos', [
            'usuario'        => $usuario,
            'mesActual'      => $mesActual,
            'anioActual'     => $anioActual,
            'mesNombre'      => $this->ObtenerNombreMes($mesActual),
            'pagoActual'     => $pagoActual,
            'mesesAdeudados' => $mesesConNombre,
            'totalAdeudado'  => $totalAdeudado,
            'yaPago'         => $yaPago,
        ]);
    }

    // =====================================================
    // API - EXPORTAR
    // =====================================================

    /**
     * Exportar pagos a Excel (JSON para JS)
     */
    public function exportarExcel()
    {
        $this->pagoModel->GenerarDeudasAutomaticas();

        $idUsuario = $this->request->getPost('id_usuario') ? (int) $this->request->getPost('id_usuario') : null;
        $mes = $this->request->getPost('mes') ? (int) $this->request->getPost('mes') : null;
        $anio = $this->request->getPost('anio') ? (int) $this->request->getPost('anio') : null;
        $estado = $this->request->getPost('estado') ?: null;

        $pagos = ($idUsuario !== null || $mes !== null || $anio !== null || $estado !== null)
            ? $this->pagoModel->ConFiltros($idUsuario, $mes, $anio, $estado)
            : $this->pagoModel->ParaDataTable();

        $datos = [];
        foreach ($pagos as $pago) {
            $datos[] = [
                'Usuario'   => $pago['nombre'],
                'Telefono'  => $pago['telefono'],
                'Mes'       => $this->ObtenerNombreMes($pago['mes']),
                'Anio'      => $pago['anio'],
                'Monto'     => 'S/ ' . number_format($pago['monto'], 2),
                'Estado'    => $pago['estado'],
                'Fecha Envio' => $pago['fecha_envio'] ?? '-',
            ];
        }

        return $this->RespuestaJson(true, 'Datos para Excel', $datos);
    }

    /**
     * Exportar pagos a PDF (JSON para JS)
     */
    public function exportarPdf()
    {
        $this->pagoModel->GenerarDeudasAutomaticas();

        $idUsuario = $this->request->getPost('id_usuario') ? (int) $this->request->getPost('id_usuario') : null;
        $mes = $this->request->getPost('mes') ? (int) $this->request->getPost('mes') : null;
        $anio = $this->request->getPost('anio') ? (int) $this->request->getPost('anio') : null;
        $estado = $this->request->getPost('estado') ?: null;

        $pagos = ($idUsuario !== null || $mes !== null || $anio !== null || $estado !== null)
            ? $this->pagoModel->ConFiltros($idUsuario, $mes, $anio, $estado)
            : $this->pagoModel->ParaDataTable();

        $datos = [];
        foreach ($pagos as $pago) {
            $datos[] = [
                'Usuario'   => $pago['nombre'],
                'Mes'       => $this->ObtenerNombreMes($pago['mes']),
                'Anio'      => $pago['anio'],
                'Monto'     => 'S/ ' . number_format($pago['monto'], 2),
                'Estado'    => $pago['estado'],
            ];
        }

        return $this->RespuestaJson(true, 'Datos para PDF', $datos);
    }

    // =====================================================
    // API - FILTROS
    // =====================================================

    /**
     * Obtener meses con registros (para filtro)
     */
    public function mesesConRegistros()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $registros = $this->pagoModel->MesesConRegistros();
        $resultado = [];
        foreach ($registros as $r) {
            $resultado[] = [
                'mes'       => (int) $r['mes'],
                'anio'      => (int) $r['anio'],
                'mes_nombre' => ($meses[(int) $r['mes']] ?? '') . ' ' . $r['anio'],
            ];
        }

        return $this->RespuestaJson(true, 'Meses obtenidos', $resultado);
    }

    // =====================================================
    // API - NOTIFICACIONES
    // =====================================================

    /**
     * Obtener notificaciones (pagos pendientes)
     */
    public function notificaciones()
    {
        $pendientes = $this->pagoModel->ContarPendientes();
        $conDeuda = $this->pagoModel->ContarConDeuda();

        return $this->RespuestaJson(true, 'Notificaciones', [
            'pendientes' => $pendientes,
            'conDeuda'   => $conDeuda,
            'hayPendientes' => $pendientes > 0,
        ]);
    }
}
