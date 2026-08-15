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
    ];

    public function listar(): ResponseInterface
    {
        $desde = max(0, (int) $this->request->getGet('desde'));
        $mensajes = (new ChatModel())->obtenerRecientes($desde);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $mensajes,
        ]);
    }

    public function enviar(): ResponseInterface
    {
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $mensaje = trim((string) $this->request->getPost('mensaje'));
        $archivo = $this->request->getFile('archivo');

        if ($mensaje === '' && (!$archivo || $archivo->getError() === UPLOAD_ERR_NO_FILE)) {
            return $this->respuestaError('Escribe un mensaje o selecciona un archivo.');
        }

        $datos = [
            'usuario_id' => $usuarioId,
            'mensaje'   => mb_substr($mensaje, 0, 2000),
            'creado_en' => date('Y-m-d H:i:s'),
        ];

        if ($archivo && $archivo->getError() !== UPLOAD_ERR_NO_FILE) {
            if (!$archivo->isValid()) {
                return $this->respuestaError('El archivo no se pudo cargar.');
            }
            if ($archivo->getSize() > self::MAX_FILE_SIZE) {
                return $this->respuestaError('El archivo debe pesar como máximo 10 MB.');
            }
            if (!in_array($archivo->getMimeType(), self::ALLOWED_MIMES, true)) {
                return $this->respuestaError('Este tipo de archivo no está permitido.');
            }

            $directorio = WRITEPATH . 'uploads/chat';
            if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
                return $this->respuestaError('No se pudo preparar el almacenamiento del archivo.');
            }

            $nombreGuardado = bin2hex(random_bytes(16)) . '.' . strtolower($archivo->getExtension());
            $archivo->move($directorio, $nombreGuardado);
            $datos['archivo_nombre'] = $archivo->getClientName();
            $datos['archivo_ruta'] = $nombreGuardado;
            $datos['archivo_mime'] = $archivo->getMimeType();
            $datos['archivo_tamano'] = $archivo->getSize();
        }

        $model = new ChatModel();
        $id = $model->insert($datos, true);
        if (!$id) {
            return $this->respuestaError('No se pudo enviar el mensaje.');
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => ['id' => (int) $id],
        ]);
    }

    public function archivo(int $id)
    {
        $mensaje = (new ChatModel())->obtenerPorId($id);
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
}
