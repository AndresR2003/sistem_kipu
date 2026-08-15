/**
 * Calendario - FullCalendar CRUD
 * Gestiona la inicializacion del calendario y las operaciones AJAX de eventos
 */

var calendar = null;
var calendarEl = document.getElementById('calendar');
var invitadosCache = null;

// Inicializar tooltips de color presets
$(document).ready(function() {
    inicializarColorPicker();
    inicializarFullCalendar();
});

// =====================================================
// INICIALIZAR COLOR PICKER
// =====================================================

function inicializarColorPicker() {
    // Al hacer clic en un preset, actualizar el color picker
    $(document).on('click', '.color-preset', function() {
        var color = $(this).data('color');
        $('#eventoColor').val(color);
        $('#eventoColorTexto').text(color);
        $('.color-preset').removeClass('active');
        $(this).addClass('active');
    });

    // Sincronizar texto con el picker
    $('#eventoColor').on('input', function() {
        var color = $(this).val();
        $('#eventoColorTexto').text(color);
        $('.color-preset').removeClass('active');
        $('.color-preset[data-color="' + color + '"]').addClass('active');
    });
}

// =====================================================
// INICIALIZAR FULLCALENDAR
// =====================================================

function inicializarFullCalendar() {
    if (!calendarEl) return;

    calendar = new FullCalendar.Calendar(calendarEl, {
        // Opciones principales
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },

        // Idioma espanol
        locale: 'es',
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Dia',
            list: 'Lista'
        },

        // Comportamiento
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekNumbers: false,
        navLinks: true,
        editable: false,
        eventDurationEditable: false,
        eventStartEditable: false,

        // Eventos desde API
        events: {
            url: BASE_URL + '/calendario/listar',
            method: 'GET',
            failure: function() {
                Swal.fire('Error', 'No se pudieron cargar los eventos.', 'error');
            }
        },

        // Al hacer clic en una fecha vacia -> crear evento
        select: function(info) {
            abrirModalNuevoDesdeFecha(info.startStr, info.endStr);
        },

        // Al hacer clic en un evento -> editar
        eventClick: function(info) {
            abrirModalEditar(info.event);
        },

        // Personalizar apariencia de eventos
        eventDidMount: function(info) {
            // Tooltip con descripcion
            if (info.event.extendedProps.description) {
                info.el.title = info.event.extendedProps.description;
            }
        },

        // Cargar mas eventos al navegar
        datesSet: function() {
            // Podria mostrar un indicador de carga si se desea
        },

        loading: function(isLoading) {
            if (isLoading) {
                showLoading();
            } else {
                hideLoading();
            }
        }
    });

    calendar.render();
}

// =====================================================
// REFRESCAR CALENDARIO
// =====================================================

function recargarEventos() {
    if (calendar) {
        calendar.refetchEvents();
    }
}

// =====================================================
// ABRIR MODAL - NUEVO EVENTO
// =====================================================

// =====================================================
// INVITADOS (departamentos y usuarios)
// =====================================================

function cargarInvitados(seleccionados) {
    if (!invitadosCache) {
        $.ajax({
            url: BASE_URL + '/calendario/destinatarios',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                invitadosCache = data;
                cargarInvitados(seleccionados);
            }
        });
        return;
    }

    var selDept = seleccionados ? (seleccionados.departamentos || []) : [];
    var selUsr = seleccionados ? (seleccionados.usuarios || []) : [];

    var dep = $('#invitadosDepartamentos');
    dep.empty();
    (invitadosCache.departamentos || []).forEach(function(d) {
        var checked = selDept.some(function(s) { return String(s.id) === String(d.id); }) ? ' checked' : '';
        dep.append(
            '<label class="pub-check"><input type="checkbox" class="inv-dept" value="' + d.id + '"' + checked + '> ' + escHtml(d.descripcion) + '</label>'
        );
    });

    var usr = $('#invitadosUsuarios');
    usr.empty();
    (invitadosCache.usuarios || []).forEach(function(u) {
        var checked = selUsr.some(function(s) { return String(s.id) === String(u.id); }) ? ' checked' : '';
        usr.append(
            '<label class="pub-check"><input type="checkbox" class="inv-usr" value="' + u.id + '"' + checked + '> ' + escHtml(u.nombre) + '</label>'
        );
    });
}

