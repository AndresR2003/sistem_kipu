<?php

namespace App\Controllers;

use App\Models\RecordatorioModel;
use App\Models\BorradorModel;
use App\Models\ComentarioModel;
use App\Models\InteraccionModel;
use App\Models\PaseTurnoModel;
use App\Models\TareaModel;
use App\Models\EventoModel;
use App\Models\DepartamentoModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Endpoints JWT para la app móvil: Recordatorio, Marcadores, Borradores,
 * Publicaciones (Noticias/Ideas/Manual), Pases de turno, Tareas y Calendario.
 * Reutiliza los mismos modelos que el panel web; solo cambia la
 * autenticación (Bearer token en vez de sesión de navegador).
 */
class AppApiController extends ResourceController
{
    protected $format = 'json';
    private $secret_key;

    public function __construct()
    {
        $this->secret_key = getenv('JWT_SECRET');
        if (!$this->secret_key) {
            throw new \RuntimeException('Configura JWT_SECRET en el archivo .env');
        }
        date_default_timezone_set('America/Lima');
    }

    private function validateToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!$header) {
            return false;
        }
        try {
            $token = str_replace('Bearer ', '', $header);
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return $decoded->data;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function usuarioId($user): int
    {
        return (int) ($user->coduser ?? 0);
    }

    private function esAdmin($user): bool
    {
        $db = \Config\Database::connect();
        $rol = $db->table('admin_usuarios')->select('rol')->where('id', $this->usuarioId($user))->get()->getRow();
        return in_array($rol->rol ?? '', ['admin', 'superadmin'], true);
    }

    private function ok(array $data = [])
    {
        return $this->response->setJSON(array_merge(['status' => true], $data));
    }

    private function fail_(string $mensaje, int $code = 200)
    {
        return $this->response->setJSON(['status' => false, 'message' => $mensaje])->setStatusCode($code);
    }

    // =====================================================
    // RECORDATORIO / MARCADORES
    // =====================================================

    public function recordatorio_listar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $tipo = $this->request->getGet('tipo') ?? 'recordatorio';
        $data = (new RecordatorioModel())->ObtenerTodosConOrigen($tipo, $this->usuarioId($user));
        return $this->ok(['data' => $data]);
    }

    public function recordatorio_obtener($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $data = (new RecordatorioModel())->ObtenerPorId((int) $id);
        if (!$data) return $this->fail_('No encontrado.');
        return $this->ok(['data' => $data]);
    }

    public function recordatorio_guardar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['titulo'])) {
            return $this->fail_('El título es obligatorio.');
        }

        $tipo = $json['tipo'] ?? 'recordatorio';
        $seccion = $json['seccion'] ?? null;
        if ($seccion === 'tareas_diarias') $seccion = 'tareas';

        $model = new RecordatorioModel();
        $datos = [
            'id'          => $json['id'] ?? null,
            'titulo'      => $json['titulo'],
            'descripcion' => $json['descripcion'] ?? '',
            'fecha'       => $json['fecha'] ?? null,
            'prioridad'   => $json['prioridad'] ?? 'media',
            'tipo'        => $tipo,
            'origen_id'   => !empty($json['origen_id']) ? (int) $json['origen_id'] : null,
            'origen_tipo' => $json['origen_tipo'] ?? null,
            'seccion'     => $seccion,
            'usuario_id'  => $this->usuarioId($user),
        ];
        if (empty($datos['id'])) unset($datos['id']);

        if ($model->Guardar($datos)) {
            return $this->ok(['message' => $tipo === 'marcador' ? 'Marcador guardado.' : 'Recordatorio guardado.']);
        }
        $errors = $model->errors();
        return $this->fail_(!empty($errors) ? implode(', ', $errors) : 'Error al guardar.');
    }

    public function recordatorio_eliminar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new RecordatorioModel();
        $item = $model->ObtenerPorId((int) $id);
        if (!$item) return $this->fail_('No encontrado.');
        if ((int) $item['usuario_id'] !== $this->usuarioId($user)) return $this->fail_('No tienes permiso.');

        return $model->Eliminar((int) $id) ? $this->ok(['message' => 'Eliminado.']) : $this->fail_('Error al eliminar.');
    }

    public function recordatorio_completar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new RecordatorioModel();
        $item = $model->ObtenerPorId((int) $id);
        if (!$item) return $this->fail_('No encontrado.');
        if ((int) $item['usuario_id'] !== $this->usuarioId($user)) return $this->fail_('No tienes permiso.');

        $json = $this->request->getJSON(true) ?? [];
        $completado = ($json['completado'] ?? 0) ? 1 : 0;

        return $model->update((int) $id, ['completado' => $completado]) ? $this->ok() : $this->fail_('Error al actualizar.');
    }

    // marcadores usan el mismo modelo, filtrados por tipo=marcador
    public function marcadores_listar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $data = (new RecordatorioModel())->ObtenerTodosConOrigen('marcador', $this->usuarioId($user));
        return $this->ok(['data' => $data]);
    }

    public function marcadores_eliminar($id)
    {
        return $this->recordatorio_eliminar($id);
    }

    // =====================================================
    // BORRADORES (mis borradores + publicar)
    // =====================================================

    public function borradores_listar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        return $this->ok(['data' => (new BorradorModel())->ObtenerTodos()]);
    }

    public function borradores_obtener($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $data = (new BorradorModel())->ObtenerPorId((int) $id);
        if (!$data) return $this->fail_('No encontrado.', 404);
        return $this->ok(['data' => $data]);
    }

    public function borradores_guardar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['titulo'])) {
            return $this->fail_('El título es obligatorio.');
        }

        $model = new BorradorModel();
        $datos = [
            'titulo'     => $json['titulo'],
            'contenido'  => $json['contenido'] ?? '',
            'usuario_id' => $this->usuarioId($user),
        ];
        if (!empty($json['id'])) $datos['id'] = (int) $json['id'];

        if ($model->Guardar($datos)) {
            $id = $datos['id'] ?? $model->getInsertID();
            return $this->ok(['message' => 'Borrador guardado correctamente.', 'data' => ['id' => (int) $id]]);
        }
        return $this->fail_('Error al guardar el borrador.');
    }

    public function borradores_eliminar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $ok = (new BorradorModel())->Eliminar((int) $id);
        if ($ok) service('cache')->delete('ultimo_anuncio');
        return $ok ? $this->ok(['message' => 'Borrador eliminado.']) : $this->fail_('Error al eliminar.');
    }

    public function borradores_publicar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['id']) || empty($json['seccion'])) {
            return $this->fail_('Faltan datos para publicar.');
        }

        $model = new BorradorModel();
        $ok = $model->Publicar((int) $json['id'], $json['seccion'], 'multiple', null, !empty($json['anuncio']) ? 1 : 0);
        $model->GuardarDepartamentos((int) $json['id'], []);
        $model->GuardarUsuarios((int) $json['id'], []);

        if ($ok && !empty($json['anuncio'])) service('cache')->delete('ultimo_anuncio');

        return $ok ? $this->ok(['message' => 'Publicado correctamente.']) : $this->fail_('Error al publicar.');
    }

    // =====================================================
    // PUBLICACIONES (Noticias / Ideas / Manual) - lectura y engagement
    // =====================================================

    public function publicaciones_listar($seccion)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $usuarioId = $this->usuarioId($user);
        $db = \Config\Database::connect();
        $u = $db->table('admin_usuarios')->select('rol, id_departamento')->where('id', $usuarioId)->get()->getRow();
        $rol = $u->rol ?? 'admin';
        $departamentoId = $u->id_departamento ?? null;

        $model = new BorradorModel();
        $data = $model->ObtenerPublicados($seccion, $usuarioId, $departamentoId ?: null, $rol);
        $ids = array_column($data, 'id');

        $interaccion = new InteraccionModel();
        if (!empty($ids)) {
            $interaccion->MarcarVistos($ids, $usuarioId);
            $likesCounts = $interaccion->ContarLikesPorPublicaciones($ids);
            $vistosCounts = $interaccion->ContarVistosPorPublicaciones($ids);
            $meGusta = $interaccion->MeGustaPublicaciones($ids, $usuarioId);
        } else {
            $likesCounts = $vistosCounts = $meGusta = [];
        }
        $counts = empty($ids) ? [] : (new ComentarioModel())->ContarPorBorradores($ids);

        foreach ($data as &$d) {
            $d['comentarios_count'] = $counts[$d['id']] ?? 0;
            $d['likes_count']       = $likesCounts[$d['id']] ?? 0;
            $d['vistos_count']      = $vistosCounts[$d['id']] ?? 0;
            $d['me_gusta']          = !empty($meGusta[$d['id']]);
        }

        return $this->ok(['data' => $data]);
    }

    public function publicaciones_ver($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $usuarioId = $this->usuarioId($user);
        $db = \Config\Database::connect();
        $u = $db->table('admin_usuarios')->select('rol, id_departamento')->where('id', $usuarioId)->get()->getRow();
        $rol = $u->rol ?? 'admin';
        $departamentoId = $u->id_departamento ?? null;

        $publicacion = (new BorradorModel())->ObtenerPublicadoDetalle((int) $id, $usuarioId, $departamentoId ?: null, $rol);
        if (!$publicacion) return $this->fail_('Publicación no encontrada.', 404);

        $interaccion = new InteraccionModel();
        $interaccion->MarcarVisto((int) $id, $usuarioId);
        $publicacion['comentarios_count'] = (new ComentarioModel())->ContarPorBorradores([$id])[$id] ?? 0;
        $publicacion['likes_count']  = $interaccion->ContarLikesPorPublicaciones([$id])[$id] ?? 0;
        $publicacion['vistos_count'] = $interaccion->ContarVistosPorPublicaciones([$id])[$id] ?? 0;
        $publicacion['me_gusta']     = !empty($interaccion->MeGustaPublicaciones([$id], $usuarioId)[$id]);

        return $this->ok(['data' => $publicacion]);
    }

    public function publicaciones_toggle_like($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $usuarioId = $this->usuarioId($user);
        if (!(new BorradorModel())->ObtenerPorId((int) $id)) return $this->fail_('Publicación no encontrada.');

        $interaccion = new InteraccionModel();
        $interaccion->ToggleLikePublicacion((int) $id, $usuarioId);
        $total = $interaccion->ContarLikesPorPublicaciones([$id])[$id] ?? 0;
        $meGusta = $interaccion->MeGustaPublicaciones([$id], $usuarioId)[$id] ?? false;

        return $this->ok(['likes' => $total, 'me_gusta' => !empty($meGusta)]);
    }

    public function publicaciones_listar_comentarios($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new ComentarioModel();
        return $this->ok(['data' => $model->EnriquecerLikes($model->ObtenerPorBorrador((int) $id))]);
    }

    public function publicaciones_guardar_comentario()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['borrador_id']) || trim($json['comentario'] ?? '') === '') {
            return $this->fail_('Faltan datos.');
        }

        $ok = (new ComentarioModel())->Guardar([
            'borrador_id' => (int) $json['borrador_id'],
            'usuario_id'  => $this->usuarioId($user),
            'comentario'  => $json['comentario'],
        ]);

        return $ok ? $this->ok(['message' => 'Comentario agregado.']) : $this->fail_('Error al guardar.');
    }

    // =====================================================
    // PASES DE TURNO (Entregas)
    // =====================================================

    public function pases_turnos()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        return $this->ok(['data' => (new PaseTurnoModel())->Turnos(false)]);
    }

    public function pases_listar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $estado = $this->request->getGet('estado') ?? '';
        return $this->ok(['data' => (new PaseTurnoModel())->ListarPases($estado)]);
    }

    public function pases_obtener($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $pase = (new PaseTurnoModel())->ObtenerPase((int) $id);
        if (!$pase) return $this->fail_('Pase de turno no encontrado.');
        return $this->ok(['data' => $pase]);
    }

    public function pases_guardar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');
        if (!$this->esAdmin($user)) return $this->fail_('No tienes permisos para realizar esta acción.');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['de_turno_id']) || empty($json['a_turno_id']) || empty($json['fecha'])) {
            return $this->fail_('Debes indicar los turnos (de y para) y la fecha.');
        }

        $id = (new PaseTurnoModel())->GuardarPase([
            'titulo'      => trim($json['titulo'] ?? '') ?: null,
            'de_turno_id' => (int) $json['de_turno_id'],
            'a_turno_id'  => (int) $json['a_turno_id'],
            'fecha'       => $json['fecha'],
            'estado'      => 'abierto',
            'creado_por'  => $this->usuarioId($user),
        ]);

        return $this->ok(['message' => 'Pase de turno creado.', 'id' => $id]);
    }

    public function pases_cerrar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');
        if (!$this->esAdmin($user)) return $this->fail_('No tienes permisos para realizar esta acción.');

        $ok = (new PaseTurnoModel())->CerrarPase((int) $id, $this->usuarioId($user));
        return $ok ? $this->ok(['message' => 'Pase de turno cerrado.']) : $this->fail_('Error al cerrar.');
    }

    public function pases_reabrir($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');
        if (!$this->esAdmin($user)) return $this->fail_('No tienes permisos para realizar esta acción.');

        $ok = (new PaseTurnoModel())->ReabrirPase((int) $id);
        return $ok ? $this->ok(['message' => 'Pase de turno reabierto.']) : $this->fail_('Error al reabrir.');
    }

    public function pases_eliminar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');
        if (!$this->esAdmin($user)) return $this->fail_('No tienes permisos para realizar esta acción.');

        $ok = (new PaseTurnoModel())->EliminarPase((int) $id);
        return $ok ? $this->ok(['message' => 'Pase de turno eliminado.']) : $this->fail_('Error al eliminar.');
    }

    public function pases_puntos($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        return $this->ok(['data' => (new PaseTurnoModel())->ObtenerPuntos((int) $id)]);
    }

    public function pases_guardar_punto()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['pase_id']) || trim($json['contenido'] ?? '') === '') {
            return $this->fail_('El contenido del punto es obligatorio.');
        }

        $model = new PaseTurnoModel();
        if (!$model->ObtenerPase((int) $json['pase_id'])) return $this->fail_('Pase de turno no encontrado.');

        $puntoId = $model->GuardarPunto([
            'pase_id'    => (int) $json['pase_id'],
            'area_id'    => !empty($json['area_id']) ? (int) $json['area_id'] : null,
            'contenido'  => trim($json['contenido']),
            'creado_por' => $this->usuarioId($user),
        ], !empty($json['id']) ? (int) $json['id'] : null);

        return $this->ok(['message' => empty($json['id']) ? 'Punto agregado.' : 'Punto actualizado.', 'id' => $puntoId]);
    }

    public function pases_cambiar_estado_punto($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true) ?? [];
        $estado = $json['estado'] ?? '';
        if (!in_array($estado, ['pendiente', 'revisado', 'completado'], true)) {
            return $this->fail_('Estado inválido.');
        }

        $ok = (new PaseTurnoModel())->CambiarEstadoPunto((int) $id, $estado, $this->usuarioId($user));
        return $ok ? $this->ok(['message' => 'Estado actualizado.']) : $this->fail_('Error al actualizar.');
    }

    public function pases_eliminar_punto($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new PaseTurnoModel();
        $punto = $model->ObtenerPunto((int) $id);
        if (!$punto) return $this->fail_('Punto no encontrado.');

        $esAutor = ((int) $punto['creado_por']) === $this->usuarioId($user);
        if (!$this->esAdmin($user) && !$esAutor) return $this->fail_('No tienes permisos para realizar esta acción.');

        $ok = $model->EliminarPunto((int) $id);
        return $ok ? $this->ok(['message' => 'Punto eliminado.']) : $this->fail_('Error al eliminar.');
    }

    public function pases_listar_comentarios($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        return $this->ok(['data' => (new PaseTurnoModel())->ListarComentarios((int) $id)]);
    }

    public function pases_guardar_comentario()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['punto_id']) || trim($json['comentario'] ?? '') === '') {
            return $this->fail_('El comentario es obligatorio.');
        }

        $ok = (new PaseTurnoModel())->GuardarComentario((int) $json['punto_id'], $this->usuarioId($user), trim($json['comentario']), []);
        return $ok ? $this->ok(['message' => 'Comentario agregado.']) : $this->fail_('Error al guardar.');
    }

    // =====================================================
    // TAREAS
    // =====================================================

    public function tareas_listar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $usuarioId = $this->usuarioId($user);
        $soloMisTareas = !$this->esAdmin($user);

        $model = new TareaModel();
        $tareas = $model->ObtenerPorDepartamento($usuarioId, $soloMisTareas);

        return $this->ok(['data' => $tareas, 'total' => count($tareas)]);
    }

    public function tareas_obtener($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new TareaModel();
        $tarea = $model->ObtenerDetallada((int) $id);
        if (!$tarea) return $this->fail_('Tarea no encontrada.');

        $tarea['asignaciones'] = $model->ObtenerAsignaciones((int) $id);
        return $this->ok(['data' => $tarea]);
    }

    public function tareas_guardar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');
        if (!$this->esAdmin($user)) return $this->fail_('No tienes permiso para crear tareas.');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['titulo'])) return $this->fail_('El título es obligatorio.');

        $fechaLimite = $json['fecha_limite'] ?? null;
        if (!empty($fechaLimite)) $fechaLimite = str_replace('T', ' ', $fechaLimite) . ':00';

        $departamentos = !empty($json['departamentos']) ? array_map('intval', (array) $json['departamentos']) : [];
        $asignados     = !empty($json['asignados']) ? array_map('intval', (array) $json['asignados']) : [];

        $model = new TareaModel();
        $datos = [
            'titulo'            => $json['titulo'],
            'descripcion'       => $json['descripcion'] ?? '',
            'prioridad'         => $json['prioridad'] ?? 'media',
            'fecha_limite'      => $fechaLimite,
            'modalidad'         => $json['modalidad'] ?? 'single_completes_all',
            'departamento_id'   => !empty($departamentos) ? $departamentos[0] : null,
            'destinatario_tipo' => $json['destinatario_tipo'] ?? 'multiple',
            'destinatario_id'   => null,
            'created_by'        => $this->usuarioId($user),
            'publicado'         => !empty($json['publicado']) ? 1 : 0,
        ];
        if (!empty($json['id'])) $datos['id'] = $json['id'];

        if ($model->Guardar($datos)) {
            $tareaId = $json['id'] ?? $model->insertID();
            $model->GuardarDepartamentos($tareaId, $departamentos);
            $model->GuardarUsuarios($tareaId, $asignados);
            return $this->ok(['message' => empty($json['id']) ? 'Tarea creada.' : 'Tarea actualizada.']);
        }
        $errors = $model->errors();
        return $this->fail_(!empty($errors) ? implode(', ', $errors) : 'Error al guardar.');
    }

    public function tareas_eliminar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');
        if (!$this->esAdmin($user)) return $this->fail_('Sin permisos.');

        $ok = (new TareaModel())->Eliminar((int) $id);
        return $ok ? $this->ok(['message' => 'Tarea eliminada.']) : $this->fail_('Error al eliminar.');
    }

    public function tareas_completar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $usuarioId = $this->usuarioId($user);
        $model = new TareaModel();
        $tarea = $model->find((int) $id);
        if (!$tarea || !$tarea['publicado']) return $this->fail_('Tarea no disponible.');

        $resultado = $tarea['modalidad'] === 'single_completes_all'
            ? $model->CompletarSingle((int) $id, $usuarioId)
            : $model->CompletarAll((int) $id, $usuarioId);

        return $this->response->setJSON(array_merge(['status' => $resultado['success'] ?? true], $resultado));
    }

    public function tareas_descompletar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $usuarioId = $this->usuarioId($user);
        $model = new TareaModel();
        $tarea = $model->find((int) $id);
        if (!$tarea) return $this->fail_('Tarea no encontrada.');

        $resultado = $tarea['modalidad'] === 'single_completes_all'
            ? $model->DescompletarSingle((int) $id, $usuarioId)
            : $model->DescompletarAll((int) $id, $usuarioId);

        return $this->response->setJSON(array_merge(['status' => $resultado['success'] ?? true], $resultado));
    }

    public function tareas_listar_comentarios($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new ComentarioModel();
        return $this->ok(['data' => $model->EnriquecerLikes($model->ObtenerPorTarea((int) $id))]);
    }

    public function tareas_guardar_comentario()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['tarea_id']) || trim($json['comentario'] ?? '') === '') {
            return $this->fail_('Faltan datos.');
        }

        $ok = (new ComentarioModel())->Guardar([
            'tarea_id'   => (int) $json['tarea_id'],
            'usuario_id' => $this->usuarioId($user),
            'comentario' => $json['comentario'],
        ]);

        return $ok ? $this->ok(['message' => 'Comentario agregado.']) : $this->fail_('Error al guardar.');
    }

    // =====================================================
    // CALENDARIO
    // =====================================================

    public function calendario_listar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $inicio = $this->request->getGet('start') ?? date('Y-m-01');
        $fin    = $this->request->getGet('end') ?? date('Y-m-t');
        $usuarioId = $this->usuarioId($user);
        $esAdmin = $this->esAdmin($user);

        $departamentoId = null;
        if (!$esAdmin) {
            $db = \Config\Database::connect();
            $u = $db->table('admin_usuarios')->select('id_departamento')->where('id', $usuarioId)->get()->getRowArray();
            $departamentoId = $u['id_departamento'] ?? null;
        }

        $model = new EventoModel();
        $eventos = $model->ObtenerVisibles($usuarioId, $departamentoId, $esAdmin, $inicio, $fin);

        $data = array_map(function ($e) use ($model, $usuarioId, $esAdmin) {
            $esCreador = $e['usuario_id'] && (int) $e['usuario_id'] === $usuarioId;
            return [
                'id'          => (int) $e['id'],
                'title'       => $e['titulo'],
                'start'       => $e['fecha_inicio'],
                'end'         => $e['fecha_fin'],
                'color'       => $e['color'] ?? '#4669FA',
                'description' => $e['descripcion'] ?? '',
                'usuario_id'  => $e['usuario_id'] ? (int) $e['usuario_id'] : null,
                'es_creador'  => $esCreador,
                'puede_editar'=> $esAdmin || $esCreador,
                'creador'     => $model->ObtenerCreador((int) $e['usuario_id']),
                'invitados'   => $model->ObtenerInvitados((int) $e['id']),
            ];
        }, $eventos);

        return $this->ok(['data' => $data]);
    }

    public function calendario_guardar()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['titulo']) || empty($json['fecha_inicio'])) {
            return $this->fail_('El título y la fecha de inicio son obligatorios.');
        }

        $usuarioId = $this->usuarioId($user);
        $esAdmin = $this->esAdmin($user);
        $model = new EventoModel();

        if (!empty($json['id'])) {
            $existente = $model->ObtenerPorId((int) $json['id']);
            if (!$existente) return $this->fail_('Evento no encontrado.');
            $esCreador = $existente['usuario_id'] && (int) $existente['usuario_id'] === $usuarioId;
            if (!$esAdmin && !$esCreador) return $this->fail_('No tienes permisos para editar este evento.');
        }

        $datos = [
            'id'           => $json['id'] ?? null,
            'titulo'       => $json['titulo'],
            'descripcion'  => $json['descripcion'] ?? '',
            'fecha_inicio' => $json['fecha_inicio'],
            'fecha_fin'    => $json['fecha_fin'] ?? null,
            'color'        => $json['color'] ?? '#4669FA',
            'usuario_id'   => !empty($json['id']) ? null : $usuarioId,
        ];
        if (empty($datos['id'])) unset($datos['id']);
        if (empty($datos['fecha_fin'])) $datos['fecha_fin'] = null;
        if ($datos['usuario_id'] === null) unset($datos['usuario_id']);

        if (!$model->GuardarEvento($datos)) {
            $errors = $model->errors();
            return $this->fail_(!empty($errors) ? implode(', ', $errors) : 'Error al guardar el evento.');
        }

        $eventoId = $datos['id'] ?? $model->getInsertID();
        $model->GuardarInvitados((int) $eventoId, $json['departamentos_invitados'] ?? [], $json['usuarios_invitados'] ?? []);

        return $this->ok(['message' => 'Evento guardado correctamente.', 'data' => ['id' => (int) $eventoId]]);
    }

    public function calendario_eliminar($id)
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $model = new EventoModel();
        $evento = $model->ObtenerPorId((int) $id);
        if (!$evento) return $this->fail_('Evento no encontrado.');

        $usuarioId = $this->usuarioId($user);
        $esAdmin = $this->esAdmin($user);
        $esCreador = $evento['usuario_id'] && (int) $evento['usuario_id'] === $usuarioId;
        if (!$esAdmin && !$esCreador) return $this->fail_('No tienes permisos para eliminar este evento.');

        return $model->EliminarEvento((int) $id) ? $this->ok(['message' => 'Evento eliminado correctamente.']) : $this->fail_('Error al eliminar el evento.');
    }

    // =====================================================
    // UTILIDADES COMPARTIDAS
    // =====================================================

    public function app_departamentos()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        return $this->ok(['data' => (new DepartamentoModel())->ObtenerTodos()]);
    }

    public function app_usuarios()
    {
        $user = $this->validateToken();
        if (!$user) return $this->failUnauthorized('Token inválido');

        $db = \Config\Database::connect();
        $usuarios = $db->table('admin_usuarios')->where('activo', 1)->orderBy('nombre', 'ASC')->get()->getResultArray();
        return $this->ok(['data' => $usuarios]);
    }
}
