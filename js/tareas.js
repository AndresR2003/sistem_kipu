var modoVista = 'mis_tareas';
var tareaActual = 0;
var comentarioTareaId = 0;
var departamentosCache = [];
var usuariosCache = [];
var acordeonesAbiertos = {};
var tareasMap = {};

$(document).ready(function() {
    if (typeof USUARIO_ROL !== 'undefined' && (USUARIO_ROL === 'admin' || USUARIO_ROL === 'superadmin')) {
        $('#btnNuevaTarea').show();
        $('#tabTodas').show();
    }
    cargarTareas();
    cargarDepartamentos();
    cargarUsuarios();
});

function escHtml(t) {
    if (!t) return '';
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(t));
    return d.innerHTML;
}

// ─── Cargar tareas ───

function cargarTareas() {
    $.ajax({
        url: BASE_URL + '/tareas/listar',
        type: 'POST',
        data: JSON.stringify({ modo: modoVista }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            if (!res.success) {
                $('#tareasContainer').html('<div class="text-center py-4 text-muted"><i class="bi bi-exclamation-triangle"></i> Error al cargar.</div>');
                return;
            }
            renderizarTareas(res.departamentos);
        },
        error: function() {
            $('#tareasContainer').html('<div class="text-center py-4 text-muted"><i class="bi bi-exclamation-triangle"></i> Error de conexion.</div>');
        }
    });
}

// ─── Renderizar tareas en acordeones ───

function renderizarTareas(departamentos) {
    var container = $('#tareasContainer');
    container.empty();

    if (!departamentos || departamentos.length === 0) {
        container.html('<div class="text-center py-5 text-muted"><i class="bi bi-check-circle" style="font-size:2rem;opacity:0.3;"></i><p style="margin-top:8px;">No hay tareas asignadas.</p></div>');
        return;
    }

    var iconos = {
        1: 'bi-building',
        2: 'bi-cart',
        3: 'bi-headset',
        4: 'bi-tools',
        5: 'bi-box-seam'
    };
    var colores = {
        1: '#4669FA',
        2: '#22c55e',
        3: '#06b6d4',
        4: '#f59e0b',
        5: '#a855f7'
    };

    departamentos.forEach(function(dept) {
        var isOpen = acordeonesAbiertos[dept.id] !== undefined ? acordeonesAbiertos[dept.id] : true;
        var icono = iconos[dept.id] || 'bi-folder';
        var color = colores[dept.id] || '#4669FA';
        var pendClass = dept.pendientes > 0 ? '' : ' vacio';
        var pendText = dept.pendientes > 0 ? dept.pendientes + ' pendiente' + (dept.pendientes > 1 ? 's' : '') : 'Completado';

        var html = '<div class="tarea-acordeon' + (isOpen ? ' open' : '') + '" data-dept="' + dept.id + '">' +
            '<div class="tarea-acordeon-header" onclick="toggleAcordeon(' + dept.id + ')">' +
            '<div class="tarea-acordeon-icon" style="background:' + color + '20;color:' + color + ';">' +
            '<i class="bi ' + icono + '"></i></div>' +
            '<div class="tarea-acordeon-nombre">' + escHtml(dept.nombre) + '</div>' +
            '<span class="tarea-acordeon-pendientes' + pendClass + '">' + pendText + '</span>' +
            '<i class="bi bi-chevron-down tarea-acordeon-chevron"></i>' +
            '</div>' +
            '<div class="tarea-acordeon-body">';

        if (dept.tareas) {
            dept.tareas.forEach(function(t) {
                html += renderizarTarjeta(t);
            });
        }

        html += '</div></div>';
        container.append(html);
    });
}

// ─── Renderizar tarjeta individual ───