function abrirModalNuevo() {
    $('#formEvento')[0].reset();
    $('#eventoId').val('');
    $('#modalEventoTitulo').html('<i class="bi bi-calendar-plus"></i> Nuevo Evento');
    $('#btnGuardarEvento').html('<i class="bi bi-check-lg"></i> Guardar Evento').show();
    $('#formEvento').find('input, textarea').prop('disabled', false);
    $('.invitados-wrap').css('opacity', '');
    $('.invitados-wrap .pub-check input').prop('disabled', false);
    $('#infoEvento').remove();

    // Resetear color al default
    $('#eventoColor').val('#4669FA');
    $('#eventoColorTexto').text('#4669FA');
    $('.color-preset').removeClass('active');
    $('.color-preset[data-color="#4669FA"]').addClass('active');

    // Fecha por defecto: hoy a las 00:00
    var ahora = new Date();
    var fechaLocal = formatearFechaLocal(ahora);
    $('#eventoFechaInicio').val(fechaLocal);
    $('#eventoFechaFin').val('');

    // Ocultar boton de eliminar si existe
    if ($('#btnEliminarEvento').length) {
        $('#btnEliminarEvento').remove();
    }

    cargarInvitados(null);

    $('#modalEvento').modal('show');
}

function abrirModalNuevoDesdeFecha(fechaInicio, fechaFin) {
    $('#formEvento')[0].reset();
    $('#eventoId').val('');
    $('#modalEventoTitulo').html('<i class="bi bi-calendar-plus"></i> Nuevo Evento');
    $('#btnGuardarEvento').html('<i class="bi bi-check-lg"></i> Guardar Evento').show();
    $('#formEvento').find('input, textarea').prop('disabled', false);
    $('.invitados-wrap').css('opacity', '');
    $('.invitados-wrap .pub-check input').prop('disabled', false);
    $('#infoEvento').remove();

    // Convertir a formato datetime-local
    var startLocal = fechaInicio.slice(0, 16);
    var endLocal = fechaFin ? fechaFin.slice(0, 16) : '';

    $('#eventoFechaInicio').val(startLocal);
    $('#eventoFechaFin').val(endLocal);

    // Resetear color
    $('#eventoColor').val('#4669FA');
    $('#eventoColorTexto').text('#4669FA');
    $('.color-preset').removeClass('active');
    $('.color-preset[data-color="#4669FA"]').addClass('active');

    // Ocultar boton de eliminar si existe
    if ($('#btnEliminarEvento').length) {
        $('#btnEliminarEvento').remove();
    }

    cargarInvitados(null);

    $('#modalEvento').modal('show');
}

// =====================================================
// ABRIR MODAL - EDITAR EVENTO
// =====================================================

function abrirModalEditar(event) {
    var eventId = event.id;
    var titulo = event.title;
    var descripcion = event.extendedProps.description || '';
    var start = event.startStr;
    var end = event.endStr || '';
    var color = event.backgroundColor || '#4669FA';
    var puedeEditar = event.extendedProps.puede_editar === true;
    var creador = event.extendedProps.creador || null;
    var creadoEn = event.extendedProps.created_at || '';
    var invitados = event.extendedProps.invitados || null;

    $('#eventoId').val(eventId);
    $('#eventoTitulo').val(titulo);
    $('#eventoDescripcion').val(descripcion);
    $('#eventoFechaInicio').val(start ? start.slice(0, 16) : '');
    $('#eventoFechaFin').val(end ? end.slice(0, 16) : '');

    // Color
    $('#eventoColor').val(color);
    $('#eventoColorTexto').text(color);
    $('.color-preset').removeClass('active');
    $('.color-preset[data-color="' + color + '"]').addClass('active');

    // Quitar boton de eliminar previo
    if ($('#btnEliminarEvento').length) {
        $('#btnEliminarEvento').remove();
    }

    if (puedeEditar) {
        // Modo edicion: campos habilitados, boton guardar visible
        $('#modalEventoTitulo').html('<i class="bi bi-pencil-fill"></i> Editar Evento');
        $('#btnGuardarEvento').html('<i class="bi bi-check-lg"></i> Actualizar Evento').show();
        $('#formEvento').find('input, textarea').prop('disabled', false);
        $('.invitados-wrap').css('opacity', '');
        $('.invitados-wrap .pub-check input').prop('disabled', false);

        var btnEliminar = '<button type="button" class="btn btn-danger-custom" id="btnEliminarEvento" onclick="eliminarEvento(' + eventId + ')">';
        btnEliminar += '<i class="bi bi-trash"></i> Eliminar';
        btnEliminar += '</button>';
        $('#modalEvento .modal-footer').prepend(btnEliminar);
    } else {
        // Modo detalle: solo lectura
        $('#modalEventoTitulo').html('<i class="bi bi-calendar3"></i> Detalle del Evento');
        $('#btnGuardarEvento').hide();
        $('#formEvento').find('input, textarea').prop('disabled', true);
        $('.invitados-wrap').css('opacity', '0.7');
        $('.invitados-wrap .pub-check input').prop('disabled', true);
    }

    cargarInvitados(invitados);
    mostrarInfoEvento(creador, creadoEn);

    $('#modalEvento').modal('show');
}

// =====================================================
// INFO DEL CREADOR EN EL MODAL
// =====================================================

