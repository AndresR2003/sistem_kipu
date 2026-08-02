/**
 * Calendario - FullCalendar CRUD
 * Gestiona la inicializacion del calendario y las operaciones AJAX de eventos
 */

var calendar = null;
var calendarEl = document.getElementById('calendar');

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

function abrirModalNuevo() {
    $('#formEvento')[0].reset();
    $('#eventoId').val('');
    $('#modalEventoTitulo').html('<i class="bi bi-calendar-plus"></i> Nuevo Evento');
    $('#btnGuardarEvento').html('<i class="bi bi-check-lg"></i> Guardar Evento');

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

    $('#modalEvento').modal('show');
}

function abrirModalNuevoDesdeFecha(fechaInicio, fechaFin) {
    $('#formEvento')[0].reset();
    $('#eventoId').val('');
    $('#modalEventoTitulo').html('<i class="bi bi-calendar-plus"></i> Nuevo Evento');
    $('#btnGuardarEvento').html('<i class="bi bi-check-lg"></i> Guardar Evento');

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

    $('#eventoId').val(eventId);
    $('#eventoTitulo').val(titulo);
    $('#eventoDescripcion').val(descripcion);
    $('#modalEventoTitulo').html('<i class="bi bi-pencil-fill"></i> Editar Evento');
    $('#btnGuardarEvento').html('<i class="bi bi-check-lg"></i> Actualizar Evento');

    // Fechas
    var startLocal = start ? start.slice(0, 16) : '';
    var endLocal = end ? end.slice(0, 16) : '';
    $('#eventoFechaInicio').val(startLocal);
    $('#eventoFechaFin').val(endLocal);

    // Color
    $('#eventoColor').val(color);
    $('#eventoColorTexto').text(color);
    $('.color-preset').removeClass('active');
    $('.color-preset[data-color="' + color + '"]').addClass('active');

    // Agregar boton de eliminar si no existe
    if (!$('#btnEliminarEvento').length) {
        var btnEliminar = '<button type="button" class="btn btn-danger-custom" id="btnEliminarEvento" onclick="eliminarEvento(' + eventId + ')">';
        btnEliminar += '<i class="bi bi-trash"></i> Eliminar';
        btnEliminar += '</button>';
        $('#modalEvento .modal-footer').prepend(btnEliminar);
    } else {
        $('#btnEliminarEvento').attr('onclick', 'eliminarEvento(' + eventId + ')');
    }

    $('#modalEvento').modal('show');
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

    var datos = {
        id: id ? parseInt(id) : null,
        titulo: titulo,
        descripcion: descripcion,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin || null,
        color: color
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