function renderizarTarjeta(t) {
    tareasMap[t.id] = t;

    var completada = parseInt(t.completada) === 1;
    var miAsignacion = t.mi_asignacion != null ? parseInt(t.mi_asignacion) : null;
    var miCompletado = t.mi_completado_at || null;
    var claseCompletada = completada ? ' completada' : '';

    var checkbox = '';
    if (t.modalidad === 'single_completes_all') {
        var checked = completada ? ' checked' : '';
        checkbox = '<input type="checkbox"' + checked + ' onchange="toggleCompletar(' + t.id + ', this.checked)" ' + (completada ? 'disabled' : '') + '>';
    } else {
        var mine = miAsignacion === 1;
        checkbox = '<input type="checkbox"' + (mine ? ' checked' : '') + ' onchange="toggleCompletar(' + t.id + ', this.checked)">';
    }

    var badgePrioridad = '';
    if (t.prioridad === 'alta') badgePrioridad = '<span class="tarea-badge badge-alta"><i class="bi bi-arrow-up"></i> Alta</span>';
    else if (t.prioridad === 'media') badgePrioridad = '<span class="tarea-badge badge-media"><i class="bi bi-dash"></i> Media</span>';
    else badgePrioridad = '<span class="tarea-badge badge-baja"><i class="bi bi-arrow-down"></i> Baja</span>';

    var fechaHtml = '';
    if (t.fecha_limite) {
        var fechaLimite = new Date(t.fecha_limite);
        var ahora = new Date();
        var diffMs = fechaLimite - ahora;
        var diffHoras = diffMs / (1000 * 60 * 60);
        var fechaStr = '';
        var fechaClass = '';

        if (diffMs < 0 && !completada) {
            fechaStr = 'Vencida';
            fechaClass = ' vencida';
        } else if (diffHoras < 24 && diffHoras >= 0) {
            fechaStr = 'Hoy, ' + fechaLimite.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
            fechaClass = ' hoy';
        } else {
            fechaStr = fechaLimite.toLocaleDateString('es-PE', { day: 'numeric', month: 'short' }) +
                ', ' + fechaLimite.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
        }
        fechaHtml = '<span class="tarea-fecha' + fechaClass + '"><i class="bi bi-clock"></i> ' + fechaStr + '</span>';
    }

    var estadoHtml = '';
    if (t.modalidad === 'single_completes_all') {
        if (completada && t.completada_por_nombre) {
            var hora = t.completada_at ? new Date(t.completada_at).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' }) : '';
            estadoHtml = '<div class="tarea-estado"><span class="completado-por"><i class="bi bi-check-circle-fill"></i> Completada por ' + escHtml(t.completada_por_nombre) + (hora ? ' &bull; ' + hora : '') + '</span></div>';
        } else if (!completada) {
            estadoHtml = '<div class="tarea-estado"><span class="solo-mi"><i class="bi bi-person"></i> Solo para mi</span></div>';
        }
    } else {
        var total = parseInt(t.total_asignados) || 0;
        var comps = parseInt(t.total_completados) || 0;
        if (completada) {
            estadoHtml = '<div class="tarea-estado"><span class="completado-por"><i class="bi bi-check-circle-fill"></i> Completada por todos (' + total + '/' + total + ')</span></div>';
        } else if (total > 0) {
            estadoHtml = '<div class="tarea-estado"><span class="progreso"><i class="bi bi-people"></i> Progreso: ' + comps + '/' + total + ' completadas</span></div>';
        }
    }

    var esAdmin = typeof USUARIO_ROL !== 'undefined' && (USUARIO_ROL === 'admin' || USUARIO_ROL === 'superadmin');
    var comCount = parseInt(t.comentarios_count) || 0;

    var adminAcciones = '';
    if (esAdmin) {
        adminAcciones += '<span class="tarea-accion-sep"></span>' +
            '<button class="tarea-accion edt" onclick="abrirModalEditar(' + t.id + ')" title="Editar"><i class="bi bi-pencil"></i> Editar</button>';
        if (parseInt(t.publicado)) {
            adminAcciones += '<button class="tarea-accion eye" onclick="despublicarTarea(' + t.id + ')" title="Despublicar"><i class="bi bi-eye-slash"></i> Despublicar</button>';
        } else {
            adminAcciones += '<button class="tarea-accion eye" onclick="publicarTarea(' + t.id + ')" title="Publicar"><i class="bi bi-eye"></i> Publicar</button>';
        }
        adminAcciones += '<button class="tarea-accion del" onclick="eliminarTarea(' + t.id + ')" title="Eliminar"><i class="bi bi-trash"></i> Eliminar</button>';
    }

    var html = '<div class="tarea-card' + claseCompletada + '" data-id="' + t.id + '" data-origen="tarea" data-origen-id="' + t.id + '" data-seccion="tareas">' +
        '<div class="tarea-check">' + checkbox + '</div>' +
        '<div class="tarea-info">' +
        '<div class="tarea-titulo">' + escHtml(t.titulo) + '</div>' +
        '<div class="tarea-meta">' + badgePrioridad + ' ' + fechaHtml + '</div>' +
        estadoHtml +
        '</div>' +
        '<div class="tarea-acciones">' +
        '<button class="tarea-accion rec" onclick="guardarComoTarea(\'recordatorio\', ' + t.id + ')" title="Agregar a Recordatorio"><i class="bi bi-bell-fill"></i> Recordatorio</button>' +
        '<button class="tarea-accion mar" onclick="guardarComoTarea(\'marcador\', ' + t.id + ')" title="Agregar a Marcadores"><i class="bi bi-bookmark-fill"></i> Marcador</button>' +
        '<button class="tarea-accion com" onclick="abrirComentarios(' + t.id + ')" title="Ver comentarios"><i class="bi bi-chat-fill"></i> Comentarios' + (comCount > 0 ? '<span class="tarea-com-count">' + comCount + '</span>' : '') + '</button>' +
        adminAcciones +
        '</div>' +
        '</div>';

    return html;
}

