function comButton(count, onclick) {
    return '<button class="com" onclick="' + onclick + '" title="Ver comentarios"><i class="bi bi-chat-fill"></i> Comentarios' + (count > 0 ? '<span class="com-count">' + count + '</span>' : '') + '</button>';
}

function setComentariosCount(wrapId, count) {
    var btn = $('#' + wrapId).closest('.pub-card').find('.com');
    if (!btn.length) return;
    btn.find('.com-count').remove();
    if (count > 0) {
        btn.append('<span class="com-count">' + count + '</span>');
    }
}

function cargarPublicaciones(seccion) {
    var c = $('#publicacionesContainer');
    c.html('<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm"></div> Cargando...</div>');

    if (seccion === 'tareas') {
        cargarTareasDiarias(true);
    } else {
        cargarBorradoresPublicados(seccion);
    }
}

function cargarBorradoresPublicados(seccion) {
    $.ajax({
        url: BASE_URL + 'borradores/listar-publicados/' + seccion,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var c = $('#publicacionesContainer');
            c.find('.text-center.py-5').remove();

            if (seccion === 'tareas') {
                var lista = $('#tareasPublicadasLista');
                if (!lista.length) return;
                lista.empty();
                if (!data || data.length === 0) {
                    $('#tareasPubCount').text('0');
                    lista.html('<div class="tareas-vacio"><i class="bi bi-inbox"></i>Sin otras tareas</div>');
                    return;
                }
                $('#tareasPubCount').text(data.length);
                data.forEach(function(p) {
                    lista.append(tareaPublicadaCard(p));
                });
                return;
            }

            if (!data || data.length === 0) {
                if ($('#tareasDiariasBlock').length === 0) {
                    c.html('<div class="text-center py-5"><i class="bi bi-inbox" style="font-size:2rem;color:var(--text-muted);"></i><p class="text-muted mt-2 small">No hay publicaciones</p></div>');
                }
                return;
            }

            var lista = $('#publicacionesLista');
            if (!lista.length) {
                lista = $('<div class="publicaciones-lista" id="publicacionesLista"></div>');
                c.append(lista);
            }
            data.forEach(function(p) {
                var badge = '';
                if (p.destinatario_tipo === 'usuarios') badge = '<span class="pub-badge"><i class="bi bi-person-fill"></i> Individual</span>';
                else if (p.destinatario_tipo === 'departamento') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Departamento</span>';
                else if (p.destinatario_tipo === 'multiple') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Multiple</span>';
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

                var card = '<div class="pub-card ' + completado + '" id="' + cardId + '" data-origen="borrador" data-origen-id="' + p.id + '" data-seccion="' + seccion + '">' +
                    badge +
                    bodyContent +
                    '<div class="pub-meta"><i class="bi bi-clock"></i> ' + d + '</div>' +
                    '<div class="pub-acciones">' +
                    '<button class="rec" onclick="guardarComo(\'recordatorio\',' + p.id + ', this)" title="Agregar a Recordatorio"><i class="bi bi-bell-fill"></i> Recordatorio</button>' +
                    '<button class="mar" onclick="guardarComo(\'marcador\',' + p.id + ', this)" title="Agregar a Marcadores"><i class="bi bi-bookmark-fill"></i> Marcador</button>' +
                    comButton(p.comentarios_count || 0, 'toggleComentarios(' + p.id + ')') +
                    '</div>' +
                    '<div class="comentarios-wrap" id="comentarios-' + p.id + '" style="display:none;">' +
                    '<div class="comentarios-lista"></div>' +
                    '<div class="comentarios-form">' +
                    '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
                    '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentario(' + p.id + ', this)">Enviar</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                lista.append(card);
            });
        }
    });
}

