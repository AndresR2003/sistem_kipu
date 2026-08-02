<style>
.btn-primary-custom{background:var(--primary);color:#fff;border:none;}
.btn-primary-custom:hover{background:var(--primary-dark);color:#fff;}
.badge-estado{font-size:0.6rem;padding:2px 8px;border-radius:8px;font-weight:600;}
.badge-estado.pub{background:rgba(34,197,94,0.15);color:#22c55e;}
.badge-estado.despub{background:rgba(239,68,68,0.15);color:#ef4444;}
.badge-dest{font-size:0.6rem;padding:2px 8px;border-radius:8px;font-weight:600;background:rgba(70,105,250,0.12);color:var(--primary);}
.nav-tabs-lito{display:flex;gap:4px;border-bottom:1px solid var(--border);padding:0 18px;}
.nav-tabs-lito button{background:transparent;border:none;padding:12px 16px;font-size:0.85rem;color:var(--text-muted);border-bottom:2px solid transparent;transition:all 0.15s;}
.nav-tabs-lito button:hover{color:var(--text);}
.nav-tabs-lito button.active{color:var(--primary);border-bottom-color:var(--primary);font-weight:600;}
.tab-pane{padding:18px;}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border);">
        <h5 class="mb-0" style="font-size:0.95rem;"><i class="bi bi-arrow-left-right"></i> Entregas / Pases de turno <small class="text-muted">(administracion)</small></h5>
    </div>

    <div class="nav-tabs-lito" id="entTabs">
        <button class="active" onclick="cambiarTab('tareas', this)"><i class="bi bi-list-task"></i> Tareas</button>
        <button onclick="cambiarTab('registros', this)"><i class="bi bi-clipboard-check"></i> Revision de tareas realizadas</button>
    </div>

    <!-- TAB TAREAS -->
    <div class="tab-pane" id="tabTareas">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h6 class="mb-0"><i class="bi bi-list-task"></i> Tareas preseleccionadas</h6>
            <button class="btn btn-primary-custom btn-sm" onclick="nuevaTarea()">
                <i class="bi bi-plus-lg"></i> Nueva tarea
            </button>
        </div>

        <div class="table-responsive">
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
            <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Revision de tareas realizadas</h6>
            <div class="d-flex gap-2">
                <input type="date" class="form-control form-control-sm" id="filtroRegInicio">
                <input type="date" class="form-control form-control-sm" id="filtroRegFin">
                <button class="btn btn-sm btn-outline-secondary" onclick="cargarRegistros()"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </div>

        <div class="table-responsive">
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