// ─── Toggle acordeón ───

function toggleAcordeon(deptId) {
    var el = $('.tarea-acordeon[data-dept="' + deptId + '"]');
    el.toggleClass('open');
    acordeonesAbiertos[deptId] = el.hasClass('open');
}

// ─── Cambiar pestaña ───

function cambiarTab(btn) {
    $('.tareas-tab').removeClass('active');
    $(btn).addClass('active');
    modoVista = $(btn).data('modo');
    cargarTareas();
}

// ─── Completar / Descompletar ───

function toggleCompletar(id, checked) {
    var url = checked ? BASE_URL + '/tareas/completar/' + id : BASE_URL + '/tareas/descompletar/' + id;
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                cargarTareas();
            } else {
                Swal.fire('Aviso', res.message, 'warning');
                cargarTareas();
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexion', 'error');
            cargarTareas();
        }
    });
}

// ─── Modal nueva tarea ───

function abrirModalNueva() {
    $('#tituloModalTarea').html('<i class="bi bi-plus-circle"></i> Nueva tarea');
    $('#tareaId').val('');
    $('#tareaTitulo').val('');
    $('#tareaDescripcion').val('');
    $('#tareaPrioridad').val('media');
    $('#tareaFechaLimite').val('');
    $('#tareaDepartamento').val('');
    $('input[name="modalidad"][value="single_completes_all"]').prop('checked', true);
    $('#tareaDestinatarioTipo').val('todos');
    $('#tareaDestinatarioId').hide().val('');
    $('#tareaPublicar').prop('checked', false);
    $('#seccionAsignados').hide();
    $('#modalTarea').modal('show');
}

// ─── Modal editar tarea ───

function abrirModalEditar(id) {
    $.ajax({
        url: BASE_URL + '/tareas/obtener/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (!res.success || !res.data) {
                Swal.fire('Error', 'Tarea no encontrada', 'error');
                return;
            }
            var t = res.data;
            $('#tituloModalTarea').html('<i class="bi bi-pencil"></i> Editar tarea');
            $('#tareaId').val(t.id);
            $('#tareaTitulo').val(t.titulo);
            $('#tareaDescripcion').val(t.descripcion || '');
            $('#tareaPrioridad').val(t.prioridad);
            $('#tareaFechaLimite').val(t.fecha_limite ? t.fecha_limite.replace(' ', 'T').substring(0, 16) : '');
            $('#tareaDepartamento').val(t.departamento_id || '');
            $('input[name="modalidad"][value="' + t.modalidad + '"]').prop('checked', true);
            $('#tareaDestinatarioTipo').val(t.destinatario_tipo);
            toggleDestinatarioId();
            if (t.destinatario_id) {
                setTimeout(function() { $('#tareaDestinatarioId').val(t.destinatario_id); }, 100);
            }
            $('#tareaPublicar').prop('checked', parseInt(t.publicado) === 1);
            toggleAsignados();

            if (t.asignaciones && t.asignaciones.length > 0) {
                var uids = t.asignaciones.map(function(a) { return a.usuario_id; });
                $('#listaAsignados input[type="checkbox"]').each(function() {
                    $(this).prop('checked', uids.indexOf(parseInt($(this).val())) !== -1);
                });
            }

            $('#modalTarea').modal('show');
        }
    });
}

// ─── Guardar tarea ───

