<style>
.entrega-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;margin-bottom:12px;transition:border-color 0.2s;}
.entrega-card:hover{border-color:var(--primary);}
.entrega-card .ent-titulo{font-size:0.95rem;font-weight:600;color:var(--text);}
.entrega-card .ent-desc{font-size:0.82rem;color:var(--text-muted);margin-top:4px;white-space:pre-line;line-height:1.5;}
.entrega-card .ent-meta{font-size:0.7rem;color:var(--text-muted);margin-top:8px;}
.entrega-card.completada{opacity:0.6;}
.entrega-card.completada .ent-titulo{text-decoration:line-through;color:var(--text-muted);}
.ent-hechos{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;}
.ent-hecho{display:inline-flex;align-items:center;gap:6px;font-size:0.7rem;background:rgba(34,197,94,0.12);color:#22c55e;padding:4px 10px;border-radius:12px;}
.ent-hecho.mio{background:rgba(70,105,250,0.15);color:var(--primary);}
.btn-primary-custom{background:var(--primary);color:#fff;border:none;}
.btn-primary-custom:hover{background:var(--primary-dark);color:#fff;}
.badge-estado{font-size:0.6rem;padding:2px 8px;border-radius:8px;font-weight:600;}
.badge-estado.pub{background:rgba(34,197,94,0.15);color:#22c55e;}
.badge-estado.despub{background:rgba(239,68,68,0.15);color:#ef4444;}
.nav-tabs-lito{display:flex;gap:4px;border-bottom:1px solid var(--border);padding:0 18px;margin-bottom:0;}
.nav-tabs-lito button{background:transparent;border:none;padding:12px 16px;font-size:0.85rem;color:var(--text-muted);border-bottom:2px solid transparent;transition:all 0.15s;}
.nav-tabs-lito button:hover{color:var(--text);}
.nav-tabs-lito button.active{color:var(--primary);border-bottom-color:var(--primary);font-weight:600;}
.tab-pane{padding:18px;}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border);">
        <h5 class="mb-0" style="font-size:0.95rem;"><i class="bi bi-arrow-left-right"></i> Entregas / Pases de turno</h5>
    </div>

    <div class="nav-tabs-lito" id="entTabs">
        <button class="active" onclick="cambiarTab('hoy', this)"><i class="bi bi-calendar-check"></i> Pase de turno</button>
        <button onclick="cambiarTab('admin', this)"><i class="bi bi-gear-fill"></i> Administrar tareas</button>
    </div>

    <!-- TAB HOY -->
    <div class="tab-pane" id="tabHoy">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <span class="badge bg-primary" id="fechaHoy"></span>
            </div>
            <div class="d-flex gap-2">
                <input type="date" class="form-control form-control-sm" id="fechaPase" onchange="cargarPaseTurno()" style="width:180px;">
            </div>
        </div>
        <div id="tareasHoy">
            <div class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm"></div> Cargando...
            </div>
        </div>
    </div>

    <!-- TAB ADMIN -->
    <div class="tab-pane" id="tabAdmin" style="display:none;">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary-custom btn-sm" onclick="nuevaTarea()">
                <i class="bi bi-plus-lg"></i> Nueva tarea
            </button>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>Tarea</th>
                        <th>Repite</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyEntregas"></tbody>
            </table>
        </div>

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
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" id="tareaRepetir" checked>
                    <label class="form-check-label" for="tareaRepetir">Repetir diariamente</label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="tareaPublicado" checked>
                    <label class="form-check-label" for="tareaPublicado">Publicada (visible en el pase de turno)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary-custom btn-sm" onclick="guardarTarea()"><i class="bi bi-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>
