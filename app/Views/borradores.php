<style>
.borrador-item{padding:12px 16px;border-bottom:1px solid var(--border);border-left:3px solid transparent;cursor:pointer;transition:background 0.15s,border-color 0.15s;}
.borrador-item.pub{border-left-color:#22c55e;}
.borrador-item:hover{background:var(--bg-input);}
.borrador-item:active{background:var(--bg-input-hover);}
.btn-sm-icon{background:transparent;border:none;padding:4px 8px;color:var(--text);border-radius:6px;font-size:0.85rem;transition:background 0.15s;}
.btn-sm-icon:hover{background:var(--bg-input);}
.btn-primary-custom{background:var(--primary);color:#fff;border:none;}
.btn-primary-custom:hover{background:var(--primary-dark);color:#fff;}
.badge-pub{font-size:0.6rem;padding:2px 8px;border-radius:8px;margin-left:6px;font-weight:600;}
.badge-pub.si{background:rgba(34,197,94,0.15);color:#22c55e;}
.badge-pub.no{background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);}
.pub-checks{max-height:130px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius);padding:6px 8px;display:flex;flex-direction:column;gap:2px;}
.pub-check{display:flex;align-items:center;gap:8px;font-size:0.78rem;color:var(--text);padding:2px 4px;border-radius:4px;cursor:pointer;}
.pub-check:hover{background:var(--bg-input);}
.pub-check input{accent-color:var(--primary);}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div style="display:flex;flex-wrap:wrap;min-height:500px;">
        <!-- Panel izquierdo - Lista -->
        <div style="flex:1;min-width:280px;border-right:1px solid var(--border);">
            <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <h5 class="mb-0" style="font-size:0.95rem;"><i class="bi bi-pencil-fill"></i> Borradores</h5>
                <button class="btn btn-primary-custom btn-sm" onclick="nuevoBorrador()">
                    <i class="bi bi-plus-lg"></i> Nuevo
                </button>
            </div>
            <div style="padding:10px 14px;border-bottom:1px solid var(--border);">
                <input type="text" class="form-control form-control-sm" id="buscarBorrador" placeholder="Buscar borrador..." oninput="filtrarBorradores()">
            </div>
            <div id="listaBorradores" style="overflow-y:auto;max-height:500px;"></div>
            <div id="sinBorradores" class="text-center py-5" style="display:none;">
                <i class="bi bi-inbox" style="font-size:2.5rem;color:var(--text-muted);"></i>
                <p class="text-muted mt-2 mb-0 small">No hay borradores</p>
            </div>
        </div>

        <!-- Panel derecho - Editor -->
        <div style="flex:2;min-width:300px;display:flex;flex-direction:column;" id="panelEditor">
            <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <h6 class="mb-0" id="tituloEditor" style="font-size:0.9rem;color:var(--text-muted);">
                    <i class="bi bi-pencil-square"></i> Selecciona un borrador
                    <span id="pubStatusBadge" style="display:none;margin-left:8px;"></span>
                </h6>
                <div class="d-flex gap-1" id="accionesEditor" style="display:none !important;">
                    <button class="btn-sm-icon" onclick="publicarBorrador()" id="btnPublicar" title="Publicar" style="color:var(--success);"><i class="bi bi-send-fill"></i></button>
                    <button class="btn-sm-icon" onclick="despublicarBorrador()" id="btnDespublicar" title="Retirar publicacion" style="color:var(--warning);display:none;"><i class="bi bi-send-x-fill"></i></button>
                    <button class="btn-sm-icon" onclick="fijarBorrador()" id="btnFijar" title="Fijar"><i class="bi bi-pin-fill"></i></button>
                    <button class="btn-sm-icon" onclick="eliminarBorrador()" id="btnEliminar" title="Eliminar" style="color:var(--danger);"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            <div id="editorContenido" style="flex:1;padding:18px;display:none;">
                <input type="hidden" id="borradorId" value="">
                <div class="mb-3">
                    <input type="text" class="form-control" id="borradorTitulo" placeholder="Asunto" style="font-weight:600;font-size:1.05rem;">
                </div>
                <div class="mb-3">
                    <textarea class="form-control" id="borradorContenido" rows="14" placeholder="Escribe tu contenido aqui..." style="resize:vertical;min-height:200px;font-size:0.9rem;line-height:1.6;"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary-custom btn-sm" onclick="guardarBorrador()">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                    <button class="btn btn-success btn-sm" onclick="publicarBorrador()" id="btnPubAccion">
                        <i class="bi bi-send-fill"></i> Publicar
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="cerrarEditor()">
                        <i class="bi bi-x-lg"></i> Cerrar
                    </button>
                </div>
            </div>
            <div id="editorVacio" style="flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;">
                <i class="bi bi-pencil-square" style="font-size:3rem;color:var(--text-muted);opacity:0.3;"></i>
                <p class="text-muted small">Selecciona un borrador o crea uno nuevo</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Publicar -->
<div class="modal fade" id="modalPublicar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-send-fill" style="color:var(--success);"></i> Publicar</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pubBorradorId">
                <div class="mb-2">
                    <label class="form-label small">Mostrar en</label>
                    <select class="form-select" id="pubSeccion" onchange="toggleAnuncioCheck()">
                        <option value="noticias">Noticias</option>
                        <option value="ideas">Ideas</option>
                        <option value="manual">Manual</option>
                        <option value="tareas">Tareas</option>
                    </select>
                </div>
                <div class="mb-2" id="anuncioCheckWrap">
                    <label class="form-check d-flex align-items-center gap-2">
                        <input type="checkbox" class="form-check-input" id="pubAnuncio" style="margin-top:0;">
                        <span class="small"><i class="bi bi-megaphone-fill" style="color:var(--warning);"></i> Mostrar como anuncio en la campanita y barra superior</span>
                    </label>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Departamentos <span class="text-muted">(puedes elegir varios)</span></label>
                    <div class="pub-checks" id="pubDepartamentos"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Usuarios <span class="text-muted">(puedes elegir varios)</span></label>
                    <div class="pub-checks" id="pubUsuarios"></div>
                </div>
                <div class="small text-muted"><i class="bi bi-info-circle"></i> Si no eliges departamentos ni usuarios, se publica para todos.</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success btn-sm" onclick="confirmarPublicar()"><i class="bi bi-send-fill"></i> Publicar</button>
            </div>
        </div>
    </div>
</div>

<script>
var destinatariosCache = null;

function llenarChecks() {
    if (!destinatariosCache) {
        $.ajax({
            url: BASE_URL + 'borradores/destinatarios',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                destinatariosCache = data;
                llenarChecks();
            }
        });
        return;
    }

    var dep = $('#pubDepartamentos');
    dep.empty();
    (destinatariosCache.departamentos || []).forEach(function(d) {
        dep.append(
            '<label class="pub-check"><input type="checkbox" value="' + d.id + '"> ' + escHtml(d.descripcion) + '</label>'
        );
    });

    var usr = $('#pubUsuarios');
    usr.empty();
    (destinatariosCache.usuarios || []).forEach(function(u) {
        usr.append(
            '<label class="pub-check"><input type="checkbox" value="' + u.id + '"> ' + escHtml(u.nombre) + '</label>'
        );
    });
}

function publicarBorrador() {
    var id = $('#borradorId').val();
    if (!id) { Swal.fire('Aviso', 'Guarda el borrador primero.', 'info'); return; }
    $('#pubBorradorId').val(id);
    $('#pubSeccion').val('noticias');
    $('#pubDepartamentos input').prop('checked', false);
    $('#pubUsuarios input').prop('checked', false);
    $('#pubAnuncio').prop('checked', false);
    llenarChecks();
    toggleAnuncioCheck();
    $('#modalPublicar').modal('show');
}

function toggleAnuncioCheck() {
    var seccion = $('#pubSeccion').val();
    if (seccion === 'noticias') {
        $('#anuncioCheckWrap').show();
    } else {
        $('#anuncioCheckWrap').hide();
        $('#pubAnuncio').prop('checked', false);
    }
}

function confirmarPublicar() {
    var id = parseInt($('#pubBorradorId').val());
    var seccion = $('#pubSeccion').val();
    var departamentos = [];
    $('#pubDepartamentos input:checked').each(function() {
        departamentos.push(parseInt(this.value));
    });
    var usuarios = [];
    $('#pubUsuarios input:checked').each(function() {
        usuarios.push(parseInt(this.value));
    });
    var anuncio = $('#pubAnuncio').is(':checked') ? 1 : 0;

    if (seccion === 'tareas' && departamentos.length === 0) {
        Swal.fire('Validacion', 'Para Tareas debes seleccionar al menos un departamento.', 'warning');
        return;
    }

    showLoading();
    $.ajax({
        url: BASE_URL + 'borradores/publicar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id: id,
            seccion: seccion,
            departamentos: departamentos,
            usuarios: usuarios,
            anuncio: anuncio,
        }),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                $('#modalPublicar').modal('hide');
                Swal.fire({ icon: 'success', title: 'Publicado', text: 'El contenido se publico en ' + seccion, timer: 1500, showConfirmButton: false });
                cargarBorradores();
                verificarNotificaciones();
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
</script>