function guardarTarea() {
    var titulo = $('#tareaTitulo').val().trim();
    if (!titulo) {
        Swal.fire('Error', 'El titulo es obligatorio', 'error');
        return;
    }

    var modalidad = $('input[name="modalidad"]:checked').val();
    var asignados = [];
    if (modalidad === 'all_must_complete') {
        $('#listaAsignados input[type="checkbox"]:checked').each(function() {
            asignados.push(parseInt($(this).val()));
        });
        if (asignados.length === 0) {
            Swal.fire('Error', 'Debes asignar al menos un usuario', 'error');
            return;
        }
    }

    var fechaLimite = $('#tareaFechaLimite').val();
    if (fechaLimite) {
        fechaLimite = fechaLimite.replace('T', ' ');
    }

    var datos = {
        id: $('#tareaId').val() || null,
        titulo: titulo,
        descripcion: $('#tareaDescripcion').val(),
        prioridad: $('#tareaPrioridad').val(),
        fecha_limite: fechaLimite || null,
        modalidad: modalidad,
        departamento_id: $('#tareaDepartamento').val() || null,
        destinatario_tipo: $('#tareaDestinatarioTipo').val(),
        destinatario_id: $('#tareaDestinatarioId').val() || null,
        publicado: $('#tareaPublicar').is(':checked') ? 1 : 0,
        asignados: asignados
    };

    showLoading();
    $.ajax({
        url: BASE_URL + '/tareas/guardar',
        type: 'POST',
        data: JSON.stringify(datos),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.success) {
                $('#modalTarea').modal('hide');
                Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                cargarTareas();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion', 'error');
        }
    });
}

// ─── Publicar / Despublicar ───

function publicarTarea(id) {
    $.ajax({
        url: BASE_URL + '/tareas/publicar/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Publicada', timer: 1200, showConfirmButton: false });
                cargarTareas();
            }
        }
    });
}

function despublicarTarea(id) {
    $.ajax({
        url: BASE_URL + '/tareas/despublicar/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                Swal.fire({ icon: 'info', title: 'Despublicada', timer: 1200, showConfirmButton: false });
                cargarTareas();
            }
        }
    });
}

// ─── Eliminar tarea ───

function eliminarTarea(id) {
    Swal.fire({
        title: 'Eliminar tarea',
        text: 'Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + '/tareas/eliminar/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Eliminada', timer: 1200, showConfirmButton: false });
                        cargarTareas();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}

// ─── Toggle asignados ───

function toggleAsignados() {
    var modalidad = $('input[name="modalidad"]:checked').val();
    if (modalidad === 'all_must_complete') {
        $('#seccionAsignados').show();
    } else {
        $('#seccionAsignados').hide();
    }
}

// ─── Toggle destinatario id ───

function toggleDestinatarioId() {
    var tipo = $('#tareaDestinatarioTipo').val();
    var select = $('#tareaDestinatarioId');
    select.empty().append('<option value="">Seleccionar...</option>');

    if (tipo === 'departamento') {
        departamentosCache.forEach(function(d) {
            select.append('<option value="' + d.id + '">' + escHtml(d.descripcion) + '</option>');
        });
        select.show();
    } else if (tipo === 'usuarios') {
        usuariosCache.forEach(function(u) {
            select.append('<option value="' + u.id + '">' + escHtml(u.nombre) + '</option>');
        });
        select.show();
    } else {
        select.hide();
    }
}

// ─── Cargar departamentos ───

function cargarDepartamentos() {
    $.ajax({
        url: BASE_URL + '/tareas/departamentos',
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                departamentosCache = res.data;
                var select = $('#tareaDepartamento');
                res.data.forEach(function(d) {
                    select.append('<option value="' + d.id + '">' + escHtml(d.descripcion) + '</option>');
                });
            }
        }
    });
}

// ─── Cargar usuarios ───

function cargarUsuarios() {
    $.ajax({
        url: BASE_URL + '/api/listar-usuarios',
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                usuariosCache = res.data;
                var container = $('#listaAsignados');
                res.data.forEach(function(u) {
                    container.append(
                        '<label class="tarea-modal-asignado">' +
                        '<input type="checkbox" value="' + u.id + '">' +
                        escHtml(u.nombre) +
                        '</label>'
                    );
                });
            }
        }
    });
}

// ─── Filtros (placeholder) ───