function cargarTareasDiarias(initial) {
    $.ajax({
        url: BASE_URL + 'entregas/listar',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var c = $('#publicacionesContainer');
            c.find('.text-center.py-5').remove();

            if (!c.find('#tareasPublicadasBlock').length) {
                c.append(
                    '<div class="tareas-grupo" id="tareasPublicadasBlock">' +
                    '<div class="tareas-grupo-titulo"><span><i class="bi bi-check2-square"></i> Otras tareas</span><span class="tareas-count" id="tareasPubCount">0</span></div>' +
                    '<div id="tareasPublicadasLista"><div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm"></div> Cargando...</div></div>' +
                    '</div>'
                );
            }

            var tareas = data.tareas || [];
            var block = $('#tareasDiariasBlock');
            if (tareas.length === 0) {
                block.remove();
            } else {
                var fechaTxt = formatearFecha(data.fecha);
                var hechas = 0;
                tareas.forEach(function(t) { if (t.hecho_por_mi) hechas++; });
                var pct = Math.round(hechas / tareas.length * 100);
                var html = '<div class="tareas-grupo-titulo diaria"><span><i class="bi bi-arrow-repeat"></i> Tareas diarias <span class="tareas-fecha">' + fechaTxt + '</span></span>' +
                    '<span class="d-flex align-items-center gap-2"><span class="tareas-progreso"><span style="width:' + pct + '%"></span></span><span class="tareas-count">' + hechas + '/' + tareas.length + '</span></span></div>';

                tareas.forEach(function(t) {
                    var hechoPor = '';
                    (t.hecho_por || []).forEach(function(h) {
                        hechoPor += '<span class="ent-hecho' + (h.mio ? ' mio' : '') + '"><i class="bi bi-check-circle-fill"></i> ' + escHtml(h.nombre) + '</span>';
                    });
                    var completado = t.hecho_por_mi ? 'completada' : '';
                    var checked = t.hecho_por_mi ? 'checked' : '';
                    html += '<div class="pub-card diaria ' + completado + '" id="pub-ent-' + t.id + '" data-origen="entrega" data-origen-id="' + t.id + '" data-seccion="tareas_diarias">' +
                        '<div class="pub-check">' +
                        '<input class="form-check-input" type="checkbox" ' + checked + ' onchange="toggleTareaEntregas(' + t.id + ', this)">' +
                        '<div class="flex-grow-1">' +
                        '<div class="d-flex align-items-center flex-wrap gap-2"><span class="pub-titulo">' + escHtml(t.titulo) + '</span>' +
                        '<span class="pub-badge" style="background:rgba(34,197,94,0.12);color:#22c55e;"><i class="bi bi-arrow-repeat"></i> Diaria</span></div>' +
                        (t.descripcion ? '<div class="pub-contenido">' + escHtml(t.descripcion) + '</div>' : '') +
                        '</div></div>' +
                        (hechoPor ? '<div class="ent-hechos">' + hechoPor + '</div>' : '') +
                        '<div class="pub-acciones">' +
                        '<button class="rec" onclick="guardarComo(\'recordatorio\',' + t.id + ', this)" title="Agregar a Recordatorio"><i class="bi bi-bell-fill"></i> Recordatorio</button>' +
                        '<button class="mar" onclick="guardarComo(\'marcador\',' + t.id + ', this)" title="Agregar a Marcadores"><i class="bi bi-bookmark-fill"></i> Marcador</button>' +
                        comButton(t.comentarios_count || 0, 'toggleComentariosEnt(' + t.id + ')') +
                        '</div>' +
                        '<div class="comentarios-wrap" id="comentarios-ent-' + t.id + '" style="display:none;">' +
                        '<div class="comentarios-lista"></div>' +
                        '<div class="comentarios-form">' +
                        '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
                        '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentarioEnt(' + t.id + ', this)">Enviar</button>' +
                        '</div></div>' +
                        '</div>';
                });

                if (block.length) {
                    block.html(html);
                } else {
                    block = $('<div class="tareas-grupo" id="tareasDiariasBlock"></div>').html(html);
                    $('#publicacionesContainer').prepend(block);
                }
            }
            if (initial) cargarBorradoresPublicados('tareas');
        }
    });
}

function tareaPublicadaCard(p) {
    var badge = '';
    if (p.destinatario_tipo === 'usuarios') badge = '<span class="pub-badge"><i class="bi bi-person-fill"></i> Individual</span>';
    else if (p.destinatario_tipo === 'departamento') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Departamento</span>';
    else if (p.destinatario_tipo === 'multiple') badge = '<span class="pub-badge"><i class="bi bi-people-fill"></i> Multiple</span>';
    else badge = '<span class="pub-badge"><i class="bi bi-globe"></i> Todos</span>';

    var d = p.updated_at ? formatearFecha(p.updated_at) : '';
    var completado = parseInt(p.completado) ? 'completada' : '';
    var checked = parseInt(p.completado) ? 'checked' : '';

    return '<div class="pub-card ' + completado + '" id="pub-' + p.id + '" data-origen="borrador" data-origen-id="' + p.id + '" data-seccion="tareas">' +
        '<div class="pub-check">' +
        '<input class="form-check-input" type="checkbox" ' + checked + ' onchange="toggleTarea(' + p.id + ', this)">' +
        '<div class="flex-grow-1">' +
        '<div class="pub-titulo">' + escHtml(p.titulo) + '</div>' +
        '<div class="pub-contenido">' + escHtml(p.contenido) + '</div>' +
        '<div class="d-flex align-items-center flex-wrap gap-2">' + badge +
        '<span class="pub-meta"><i class="bi bi-clock"></i> ' + d + '</span></div>' +
        '</div></div>' +
        '<div class="pub-acciones">' +
        '<button class="rec" onclick="guardarComo(\'recordatorio\',' + p.id + ', this)" title="Agregar a Recordatorio"><i class="bi bi-bell-fill"></i> Recordatorio</button>' +
        '<button class="mar" onclick="guardarComo(\'marcador\',' + p.id + ', this)" title="Agregar a Marcadores"><i class="bi bi-bookmark-fill"></i> Marcador</button>' +
        comButton(p.comentarios_count || 0, 'toggleComentarios(' + p.id + ')') +
        '</div>' +
        '<div class="comentarios-wrap" id="comentarios-' + p.id + '" style="display:none;">' +
        '<div class="comentarios-lista"></div>' +
        '<div class="comentarios-form">' +
        '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
        '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentario(' + p.id + ', this)">Enviar</button>' +
        '</div></div>' +
        '</div>';
}

