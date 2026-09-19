<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =====================================================
// RUTAS DE LOGIN (sin autenticacion)
// =====================================================
$routes->get('login', 'Login::index');
$routes->post('login/autenticar', 'Login::authenticate');
$routes->get('logout', 'Login::logout');

// =====================================================
// RUTA PUBLICA - Landing page
// =====================================================
$routes->get('/', 'Landing::index');



// *************************************
// *************************************
// *************************************
// *************************************
// APi
// *************************************
// *************************************
// *************************************
// *************************************

//iniciar session - gestor campo
$routes->get('obtener_data_usuario', 'GeoApiControlller::get_data_usuario'); //✅
$routes->get('obtener_gestores_campo', 'GeoApiControlller::get_gestores_campo'); //✅

$routes->post('iniciar_session', 'GeoApiControlller::login');
$routes->post('save_fcm_token', 'GeoApiControlller::api_registrar_token_fcm3');
$routes->post('send_notification', 'GeoApiControlller::enviar_notificacion_fcm_v1');
$routes->post('actualizar_datos_personales', 'GeoApiControlller::update_datos_personales');

// Chat (app móvil) - JWT
$routes->get('chat_usuarios', 'GeoApiControlller::chat_usuarios');
$routes->get('chat_conversaciones', 'GeoApiControlller::chat_conversaciones');
$routes->get('chat_listar', 'GeoApiControlller::chat_listar');
$routes->post('chat_marcar_leidos', 'GeoApiControlller::chat_marcar_leidos');
$routes->post('chat_enviar', 'GeoApiControlller::chat_enviar');
$routes->get('chat_archivo/(:num)', 'GeoApiControlller::chat_archivo/$1');

// Recordatorio / Marcadores (app móvil) - JWT
$routes->get('recordatorio_listar', 'AppApiController::recordatorio_listar');
$routes->get('recordatorio_obtener/(:num)', 'AppApiController::recordatorio_obtener/$1');
$routes->post('recordatorio_guardar', 'AppApiController::recordatorio_guardar');
$routes->post('recordatorio_eliminar/(:num)', 'AppApiController::recordatorio_eliminar/$1');
$routes->post('recordatorio_completar/(:num)', 'AppApiController::recordatorio_completar/$1');
$routes->get('marcadores_listar', 'AppApiController::marcadores_listar');
$routes->post('marcadores_eliminar/(:num)', 'AppApiController::marcadores_eliminar/$1');

// Borradores (app móvil) - JWT
$routes->get('borradores_listar', 'AppApiController::borradores_listar');
$routes->get('borradores_obtener/(:num)', 'AppApiController::borradores_obtener/$1');
$routes->post('borradores_guardar', 'AppApiController::borradores_guardar');
$routes->post('borradores_eliminar/(:num)', 'AppApiController::borradores_eliminar/$1');
$routes->post('borradores_publicar', 'AppApiController::borradores_publicar');

// Publicaciones: Noticias / Ideas / Manual (app móvil) - JWT
$routes->get('publicaciones_listar/(:segment)', 'AppApiController::publicaciones_listar/$1');
$routes->get('publicaciones_ver/(:num)', 'AppApiController::publicaciones_ver/$1');
$routes->post('publicaciones_toggle_like/(:num)', 'AppApiController::publicaciones_toggle_like/$1');
$routes->get('publicaciones_listar_comentarios/(:num)', 'AppApiController::publicaciones_listar_comentarios/$1');
$routes->post('publicaciones_guardar_comentario', 'AppApiController::publicaciones_guardar_comentario');

// Pases de turno (app móvil) - JWT
$routes->get('pases_turnos', 'AppApiController::pases_turnos');
$routes->get('pases_listar', 'AppApiController::pases_listar');
$routes->get('pases_obtener/(:num)', 'AppApiController::pases_obtener/$1');
$routes->post('pases_guardar', 'AppApiController::pases_guardar');
$routes->post('pases_cerrar/(:num)', 'AppApiController::pases_cerrar/$1');
$routes->post('pases_reabrir/(:num)', 'AppApiController::pases_reabrir/$1');
$routes->post('pases_eliminar/(:num)', 'AppApiController::pases_eliminar/$1');
$routes->get('pases_puntos/(:num)', 'AppApiController::pases_puntos/$1');
$routes->post('pases_guardar_punto', 'AppApiController::pases_guardar_punto');
$routes->post('pases_cambiar_estado_punto/(:num)', 'AppApiController::pases_cambiar_estado_punto/$1');
$routes->post('pases_eliminar_punto/(:num)', 'AppApiController::pases_eliminar_punto/$1');
$routes->get('pases_listar_comentarios/(:num)', 'AppApiController::pases_listar_comentarios/$1');
$routes->post('pases_guardar_comentario', 'AppApiController::pases_guardar_comentario');