function toggleFiltros() {
    Swal.fire({
        title: 'Filtros',
        html: '<div style="text-align:left;">' +
            '<label style="font-size:0.82rem;font-weight:600;">Departamento</label>' +
            '<select id="filtroDeptSwal" class="form-select" style="font-size:0.85rem;margin-bottom:12px;">' +
            '<option value="">Todos</option>' +
            departamentosCache.map(function(d) { return '<option value="' + d.id + '">' + escHtml(d.descripcion) + '</option>'; }).join('') +
            '</select>' +
            '<label style="font-size:0.82rem;font-weight:600;">Prioridad</label>' +
            '<select id="filtroPriorSwal" class="form-select" style="font-size:0.85rem;">' +
            '<option value="">Todas</option>' +
            '<option value="alta">Alta</option>' +
            '<option value="media">Media</option>' +
            '<option value="baja">Baja</option>' +
            '</select>' +
            '</div>',
        showCancelButton: true,
        confirmButtonText: 'Aplicar',
        cancelButtonText: 'Limpiar',
        confirmButtonColor: 'var(--primary)',
        preConfirm: function() {
            return {
                dept: $('#filtroDeptSwal').val(),
                prioridad: $('#filtroPriorSwal').val()
            };
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            cargarTareas();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            cargarTareas();
        }
    });
}

// ─── Comentarios ───

function abrirComentarios(id) {
    comentarioTareaId = id;
    $('#comentarioInput').val('');
    $('#comentariosLista').html('<div class="text-center py-2"><div class="spinner-border spinner-border-sm"></div></div>');
    $('#modalComentarios').modal('show');

    $.ajax({
        url: BASE_URL + '/tareas/listar-comentarios/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            var container = $('#comentariosLista');
            container.empty();
            if (!res.success || !res.data || res.data.length === 0) {
                container.html('<div class="tarea-comentario-vacio">No hay comentarios aun.</div>');
                return;
            }
            res.data.forEach(function(c) {
                var iniciales = (c.autor_nombre || '?').substring(0, 2).toUpperCase();
                var fotoHtml = c.autor_foto
                    ? '<img src="' + c.autor_foto + '" alt="">'
                    : iniciales;
                container.append(
                    '<div class="tarea-comentario">' +
                    '<div class="tarea-comentario-avatar">' + fotoHtml + '</div>' +
                    '<div class="tarea-comentario-body">' +
                    '<span class="tarea-comentario-autor">' + escHtml(c.autor_nombre) +
                    '<span class="tarea-comentario-fecha">' + new Date(c.created_at).toLocaleString('es-PE') + '</span></span>' +
                    '<div class="tarea-comentario-texto">' + escHtml(c.comentario) + '</div>' +
                    '</div></div>'
                );
            });
        }
    });
}

function guardarComentarioTarea() {
    var texto = $('#comentarioInput').val().trim();
    if (!texto) return;

    $.ajax({
        url: BASE_URL + '/tareas/guardar-comentario',
        type: 'POST',
        data: JSON.stringify({ tarea_id: comentarioTareaId, comentario: texto }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#comentarioInput').val('');
                abrirComentarios(comentarioTareaId);
            }
        }
    });
}

// ─── Guardar como recordatorio / marcador ───

function guardarComoTarea(tipo, id) {
    var t = tareasMap[id];
    if (!t) return;

    var data = {
        titulo: t.titulo,
        descripcion: t.descripcion || '',
        tipo: tipo === 'marcador' ? 'marcador' : 'recordatorio',
        origen_tipo: 'tarea',
        origen_id: t.id,
        seccion: 'tareas',
        fecha: new Date().toISOString().slice(0, 10),
    };

    if (tipo === 'recordatorio') {
        data.prioridad = 'media';
    }

    Swal.fire({
        title: tipo === 'marcador' ? 'Agregar a Marcadores' : 'Agregar a Recordatorio',
        text: 'Se guardara la tarea completa.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (!result.isConfirmed) return;
        showLoading();
        $.ajax({
            url: BASE_URL + '/recordatorio/guardar',
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

// ─── Enfocar tarea desde URL (?select=ID) ───

function enfocarTareaDesdeUrl() {
    var params = new URLSearchParams(window.location.search);
    var selectId = params.get('select');
    if (!selectId) return;

    var card = $('.tarea-card[data-id="' + selectId + '"]');
    if (!card.length) {
        setTimeout(enfocarTareaDesdeUrl, 300);
        return;
    }

    card.closest('.tarea-acordeon').addClass('open');
    card.css('outline', '2px solid var(--primary)');
    card.css('scroll-margin-top', '90px');
    card[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
}

$(document).ready(function() {
    setTimeout(enfocarTareaDesdeUrl, 600);
});
