function toggleComentariosDetalle() {
    var wrap = $('#detalleComentarios');
    var visible = wrap.is(':visible');
    wrap.slideToggle(200);
    if (!visible) cargarComentariosDetalle();
}

function cargarComentariosDetalle() {
    var lista = $('#detalleComentariosLista');
    $.ajax({
        url: BASE_URL + 'borradores/listar-comentarios/' + DETALLE_PUB_ID,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            lista.empty();
            setComentariosCountDetalle(data ? data.length : 0);
            if (!data || data.length === 0) {
                lista.html('<div class="comentario-vacio">Sin comentarios</div>');
                return;
            }
            data.forEach(function(c) {
                var nombre = c.autor_nombre || 'Desconocido';
                var fecha  = c.created_at ? formatearFechaHoraDetalle(c.created_at) : '';
                lista.append(
                    '<div class="comentario-item">' +
                    '<div class="comentario-avatar">' + avatarComentarioDetalle(c, nombre) + '</div>' +
                    '<div class="comentario-body">' +
                    '<div class="comentario-autor">' + escHtmlDetalle(nombre) + '<span class="comentario-fecha">' + fecha + '</span></div>' +
                    '<div class="comentario-texto">' + escHtmlDetalle(c.comentario) + '</div>' +
                    '</div>' +
                    '</div>'
                );
            });
        }
    });
}

function setComentariosCountDetalle(count) {
    $('.nd-accion.com .com-count').text(count).show();
    if (count === 0) {
        $('.nd-accion.com .com-count').hide();
    }
}

function guardarComentarioDetalle(btn) {
    var ta = $('#detalleComentarioTexto');
    var texto = ta.val().trim();
    if (!texto) return;

    $(btn).prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'borradores/guardar-comentario',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ borrador_id: DETALLE_PUB_ID, comentario: texto }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                ta.val('');
                cargarComentariosDetalle();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(jqXHR) {
            var msg = 'Error de conexion.';
            try {
                var r = JSON.parse(jqXHR.responseText);
                if (r.message) msg = r.message;
                else if (r.error) msg = r.error;
            } catch(e) { /* ignore */ }
            Swal.fire('Error (' + jqXHR.status + ')', msg, 'error');
        },
        complete: function() {
            $(btn).prop('disabled', false);
        }
    });
}

function guardarComoDetalle(tipo) {
    var data = {
        titulo: DETALLE_PUB_TITULO,
        descripcion: DETALLE_PUB_CONTENIDO,
        tipo: tipo === 'marcador' ? 'marcador' : 'recordatorio',
        origen_tipo: 'borrador',
        origen_id: DETALLE_PUB_ID,
        seccion: DETALLE_PUB_SECCION,
        fecha: new Date().toISOString().slice(0, 10),
    };
    if (tipo === 'recordatorio') {
        data.prioridad = 'media';
    }

    Swal.fire({
        title: tipo === 'marcador' ? 'Agregar a Marcadores' : 'Agregar a Recordatorio',
        text: 'Se guardara el post completo.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (!result.isConfirmed) return;
        showLoading();
        $.ajax({
            url: BASE_URL + 'recordatorio/guardar',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'Guardado', timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                Swal.fire('Error', 'Error de conexion.', 'error');
            }
        });
    });
}

function despublicarDetalle() {
    Swal.fire({
        title: 'Despublicar',
        text: 'La publicacion dejara de ser visible. Continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Despublicar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (!result.isConfirmed) return;
        showLoading();
        $.ajax({
            url: BASE_URL + 'borradores/despublicar/' + DETALLE_PUB_ID,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'Publicacion retirada', timer: 1200, showConfirmButton: false })
                        .then(function() { window.location.href = BASE_URL + 'noticias'; });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                hideLoading();
                Swal.fire('Error', 'Error de conexion.', 'error');
            }
        });
    });
}

function avatarComentarioDetalle(c, nombre) {
    if (c.autor_foto) {
        return '<img src="' + BASE_URL + c.autor_foto + '" alt="">';
    }
    var inicial = (nombre && nombre.charAt(0)) ? nombre.charAt(0).toUpperCase() : 'A';
    return '<span>' + escHtmlDetalle(inicial) + '</span>';
}

function formatearFechaHoraDetalle(f) {
    if (!f) return '';
    var d = new Date(f.replace(' ', 'T'));
    if (isNaN(d.getTime())) return f.slice(0, 10);
    var dd = ('0' + d.getDate()).slice(-2), mm = ('0' + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
    var hh = ('0' + d.getHours()).slice(-2), mi = ('0' + d.getMinutes()).slice(-2);
    return dd + '/' + mm + '/' + yy + ' ' + hh + ':' + mi;
}

function escHtmlDetalle(s) {
    if (!s) return '';
    return $('<div>').text(s).html();
}