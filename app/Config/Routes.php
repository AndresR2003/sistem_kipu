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
// RUTA PUBLICA - Vista del usuario (acceso por token)
// =====================================================
$routes->get('pago/(:segment)', 'Api::vistaPago/$1');

// =====================================================
// RUTAS PROTEGIDAS (requieren autenticacion)
// =====================================================
$routes->group('', ['filter' => 'auth'], function($routes) {

    // RUTAS PRINCIPAL - Redirigir al dashboard
    $routes->get('/', 'Dashboard::index');

    // RUTAS ADMIN - Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // RUTAS ADMIN - Usuarios
    $routes->get('usuarios', 'Usuarios::index');

    // RUTAS ADMIN - Pagos
    $routes->get('pagos', 'Pagos::index');
    $routes->get('historial/(:num)', 'Pagos::historial/$1');

    // API AJAX - Estadisticas y datos
    $routes->post('api/estadisticas', 'Api::estadisticas');
    $routes->post('api/listar-usuarios', 'Api::listarUsuarios');
    $routes->post('api/guardar-usuario', 'Api::guardarUsuario');
    $routes->post('api/eliminar-usuario/(:num)', 'Api::eliminarUsuario/$1');

    // API AJAX - Pagos
    $routes->post('api/guardar-pago', 'Api::guardarPago');
    $routes->post('api/aprobar-pago/(:num)', 'Api::aprobarPago/$1');
    $routes->post('api/rechazar-pago/(:num)', 'Api::rechazarPago/$1');
    $routes->post('api/eliminar-pago/(:num)', 'Api::eliminarPago/$1');
    $routes->post('api/listar-pagos', 'Api::listarPagos');

    // API AJAX - Comprobantes
    $routes->post('api/subir-comprobante', 'Api::subirComprobante');
    $routes->post('api/ver-comprobante/(:num)', 'Api::verComprobante/$1');

    // API AJAX - Historial y reportes
    $routes->post('api/historial-usuario/(:num)', 'Api::historialUsuario/$1');
    $routes->post('api/datos-usuario-token/(:segment)', 'Api::datosUsuarioToken/$1');

    // API AJAX - Exportar
    $routes->post('api/exportar-excel', 'Api::exportarExcel');
    $routes->post('api/exportar-pdf', 'Api::exportarPdf');

    // API AJAX - Notificaciones
    $routes->post('api/notificaciones', 'Api::notificaciones');

    // API AJAX - Filtros
    $routes->post('api/meses-registros', 'Api::mesesConRegistros');

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
    $routes->get('entregas', 'Entregas::index');
    $routes->get('entregas/listar', 'Entregas::listar');
    $routes->get('entregas/listarAdmin', 'Entregas::listarAdmin');
    $routes->get('entregas/destinatarios', 'Entregas::destinatarios');
    $routes->get('entregas/obtener/(:num)', 'Entregas::obtener/$1');
    $routes->get('entregas/registros', 'Entregas::registros');
    $routes->post('entregas/guardar', 'Entregas::guardar');
    $routes->post('entregas/eliminar/(:num)', 'Entregas::eliminar/$1');
    $routes->post('entregas/publicar/(:num)', 'Entregas::publicar/$1');
    $routes->post('entregas/despublicar/(:num)', 'Entregas::despublicar/$1');
    $routes->post('entregas/completar/(:num)', 'Entregas::completar/$1');
    $routes->post('entregas/eliminarRegistro/(:num)', 'Entregas::eliminarRegistro/$1');
    $routes->get('entregas/comentarios/(:num)', 'Entregas::listarComentarios/$1');
    $routes->post('entregas/comentario', 'Entregas::guardarComentario');

    $routes->get('noticias', 'Noticias::index');
    $routes->get('ideas', 'Ideas::index');
    $routes->get('manual', 'Manual::index');
    $routes->get('tareas', 'Tareas::index');
    $routes->get('calendario', 'Calendario::index');

    // API AJAX - Calendario / Eventos
    $routes->get('calendario/listar', 'Calendario::listar');
    $routes->post('calendario/guardar', 'Calendario::guardar');
    $routes->post('calendario/eliminar/(:num)', 'Calendario::eliminar/$1');

    $routes->get('reparaciones', 'Reparaciones::index');
    $routes->get('peticiones', 'Peticiones::index');
    $routes->get('configuracion', 'Configuracion::index');
    $routes->get('configuracion/obtener', 'Configuracion::obtener');
    $routes->post('configuracion/guardar', 'Configuracion::guardar');
    $routes->post('configuracion/subir-logo', 'Configuracion::subirLogo');
    $routes->get('soporte', 'Soporte::index');
});
