var BASE = BASE_URL + 'entregas/';

$(document).ready(function() {
    cargarPaseTurno();
});

function formatearFecha(fecha) {
    if (!fecha) return '';
    var d = new Date(fecha.replace(' ', 'T'));
    if (isNaN(d.getTime())) return fecha.slice(0, 10);
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = ('0' + (d.getMonth() + 1)).slice(-2);
    var yyyy = d.getFullYear();
    return dd + '/' + mm + '/' + yyyy;
}

function formatearFechaHora(fecha) {
    if (!fecha) return '';
    var d = new Date(fecha.replace(' ', 'T'));
    if (isNaN(d.getTime())) return '';
    return formatearFecha(fecha) + ' ' + ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
}

function escHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}

function cambiarTab(tab, btn) {
    $('.nav-tabs-lito button').removeClass('active');
    $(btn).addClass('active');
    if (tab === 'hoy') {
        $('#tabHoy').show();
        $('#tabAdmin').hide();
    } else {
        $('#tabHoy').hide();
        $('#tabAdmin').show();
        cargarTareasAdmin();
        cargarRegistros();
    }
}

function cargarPaseTurno() {
    var fecha = $('#fechaPase').val();
    if (!fecha) {
        fecha = new Date().toISOString().slice(0, 10);
        $('#fechaPase').val(fecha);
    }
    $('#fechaHoy').text('Turno del dia: ' + formatearFecha(fecha));

    var cont = $('#tareasHoy');
    cont.html('<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm"></div> Cargando...</div>');

    $.ajax({
        url: BASE + 'listar?fecha=' + fecha,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var hoy = new Date().toISOString().slice(0, 10);

            if (!data.tareas || data.tareas.length === 0) {
                cont.html('<div class="text-center py-5 text-muted"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No hay tareas publicadas para este dia.</p></div>');
                return;
            }

            var html = '';
            data.tareas.forEach(function(t) {
                var hechoPor = '';
                (t.hecho_por || []).forEach(function(h) {
                    hechoPor += '<span class="ent-hecho' + (h.mio ? ' mio' : '') + '"><i class="bi bi-check-circle-fill"></i> ' + escHtml(h.nombre) + ' · ' + formatearFechaHora(h.hora) + '</span>';
                });

                var boton;
                if (t.hecho_por_mi) {
                    boton = '<span class="badge bg-success"><i class="bi bi-check2-all"></i> Completado</span>';
                } else if (fecha === hoy) {
                    boton = '<button class="btn btn-primary-custom btn-sm" onclick="completarTarea(' + t.id + ', \'' + fecha + '\')"><i class="bi bi-check2"></i> Marcar realizada</button>';
                } else {
                    boton = '<span class="badge bg-secondary">No registrado</span>';
                }

                html += '<div class="entrega-card' + (t.hecho_por_mi ? ' completada' : '') + '">' +
                    '<div class="d-flex justify-content-between align-items-start gap-2">' +
                    '<div><div class="ent-titulo">' + escHtml(t.titulo) + '</div>' +
                    (t.descripcion ? '<div class="ent-desc">' + escHtml(t.descripcion) + '</div>' : '') +
                    '</div>' + boton + '</div>' +
                    '<div class="ent-meta"><i class="bi bi-arrow-repeat"></i> ' + (parseInt(t.repetir_diario) ? 'Diaria' : 'Unica') + '</div>' +
                    (hechoPor ? '<div class="ent-hechos">' + hechoPor + '</div>' : '') +
                    '</div>';
            });
            cont.html(html);
        },
        error: function() {
            cont.html('<div class="text-center py-5 text-danger">Error al cargar las tareas.</div>');
        }
    });
}

function completarTarea(id, fecha) {
    showLoading();
    $.ajax({
        url: BASE + 'completar/' + id + '?fecha=' + fecha,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                Swal.fire({ icon: 'success', title: 'Tarea registrada', text: response.message, timer: 1500, showConfirmButton: false });
                cargarPaseTurno();
            } else {
                Swal.fire('Error', response.message || 'No se pudo registrar.', 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion.', 'error');
        }
    });
}

function cargarTareasAdmin() {
    $.ajax({
        url: BASE + 'listarAdmin',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#tbodyEntregas');
            tbody.empty();
            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="6" class="text-center text-muted py-4">Sin tareas configuradas.</td></tr>');
                return;
            }
            data.forEach(function(t) {
                var estado = parseInt(t.publicado)
                    ? '<span class="badge-estado pub">Publicada</span>'
                    : '<span class="badge-estado despub">Oculta</span>';
                var jsonTarea = JSON.stringify(t).replace(/'/g, '&#39;');
                var fila = '<tr>' +
                    '<td><div class="fw-semibold">' + escHtml(t.titulo) + '</div>' +
                    (t.descripcion ? '<small class="text-muted">' + escHtml(t.descripcion) + '</small>' : '') + '</td>' +
                    '<td>' + (parseInt(t.repetir_diario) ? '<i class="bi bi-arrow-repeat"></i> Diaria' : 'Unica') + '</td>' +
                    '<td>' + formatearFecha(t.fecha_inicio) + '</td>' +
                    '<td>' + formatearFecha(t.fecha_fin) + '</td>' +
                    '<td>' + estado + '</td>' +
                    '<td class="text-center text-nowrap">' +
                    '<button class="btn btn-sm btn-outline-primary" title="Editar" onclick="editarTarea(' + t.id + ', \'' + jsonTarea + '\')"><i class="bi bi-pencil"></i></button> ' +
                    '<button class="btn btn-sm btn-outline-secondary" title="Publicar/Despublicar" onclick="togglePublicado(' + t.id + ')"><i class="bi bi-eye"></i></button> ' +
                    '<button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminarTarea(' + t.id + ', \'' + escHtml(t.titulo).replace(/'/g, '\\\'') + '\')"><i class="bi bi-trash"></i></button>' +
                    '</td></tr>';
                tbody.append(fila);
            });
        },
        error: function() {
            $('#tbodyEntregas').html('<tr><td colspan="6" class="text-center text-danger py-4">Error al cargar tareas.</td></tr>');
        }
    });
}

