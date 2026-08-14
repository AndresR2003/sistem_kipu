<?php

namespace App\Controllers;

use App\Models\BorradorModel;
use App\Models\ComentarioModel;

class Noticias extends BaseController
{
    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('seccion_publicaciones', ['seccion' => 'noticias']),
            'titulo'     => 'Noticias - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/publicaciones.js') . '?v=' . time() . '"></script>',
        ]);
    }

    public function ver(int $id)
    {
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $rol = session()->get('admin_rol') ?? 'empleado';
        $departamentoId = (int) (session()->get('id_departamento') ?? 0);

        $publicacion = (new BorradorModel())->ObtenerPublicadoDetalle($id, $usuarioId, $departamentoId ?: null, $rol);
        if (!$publicacion) {
            return redirect()->to(base_url('noticias'))->with('error', 'Publicacion no encontrada.');
        }

        $publicacion['comentarios_count'] = (new ComentarioModel())->ContarPorBorradores([$id])[$id] ?? 0;

        return view('layout', [
            'contenido'  => view('detalle_publicacion', [
                'seccion'     => 'noticias',
                'publicacion' => $publicacion,
                'esAutor'     => (int) $publicacion['usuario_id'] === $usuarioId,
                'esAdmin'     => in_array($rol, ['admin', 'superadmin'], true),
                'tituloPagina' => 'Noticias - Kipucloud',
            ]),
            'titulo'     => 'Noticias - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/detalle_publicacion.js') . '?v=' . time() . '"></script>',
        ]);
    }
}