// Tareas (app móvil) - JWT
$routes->get('tareas_listar', 'AppApiController::tareas_listar');
$routes->get('tareas_obtener/(:num)', 'AppApiController::tareas_obtener/$1');
$routes->post('tareas_guardar', 'AppApiController::tareas_guardar');
$routes->post('tareas_eliminar/(:num)', 'AppApiController::tareas_eliminar/$1');
$routes->post('tareas_completar/(:num)', 'AppApiController::tareas_completar/$1');
$routes->post('tareas_descompletar/(:num)', 'AppApiController::tareas_descompletar/$1');
$routes->get('tareas_listar_comentarios/(:num)', 'AppApiController::tareas_listar_comentarios/$1');
$routes->post('tareas_guardar_comentario', 'AppApiController::tareas_guardar_comentario');

// Calendario (app móvil) - JWT
$routes->get('calendario_listar', 'AppApiController::calendario_listar');
$routes->post('calendario_guardar', 'AppApiController::calendario_guardar');
$routes->post('calendario_eliminar/(:num)', 'AppApiController::calendario_eliminar/$1');

// Utilidades compartidas (app móvil) - JWT
$routes->get('app_departamentos', 'AppApiController::app_departamentos');
$routes->get('app_usuarios', 'AppApiController::app_usuarios');










