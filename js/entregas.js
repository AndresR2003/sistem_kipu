var BASE = BASE_URL + 'entregas/';
var destinatariosCache = null;

$(document).ready(function() {
    cargarTareasAdmin();
    cargarRegistros();
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

function cargarTareasAdmin() {
    $.ajax({
        url: BASE + 'listarAdmin',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#tbodyEntregas');
            tbody.empty();
            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">Sin tareas configuradas.</td></tr>');
                return;
            }
            data.forEach(function(t) {
                var estado = parseInt(t.publicado)
                    ? '<span class="badge-estado pub">Publicada</span>'
                    : '<span class="badge-estado despub">Oculta</span>';
                var dest = t.destinatario_tipo === 'todos'
                    ? '<span class="badge-dest"><i class="bi bi-globe"></i> Todos</span>'
                    : (t.destinatario_tipo === 'usuarios'
                        ? '<span class="badge-dest"><i class="bi bi-person-fill"></i> ' + escHtml(t.destinatario_nombre || 'Usuario') + '</span>'
                        : '<span class="badge-dest"><i class="bi bi-people-fill"></i> ' + escHtml(t.destinatario_nombre || 'Departamento') + '</span>');
                var fila = '<tr>' +
                    '<td><div class="fw-semibold">' + escHtml(t.titulo) + '</div>' +
                    (t.descripcion ? '<small class="text-muted">' + escHtml(t.descripcion) + '</small>' : '') + '</td>' +
                    '<td>' + (parseInt(t.repetir_diario) ? '<i class="bi bi-arrow-repeat"></i> Diaria' : 'Unica') + '</td>' +
                    '<td>' + formatearFecha(t.fecha_inicio) + '</td>' +
                    '<td>' + formatearFecha(t.fecha_fin) + '</td>' +
                    '<td>' + dest + '</td>' +
                    '<td>' + estado + '</td>' +
                    '<td class="text-center text-nowrap">' +
                    '<button class="btn btn-sm btn-outline-primary" title="Editar" onclick="editarTarea(' + t.id + ')"><i class="bi bi-pencil"></i></button> ' +
                    '<button class="btn btn-sm btn-outline-secondary" title="Publicar/Despublicar" onclick="gestionarPublicacion(' + t.id + ')"><i class="bi bi-eye"></i></button> ' +
                    '<button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminarTarea(' + t.id + ', \'' + escHtml(t.titulo).replace(/'/g, '\\\'') + '\')"><i class="bi bi-trash"></i></button>' +
                    '</td></tr>';
                tbody.append(fila);
            });
        },
        error: function() {
            $('#tbodyEntregas').html('<tr><td colspan="7" class="text-center text-danger py-4">Error al cargar tareas.</td></tr>');
        }
    });
}

function toggleTareaDestinatario() {
    var tipo = $('#tareaTipo').val();
    if (tipo === 'todos') {
        $('#tareaDestinatarioWrap').hide();
        return;
    }
    $('#tareaDestinatarioWrap').show();

    if (destinatariosCache) {
        llenarTareaDestinatarios(tipo);
        return;
    }

    $.ajax({
        url: BASE_URL + 'borradores/destinatarios',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            destinatariosCache = data;
            llenarTareaDestinatarios(tipo);
        }
    });
}

function llenarTareaDestinatarios(tipo) {
    var sel = $('#tareaDestinatario');
    sel.empty();
    if (tipo === 'usuarios' && destinatariosCache.usuarios) {
        destinatariosCache.usuarios.forEach(function(u) {
            sel.append('<option value="' + u.id + '">' + escHtml(u.nombre) + '</option>');
        });
    } else if (tipo === 'departamento' && destinatariosCache.departamentos) {
        destinatariosCache.departamentos.forEach(function(d) {
            sel.append('<option value="' + d.id + '">' + escHtml(d.descripcion) + '</option>');
        });
    }
}

function nuevaTarea() {
    $('#tareaId').val('');
    $('#tareaTitulo').val('');
    $('#tareaDescripcion').val('');
    $('#tareaInicio').val(new Date().toISOString().slice(0, 10));
    $('#tareaFin').val('');
    $('#tareaTipo').val('todos');
    $('#tareaDestinatarioWrap').hide();
    $('#tareaRepetir').prop('checked', true);
    $('#tareaPublicado').prop('checked', true);
    $('#modalTareaTitulo').html('<i class="bi bi-plus-circle"></i> Nueva tarea');
    new bootstrap.Modal(document.getElementById('modalTarea')).show();
}