function mostrarInfoEvento(creador, creadoEn) {
    $('#infoEvento').remove();

    var html = '<div class="evento-info-creador" id="infoEvento">';

    if (creador) {
        var nombre = escHtml(creador.nombre || 'Usuario');
        var rol = escHtml(rolLegible(creador.rol || ''));
        var base = BASE_URL.charAt(BASE_URL.length - 1) === '/' ? BASE_URL : BASE_URL + '/';
        var avatar = '';
        if (creador.foto) {
            var foto = creador.foto.indexOf('http') === 0 ? creador.foto : base + creador.foto;
            avatar = '<img src="' + foto + '" alt="" onerror="this.outerHTML=\'<span>\' + \'' + (creador.nombre ? escHtml(creador.nombre.charAt(0).toUpperCase()) : 'A') + '\' + \'</span>\';">';
        } else {
            avatar = '<span>' + (creador.nombre ? escHtml(creador.nombre.charAt(0).toUpperCase()) : 'A') + '</span>';
        }
        html += '<div class="evento-info-creador-row">' +
                '<div class="evento-info-avatar">' + avatar + '</div>' +
                '<div>' +
                '<div class="evento-info-nombre">' + nombre + (rol ? ' &bull; ' + rol : '') + '</div>' +
                '<div class="evento-info-creado">Creado el ' + (creadoEn ? escHtml(formatearFechaHora(creadoEn)) : '') + '</div>' +
                '</div>' +
                '</div>';
    }

    html += '</div>';
    $('#eventoDescripcion').closest('.mb-3').before(html);
}

function rolLegible(rol) {
    var map = { 'superadmin': 'Administrador', 'admin': 'Administrador', 'empleado': 'Empleado', 'soporte': 'Soporte', 'vendedor': 'Vendedor', 'tecnico': 'Tecnico' };
    return map[rol] || '';
}

function formatearFechaHora(iso) {
    var d = new Date(iso);
    if (isNaN(d.getTime())) return '';
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = ('0' + (d.getMonth() + 1)).slice(-2);
    var yy = d.getFullYear();
    var hh = ('0' + d.getHours()).slice(-2);
    var mi = ('0' + d.getMinutes()).slice(-2);
    return dd + '/' + mm + '/' + yy + ' a las ' + hh + ':' + mi;
}

// =====================================================
// GUARDAR EVENTO (AJAX)
// =====================================================

$('#formEvento').on('submit', function(e) {
    e.preventDefault();

    var id = $('#eventoId').val();
    var titulo = $('#eventoTitulo').val().trim();
    var descripcion = $('#eventoDescripcion').val().trim();
    var fechaInicio = $('#eventoFechaInicio').val();
    var fechaFin = $('#eventoFechaFin').val();
    var color = $('#eventoColor').val();

    // Validaciones basicas
    if (!titulo) {
        Swal.fire('Campo requerido', 'El titulo del evento es obligatorio.', 'warning');
        return;
    }
    if (!fechaInicio) {
        Swal.fire('Campo requerido', 'La fecha de inicio es obligatoria.', 'warning');
        return;
    }

    var departamentosInvitados = [];
    $('.inv-dept:checked').each(function() {
        departamentosInvitados.push(parseInt($(this).val()));
    });
    var usuariosInvitados = [];
    $('.inv-usr:checked').each(function() {
        usuariosInvitados.push(parseInt($(this).val()));
    });

    var datos = {
        id: id ? parseInt(id) : null,
        titulo: titulo,
        descripcion: descripcion,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin || null,
        color: color,
        departamentos_invitados: departamentosInvitados,
        usuarios_invitados: usuariosInvitados
    };

    showLoading();

    $.ajax({
        url: BASE_URL + '/calendario/guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                $('#modalEvento').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Evento guardado',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                recargarEventos();
            } else {
                Swal.fire('Error', response.message || 'Error al guardar el evento.', 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var msg = 'Error de conexion con el servidor.';
            try {
                var resp = JSON.parse(xhr.responseText);
                if (resp.message) msg = resp.message;
            } catch(e) {}
            Swal.fire('Error', msg, 'error');
        }
    });
});

// =====================================================
// ELIMINAR EVENTO (AJAX)
// =====================================================

function eliminarEvento(eventId) {
    Swal.fire({
        title: 'Eliminar evento',
        text: '¿Estas seguro de eliminar este evento? Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();

            $.ajax({
                url: BASE_URL + '/calendario/eliminar/' + eventId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        $('#modalEvento').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        recargarEventos();
                    } else {
                        Swal.fire('Error', response.message || 'Error al eliminar el evento.', 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Error', 'Error de conexion con el servidor.', 'error');
                }
            });
        }
    });
}

// =====================================================
// UTILIDADES
// =====================================================

function formatearFechaLocal(fecha) {
    var year = fecha.getFullYear();
    var month = String(fecha.getMonth() + 1).padStart(2, '0');
    var day = String(fecha.getDate()).padStart(2, '0');
    var hours = String(fecha.getHours()).padStart(2, '0');
    var minutes = String(fecha.getMinutes()).padStart(2, '0');
    return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
}

function escHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
