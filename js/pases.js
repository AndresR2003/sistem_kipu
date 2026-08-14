var BASE = BASE_URL + 'entregas/';
var filtroEstado = '';

function escHtml(texto) {
    return String(texto == null ? '' : texto)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function formatearFecha(fechaHora) {
    if (!fechaHora) return '';
    var f = new Date(fechaHora.replace(' ', 'T'));
    if (isNaN(f)) return fechaHora;
    return f.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }) +
        (fechaHora.indexOf('T') >= 0 || fechaHora.indexOf(' ') >= 0 && fechaHora.length > 10
            ? ' ' + f.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
            : '');
}

function toastExito(mensaje) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'success', title: 'Listo', text: mensaje, timer: 1800, showConfirmButton: false });
    }
}

// ─── Carga inicial ───

$(document).ready(function () {
    $('#paseFecha').val(new Date().toISOString().slice(0, 10));
    cargarPases();
    cargarAreas();
    cargarUsuarios();
    cargarTurnosSelects();
});

// ─── Listado de pases ───

function cambiarFiltroPase(btn) {
    $('#paseFiltros .chip-filtro').removeClass('active');
    $(btn).addClass('active');
    filtroEstado = $(btn).data('estado');
    cargarPases();
}

function cargarPases() {
    showLoading();
    $.get(BASE + 'listar', { estado: filtroEstado }, function (res) {
        hideLoading();
        var datos = (res && res.data) || [];
        var html = '';
        if (!datos.length) {
            html = '<div class="tabla-vacia">' +
                '<i class="bi bi-arrow-left-right"></i>' +
                '<p>No hay pases de turno' + (filtroEstado ? ' ' + filtroEstado + 's' : '') + '.</p>' +
                (esAdmin ? '<p class="small"><a href="#" onclick="abrirModalNuevoPase();return false;">Crear el primero</a></p>' : '') +
                '</div>';
        } else {
            datos.forEach(function (p) {
                var total = parseInt(p.total_puntos || 0);
                var avanzados = parseInt(p.puntos_avanzados || 0);
                var pct = total > 0 ? Math.round((avanzados / total) * 100) : 0;
                html += '<div class="pase-card" onclick="abrirDetalle(' + p.id + ')">' +
                    '<div class="pase-flecha"><i class="bi bi-arrow-left-right"></i></div>' +
                    '<div class="pase-info">' +
                        '<div class="pase-titulo">' +
                            (p.titulo ? escHtml(p.titulo) : 'Pase de turno') +
                            ' <span class="badge-estado ' + p.estado + '">' + (p.estado === 'abierto' ? 'Abierto' : 'Cerrado') + '</span>' +
                        '</div>' +
                        '<div class="pase-meta">' +
                            '<i class="bi bi-arrow-right"></i> De <b>' + escHtml(p.de_turno) + '</b> a <b>' + escHtml(p.a_turno) + '</b>' +
                            ' &middot; ' + formatearFecha(p.fecha) +
                            ' &middot; <i class="bi bi-person-fill"></i> ' + escHtml(p.creador_nombre) +
                            (total > 0 ? ' &middot; <span style="color:' + (p.puntos_pendientes > 0 ? '#f59e0b' : '#22c55e') + '">' + p.puntos_pendientes + ' pendiente(s)</span>' : '') +
                        '</div>' +
                    '</div>' +
                    '<div class="pase-progreso d-none d-sm-block">' +
                        '<div class="small" style="font-size:0.7rem;color:var(--text-muted);">' + avanzados + '/' + total + ' puntos</div>' +
                        '<div class="barra"><div style="width:' + pct + '%;"></div></div>' +
                    '</div>' +
                '</div>';
            });
        }
        $('#listaPases').html(html);
    });
}

// ─── Crear pase ───

function cargarTurnosSelects() {
    $.get(BASE + 'turnos', function (res) {
        var turnos = (res && res.data) || [];
        var opts = turnos.map(function (t) {
            return '<option value="' + t.id + '">' + escHtml(t.nombre) + '</option>';
        }).join('');
        $('#paseDeTurno').html(opts);
        $('#paseATurno').html(opts);
    });
}

function abrirModalNuevoPase() {
    $('#paseTitulo').val('');
    $('#paseFecha').val(new Date().toISOString().slice(0, 10));
    new bootstrap.Modal(document.getElementById('modalNuevoPase')).show();
}