function nuevaTarea() {
    $('#tareaId').val('');
    $('#tareaTitulo').val('');
    $('#tareaDescripcion').val('');
    $('#tareaInicio').val(new Date().toISOString().slice(0, 10));
    $('#tareaFin').val('');
    $('#tareaRepetir').prop('checked', true);
    $('#tareaPublicado').prop('checked', true);
    $('#modalTareaTitulo').html('<i class="bi bi-plus-circle"></i> Nueva tarea');
    new bootstrap.Modal(document.getElementById('modalTarea')).show();
}

function editarTarea(id, jsonTarea) {
    var t;
    try {
        t = JSON.parse(jsonTarea.replace(/&#39;/g, "'"));
    } catch (e) {
        Swal.fire('Error', 'No se pudo cargar la tarea.', 'error');
        return;
    }
    $('#tareaId').val(t.id);
    $('#tareaTitulo').val(t.titulo || '');
    $('#tareaDescripcion').val(t.descripcion || '');
    $('#tareaInicio').val((t.fecha_inicio || '').slice(0, 10));
    $('#tareaFin').val(t.fecha_fin ? t.fecha_fin.slice(0, 10) : '');
    $('#tareaRepetir').prop('checked', !!t.repetir_diario);
    $('#tareaPublicado').prop('checked', !!t.publicado);
    $('#modalTareaTitulo').html('<i class="bi bi-pencil"></i> Editar tarea');
    new bootstrap.Modal(document.getElementById('modalTarea')).show();
}

function guardarTarea() {
    var payload = {
        id: $('#tareaId').val() || null,
        titulo: $('#tareaTitulo').val().trim(),
        descripcion: $('#tareaDescripcion').val().trim(),
        fecha_inicio: $('#tareaInicio').val(),
        fecha_fin: $('#tareaFin').val() || null,
        repetir_diario: $('#tareaRepetir').is(':checked') ? 1 : 0,
        publicado: $('#tareaPublicado').is(':checked') ? 1 : 0
    };

    if (!payload.titulo || !payload.fecha_inicio) {
        Swal.fire('Validacion', 'El titulo y la fecha de inicio son obligatorios.', 'warning');
        return;
    }

    showLoading();
    $.ajax({
        url: BASE + 'guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalTarea')).hide();
                Swal.fire({ icon: 'success', title: 'Guardado', text: response.message, timer: 1500, showConfirmButton: false });
                cargarTareasAdmin();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion.', 'error');
        }
    });
}

function togglePublicado(id) {
    showLoading();
    $.ajax({
        url: BASE + 'publicar/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            Swal.fire(response.success ? 'Exito' : 'Error', response.message, response.success ? 'success' : 'error');
            cargarTareasAdmin();
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion.', 'error');
        }
    });
}

function eliminarTarea(id, titulo) {
    Swal.fire({
        title: 'Eliminar tarea',
        text: '¿Seguro que deseas eliminar "' + titulo + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        showLoading();
        $.ajax({
            url: BASE + 'eliminar/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                Swal.fire(response.success ? 'Eliminado' : 'Error', response.message, response.success ? 'success' : 'error');
                cargarTareasAdmin();
            },
            error: function() {
                hideLoading();
                Swal.fire('Error', 'Error de conexion.', 'error');
            }
        });
    });
}

function cargarRegistros() {
    var params = [];
    var inicio = $('#filtroRegInicio').val();
    var fin = $('#filtroRegFin').val();
    if (inicio) params.push('inicio=' + inicio);
    if (fin) params.push('fin=' + fin);
    var url = BASE + 'registros' + (params.length ? '?' + params.join('&') : '');

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#tbodyRegistros');
            tbody.empty();
            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="5" class="text-center text-muted py-4">Sin registros en el rango seleccionado.</td></tr>');
                return;
            }
            data.forEach(function(r) {
                var fila = '<tr>' +
                    '<td>' + formatearFecha(r.fecha) + '</td>' +
                    '<td>' + formatearFechaHora(r.completado_at) + '</td>' +
                    '<td>' + escHtml(r.titulo) + '</td>' +
                    '<td><i class="bi bi-person-fill"></i> ' + escHtml(r.usuario_nombre || 'Desconocido') + '</td>' +
                    '<td class="text-center">' +
                    '<button class="btn btn-sm btn-outline-danger" title="Eliminar registro" onclick="eliminarRegistro(' + r.id + ')"><i class="bi bi-trash"></i></button>' +
                    '</td></tr>';
                tbody.append(fila);
            });
        },
        error: function() {
            $('#tbodyRegistros').html('<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar registros.</td></tr>');
        }
    });
}

function eliminarRegistro(id) {
    Swal.fire({
        title: 'Eliminar registro',
        text: '¿Eliminar este registro de tarea realizada?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        showLoading();
        $.ajax({
            url: BASE + 'eliminarRegistro/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                Swal.fire(response.success ? 'Eliminado' : 'Error', response.message, response.success ? 'success' : 'error');
                cargarRegistros();
            },
            error: function() {
                hideLoading();
                Swal.fire('Error', 'Error de conexion.', 'error');
            }
        });
    });
}
