function cargarPublicaciones(seccion) {
    $.ajax({
        url: BASE_URL + 'borradores/listar-publicados/' + seccion,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var c = $('#publicacionesContainer');
            c.empty();
            if (!data || data.length === 0) {
                c.html('<div class="text-center py-5"><i class="bi bi-inbox" style="font-size:2rem;color:var(--text-muted);"></i><p class="text-muted mt-2 small">No hay publicaciones</p></div>');
                return;
            }
            data.forEach(function(p) {
                var badge = '';
                if (p.destinatario_tipo === 'usuarios') badge = '<span class="pub-badge"><i class="bi bi-person-fill"></i> Individual</span>';
                else if (p.destinatario_tipo === 'departamento') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Departamento</span>';
                else badge = '<span class="pub-badge"><i class="bi bi-globe"></i> Todos</span>';

                var d = p.updated_at ? formatearFecha(p.updated_at) : '';
                var cardId = 'pub-' + p.id;
                var completado = parseInt(p.completado) ? 'completada' : '';
                var checked = parseInt(p.completado) ? 'checked' : '';

                var bodyContent = (seccion === 'tareas')
                    ? '<div class="pub-check">' +
                      '<input class="form-check-input" type="checkbox" ' + checked + ' onchange="toggleTarea(' + p.id + ', this)">' +
                      '<div>' +
                      '<div class="pub-titulo">' + escHtml(p.titulo) + '</div>' +
                      '<div class="pub-contenido">' + escHtml(p.contenido) + '</div>' +
                      '</div></div>'
                    : '<div class="pub-titulo">' + escHtml(p.titulo) + '</div>' +
                      '<div class="pub-contenido">' + escHtml(p.contenido) + '</div>';

                var card = '<div class="pub-card ' + completado + '" id="' + cardId + '">' +
                    badge +
                    bodyContent +
                    '<div class="pub-meta"><i class="bi bi-clock"></i> ' + d + '</div>' +
                    '<div class="pub-acciones">' +
                    '<button class="rec" onclick="agregarRecordatorio(' + p.id + ',\'' + escHtml(p.titulo).replace(/'/g, "\\'") + '\')" title="Agregar a Recordatorio"><i class="bi bi-bell-fill"></i> Recordatorio</button>' +
                    '<button class="mar" onclick="agregarMarcador(' + p.id + ',\'' + escHtml(p.titulo).replace(/'/g, "\\'") + '\')" title="Agregar a Marcadores"><i class="bi bi-bookmark-fill"></i> Marcador</button>' +
                    '<button class="com" onclick="toggleComentarios(' + p.id + ')" title="Ver comentarios"><i class="bi bi-chat-fill"></i> Comentarios</button>' +
                    '</div>' +
                    '<div class="comentarios-wrap" id="comentarios-' + p.id + '" style="display:none;">' +
                    '<div class="comentarios-lista"></div>' +
                    '<div class="comentarios-form">' +
                    '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
                    '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentario(' + p.id + ', this)">Enviar</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                c.append(card);
            });
        }
    });
}

function toggleTarea(id, checkbox) {
    $.ajax({
        url: BASE_URL + 'borradores/completar/' + id,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ completado: checkbox.checked ? 1 : 0 }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var card = $('#pub-' + id);
                if (checkbox.checked) {
                    card.addClass('completada');
                } else {
                    card.removeClass('completada');
                }
            } else {
                checkbox.checked = !checkbox.checked;
            }
        },
        error: function() {
            checkbox.checked = !checkbox.checked;
        }
    });
}

function toggleComentarios(id) {
    var wrap = $('#comentarios-' + id);
    var visible = wrap.is(':visible');
    wrap.slideToggle(200);
    if (!visible) cargarComentarios(id);
}

function cargarComentarios(id) {
    var lista = $('#comentarios-' + id + ' .comentarios-lista');
    $.ajax({
        url: BASE_URL + 'borradores/listar-comentarios/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            lista.empty();
            if (!data || data.length === 0) {
                lista.html('<div class="comentario-vacio">Sin comentarios</div>');
                return;
            }
            data.forEach(function(c) {
                var nombre = c.autor_nombre || 'Desconocido';
                var fecha  = c.created_at ? formatearFechaHora(c.created_at) : '';
                lista.append(
                    '<div class="comentario-item">' +
                    '<div class="comentario-autor">' + escHtml(nombre) + ' <span class="comentario-fecha">' + fecha + '</span></div>' +
                    '<div class="comentario-texto">' + escHtml(c.comentario) + '</div>' +
                    '</div>'
                );
            });
        }
    });
}

function guardarComentario(id, btn) {
    var wrap = $('#comentarios-' + id);
    var ta   = wrap.find('textarea');
    var texto = ta.val().trim();
    if (!texto) return;

    $(btn).prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'borradores/guardar-comentario',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ borrador_id: id, comentario: texto }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                ta.val('');
                cargarComentarios(id);
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
            } catch(e) {
                if (jqXHR.responseText) msg = jqXHR.status + ': ' + jqXHR.responseText.slice(0,200);
            }
            Swal.fire('Error (' + jqXHR.status + ')', msg, 'error');
        },
        complete: function() {
            $(btn).prop('disabled', false);
        }
    });
}

function formatearFecha(f) {
    if (!f) return '';
    var d = new Date(f.replace(' ', 'T'));
    if (isNaN(d.getTime())) return f.slice(0, 10);
    var dd = ('0' + d.getDate()).slice(-2), mm = ('0' + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
    return dd + '/' + mm + '/' + yy;
}

function formatearFechaHora(f) {
    if (!f) return '';
    var d = new Date(f.replace(' ', 'T'));
    if (isNaN(d.getTime())) return f.slice(0, 10);
    var dd = ('0' + d.getDate()).slice(-2), mm = ('0' + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
    var hh = ('0' + d.getHours()).slice(-2), mi = ('0' + d.getMinutes()).slice(-2);
    return dd + '/' + mm + '/' + yy + ' ' + hh + ':' + mi;
}

function escHtml(s) {
    if (!s) return '';
    return $('<div>').text(s).html();
}

function agregarRecordatorio(id, titulo) {
    Swal.fire({
        title: 'Agregar a Recordatorio',
        html:
            '<input class="swal2-input" id="recFecha" type="date" value="' + new Date().toISOString().slice(0, 10) + '">' +
            '<select class="swal2-input" id="recPrioridad">' +
            '<option value="baja">Baja</option>' +
            '<option value="media" selected>Media</option>' +
            '<option value="alta">Alta</option>' +
            '</select>',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: function() {
            return {
                fecha: document.getElementById('recFecha').value,
                prioridad: document.getElementById('recPrioridad').value,
            };
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + 'recordatorio/guardar',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    titulo: titulo,
                    descripcion: 'Desde publicacion',
                    fecha: result.value.fecha,
                    prioridad: result.value.prioridad,
                }),
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Recordatorio agregado', timer: 1500, showConfirmButton: false });
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

function agregarMarcador(id, titulo) {
    showLoading();
    $.ajax({
        url: BASE_URL + 'recordatorio/guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            titulo: titulo,
            descripcion: 'Desde publicacion #' + id,
            tipo: 'marcador',
        }),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                Swal.fire({ icon: 'success', title: 'Marcador agregado', timer: 1500, showConfirmButton: false });
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

$(document).ready(function() {
    var sec = $('#publicacionesContainer').data('seccion');
    if (sec) cargarPublicaciones(sec);
});
