var borradorActualId = null;

$(document).ready(function() {
    cargarBorradores();
});

function seleccionarBorradorDesdeUrl(intentos) {
    var params = new URLSearchParams(window.location.search);
    var selectId = params.get('select');
    if (!selectId) return;
    intentos = intentos || 0;
    if (intentos > 10) return;
    var item = $('.borrador-item[data-id="' + selectId + '"]');
    if (!item.length) {
        setTimeout(function() { seleccionarBorradorDesdeUrl(intentos + 1); }, 300);
        return;
    }
    seleccionarBorrador(selectId);
    item[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function cargarBorradores() {
    showLoading();
    $.ajax({
        url: BASE_URL + 'borradores/listar',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var lista = $('#listaBorradores');
            lista.empty();

            if (!data || data.length === 0) {
                $('#sinBorradores').show();
                hideLoading();
                return;
            }

            $('#sinBorradores').hide();

            data.forEach(function(b) {
                var preview = b.contenido ? b.contenido.replace(/<[^>]*>/g, '').slice(0, 100) : '';
                var fecha = b.updated_at ? formatearFecha(b.updated_at) : '';
                var fijado = parseInt(b.fijado) ? '<i class="bi bi-pin-fill" style="color:var(--primary);font-size:0.65rem;"></i> ' : '';
                var esPub = parseInt(b.publicado) === 1;
                var publicado = esPub
                    ? '<span class="badge-pub si"><i class="bi bi-check-circle-fill"></i> ' + (b.seccion_destino || 'pub') + '</span>'
                    : '<span class="badge-pub no"><i class="bi bi-x-circle-fill"></i> SIN PUBLICAR</span>';

                var item = '<div class="borrador-item' + (esPub ? ' pub' : '') + '" data-id="' + b.id + '" onclick="seleccionarBorrador(' + b.id + ')">' +
                    '<div class="d-flex justify-content-between align-items-start">' +
                    '<div style="font-weight:600;font-size:0.85rem;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">' + fijado + escHtml(b.titulo) + '</div>' +
                    '<small style="color:var(--text-muted);font-size:0.65rem;white-space:nowrap;margin-left:8px;">' + fecha + '</small>' +
                    '</div>' +
                    (preview ? '<div style="font-size:0.75rem;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;">' + escHtml(preview) + '</div>' : '') +
                    '<div style="margin-top:4px;">' + publicado + '</div>' +
                    '</div>';

                lista.append(item);
            });

            if (borradorActualId) {
                $('.borrador-item[data-id="' + borradorActualId + '"]').css('background', 'var(--bg-input)');
            }

            seleccionarBorradorDesdeUrl();
            hideLoading();
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error al cargar borradores.', 'error');
        }
    });
}

function filtrarBorradores() {
    var q = $('#buscarBorrador').val().toLowerCase();
    $('.borrador-item').each(function() {
        $(this).css('display', q === '' || $(this).text().toLowerCase().indexOf(q) > -1 ? '' : 'none');
    });
}

function nuevoBorrador() {
    borradorActualId = null;
    $('#borradorId').val('');
    $('#borradorTitulo').val('');
    $('#borradorContenido').val('');
    $('#tituloEditor').html('<i class="bi bi-pencil-square"></i> Nuevo borrador');
    $('#accionesEditor').hide();
    $('#editorVacio').hide();
    $('#editorContenido').show();
    $('#borradorTitulo').focus();
    $('.borrador-item').css('background', '');
}

function seleccionarBorrador(id) {
    borradorActualId = id;
    $('.borrador-item').css('background', '');
    $('.borrador-item[data-id="' + id + '"]').css('background', 'var(--bg-input)');

    showLoading();
    $.ajax({
        url: BASE_URL + 'borradores/obtener/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(b) {
            $('#borradorId').val(b.id);
            $('#borradorTitulo').val(b.titulo);
            $('#borradorContenido').val(b.contenido || '');
            $('#tituloEditor').html('<i class="bi bi-pencil-square"></i> ' + escHtml(b.titulo));
            $('#accionesEditor').show();
            $('#editorVacio').hide();
            $('#editorContenido').show();

            var pubStatus = $('#pubStatusBadge');
            if (parseInt(b.publicado) === 1) {
                pubStatus.html('<span class="badge-pub si"><i class="bi bi-check-circle-fill"></i> Publicado en ' + escHtml(b.seccion_destino || '') + '</span>').show();
                $('#btnPublicar').hide();
                $('#btnDespublicar').show();
            } else {
                pubStatus.hide();
                $('#btnPublicar').show();
                $('#btnDespublicar').hide();
            }

            hideLoading();
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error al cargar el borrador.', 'error');
        }
    });
}

