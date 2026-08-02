var tablaUsuarios;

$(document).ready(function() {
    cargarUsuarios();
});

function cargarUsuarios() {
    $.ajax({
        url: BASE_URL + '/api/listar-usuarios',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var datos = response.data;
                var html = '';
                var estados = {
                    'PAGADO': '<span class="badge-estado badge-pagado"><i class="bi bi-check-circle"></i> Pagado</span>',
                    'PENDIENTE': '<span class="badge-estado badge-pendiente"><i class="bi bi-clock"></i> Pendiente</span>',
                    'NO_PAGADO': '<span class="badge-estado badge-no-pagado"><i class="bi bi-x-circle"></i> No Pagado</span>',
                    'RECHAZADO': '<span class="badge-estado badge-rechazado"><i class="bi bi-exclamation-circle"></i> Rechazado</span>',
                    'SIN REGISTRO': '<span class="badge-estado badge-no-pagado"><i class="bi bi-dash-circle"></i> Sin Registro</span>',
                };

                datos.forEach(function(usuario) {
                    var fecha = new Date(usuario.fecha_creacion);
                    var fechaStr = fecha.toLocaleDateString('es-PE');
                    var estadoHtml = estados[usuario.estado_pago] || estados['SIN REGISTRO'];

                    html += '<tr>';
                    html += '<td>' + usuario.id + '</td>';
                    html += '<td><strong>' + escapeHtml(usuario.nombre) + '</strong></td>';
                    html += '<td>' + escapeHtml(usuario.telefono || '-') + '</td>';
                    html += '<td>' + estadoHtml + '</td>';
                    html += '<td>';
                    html += '<button class="btn btn-sm btn-outline-info" onclick="verEnlace(\'' + escapeHtml(usuario.token_url) + '\')" title="Ver enlace"><i class="bi bi-link-45deg"></i></button> ';
                    html += '<button class="btn btn-sm btn-success" onclick="enviarWhatsApp(\'' + escapeHtml(usuario.telefono || '') + '\', \'' + escapeHtml(usuario.token_url) + '\', \'' + escapeHtml(usuario.nombre) + '\')" title="Enviar por WhatsApp"><i class="bi bi-whatsapp"></i></button>';
                    html += '</td>';
                    html += '<td>' + fechaStr + '</td>';
                    html += '<td class="text-center">';
                    html += '<div class="d-flex gap-1 justify-content-center">';
                    html += '<button class="btn btn-sm btn-outline-primary btn-sm-icon" onclick="editarUsuario(' + usuario.id + ', \'' + escapeHtml(usuario.nombre) + '\', \'' + escapeHtml(usuario.telefono || '') + '\', ' + (usuario.monto || 12.00) + ', ' + usuario.activo + ')" title="Editar"><i class="bi bi-pencil"></i></button>';
                    html += '<button class="btn btn-sm btn-outline-warning btn-sm-icon" onclick="verHistorial(' + usuario.id + ')" title="Historial"><i class="bi bi-clock-history"></i></button>';
                    html += '<button class="btn btn-sm btn-outline-danger btn-sm-icon" onclick="eliminarUsuario(' + usuario.id + ', \'' + escapeHtml(usuario.nombre) + '\')" title="Eliminar"><i class="bi bi-trash"></i></button>';
                    html += '</div>';
                    html += '</td>';
                    html += '</tr>';
                });

                if ($.fn.DataTable.isDataTable('#tablaUsuarios')) {
                    $('#tablaUsuarios').DataTable().destroy();
                }

                $('#tbodyUsuarios').html(html);

                tablaUsuarios = $('#tablaUsuarios').DataTable({
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
                        paginate: { previous: "Anterior", next: "Siguiente" },
                        zeroRecords: "No se encontraron usuarios",
                    },
                    pageLength: 10,
                    order: [[1, 'asc']],
                });
            }
        }
    });
}

function abrirModalUsuario() {
    $('#tituloModalUsuario').html('<i class="bi bi-person-plus"></i> Nuevo Usuario');
    $('#usuarioId').val('');
    $('#usuarioNombre').val('');
    $('#usuarioTelefono').val('');
    $('#usuarioMonto').val('12.00');
    $('#usuarioActivo').val('1');
    $('#modalUsuario').modal('show');
}

function editarUsuario(id, nombre, telefono, monto, activo) {
    $('#tituloModalUsuario').html('<i class="bi bi-pencil"></i> Editar Usuario');
    $('#usuarioId').val(id);
    $('#usuarioNombre').val(nombre);
    $('#usuarioTelefono').val(telefono);
    $('#usuarioMonto').val(monto || '12.00');
    $('#usuarioActivo').val(activo);
    $('#modalUsuario').modal('show');
}

function guardarUsuario() {
    var nombre = $('#usuarioNombre').val().trim();
    if (!nombre) {
        Swal.fire('Error', 'El nombre es obligatorio', 'error');
        return;
    }
    showLoading();
    $.ajax({
        url: BASE_URL + '/api/guardar-usuario',
        type: 'POST',
        data: {
            id: $('#usuarioId').val(),
            nombre: nombre,
            telefono: $('#usuarioTelefono').val(),
            monto: $('#usuarioMonto').val() || '12.00',
            activo: $('#usuarioActivo').val(),
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                $('#modalUsuario').modal('hide');
                Swal.fire('Exito', response.message, 'success');
                cargarUsuarios();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion', 'error');
        }
    });
}

function eliminarUsuario(id, nombre) {
    Swal.fire({
        title: 'Eliminar usuario',
        html: 'Estas seguro de eliminar a <strong>' + escapeHtml(nombre) + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4669FA',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + '/api/eliminar-usuario/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        cargarUsuarios();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Error', 'Error de conexion', 'error');
                }
            });
        }
    });
}

function verEnlace(url) {
    $('#enlaceUsuario').val(url);
    $('#modalEnlace').modal('show');
}

function copiarEnlace() {
    var input = document.getElementById('enlaceUsuario');
    input.select();
    document.execCommand('copy');
    Swal.fire({ icon: 'success', title: 'Copiado', text: 'Enlace copiado al portapapeles', timer: 1500, showConfirmButton: false });
}

function verHistorial(id) {
    window.location.href = BASE_URL + '/historial/' + id;
}

function enviarWhatsApp(telefono, url, nombre) {
    if (!telefono || telefono === '-') {
        Swal.fire('Sin teléfono', 'Este usuario no tiene número de WhatsApp registrado', 'warning');
        return;
    }
    var mensaje = 'Hola ' + nombre + ', te comparto el enlace para registrar tu pago:\n\n' + url;
    var waUrl = 'https://wa.me/51' + telefono + '?text=' + encodeURIComponent(mensaje);
    window.open(waUrl, '_blank');
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
