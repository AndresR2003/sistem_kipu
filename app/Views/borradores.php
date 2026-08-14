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

/* Tabs de Borradores */
.brd-tabs{display:flex;gap:4px;border-bottom:1px solid var(--border);padding:0 14px;background:var(--bg-card);}
.brd-tabs button{background:transparent;border:none;padding:11px 16px;font-size:0.8rem;color:var(--text-muted);border-bottom:2px solid transparent;transition:all 0.15s;display:inline-flex;align-items:center;gap:7px;cursor:pointer;}
.brd-tabs button:hover{color:var(--text);}
.brd-tabs button.active{color:var(--primary);border-bottom-color:var(--primary);font-weight:600;}
.brd-tabpane{display:none;}
.brd-tabpane.active{display:block;}

/* Pase de turno */
.ent-header{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:linear-gradient(135deg,rgba(70,105,250,0.09),transparent 60%);}
.ent-sub{font-size:0.8rem;color:var(--text-muted);margin-top:2px;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;padding:12px 14px 4px;}
.stat-card{background:var(--bg-card-alt);border:1px solid var(--border);border-radius:var(--radius);padding:11px 14px;display:flex;align-items:center;gap:12px;transition:all 0.2s;}
.stat-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:var(--shadow);}
.stat-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.05rem;flex-shrink:0;}
.stat-icon.blue{background:rgba(70,105,250,0.12);color:var(--primary);}
.stat-icon.green{background:rgba(34,197,94,0.12);color:#22c55e;}
.stat-icon.amber{background:rgba(245,158,11,0.12);color:#f59e0b;}
.stat-icon.gray{background:rgba(148,163,184,0.12);color:#94a3b8;}
.stat-num{font-size:1.25rem;font-weight:700;color:var(--text);line-height:1;}
.stat-label{font-size:0.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:4px;}
.nav-tabs-lito{display:flex;gap:4px;border-bottom:1px solid var(--border);padding:0 14px;}
.nav-tabs-lito button{background:transparent;border:none;padding:10px 14px;font-size:0.8rem;color:var(--text-muted);border-bottom:2px solid transparent;transition:all 0.15s;display:inline-flex;align-items:center;gap:7px;}
.nav-tabs-lito button:hover{color:var(--text);}
.nav-tabs-lito button.active{color:var(--primary);border-bottom-color:var(--primary);font-weight:600;}
.tab-pane{padding:14px;}
.badge-estado.pub{background:rgba(34,197,94,0.12);color:#22c55e;border:1px solid rgba(34,197,94,0.18);}
.badge-estado.despub{background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.18);}
.badge-dest{font-size:0.6rem;padding:3px 10px;border-radius:14px;font-weight:600;background:rgba(70,105,250,0.12);color:var(--primary);border:1px solid rgba(70,105,250,0.18);display:inline-flex;align-items:center;gap:5px;white-space:nowrap;}
.btn-sm-icon.edt:hover{background:rgba(70,105,250,0.15);color:var(--primary);}
.btn-sm-icon.eye:hover{background:rgba(34,197,94,0.15);color:#22c55e;}
.btn-sm-icon.del:hover{background:rgba(239,68,68,0.15);color:#ef4444;}
.tabla-vacia{text-align:center;padding:32px 16px;color:var(--text-muted);}
.tabla-vacia i{font-size:2rem;display:block;margin-bottom:8px;opacity:0.4;}
.tabla-vacia p{margin-bottom:0;font-size:0.85rem;}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div class="brd-tabs">
        <button class="active" data-tab="borradores" onclick="cambiarTabBorradores('borradores', this)">
            <i class="bi bi-pencil-fill"></i> Borradores
        </button>
        <button data-tab="pase" onclick="cambiarTabBorradores('pase', this)">
            <i class="bi bi-arrow-left-right"></i> Pase de turno
        </button>
    </div>

    <!-- TAB BORRADORES -->
    <div class="brd-tabpane active" id="paneBorradores">
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

    <!-- TAB PASE DE TURNO -->
    <div class="brd-tabpane" id="panePase" style="min-height:500px;display:none;">
        <div class="ent-header">
            <div>
                <h5 class="mb-0" style="font-size:1rem;"><i class="bi bi-arrow-left-right" style="color:var(--primary);"></i> Pases de turno</h5>
                <div class="ent-sub"><i class="bi bi-shield-lock-fill"></i> Administracion de tareas diarias y revision de cumplimiento</div>
            </div>
        </div>

        <div class="stats-grid" id="statsEntregas">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-list-task"></i></div>
                <div>
                    <div class="stat-num" id="statTotal">0</div>
                    <div class="stat-label">Tareas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-num" id="statPub">0</div>
                    <div class="stat-label">Publicadas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gray"><i class="bi bi-eye-slash-fill"></i></div>
                <div>
                    <div class="stat-num" id="statOcultas">0</div>
                    <div class="stat-label">Ocultas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="bi bi-clipboard-check-fill"></i></div>
                <div>
                    <div class="stat-num" id="statHoy">0</div>
                    <div class="stat-label">Realizadas hoy</div>
                </div>
            </div>
        </div>

        <div class="nav-tabs-lito" id="entTabs">
            <button class="active" onclick="cambiarTabEntregas('tareas', this)"><i class="bi bi-list-task"></i> Tareas</button>
            <button onclick="cambiarTabEntregas('registros', this)"><i class="bi bi-clipboard-check"></i> Revision de tareas realizadas</button>
        </div>

        <!-- TAB TAREAS -->
        <div class="tab-pane" id="tabTareas">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h6 class="mb-0" style="font-size:0.9rem;"><i class="bi bi-arrow-repeat" style="color:var(--primary);"></i> Tareas preseleccionadas</h6>
                <button class="btn btn-primary-custom btn-sm" onclick="nuevaTarea()">
                    <i class="bi bi-plus-lg"></i> Nueva tarea
                </button>
            </div>

            <div class="table-responsive" style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
                <table class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Repite</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Asignada a</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyEntregas"></tbody>
                </table>
            </div>
        </div>

        <!-- TAB REGISTROS -->
        <div class="tab-pane" id="tabRegistros" style="display:none;">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h6 class="mb-0" style="font-size:0.9rem;"><i class="bi bi-clipboard-check" style="color:var(--primary);"></i> Revision de tareas realizadas</h6>
                <div class="d-flex gap-2">
                    <input type="date" class="form-control form-control-sm" id="filtroRegInicio">
                    <input type="date" class="form-control form-control-sm" id="filtroRegFin">
                    <button class="btn btn-sm btn-outline-secondary" onclick="cargarRegistros()"><i class="bi bi-search"></i> Filtrar</button>
                </div>
            </div>

            <div class="table-responsive" style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
                <table class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tarea</th>
                            <th>Realizado por</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyRegistros"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva/Editar pase de turno -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalTareaTitulo"><i class="bi bi-plus-circle"></i> Nueva tarea</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tareaId">
                <div class="mb-3">
                    <label class="form-label">Titulo de la tarea</label>
                    <input type="text" class="form-control" id="tareaTitulo" placeholder="Ej: Revisar habitaciones">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripcion</label>
                    <textarea class="form-control" id="tareaDescripcion" rows="3" placeholder="Detalle de la tarea..."></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Fecha de inicio</label>
                        <input type="date" class="form-control" id="tareaInicio">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Fecha de fin <span class="text-muted">(opcional)</span></label>
                        <input type="date" class="form-control" id="tareaFin">
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">Asignada a</label>
                    <select class="form-select" id="tareaTipo" onchange="toggleTareaDestinatario()">
                        <option value="todos">Todos</option>
                        <option value="usuarios">Usuarios</option>
                        <option value="departamento">Departamento</option>
                    </select>
                </div>
                <div class="mb-3" id="tareaDestinatarioWrap" style="display:none;">
                    <select class="form-select" id="tareaDestinatario"></select>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" id="tareaRepetir" checked>
                    <label class="form-check-label" for="tareaRepetir">Repetir diariamente</label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="tareaPublicado" checked>
                    <label class="form-check-label" for="tareaPublicado">Publicada (visible en Tareas)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary-custom btn-sm" onclick="guardarTarea()"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Publicar pase de turno -->
<div class="modal fade" id="modalPublicarEntrega" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-send-fill" style="color:var(--success);"></i> Publicar tarea</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pubTareaId">
                <div class="mb-2">
                    <label class="form-label small">Dirigido a</label>
                    <select class="form-select" id="pubTipo" onchange="togglePubDestinatario()">
                        <option value="todos">Todos</option>
                        <option value="usuarios">Usuarios</option>
                        <option value="departamento">Departamento</option>
                    </select>
                </div>
                <div class="mb-2" id="pubDestinatarioWrap" style="display:none;">
                    <select class="form-select" id="pubDestinatario"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success btn-sm" onclick="confirmarPublicarEntrega()"><i class="bi bi-send-fill"></i> Publicar</button>
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

function cambiarTabBorradores(nombre, btn) {
    $('.brd-tabs button').removeClass('active');
    $(btn).addClass('active');
    $('.brd-tabpane').removeClass('active');
    $('#pane' + nombre.charAt(0).toUpperCase() + nombre.slice(1)).addClass('active');
}

function cambiarTabEntregas(tab, btn) {
    $('#entTabs button').removeClass('active');
    $(btn).addClass('active');
    if (tab === 'tareas') {
        $('#tabTareas').show();
        $('#tabRegistros').hide();
    } else {
        $('#tabTareas').hide();
        $('#tabRegistros').show();
    }
}

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