function guardarBorrador() {
    var titulo = $('#borradorTitulo').val().trim();
    if (!titulo) {
        Swal.fire('Validacion', 'El asunto es obligatorio.', 'warning');
        return;
    }

    var datos = {
        id: borradorActualId,
        titulo: titulo,
        contenido: $('#borradorContenido').val(),
    };

    showLoading();
    $.ajax({
        url: BASE_URL + 'borradores/guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                });
                borradorActualId = response.data ? response.data.id : null;
                $('#borradorId').val(borradorActualId);
                cargarBorradores();
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

function cerrarEditor() {
    borradorActualId = null;
    $('#editorContenido').hide();
    $('#editorVacio').show();
    $('#tituloEditor').html('<i class="bi bi-pencil-square"></i> Selecciona un borrador');
    $('#accionesEditor').hide();
    $('#pubStatusBadge').hide();
    $('.borrador-item').css('background', '');
}

function eliminarBorrador() {
    var id = $('#borradorId').val();
    if (!id) return;

    Swal.fire({
        title: 'Eliminar borrador',
        text: 'Este borrador se eliminara permanentemente.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + 'borradores/eliminar/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
                        cerrarEditor();
                        cargarBorradores();
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
    });
}

function fijarBorrador() {
    var id = $('#borradorId').val();
    if (!id) return;

    showLoading();
    $.ajax({
        url: BASE_URL + 'borradores/fijar/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                cargarBorradores();
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

function despublicarBorrador() {
    var id = $('#borradorId').val();
    if (!id) return;

    Swal.fire({
        title: 'Retirar publicacion',
        text: 'El contenido dejara de verse en la seccion.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, retirar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + 'borradores/despublicar/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Retirado', text: 'La publicacion fue retirada.', timer: 1500, showConfirmButton: false });
                        cargarBorradores();
                        $('#btnPublicar').show();
                        $('#btnDespublicar').hide();
                        $('#pubStatusBadge').hide();
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
    });
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    var d = new Date(fecha.replace(' ', 'T'));
    if (isNaN(d.getTime())) return fecha.slice(0, 10);
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = ('0' + (d.getMonth() + 1)).slice(-2);
    var yy = d.getFullYear().toString().slice(-2);
    return dd + '/' + mm + '/' + yy;
}

function escHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}

function cargarPublicaciones(seccion) {
    $.ajax({
        url: BASE_URL + 'borradores/listar-publicados/' + seccion,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var c = $('#publicacionesContainer');
            var sep = c.data('seccion');
            if (sep) seccion = sep;
            c.empty();
            if (!data || data.length === 0) {
                c.html('<div class="text-center py-5"><i class="bi bi-inbox" style="font-size:2rem;color:var(--text-muted);"></i><p class="text-muted mt-2 small">No hay publicaciones</p></div>');
                return;
            }
            data.forEach(function(p) {
                var badge = '';
                if (p.destinatario_tipo === 'usuarios') badge = '<span class="pub-badge"><i class="bi bi-person-fill"></i> Individual</span>';
                else if (p.destinatario_tipo === 'departamento') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Departamento</span>';
                else if (p.destinatario_tipo === 'multiple') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Multiple</span>';
                else badge = '<span class="pub-badge"><i class="bi bi-globe"></i> Todos</span>';

                var card = '<div class="pub-card">' +
                    badge +
                    '<div class="pub-titulo">' + escHtml(p.titulo) + '</div>' +
                    '<div class="pub-contenido">' + escHtml(p.contenido ? p.contenido.slice(0, 300) : '') + '</div>' +
                    '<div class="pub-meta"><i class="bi bi-clock"></i> ' + formatearFecha(p.updated_at) + '</div>' +
                    '</div>';
                c.append(card);
            });
        }
    });
}
