<script>
var USUARIO_ROL = '<?= session()->get("admin_rol") ?? "empleado" ?>';
</script>

<style>
.tareas-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;}
.tareas-header-text h5{font-size:1.1rem;font-weight:700;color:var(--text);margin:0;}
.tareas-header-text small{color:var(--text-muted);font-size:0.78rem;}
.tareas-header-actions{display:flex;gap:8px;align-items:center;}
.btn-nueva-tarea{background:var(--primary);color:#fff;border:none;padding:8px 18px;border-radius:var(--radius);font-size:0.82rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:opacity 0.2s;}
.btn-nueva-tarea:hover{opacity:0.85;color:#fff;}
.btn-filtros{background:var(--bg-input);color:var(--text);border:1px solid var(--border);padding:8px 16px;border-radius:var(--radius);font-size:0.82rem;font-weight:500;display:inline-flex;align-items:center;gap:6px;cursor:pointer;}
.btn-filtros:hover{border-color:var(--primary);color:var(--primary);}

.tareas-tabs{display:flex;gap:4px;margin-bottom:20px;}
.tareas-tab{padding:8px 20px;border-radius:var(--radius);font-size:0.82rem;font-weight:600;border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.2s;}
.tareas-tab.active{background:var(--primary);color:#fff;border-color:var(--primary);}
.tareas-tab:hover:not(.active){border-color:var(--primary);color:var(--primary);}

.tarea-acordeon{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:10px;overflow:hidden;}
.tarea-acordeon-header{display:flex;align-items:center;gap:10px;padding:12px 16px;cursor:pointer;transition:background 0.15s;user-select:none;}
.tarea-acordeon-header:hover{background:var(--bg-input);}
.tarea-acordeon-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.85rem;flex-shrink:0;}
.tarea-acordeon-nombre{font-size:0.88rem;font-weight:600;color:var(--text);flex:1;}
.tarea-acordeon-pendientes{font-size:0.7rem;font-weight:600;padding:3px 10px;border-radius:12px;background:rgba(70,105,250,0.12);color:var(--primary);}
.tarea-acordeon-pendientes.vacio{background:rgba(34,197,94,0.12);color:#22c55e;}
.tarea-acordeon-chevron{color:var(--text-muted);font-size:0.8rem;transition:transform 0.25s;}
.tarea-acordeon.open .tarea-acordeon-chevron{transform:rotate(180deg);}
.tarea-acordeon-body{border-top:1px solid var(--border);display:none;}
.tarea-acordeon.open .tarea-acordeon-body{display:block;}

.tarea-card{display:grid;grid-template-columns:minmax(0,1fr) 260px;gap:10px 20px;padding:14px 16px;border-bottom:1px solid var(--border);transition:background 0.15s;}
.tarea-card:last-child{border-bottom:none;}
.tarea-card:hover{background:var(--bg-input);}
.tarea-card.completada{opacity:0.55;}
.tarea-card.completada .tarea-titulo{text-decoration:line-through;color:var(--text-muted);}

.tarea-main{display:flex;align-items:flex-start;gap:10px;min-width:0;}
.tarea-side{display:flex;flex-direction:column;gap:10px;min-width:0;}

.tarea-acciones{display:flex;flex-wrap:wrap;align-items:center;gap:4px;grid-column:1 / -1;margin-top:6px;padding-top:10px;border-top:1px solid var(--border);}
.tarea-accion{background:transparent;border:none;padding:3px 8px;border-radius:5px;font-size:0.7rem;color:var(--text-muted);transition:all 0.15s;cursor:pointer;}
.tarea-accion:hover{background:var(--bg-input);color:var(--text);}
.tarea-accion.rec:hover{color:var(--warning);}
.tarea-accion.mar:hover{color:var(--primary);}
.tarea-accion.com:hover{color:var(--success);}
.tarea-accion.edt:hover{color:var(--primary);}
.tarea-accion.eye:hover{color:#06b6d4;}
.tarea-accion.del:hover{color:#ef4444;}
.tarea-accion-sep{width:1px;height:14px;background:var(--border);margin:0 4px;}
.tarea-accion .tarea-com-count{display:inline-flex;align-items:center;justify-content:center;min-width:15px;height:15px;padding:0 4px;margin-left:3px;border-radius:8px;background:var(--success);color:#fff;font-size:0.6rem;font-weight:700;line-height:1;}

.tarea-check{flex-shrink:0;padding-top:2px;}
.tarea-check input[type="checkbox"]{width:18px;height:18px;cursor:pointer;accent-color:var(--success);}
.tarea-info{flex:1;min-width:0;}
.tarea-titulo{font-size:0.88rem;font-weight:600;color:var(--text);margin-bottom:4px;line-height:1.3;}
.tarea-descripcion{font-size:0.8rem;color:var(--text-muted);white-space:pre-line;line-height:1.5;margin-bottom:4px;}
.tarea-meta{display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-top:4px;}
.tarea-badge{display:inline-flex;align-items:center;gap:4px;font-size:0.6rem;font-weight:700;padding:2px 8px;border-radius:10px;}
.badge-alta{background:rgba(239,68,68,0.15);color:#ef4444;}
.badge-media{background:rgba(234,179,8,0.15);color:#eab308;}
.badge-baja{background:rgba(34,197,94,0.15);color:#22c55e;}
.badge-dept{background:rgba(70,105,250,0.15);color:#4669FA;}
.tarea-fecha{font-size:0.7rem;color:var(--text-muted);display:inline-flex;align-items:center;gap:4px;}
.tarea-fecha.vencida{color:#ef4444;font-weight:600;}
.tarea-fecha.hoy{color:#eab308;font-weight:600;}
.tarea-estado{font-size:0.7rem;margin-top:4px;color:var(--text-muted);}
.tarea-estado .completado-por{color:#22c55e;font-weight:600;}
.tarea-estado .solo-mi{color:var(--primary);font-weight:500;}
.tarea-estado .progreso{color:var(--primary);font-weight:500;}

/* Tarjeta lateral: autor y fecha */
.tarea-side-card{background:transparent;border:none;padding:2px 0;font-size:0.9em;}
.tarea-side-label{font-size:0.66rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:600;margin-bottom:8px;}
.tarea-autor-row{display:flex;align-items:center;gap:10px;}
.tarea-autor-avatar{width:40px;height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:#fff;}
.tarea-autor-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.tarea-autor-nombre{font-size:0.82rem;font-weight:600;color:var(--text);line-height:1.25;}
.tarea-autor-rol{font-size:0.7rem;color:var(--text-muted);margin-top:2px;}
.tarea-fecha-item{display:flex;align-items:center;gap:8px;font-size:0.76rem;color:var(--text);padding:4px 0;}
.tarea-fecha-item i{color:var(--primary);}
.tarea-fecha-item .lbl{color:var(--text-muted);font-size:0.66rem;display:block;}
.tarea-fecha-item .val{font-weight:600;}

@media (max-width: 900px){
    .tarea-card{grid-template-columns:1fr;gap:12px;}
}

.tareas-footer{text-align:center;padding:16px;color:var(--text-muted);font-size:0.75rem;border-top:1px solid var(--border);margin-top:8px;}

/* Modal */
.tarea-modal-label{font-size:0.78rem;font-weight:600;color:var(--text);margin-bottom:4px;}
.tarea-modal-input{font-size:0.85rem;background:var(--bg-input);color:var(--text);border-color:var(--border);}
.tarea-modal-input:focus{border-color:var(--primary);box-shadow:none;}
.tarea-modal-select{font-size:0.85rem;background:var(--bg-input);color:var(--text);border-color:var(--border);}
.tarea-modal-select:focus{border-color:var(--primary);box-shadow:none;}
.tarea-modal-radio{display:flex;gap:16px;margin-top:6px;}
.tarea-modal-radio label{font-size:0.82rem;color:var(--text);display:flex;align-items:center;gap:6px;cursor:pointer;}
.tarea-modal-asignados{max-height:180px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius);padding:8px;margin-top:6px;}
.tarea-modal-asignado{display:flex;align-items:center;gap:8px;padding:5px 0;font-size:0.82rem;color:var(--text);}
.tarea-modal-asignado input{accent-color:var(--primary);}

/* Comentarios inline */
.comentarios-wrap{border-top:1px solid var(--border);margin-top:12px;padding-top:12px;grid-column:1 / -1;}
.comentarios-lista{max-height:280px;overflow-y:auto;}
.tarea-comentario{display:flex;gap:8px;padding:8px 0;border-bottom:1px solid var(--border);font-size:0.8rem;}
.tarea-comentario:last-child{border-bottom:none;}
.tarea-comentario-avatar{flex-shrink:0;width:30px;height:30px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;}
.tarea-comentario-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.tarea-comentario-body{flex:1;}
.tarea-comentario-autor{font-weight:600;font-size:0.75rem;}
.tarea-comentario-fecha{color:var(--text-muted);font-size:0.65rem;margin-left:6px;}
.tarea-comentario-texto{color:var(--text);margin-top:2px;line-height:1.4;}
.tarea-comentario-vacio{text-align:center;color:var(--text-muted);font-size:0.78rem;padding:12px 0;}
.comentarios-form{margin-top:8px;display:flex;flex-direction:column;}
.comentarios-form textarea{font-size:0.78rem;background:var(--bg-input);color:var(--text);border-color:var(--border);border-radius:var(--radius);}
.comentarios-form textarea:focus{border-color:var(--primary);box-shadow:none;}
.comentario-enviar{align-self:flex-end;background:var(--primary);color:#fff;border:none;padding:6px 12px;border-radius:var(--radius);font-size:0.75rem;margin-top:6px;cursor:pointer;transition:all 0.15s;}
.comentario-enviar:hover{opacity:0.9;}
</style>

<div class="table-container">
    <div class="tareas-header">
        <div class="tareas-header-text">
            <h5><i class="bi bi-check2-square"></i> Tareas</h5>
            <small>Organiza y gestiona las tareas de tu equipo</small>
        </div>
        <div class="tareas-header-actions">
            <button class="btn-filtros" onclick="toggleFiltros()">
                <i class="bi bi-funnel"></i> Filtros
            </button>
        </div>
    </div>

    <div class="tareas-tabs" id="tareasTabs">
        <button class="tareas-tab active" data-modo="mis_tareas" onclick="cambiarTab(this)">
            <i class="bi bi-person"></i> Mis tareas
        </button>
        <button class="tareas-tab" data-modo="todas" onclick="cambiarTab(this)" id="tabTodas" style="display:none;">
            <i class="bi bi-globe"></i> Todas
        </button>
    </div>

    <div id="tareasContainer">
        <div class="text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm"></div> Cargando...
        </div>
    </div>

    <div class="tareas-footer">
        <i class="bi bi-info-circle"></i> Solo se muestran los departamentos donde tienes tareas asignadas.
    </div>
</div>

<!-- Modal Nueva/Editar Tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-card);border:1px solid var(--border);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h6 class="modal-title" id="tituloModalTarea" style="font-size:0.95rem;font-weight:700;">
                    <i class="bi bi-pencil"></i> Editar tarea
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <input type="hidden" id="tareaId">
                <div class="mb-3">
                    <label class="tarea-modal-label">Titulo *</label>
                    <input type="text" class="form-control tarea-modal-input" id="tareaTitulo" placeholder="Ej: Revisar inventario" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label class="tarea-modal-label">Descripcion</label>
                    <textarea class="form-control tarea-modal-input" id="tareaDescripcion" rows="2" placeholder="Detalle de la tarea..."></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="tarea-modal-label">Prioridad</label>
                        <select class="form-select tarea-modal-select" id="tareaPrioridad">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="tarea-modal-label">Fecha limite</label>
                        <input type="datetime-local" class="form-control tarea-modal-input" id="tareaFechaLimite">
                    </div>
                    <div class="col-md-4">
                        <label class="tarea-modal-label">Modalidad de completado *</label>
                        <select class="form-select tarea-modal-select" id="tareaModalidad" onchange="toggleAsignados()">
                            <option value="single_completes_all">Una persona completa por todos</option>
                            <option value="all_must_complete">Todos deben completar</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="tarea-modal-label">Departamentos * <span class="text-muted">(puedes elegir varios)</span></label>
                    <div class="tarea-modal-asignados" id="listaDepartamentos"></div>
                </div>
                <div id="seccionAsignados">
                    <label class="tarea-modal-label">Usuarios asignados <span class="text-muted">(puedes elegir varios)</span></label>
                    <div class="tarea-modal-asignados" id="listaAsignados"></div>
                </div>
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" id="tareaPublicar">
                    <label class="form-check-label" style="font-size:0.82rem;color:var(--text);">Publicar ahora</label>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="guardarTarea()" style="background:var(--primary);border:none;">
                    <i class="bi bi-check-lg"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
