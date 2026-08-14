<?php

namespace App\Controllers;

use App\Models\BorradorModel;
use App\Models\ComentarioModel;
use App\Models\DepartamentoModel;
use App\Models\TareaModel;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\API\ResponseTrait;

class Borradores extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('layout', [
            'contenido'   => view('borradores'),
            'titulo'      => 'Borradores - Kipucloud',
            'pageScripts' => '<script src="' . base_url('js/borradores.js') . '?v=' . filemtime(FCPATH . 'js/borradores.js') . '"></script>'
                           . '<script src="' . base_url('js/entregas.js') . '?v=' . filemtime(FCPATH . 'js/entregas.js') . '"></script>',
        ]);
    }

    public function listar()
    {
        $model = new BorradorModel();
        $data  = $model->ObtenerTodos();
        return $this->response->setJSON($data);
    }

    public function obtener(int $id)
    {
        $model = new BorradorModel();
        $data  = $model->ObtenerPorId($id);
        if (!$data) {
            return $this->response->setJSON(['error' => 'No encontrado'])->setStatusCode(404);
        }
        return $this->response->setJSON($data);
    }

    public function guardar()
    {
        $json = $this->request->getJSON(true);
        if (!$json || empty($json['titulo'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El titulo es obligatorio.',
            ]);
        }

        $model = new BorradorModel();
        $datos = [
            'titulo'    => $json['titulo'],
            'contenido' => $json['contenido'] ?? '',
            'usuario_id' => session()->get('usuario_id') ?? session()->get('admin_id'),
        ];

        if (!empty($json['id'])) {
            $datos['id'] = (int) $json['id'];
        }

        $ok = $model->Guardar($datos);

        if ($ok) {
            $id = $datos['id'] ?? $model->getInsertID();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Borrador guardado correctamente.',
                'data'    => ['id' => (int) $id],
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al guardar el borrador.',
        ]);
    }

    public function eliminar(int $id)
    {
        $model = new BorradorModel();
        $ok    = $model->Eliminar($id);
        if ($ok) {
            service('cache')->delete('ultimo_anuncio');
        }
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Borrador eliminado.' : 'Error al eliminar.',
        ]);
    }

    public function fijar(int $id)
    {
        $model  = new BorradorModel();
        $actual = $model->ObtenerPorId($id);
        if (!$actual) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Borrador no encontrado.',
            ]);
        }

        $nuevo = $actual['fijado'] ? 0 : 1;
        $model->update($id, ['fijado' => $nuevo]);

        return $this->response->setJSON([
            'success' => true,
            'fijado'  => (bool) $nuevo,
            'message' => $nuevo ? 'Borrador fijado.' : 'Borrador desfijado.',
        ]);
    }

    public function publicar()
    {
        $json = $this->request->getJSON(true);
        if (!$json || empty($json['id']) || empty($json['seccion'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos para publicar.',
            ]);
        }

        $borradorId    = (int) $json['id'];
        $seccion       = $json['seccion'];
        $departamentos = !empty($json['departamentos']) ? array_map('intval', (array) $json['departamentos']) : [];
        $usuarios      = !empty($json['usuarios']) ? array_map('intval', (array) $json['usuarios']) : [];

        $model = new BorradorModel();
        $ok = $model->Publicar(
            $borradorId,
            $seccion,
            'multiple',
            null,
            !empty($json['anuncio']) ? 1 : 0
        );

        $model->GuardarDepartamentos($borradorId, $departamentos);
        $model->GuardarUsuarios($borradorId, $usuarios);

        // Si se publica en la seccion Tareas, se crea/actualiza la tarea enlazada
        if ($seccion === 'tareas') {
            $this->sincronizarTareaDesdeBorrador($borradorId, $departamentos, $usuarios);
        }

        if ($ok && !empty($json['anuncio'])) {
            service('cache')->delete('ultimo_anuncio');
        }

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Publicado correctamente.' : 'Error al publicar.',
        ]);
    }

    private function sincronizarTareaDesdeBorrador(int $borradorId, array $departamentos, array $usuarios): void
    {
        $borrador = (new BorradorModel())->ObtenerPorId($borradorId);
        if (!$borrador) {
            return;
        }

        $tareaModel = new TareaModel();
        $tarea = $tareaModel->where('borrador_id', $borradorId)->first();

        $datos = [
            'titulo'     => $borrador['titulo'],
            'descripcion'=> $borrador['contenido'] ?? '',
            'publicado'  => 1,
        ];

        if ($tarea) {
            $tareaModel->update($tarea['id'], $datos);
            $tareaId = $tarea['id'];
        } else {
            $datos['borrador_id'] = $borradorId;
            $datos['created_by']  = (int) ($borrador['usuario_id'] ?? session()->get('usuario_id') ?? session()->get('admin_id'));
            $datos['prioridad']   = 'media';
            $datos['modalidad']   = 'single_completes_all';
            $tareaModel->insert($datos);
            $tareaId = $tareaModel->insertID();
        }

        $tareaModel->GuardarDepartamentos($tareaId, $departamentos);
        $tareaModel->GuardarUsuarios($tareaId, $usuarios);
    }

    public function anuncio()
    {
        $cache = service('cache');
        $anuncio = $cache->get('ultimo_anuncio');
        if ($anuncio === null) {
            $anuncio = (new BorradorModel())->ObtenerUltimoAnuncio();
            $cache->save('ultimo_anuncio', $anuncio, 60);
        }
        return $this->response->setJSON(['success' => true, 'data' => $anuncio]);
    }

    public function destinatarios()
    {
        $db = \Config\Database::connect();
        $usuarios = $db->table('admin_usuarios')->where('activo', 1)->orderBy('nombre', 'ASC')->get()->getResultArray();
        $deptoModel = new DepartamentoModel();
        $deptos = $deptoModel->ObtenerTodos();

        return $this->response->setJSON([
            'usuarios' => $usuarios,
            'departamentos' => $deptos,
        ]);
    }

    public function listarPublicados(string $seccion)
    {
        $model = new BorradorModel();
        $usuarioId = (int) (session()->get('usuario_id') ?? session()->get('admin_id'));
        $rol = session()->get('admin_rol') ?? 'empleado';
        $departamentoId = (int) (session()->get('id_departamento') ?? 0);
        $data  = $model->ObtenerPublicados($seccion, $usuarioId, $departamentoId ?: null, $rol);

        $ids = array_column($data, 'id');
        $counts = empty($ids) ? [] : (new ComentarioModel())->ContarPorBorradores($ids);
        foreach ($data as &$d) {
            $d['comentarios_count'] = $counts[$d['id']] ?? 0;
        }

        return $this->response->setJSON($data);
    }

    public function despublicar(int $id)
    {
        $model  = new BorradorModel();
        $actual = $model->ObtenerPorId($id);
        if (!$actual || !$actual['publicado']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No esta publicado.',
            ]);
        }

        $ok = $model->Despublicar($id);
        if ($ok) {
            service('cache')->delete('ultimo_anuncio');
        }
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Publicacion retirada.' : 'Error al despublicar.',
        ]);
    }

    public function listarComentarios(int $borradorId)
    {
        $model = new ComentarioModel();
        $data  = $model->ObtenerPorBorrador($borradorId);
        return $this->response->setJSON($data);
    }

    public function guardarComentario()
    {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['borrador_id']) || empty($json['comentario'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faltan datos.',
            ]);
        }

        $model = new ComentarioModel();
        $ok    = $model->Guardar([
            'borrador_id' => (int) $json['borrador_id'],
            'usuario_id'  => session()->get('usuario_id') ?? session()->get('admin_id'),
            'comentario'  => $json['comentario'],
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Comentario agregado.' : 'Error al guardar.',
        ]);
    }

    public function completar(int $id)
    {
        $json = $this->request->getJSON(true);
        $completado = ($json['completado'] ?? 0) ? 1 : 0;

        $model  = new BorradorModel();
        $actual = $model->ObtenerPorId($id);
        if (!$actual || !$actual['publicado']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tarea no encontrada.',
            ]);
        }

        $ok = $model->ToggleCompletado($id, $completado);
        return $this->response->setJSON([
            'success' => $ok,
            'message' => $ok ? 'Estado actualizado.' : 'Error al actualizar.',
        ]);
    }
}