// =====================================================
// RUTAS PROTEGIDAS (requieren autenticacion)
// =====================================================
$routes->group('', ['filter' => 'auth'], function($routes) {

    // RUTAS ADMIN - Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // RUTAS ADMIN - Usuarios
    $routes->get('usuarios', 'Usuarios::index');

    // API AJAX - Estadisticas y datos
    $routes->post('api/estadisticas', 'Api::estadisticas');
    $routes->post('api/listar-usuarios', 'Api::listarUsuarios');
    $routes->post('api/guardar-usuario', 'Api::guardarUsuario');
    $routes->post('api/eliminar-usuario/(:num)', 'Api::eliminarUsuario/$1');

    // API AJAX - Notificaciones
    $routes->post('api/notificaciones', 'Api::notificaciones');

    // API AJAX - Adjuntos de comentarios
    $routes->post('comentarios/subir-archivo', 'Comentarios::subirArchivo');
    $routes->post('comentarios/toggle-like/(:num)', 'Comentarios::toggleLike/$1');
    $routes->post('comentarios/toggle-like-pase/(:num)', 'Comentarios::toggleLikePase/$1');

    // API AJAX - Chat grupal
    $routes->get('chat/usuarios', 'Chat::usuarios');
    $routes->get('chat/conversaciones', 'Chat::conversaciones');
    $routes->get('chat/listar', 'Chat::listar');
    $routes->post('chat/marcar-leidos', 'Chat::marcarLeidos');
    $routes->post('chat/enviar', 'Chat::enviar');
    $routes->get('chat/archivo/(:num)', 'Chat::archivo/$1');

    // =====================================================
    // SECCIONES DEL SIDEBAR
    // =====================================================
    $routes->get('recordatorio', 'Recordatorio::index');
    $routes->get('recordatorio/listar', 'Recordatorio::listar');
    $routes->get('recordatorio/obtener/(:num)', 'Recordatorio::obtener/$1');
    $routes->post('recordatorio/guardar', 'Recordatorio::guardar');
    $routes->post('recordatorio/eliminar/(:num)', 'Recordatorio::eliminar/$1');
    $routes->post('recordatorio/completar/(:num)', 'Recordatorio::completar/$1');
    $routes->get('marcadores', 'Marcadores::index');
    $routes->get('marcadores/listar', 'Marcadores::listar');
    $routes->post('marcadores/eliminar/(:num)', 'Marcadores::eliminar/$1');
    $routes->get('perfil', 'Perfil::index');
    $routes->get('perfil/obtener', 'Perfil::obtener');
    $routes->post('perfil/guardar', 'Perfil::guardar');
    $routes->post('perfil/subir-foto', 'Perfil::subirFoto');
    $routes->get('colaboradores', 'Colaboradores::index');
    $routes->get('colaboradores/listar', 'Colaboradores::listar');
    $routes->get('colaboradores/obtener/(:num)', 'Colaboradores::obtener/$1');
    $routes->post('colaboradores/guardar', 'Colaboradores::guardar');
    $routes->post('colaboradores/eliminar/(:num)', 'Colaboradores::eliminar/$1');
    $routes->post('colaboradores/subir-foto/(:num)', 'Colaboradores::subirFoto/$1');
    $routes->get('colaboradores/departamentos', 'Colaboradores::departamentos');
    $routes->get('borradores', 'Borradores::index');
    $routes->get('borradores/listar', 'Borradores::listar');
    $routes->get('borradores/obtener/(:num)', 'Borradores::obtener/$1');
    $routes->post('borradores/guardar', 'Borradores::guardar');
    $routes->post('borradores/eliminar/(:num)', 'Borradores::eliminar/$1');
    $routes->post('borradores/fijar/(:num)', 'Borradores::fijar/$1');
    $routes->get('borradores/listar-comentarios/(:num)', 'Borradores::listarComentarios/$1');
    $routes->post('borradores/guardar-comentario', 'Borradores::guardarComentario');
    $routes->post('borradores/publicar', 'Borradores::publicar');
    $routes->post('borradores/despublicar/(:num)', 'Borradores::despublicar/$1');
    $routes->post('borradores/completar/(:num)', 'Borradores::completar/$1');
    $routes->get('borradores/destinatarios', 'Borradores::destinatarios');
    $routes->get('borradores/listar-publicados/(:segment)', 'Borradores::listarPublicados/$1');
    $routes->get('borradores/anuncio', 'Borradores::anuncio');
    $routes->post('borradores/toggle-like/(:num)', 'Borradores::toggleLike/$1');
    $routes->get('borradores/likes/(:num)', 'Borradores::likes/$1');
    $routes->get('borradores/vistos/(:num)', 'Borradores::vistos/$1');
    $routes->get('entregas', 'Entregas::index');
    $routes->get('entregas/listar', 'Entregas::listar');
    $routes->get('entregas/turnos', 'Entregas::turnos');
    $routes->post('entregas/guardar-turno', 'Entregas::guardarTurno');
    $routes->post('entregas/eliminar-turno/(:num)', 'Entregas::eliminarTurno/$1');
    $routes->get('entregas/obtener/(:num)', 'Entregas::obtener/$1');
    $routes->post('entregas/guardar', 'Entregas::guardar');
    $routes->post('entregas/cerrar/(:num)', 'Entregas::cerrar/$1');
    $routes->post('entregas/reabrir/(:num)', 'Entregas::reabrir/$1');
    $routes->post('entregas/eliminar/(:num)', 'Entregas::eliminar/$1');
    $routes->get('entregas/puntos/(:num)', 'Entregas::puntos/$1');
    $routes->post('entregas/guardar-punto', 'Entregas::guardarPunto');
    $routes->post('entregas/cambiar-estado-punto/(:num)', 'Entregas::cambiarEstadoPunto/$1');
    $routes->post('entregas/eliminar-punto/(:num)', 'Entregas::eliminarPunto/$1');
    $routes->post('entregas/convertir-tarea/(:num)', 'Entregas::convertirEnTarea/$1');
    $routes->post('entregas/desvincular-tarea/(:num)', 'Entregas::desvincularTarea/$1');
    $routes->get('entregas/comentarios/(:num)', 'Entregas::listarComentarios/$1');
    $routes->post('entregas/comentario', 'Entregas::guardarComentario');
    $routes->get('entregas/areas', 'Entregas::areas');
    $routes->get('entregas/usuarios', 'Entregas::usuarios');

    $routes->get('noticias', 'Noticias::index');
    $routes->get('noticias/ver/(:num)', 'Noticias::ver/$1');
    $routes->get('ideas', 'Ideas::index');
    $routes->get('manual', 'Manual::index');
    $routes->get('tareas', 'Tareas::index');

    // API AJAX - Tareas
    $routes->post('tareas/listar', 'Tareas::listar');
    $routes->post('tareas/obtener/(:num)', 'Tareas::obtener/$1');
    $routes->post('tareas/guardar', 'Tareas::guardar');
    $routes->post('tareas/eliminar/(:num)', 'Tareas::eliminar/$1');
    $routes->post('tareas/publicar/(:num)', 'Tareas::publicar/$1');
    $routes->post('tareas/despublicar/(:num)', 'Tareas::despublicar/$1');
    $routes->post('tareas/completar/(:num)', 'Tareas::completar/$1');
    $routes->post('tareas/descompletar/(:num)', 'Tareas::descompletar/$1');
    $routes->post('tareas/asignar', 'Tareas::asignar');
    $routes->post('tareas/listar-asignaciones/(:num)', 'Tareas::listarAsignaciones/$1');
    $routes->post('tareas/listar-comentarios/(:num)', 'Tareas::listarComentarios/$1');
    $routes->post('tareas/guardar-comentario', 'Tareas::guardarComentario');
    $routes->post('tareas/departamentos', 'Tareas::departamentos');
    $routes->get('calendario', 'Calendario::index');

    // API AJAX - Calendario / Eventos
    $routes->get('calendario/listar', 'Calendario::listar');
    $routes->get('calendario/destinatarios', 'Calendario::destinatarios');
    $routes->post('calendario/guardar', 'Calendario::guardar');
    $routes->post('calendario/eliminar/(:num)', 'Calendario::eliminar/$1');

    $routes->get('reparaciones', 'Reparaciones::index');
    $routes->get('peticiones', 'Peticiones::index');
    $routes->get('configuracion', 'Configuracion::index');
    $routes->get('configuracion/obtener', 'Configuracion::obtener');
    $routes->post('configuracion/guardar', 'Configuracion::guardar');
    $routes->post('configuracion/subir-logo', 'Configuracion::subirLogo');
    $routes->get('soporte', 'Soporte::index');
    $routes->get('soporte/centro-ayuda', 'Soporte::centroAyuda');
    $routes->get('soporte/contactar', 'Soporte::contactar');
    $routes->get('soporte/reportar', 'Soporte::reportar');
    $routes->get('soporte/terminos', 'Soporte::terminos');
    $routes->get('soporte/privacidad', 'Soporte::privacidad');
    $routes->get('soporte/reclamaciones', 'Soporte::reclamaciones');
});
