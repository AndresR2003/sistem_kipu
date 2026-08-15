<?php

namespace App\Controllers;

use App\Models\ChatModel;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    private const MAX_FILE_SIZE = 10485760;

    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain', 'text/csv',
        'application/zip', 'application/x-zip-compressed',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'audio/mpeg', 'audio/ogg', 'audio/wav', 'video/mp4', 'video/webm',
        'application/x-rar-compressed', 'application/vnd.rar', 'application/octet-stream',
    ];

    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'csv',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar',
        'mp3', 'ogg', 'wav', 'mp4', 'webm',
    ];

    public function usuarios(): ResponseInterface
    {
        $usuarioId = $this->usuarioActual();

        try {
            return $this->response->setJSON([
                'success' => true,
                'data'    => (new ChatModel())->obtenerUsuarios($usuarioId),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Chat usuarios: {message}', ['message' => $e->getMessage()]);
            return $this->respuestaError('No se pudieron cargar los usuarios. Verifica la base de datos.');
        }
    }

    public function conversaciones(): ResponseInterface
    {
        try {
            return $this->response->setJSON([
                'success' => true,
                'data'    => (new ChatModel())->obtenerConversaciones($this->usuarioActual()),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Chat conversaciones: {message}', ['message' => $e->getMessage()]);
            return $this->respuestaError('No se pudieron cargar las conversaciones.');
        }
    }

    public function listar(): ResponseInterface
    {
        $usuarioId = $this->usuarioActual();
        $desde = max(0, (int) $this->request->getGet('desde'));
        $destinatarioId = $this->destinatario();

        try {
            $mensajes = (new ChatModel())->obtenerRecientes($usuarioId, $destinatarioId, $desde);
        } catch (\Throwable $e) {
            log_message('error', 'Chat listar: {message}', ['message' => $e->getMessage()]);
            return $this->respuestaError('No se pudo cargar el chat. Ejecuta la migración de chat_mensajes.');
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $mensajes,
        ]);
    }

    public function enviar(): ResponseInterface
    {
        $usuarioId = $this->usuarioActual();
        $destinatarioId = $this->destinatario();
        $mensaje = trim((string) $this->request->getPost('mensaje'));
        $archivo = $this->request->getFile('archivo');

        if ($destinatarioId === $usuarioId) {
            return $this->respuestaError('No puedes iniciar una conversación contigo mismo.');
        }
        if ($destinatarioId !== null) {
            $destinatario = (new \App\Models\AdminModel())->where('id', $destinatarioId)->where('activo', 1)->first();
            if (!$destinatario) {
                return $this->respuestaError('El usuario seleccionado no está disponible.');
            }
        }

        if ($mensaje === '' && (!$archivo || $archivo->getError() === UPLOAD_ERR_NO_FILE)) {
            return $this->respuestaError('Escribe un mensaje o selecciona un archivo.');
        }

        $datos = [
            'usuario_id'      => $usuarioId,
            'destinatario_id' => $destinatarioId,
            'mensaje'         => mb_substr($mensaje, 0, 2000),
            'creado_en'       => date('Y-m-d H:i:s'),
        ];

        if ($archivo && $archivo->getError() !== UPLOAD_ERR_NO_FILE) {
            if (!$archivo->isValid()) {
                return $this->respuestaError('El archivo no se pudo cargar.');
            }
            if ($archivo->getSize() > self::MAX_FILE_SIZE) {
                return $this->respuestaError('El archivo debe pesar como máximo 10 MB.');
            }
            $extension = strtolower($archivo->getExtension());
            $mime = $archivo->getMimeType();
            if (!in_array($extension, self::ALLOWED_EXTENSIONS, true) || !in_array($mime, self::ALLOWED_MIMES, true)) {
                return $this->respuestaError('Este tipo de archivo no está permitido.');
            }

            $directorio = WRITEPATH . 'uploads/chat';
            if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
                return $this->respuestaError('No se pudo preparar el almacenamiento del archivo.');
            }

            $nombreGuardado = bin2hex(random_bytes(16)) . '.' . $extension;
            $archivo->move($directorio, $nombreGuardado);
            $datos['archivo_nombre'] = $archivo->getClientName();
            $datos['archivo_ruta'] = $nombreGuardado;
            $datos['archivo_mime'] = $archivo->getMimeType();
            $datos['archivo_tamano'] = $archivo->getSize();
        }

        $model = new ChatModel();
        try {
            $id = $model->insert($datos, true);
        } catch (\Throwable $e) {
            log_message('error', 'Chat enviar: {message}', ['message' => $e->getMessage()]);
            return $this->respuestaError('No se pudo enviar. Ejecuta la migración de chat_mensajes.');
        }
        if (!$id) {
            log_message('error', 'Chat enviar: ' . json_encode($model->errors()));
            return $this->respuestaError('No se pudo guardar el mensaje. Revisa la tabla chat_mensajes.');
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => ['id' => (int) $id],
        ]);
    }

    public function archivo(int $id)
    {
        $mensaje = (new ChatModel())->obtenerPorId($id, $this->usuarioActual());
        if (!$mensaje || empty($mensaje['archivo_ruta'])) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado.');
        }

        $ruta = realpath(WRITEPATH . 'uploads/chat' . DIRECTORY_SEPARATOR . basename($mensaje['archivo_ruta']));
        $directorio = realpath(WRITEPATH . 'uploads/chat');
        if (!$ruta || !$directorio || dirname($ruta) !== $directorio || !is_file($ruta)) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado.');
        }

        return $this->response->download($ruta, null)->setFileName($mensaje['archivo_nombre'] ?: basename($ruta));
    }

    private function respuestaError(string $mensaje): ResponseInterface
    {
        return $this->response->setJSON([
            'success' => false,
            'message' => $mensaje,
        ])->setStatusCode(422);
    }

    private function usuarioActual(): int
    {
        return (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
    }

    private function destinatario(): ?int
    {
        $valor = $this->request->getPost('destinatario_id') ?? $this->request->getGet('destinatario_id');
        return $valor === null || $valor === '' ? null : max(0, (int) $valor);
    }
}