function editarTarea(id) {
    showLoading();
    $.ajax({
        url: BASE + 'obtener/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(t) {
            hideLoading();
            $('#tareaId').val(t.id);
            $('#tareaTitulo').val(t.titulo || '');
            $('#tareaDescripcion').val(t.descripcion || '');
            $('#tareaInicio').val((t.fecha_inicio || '').slice(0, 10));
            $('#tareaFin').val(t.fecha_fin ? t.fecha_fin.slice(0, 10) : '');
            $('#tareaTipo').val(t.destinatario_tipo || 'todos');
            toggleTareaDestinatario();
            if (t.destinatario_id) {
                $('#tareaDestinatario').val(String(t.destinatario_id));
            }
            $('#tareaRepetir').prop('checked', !!t.repetir_diario);
            $('#tareaPublicado').prop('checked', !!t.publicado);
            $('#modalTareaTitulo').html('<i class="bi bi-pencil"></i> Editar tarea');
            new bootstrap.Modal(document.getElementById('modalTarea')).show();
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'No se pudo cargar la tarea.', 'error');
        }
    });
}

function guardarTarea() {
    var payload = {
        id: $('#tareaId').val() || null,
        titulo: $('#tareaTitulo').val().trim(),
        descripcion: $('#tareaDescripcion').val().trim(),
        fecha_inicio: $('#tareaInicio').val(),
        fecha_fin: $('#tareaFin').val() || null,
        repetir_diario: $('#tareaRepetir').is(':checked') ? 1 : 0,
        publicado: $('#tareaPublicado').is(':checked') ? 1 : 0,
        destinatario_tipo: $('#tareaTipo').val(),
        destinatario_id: $('#tareaTipo').val() !== 'todos' ? parseInt($('#tareaDestinatario').val()) : null
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

function gestionarPublicacion(id) {
    showLoading();
    $.ajax({
        url: BASE + 'obtener/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(t) {
            hideLoading();
            if (parseInt(t.publicado)) {
                Swal.fire({
                    title: 'Despublicar tarea',
                    text: '¿Retirar la tarea "' + t.titulo + '" de la seccion Tareas?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si, despublicar',
                    cancelButtonText: 'Cancelar'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        despublicarTarea(id);
                    }
                });
            } else {
                abrirModalPublicar(t);
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'No se pudo cargar la tarea.', 'error');
        }
    });
}

function abrirModalPublicar(t) {
    $('#pubTareaId').val(t.id);
    $('#pubTipo').val(t.destinatario_tipo || 'todos');
    togglePubDestinatario();
    if (t.destinatario_id) {
        $('#pubDestinatario').val(String(t.destinatario_id));
    }
    new bootstrap.Modal(document.getElementById('modalPublicar')).show();
}

function togglePubDestinatario() {
    var tipo = $('#pubTipo').val();
    if (tipo === 'todos') {
        $('#pubDestinatarioWrap').hide();
        return;
    }
    $('#pubDestinatarioWrap').show();

    if (destinatariosCache) {
        llenarPubDestinatarios(tipo);
        return;
    }

    $.ajax({
        url: BASE_URL + 'borradores/destinatarios',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            destinatariosCache = data;
            llenarPubDestinatarios(tipo);
        }
    });
}

function llenarPubDestinatarios(tipo) {
    var sel = $('#pubDestinatario');
    sel.empty();
    if (tipo === 'usuarios' && destinatariosCache.usuarios) {
        destinatariosCache.usuarios.forEach(function(u) {
            sel.append('<option value="' + u.id + '">' + escHtml(u.nombre) + '</option>');
        });
    } else if (tipo === 'departamento' && destinatariosCache.departamentos) {
        destinatariosCache.departamentos.forEach(function(d) {
            sel.append('<option value="' + d.id + '">' + escHtml(d.descripcion) + '</option>');
        });
    }
}

function confirmarPublicar() {
    var id = $('#pubTareaId').val();
    var tipo = $('#pubTipo').val();
    var destId = tipo !== 'todos' ? parseInt($('#pubDestinatario').val()) : null;

    showLoading();
    $.ajax({
        url: BASE + 'publicar/' + id,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            destinatario_tipo: tipo,
            destinatario_id: destId
        }),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalPublicar')).hide();
                Swal.fire({ icon: 'success', title: 'Publicada', text: 'La tarea se publico en Tareas.', timer: 1500, showConfirmButton: false });
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

function despublicarTarea(id) {
    showLoading();
    $.ajax({
        url: BASE + 'despublicar/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            Swal.fire(response.success ? 'Despublicada' : 'Error', response.message, response.success ? 'success' : 'error');
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
