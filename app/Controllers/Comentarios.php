<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Comentarios extends BaseController
{
    private const MAX_FILE_SIZE = 10485760;

    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'text/plain', 'text/csv',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'csv', 'doc', 'docx', 'xls', 'xlsx',
    ];

    public function subirArchivo(): ResponseInterface
    {
        $archivo = $this->request->getFile('archivo');

        if (!$archivo || $archivo->getError() === UPLOAD_ERR_NO_FILE || !$archivo->isValid()) {
            return $this->respuestaError('No se recibió un archivo válido.');
        }

        if ($archivo->getSize() > self::MAX_FILE_SIZE) {
            return $this->respuestaError('El archivo debe pesar como máximo 10 MB.');
        }

        $extension = strtolower(pathinfo($archivo->getClientName(), PATHINFO_EXTENSION) ?: $archivo->getExtension());
        $mime = strtolower((string) $archivo->getMimeType());
        $mimeBase = trim(explode(';', $mime)[0]);
        $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
        $imagenValida = $esImagen && @getimagesize($archivo->getTempName()) !== false;

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true) || (!$imagenValida && !in_array($mimeBase, self::ALLOWED_MIMES, true))) {
            return $this->respuestaError('Tipo de archivo no permitido. Solo PDF, Word, Excel o imágenes.');
        }

        $directorio = FCPATH . 'uploads/comentarios';
        if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            return $this->respuestaError('No se pudo preparar el almacenamiento.');
        }

        $nombreGuardado = bin2hex(random_bytes(16)) . '.' . $extension;
        $archivo->move($directorio, $nombreGuardado);

        return $this->response->setJSON([
            'success' => true,
            'archivo' => [
                'nombre'   => $archivo->getClientName(),
                'ruta'     => 'uploads/comentarios/' . $nombreGuardado,
                'url'      => base_url('uploads/comentarios/' . $nombreGuardado),
                'mime'     => $mimeBase,
                'extension' => $extension,
                'tamano'   => $archivo->getSize(),
            ],
        ]);
    }

    private function respuestaError(string $mensaje): ResponseInterface
    {
        return $this->response->setJSON([
            'success' => false,
            'message' => $mensaje,
        ])->setStatusCode(422);
    }
}
