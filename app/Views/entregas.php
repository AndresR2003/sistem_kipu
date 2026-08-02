<style>
.ent-header{padding:20px 22px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:linear-gradient(135deg,rgba(70,105,250,0.09),transparent 60%);}
.ent-sub{font-size:0.8rem;color:var(--text-muted);margin-top:2px;}

.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;padding:18px 18px 6px;}
.stat-card{background:var(--bg-card-alt);border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px;display:flex;align-items:center;gap:14px;transition:all 0.2s;}
.stat-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:var(--shadow);}
.stat-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.stat-icon.blue{background:rgba(70,105,250,0.12);color:var(--primary);}
.stat-icon.green{background:rgba(34,197,94,0.12);color:#22c55e;}
.stat-icon.amber{background:rgba(245,158,11,0.12);color:#f59e0b;}
.stat-icon.gray{background:rgba(148,163,184,0.12);color:#94a3b8;}
.stat-num{font-size:1.4rem;font-weight:700;color:var(--text);line-height:1;}
.stat-label{font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:4px;}

.nav-tabs-lito{display:flex;gap:4px;border-bottom:1px solid var(--border);padding:0 18px;}
.nav-tabs-lito button{background:transparent;border:none;padding:13px 16px;font-size:0.85rem;color:var(--text-muted);border-bottom:2px solid transparent;transition:all 0.15s;display:inline-flex;align-items:center;gap:7px;}
.nav-tabs-lito button:hover{color:var(--text);}
.nav-tabs-lito button.active{color:var(--primary);border-bottom-color:var(--primary);font-weight:600;}
.tab-pane{padding:18px;}

.badge-estado.pub{background:rgba(34,197,94,0.12);color:#22c55e;border:1px solid rgba(34,197,94,0.18);}
.badge-estado.despub{background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.18);}
.badge-dest{font-size:0.6rem;padding:3px 10px;border-radius:14px;font-weight:600;background:rgba(70,105,250,0.12);color:var(--primary);border:1px solid rgba(70,105,250,0.18);display:inline-flex;align-items:center;gap:5px;white-space:nowrap;}

.btn-sm-icon.edt:hover{background:rgba(70,105,250,0.15);color:var(--primary);}
.btn-sm-icon.eye:hover{background:rgba(34,197,94,0.15);color:#22c55e;}
.btn-sm-icon.del:hover{background:rgba(239,68,68,0.15);color:#ef4444;}

.tabla-vacia{text-align:center;padding:48px 16px;color:var(--text-muted);}
.tabla-vacia i{font-size:2.2rem;display:block;margin-bottom:10px;opacity:0.4;}
.tabla-vacia p{margin-bottom:0;font-size:0.85rem;}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div class="ent-header">
        <div>
            <h5 class="mb-0" style="font-size:1rem;"><i class="bi bi-arrow-left-right" style="color:var(--primary);"></i> Entregas / Pases de turno</h5>
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
        <button class="active" onclick="cambiarTab('tareas', this)"><i class="bi bi-list-task"></i> Tareas</button>
        <button onclick="cambiarTab('registros', this)"><i class="bi bi-clipboard-check"></i> Revision de tareas realizadas</button>
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

<!-- Modal Nueva/Editar tarea -->
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

<!-- Modal Publicar tarea -->
<div class="modal fade" id="modalPublicar" tabindex="-1">
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
                <button class="btn btn-success btn-sm" onclick="confirmarPublicar()"><i class="bi bi-send-fill"></i> Publicar</button>
            </div>
        </div>
    </div>
</div>