function toggleTareaEntregas(id, checkbox) {
    $.ajax({
        url: BASE_URL + 'entregas/completar/' + id,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ completado: checkbox.checked ? 1 : 0 }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                cargarTareasDiarias(false);
            } else {
                checkbox.checked = !checkbox.checked;
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            checkbox.checked = !checkbox.checked;
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

function toggleComentariosEnt(id) {
    var wrap = $('#comentarios-ent-' + id);
    var visible = wrap.is(':visible');
    wrap.slideToggle(200);
    if (!visible) cargarComentariosEnt(id);
}

function avatarComentario(c, nombre) {
    if (c.autor_foto) {
        return '<img src="' + BASE_URL + c.autor_foto + '" alt="">';
    }
    var inicial = (nombre && nombre.charAt(0)) ? nombre.charAt(0).toUpperCase() : 'A';
    return '<span>' + escHtml(inicial) + '</span>';
}

function cargarComentariosEnt(id) {
    var lista = $('#comentarios-ent-' + id + ' .comentarios-lista');
    $.ajax({
        url: BASE_URL + 'entregas/comentarios/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            lista.empty();
            setComentariosCount('comentarios-ent-' + id, data ? data.length : 0);
            if (!data || data.length === 0) {
                lista.html('<div class="comentario-vacio">Sin comentarios</div>');
                return;
            }
            data.forEach(function(c) {
                var nombre = c.autor_nombre || 'Desconocido';
                var fecha  = c.created_at ? formatearFechaHora(c.created_at) : '';
                lista.append(
                    '<div class="comentario-item">' +
                    '<div class="comentario-avatar">' + avatarComentario(c, nombre) + '</div>' +
                    '<div class="comentario-body">' +
                    '<div class="comentario-autor">' + escHtml(nombre) + ' <span class="comentario-fecha">' + fecha + '</span></div>' +
                    '<div class="comentario-texto">' + escHtml(c.comentario) + '</div>' +
                    '</div>' +
                    '</div>'
                );
            });
        }
    });
}

function guardarComentarioEnt(id, btn) {
    var wrap = $('#comentarios-ent-' + id);
    var ta   = wrap.find('textarea');
    var texto = ta.val().trim();
    if (!texto) return;

    $(btn).prop('disabled', true);
    $.ajax({
        url: BASE_URL + 'entregas/comentario',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ entrega_id: id, comentario: texto }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                ta.val('');
                cargarComentariosEnt(id);
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexion.', 'error');
        },
        complete: function() {
            $(btn).prop('disabled', false);
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
            setComentariosCount('comentarios-' + id, data ? data.length : 0);
            if (!data || data.length === 0) {
                lista.html('<div class="comentario-vacio">Sin comentarios</div>');
                return;
            }
            data.forEach(function(c) {
                var nombre = c.autor_nombre || 'Desconocido';
                var fecha  = c.created_at ? formatearFechaHora(c.created_at) : '';
                lista.append(
                    '<div class="comentario-item">' +
                    '<div class="comentario-avatar">' + avatarComentario(c, nombre) + '</div>' +
                    '<div class="comentario-body">' +
                    '<div class="comentario-autor">' + escHtml(nombre) + ' <span class="comentario-fecha">' + fecha + '</span></div>' +
                    '<div class="comentario-texto">' + escHtml(c.comentario) + '</div>' +
                    '</div>' +
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

function guardarComo(tipo, id, btn) {
    var card = $(btn).closest('.pub-card');
    var titulo = $.trim(card.find('.pub-titulo').first().text());
    var contenido = $.trim(card.find('.pub-contenido').first().text());
    var origenTipo = card.data('origen') || 'borrador';
    var origenId = card.data('origen-id');
    var seccion = card.data('seccion') || '';

    if (seccion === 'tareas_diarias') {
        seccion = 'tareas';
    }

    var data = {
        titulo: titulo,
        descripcion: contenido,
        tipo: tipo === 'marcador' ? 'marcador' : 'recordatorio',
        origen_tipo: origenTipo,
        origen_id: origenId,
        seccion: seccion,
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

$(document).ready(function() {
    var sec = $('#publicacionesContainer').data('seccion');
    if (sec) cargarPublicaciones(sec);
});

function enfocarPublicacionDesdeUrl() {
    var params = new URLSearchParams(window.location.search);
    var selectId = params.get('select');
    if (!selectId) return;
    var card = $('#pub-' + selectId);
    if (!card.length) return;
    card.css('outline', '2px solid var(--primary)');
    card.css('scroll-margin-top', '90px');
    card[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Cuando terminen de cargarse las publicaciones, intenta enfocar el post original
$(document).ajaxStop(function() {
    enfocarPublicacionDesdeUrl();
});