function guardarPase() {
    var deTurno = $('#paseDeTurno').val();
    var aTurno = $('#paseATurno').val();
    var fecha = $('#paseFecha').val();

    if (!deTurno || !aTurno || !fecha) {
        Swal.fire('Faltan datos', 'Indica los turnos y la fecha.', 'warning');
        return;
    }
    if (deTurno === aTurno) {
        Swal.fire('Turnos iguales', 'El turno de origen y el de destino deben ser distintos.', 'warning');
        return;
    }

    showLoading();
    $.ajax({
        url: BASE + 'guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            titulo: $('#paseTitulo').val(),
            de_turno_id: deTurno,
            a_turno_id: aTurno,
            fecha: fecha
        }),
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuevoPase')).hide();
                toastExito(res.message);
                cargarPases();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

// ─── Detalle de pase ───

function abrirDetalle(id) {
    paseActualId = id;
    $('#paseListView').hide();
    $('#paseDetailView').show();
    recargarDetalle();
}

function volverALista() {
    $('#paseDetailView').hide();
    $('#paseListView').show();
    paseActualId = null;
    cargarPases();
}

function recargarDetalle() {
    if (!paseActualId) return;
    showLoading();
    $.when(
        $.get(BASE + 'obtener/' + paseActualId),
        $.get(BASE + 'puntos/' + paseActualId)
    ).done(function (rPase, rPuntos) {
        hideLoading();
        var pase = rPase[0].data;
        var puntos = rPuntos[0].data || [];

        $('#detTitulo').html('<i class="bi bi-arrow-right" style="color:var(--primary);"></i> De ' +
            escHtml(pase.de_turno) + ' a ' + escHtml(pase.a_turno) +
            ' <span class="badge-estado ' + pase.estado + '">' + (pase.estado === 'abierto' ? 'Abierto' : 'Cerrado') + '</span>');
        $('#detMeta').html(
            (pase.titulo ? '<b>' + escHtml(pase.titulo) + '</b> &middot; ' : '') +
            formatearFecha(pase.fecha) +
            ' &middot; Creado por <i class="bi bi-person-fill"></i> ' + escHtml(pase.creador_nombre) +
            (pase.cerrado_por ? ' &middot; Cerrado por ' + escHtml(pase.cerrado_por_nombre) + ' (' + formatearFecha(pase.cerrado_at) + ')' : '')
        );

        renderizarPuntos(puntos, pase.estado);
    });
}

function renderizarPuntos(puntos, estadoPase) {
    var grupos = {};
    puntos.forEach(function (pp) {
        var clave = pp.area_nombre || 'General';
        if (!grupos[clave]) grupos[clave] = [];
        grupos[clave].push(pp);
    });

    var html = '';
    var claves = Object.keys(grupos).sort(function (a, b) {
        if (a === 'General') return 1;
        if (b === 'General') return -1;
        return a.localeCompare(b);
    });

    if (!puntos.length) {
        html = '<div class="tabla-vacia">' +
            '<i class="bi bi-pin-angle"></i>' +
            '<p>Este pase aun no tiene puntos. Añade informacion o pendientes para el siguiente turno.</p>' +
            (esAdmin ? '<p class="small"><a href="#" onclick="abrirModalPunto(null);return false;">Añadir el primer punto</a></p>' : '') +
            '</div>';
    } else {
        claves.forEach(function (area) {
            var items = grupos[area];
            html += '<div class="area-grupo">' +
                '<div class="area-grupo-head">' +
                    '<span><i class="bi bi-grid-fill" style="color:var(--primary);"></i> ' + escHtml(area) + '</span>' +
                    '<span class="small" style="color:var(--text-muted);font-weight:400;">' + items.length + ' punto(s)</span>' +
                '</div>';
            items.forEach(function (pp) {
                html += renderPunto(pp, estadoPase);
            });
            html += '</div>';
        });
    }

    $('#detPuntos').html(html);
}

function renderPunto(pp, estadoPase) {
    var etiquetas = { pendiente: 'Pendiente', revisado: 'Revisado', completado: 'Completado' };
    var acciones = '';
    if (esAdmin) {
        acciones += '<button class="btn-sm-pase" onclick="editarPunto(' + pp.id + ')"><i class="bi bi-pencil"></i> Editar</button>';
    }
    acciones += '<button class="btn-sm-pase cyan" onclick="cambiarEstadoPunto(' + pp.id + ', \'revisado\')"><i class="bi bi-eye"></i> Revisado</button>';
    acciones += '<button class="btn-sm-pase green" onclick="cambiarEstadoPunto(' + pp.id + ', \'completado\')"><i class="bi bi-check-lg"></i> Completado</button>';
    acciones += '<button class="btn-sm-pase amber" onclick="cambiarEstadoPunto(' + pp.id + ', \'pendiente\')"><i class="bi bi-arrow-counterclockwise"></i> Pendiente</button>';
    if (esAdmin && !pp.tarea_id) {
        acciones += '<button class="btn-sm-pase blue" onclick="abrirModalTarea(' + pp.id + ')"><i class="bi bi-list-task"></i> Convertir en tarea</button>';
    }
    if (pp.tarea_id) {
        acciones += '<a class="vinculo-tarea" href="' + BASE_URL + 'tareas" target="_blank"><i class="bi bi-list-task"></i> Tarea vinculada</a>';
        if (esAdmin) {
            acciones += '<button class="btn-sm-pase red" onclick="desvincularTarea(' + pp.id + ')"><i class="bi bi-unlink"></i></button>';
        }
    }
    acciones += '<button class="btn-sm-pase" onclick="toggleComentarios(' + pp.id + ', this)"><i class="bi bi-chat-dots"></i> Comentarios</button>';
    if (esAdmin || pp.creado_por == USUARIO_ID) {
        acciones += '<button class="btn-sm-pase red" onclick="eliminarPunto(' + pp.id + ')"><i class="bi bi-trash"></i></button>';
    }

    return '<div class="punto-item ' + pp.estado + '">' +
        '<div class="punto-texto">' + escHtml(pp.contenido) + '</div>' +
        '<div class="punto-meta">' +
            '<span class="badge-punto ' + pp.estado + '">' + etiquetas[pp.estado] + '</span>' +
            '<span class="autor"><i class="bi bi-person-fill"></i> ' + escHtml(pp.creador_nombre) + '</span>' +
            '<span>' + formatearFecha(pp.created_at) + '</span>' +
            (pp.actualizado_por && pp.actualizado_nombre && pp.created_at !== pp.updated_at
                ? '<span class="autor"><i class="bi bi-pencil-fill"></i> Editado por ' + escHtml(pp.actualizado_nombre) + ' ' + formatearFecha(pp.updated_at) + '</span>'
                : '') +
        '</div>' +
        '<div class="punto-acciones">' + acciones + '</div>' +
        '<div class="comentarios-wrap" id="comentarios-' + pp.id + '" style="display:none;"></div>' +
    '</div>';
}

// ─── Puntos ───

function cargarAreas() {
    $.get(BASE + 'areas', function (res) {
        areasCache = (res && res.data) || [];
        var opts = '<option value="">General</option>';
        areasCache.forEach(function (a) {
            opts += '<option value="' + a.id + '">' + escHtml(a.descripcion) + '</option>';
        });
        $('#puntoArea').html(opts);
        $('#tareaDepartamentos').html(opts.replace('<option value="">General</option>', ''));
    });
}

function cargarUsuarios() {
    $.get(BASE + 'usuarios', function (res) {
        usuariosCache = (res && res.data) || [];
        var opts = usuariosCache.map(function (u) {
            return '<option value="' + u.id + '">' + escHtml(u.nombre) + '</option>';
        }).join('');
        $('#tareaAsignados').html(opts);
    });
}

function abrirModalPunto(id) {
    $('#puntoId').val(id || '');
    $('#puntoContenido').val('');
    if (id) {
        $('#puntoModalTitulo').html('<i class="bi bi-pencil-fill" style="color:var(--primary);"></i> Editar punto');
    } else {
        $('#puntoModalTitulo').html('<i class="bi bi-pin-fill" style="color:var(--primary);"></i> Nuevo punto');
    }
    new bootstrap.Modal(document.getElementById('modalPunto')).show();
}

function editarPunto(id) {
    abrirModalPunto(id);
}

function guardarPunto() {
    var contenido = $('#puntoContenido').val().trim();
    if (!contenido) {
        Swal.fire('Faltan datos', 'Escribe el contenido del punto.', 'warning');
        return;
    }

    showLoading();
    $.ajax({
        url: BASE + 'guardar-punto',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id: $('#puntoId').val() || null,
            pase_id: paseActualId,
            area_id: $('#puntoArea').val() || null,
            contenido: contenido
        }),
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalPunto')).hide();
                toastExito(res.message);
                recargarDetalle();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

function cambiarEstadoPunto(id, estado) {
    showLoading();
    $.ajax({
        url: BASE + 'cambiar-estado-punto/' + id,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ estado: estado }),
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                toastExito(res.message);
                recargarDetalle();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

function eliminarPunto(id) {
    Swal.fire({
        title: '¿Eliminar punto?',
        text: 'Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE + 'eliminar-punto/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    hideLoading();
                    if (res.success) {
                        toastExito(res.message);
                        recargarDetalle();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}

// ─── Comentarios por punto ───

function toggleComentarios(puntoId, btn) {
    var wrap = $('#comentarios-' + puntoId);
    if (wrap.is(':visible')) {
        wrap.hide();
        return;
    }
    wrap.show();
    wrap.html('<div class="small text-muted">Cargando...</div>');
    cargarComentarios(puntoId);
}

function cargarComentarios(puntoId) {
    $.get(BASE + 'comentarios/' + puntoId, function (res) {
        var comentarios = (res && res.data) || [];
        var html = '';
        if (comentarios.length) {
            comentarios.forEach(function (c) {
                html += '<div class="comentario-item">' +
                    '<span class="autor-c"><i class="bi bi-person-fill"></i> ' + escHtml(c.autor_nombre) + '</span>' +
                    '<span class="fecha-c">' + formatearFecha(c.created_at) + '</span>' +
                    '<div>' + escHtml(c.comentario) + '</div>' +
                    '</div>';
            });
        } else {
            html = '<div class="small text-muted">Sin comentarios todavia.</div>';
        }
        html += '<div class="comentario-input">' +
            '<input type="text" class="form-control form-control-sm" id="comentarioInput-' + puntoId + '" placeholder="Escribe un comentario...">' +
            '<button class="btn btn-primary-custom btn-sm" onclick="guardarComentario(' + puntoId + ')"><i class="bi bi-send-fill"></i></button>' +
            '</div>';
        $('#comentarios-' + puntoId).html(html);
    });
}

function guardarComentario(puntoId) {
    var texto = $('#comentarioInput-' + puntoId).val().trim();
    if (!texto) return;
    showLoading();
    $.ajax({
        url: BASE + 'comentario',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ punto_id: puntoId, comentario: texto }),
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                cargarComentarios(puntoId);
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

// ─── Convertir en tarea ───

function abrirModalTarea(puntoId) {
    $('#tareaPuntoId').val(puntoId);
    $('#tareaTitulo').val('');
    $('#tareaDescripcion').val('');
    $('#tareaPrioridad').val('media');
    $('#tareaFechaLimite').val('');
    $('#tareaModalidad').val('single_completes_all');
    $('#tareaDepartamentos').val([]).trigger('change');
    $('#tareaAsignados').val([]).trigger('change');
    $('#tareaPublicada').prop('checked', true);
    new bootstrap.Modal(document.getElementById('modalTareaPase')).show();
}

function convertirEnTarea() {
    var puntoId = $('#tareaPuntoId').val();
    var titulo = $('#tareaTitulo').val().trim();
    if (!titulo) {
        Swal.fire('Faltan datos', 'El titulo de la tarea es obligatorio.', 'warning');
        return;
    }

    showLoading();
    $.ajax({
        url: BASE + 'convertir-tarea/' + puntoId,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            titulo: titulo,
            descripcion: $('#tareaDescripcion').val(),
            prioridad: $('#tareaPrioridad').val(),
            fecha_limite: $('#tareaFechaLimite').val(),
            modalidad: $('#tareaModalidad').val(),
            departamentos: $('#tareaDepartamentos').val() || [],
            asignados: $('#tareaAsignados').val() || [],
            publicado: $('#tareaPublicada').is(':checked') ? 1 : 0
        }),
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalTareaPase')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Tarea creada',
                    text: res.message,
                    showConfirmButton: true,
                    confirmButtonText: 'Ver tareas'
                }).then(function () {
                    window.open(BASE_URL + 'tareas', '_blank');
                });
                recargarDetalle();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

function desvincularTarea(puntoId) {
    Swal.fire({
        title: '¿Desvincular tarea?',
        text: 'La tarea seguira existiendo en el modulo Tareas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, desvincular',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE + 'desvincular-tarea/' + puntoId,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    hideLoading();
                    if (res.success) {
                        toastExito(res.message);
                        recargarDetalle();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}

// ─── Cerrar / Reabrir / Eliminar pase ───

function toggleAccionPase() {
    var estado = $('#detTitulo').text();
    if (estado.indexOf('Cerrado') >= 0) {
        reabrirPase();
    } else {
        cerrarPase();
    }
}

function cerrarPase() {
    if (!paseActualId) return;
    Swal.fire({
        title: '¿Cerrar pase de turno?',
        text: 'Se marcara como cerrado y no podra recibir mas ediciones desde el listado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Si, cerrar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE + 'cerrar/' + paseActualId,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    hideLoading();
                    if (res.success) {
                        toastExito(res.message);
                        recargarDetalle();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}

function reabrirPase() {
    if (!paseActualId) return;
    showLoading();
    $.ajax({
        url: BASE + 'reabrir/' + paseActualId,
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                toastExito(res.message);
                recargarDetalle();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

function eliminarPase() {
    if (!paseActualId) return;
    Swal.fire({
        title: '¿Eliminar pase de turno?',
        text: 'Se eliminaran tambien todos sus puntos y comentarios.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE + 'eliminar/' + paseActualId,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    hideLoading();
                    if (res.success) {
                        toastExito(res.message);
                        volverALista();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}

// ─── Administrar turnos ───

function abrirModalTurnos() {
    cargarTurnos();
    new bootstrap.Modal(document.getElementById('modalTurnos')).show();
}

function cargarTurnos() {
    $.get(BASE + 'turnos', function (res) {
        var turnos = (res && res.data) || [];
        var html = '';
        if (!turnos.length) {
            html = '<div class="small text-muted">No hay turnos registrados.</div>';
        }
        turnos.forEach(function (t) {
            html += '<div class="d-flex align-items-center gap-2 py-2" style="border-bottom:1px solid var(--border);">' +
                '<div style="flex:1;">' +
                    '<div class="small fw-semibold" style="color:var(--text);">' + escHtml(t.nombre) +
                    (t.activo ? '' : ' <span class="badge-estado cerrado">Inactivo</span>') + '</div>' +
                    (t.descripcion ? '<div class="small text-muted">' + escHtml(t.descripcion) + '</div>' : '') +
                '</div>' +
                '<button class="btn-sm-pase blue" onclick="editarTurno(' + t.id + ')"><i class="bi bi-pencil"></i></button>' +
                '<button class="btn-sm-pase red" onclick="eliminarTurno(' + t.id + ')"><i class="bi bi-trash"></i></button>' +
            '</div>';
        });
        $('#listaTurnos').html(html);
    });
}

function editarTurno(id) {
    $.get(BASE + 'turnos', function (res) {
        var turnos = (res && res.data) || [];
        var t = turnos.find(function (x) { return parseInt(x.id) === parseInt(id); });
        if (!t) return;
        $('#turnoNombre').val(t.nombre);
        $('#turnoDescripcion').val(t.descripcion || '');
        $('#turnoOrden').val(t.orden);
        $('#turnoActivo').prop('checked', parseInt(t.activo) === 1);
        $('#turnoNombre').data('edit-id', id);
    });
}

function guardarTurno() {
    var nombre = $('#turnoNombre').val().trim();
    if (!nombre) {
        Swal.fire('Faltan datos', 'El nombre del turno es obligatorio.', 'warning');
        return;
    }
    var id = $('#turnoNombre').data('edit-id') || null;
    showLoading();
    $.ajax({
        url: BASE + 'guardar-turno',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id: id,
            nombre: nombre,
            descripcion: $('#turnoDescripcion').val(),
            orden: $('#turnoOrden').val(),
            activo: $('#turnoActivo').is(':checked') ? 1 : 0
        }),
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.success) {
                toastExito(res.message);
                $('#turnoNombre').val('').removeData('edit-id');
                $('#turnoDescripcion').val('');
                $('#turnoOrden').val('');
                $('#turnoActivo').prop('checked', true);
                cargarTurnos();
                cargarTurnosSelects();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}

function eliminarTurno(id) {
    Swal.fire({
        title: '¿Eliminar turno?',
        text: 'Los pases que lo usen se veran afectados.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE + 'eliminar-turno/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    hideLoading();
                    if (res.success) {
                        toastExito(res.message);
                        cargarTurnos();
                        cargarTurnosSelects();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}