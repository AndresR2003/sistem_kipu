var tablaUsuarios = null;

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

                datos.forEach(function(usuario) {
                    var fecha = new Date(usuario.fecha_creacion);
                    var fechaStr = fecha.toLocaleDateString('es-PE');

                    html += '<tr>';
                    html += '<td>' + usuario.id + '</td>';
                    html += '<td><strong>' + escapeHtml(usuario.nombre) + '</strong></td>';
                    html += '<td>' + escapeHtml(usuario.telefono || '-') + '</td>';
                    html += '<td>' + fechaStr + '</td>';
                    html += '<td class="text-center">';
                    html += '<div class="d-flex gap-1 justify-content-center">';
                    html += '<button class="btn btn-sm btn-outline-primary btn-sm-icon" onclick="editarUsuario(' + usuario.id + ', \'' + escapeHtml(usuario.nombre) + '\', \'' + escapeHtml(usuario.telefono || '') + '\', ' + usuario.activo + ')" title="Editar"><i class="bi bi-pencil"></i></button>';
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
    $('#usuarioActivo').val('1');
    $('#modalUsuario').modal('show');
}

function editarUsuario(id, nombre, telefono, activo) {
    $('#tituloModalUsuario').html('<i class="bi bi-pencil"></i> Editar Usuario');
    $('#usuarioId').val(id);
    $('#usuarioNombre').val(nombre);
    $('#usuarioTelefono').val(telefono);
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

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
