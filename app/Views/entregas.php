<style>
.ent-header{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:linear-gradient(135deg,rgba(70,105,250,0.09),transparent 60%);}
.ent-sub{font-size:0.8rem;color:var(--text-muted);margin-top:2px;}
.btn-primary-custom{background:var(--primary);color:#fff;border:none;}
.btn-primary-custom:hover{background:var(--primary-dark);color:#fff;}
.btn-outline-custom{background:transparent;border:1px solid var(--border);color:var(--text);}
.btn-outline-custom:hover{background:var(--bg-input);color:var(--text);}
.pase-filtros{display:flex;gap:8px;align-items:center;flex-wrap:wrap;padding:12px 16px;border-bottom:1px solid var(--border);}
.pase-filtros .btn-chips{display:flex;gap:6px;}
.chip-filtro{background:var(--bg-card-alt);border:1px solid var(--border);color:var(--text-muted);padding:6px 14px;border-radius:20px;font-size:0.78rem;cursor:pointer;transition:all 0.15s;display:inline-flex;align-items:center;gap:6px;}
.chip-filtro:hover{color:var(--text);border-color:var(--primary);}
.chip-filtro.active{background:rgba(70,105,250,0.14);color:var(--primary);border-color:var(--primary);font-weight:600;}

.pase-card{display:flex;align-items:center;gap:14px;padding:13px 16px;border-bottom:1px solid var(--border);cursor:pointer;transition:background 0.15s;}
.pase-card:hover{background:var(--bg-input);}
.pase-flecha{width:46px;height:46px;border-radius:12px;background:rgba(70,105,250,0.12);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.pase-info{flex:1;min-width:0;}
.pase-titulo{font-size:0.9rem;font-weight:600;color:var(--text);display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.pase-meta{font-size:0.75rem;color:var(--text-muted);margin-top:2px;}
.pase-progreso{width:180px;flex-shrink:0;}
.pase-progreso .barra{height:5px;background:var(--bg-input);border-radius:10px;overflow:hidden;margin-top:4px;}
.pase-progreso .barra > div{height:100%;background:var(--success);border-radius:10px;}
.badge-estado{font-size:0.6rem;padding:3px 10px;border-radius:14px;font-weight:700;letter-spacing:0.3px;}
.badge-estado.abierto{background:rgba(34,197,94,0.14);color:#22c55e;}
.badge-estado.cerrado{background:rgba(100,116,139,0.15);color:#94a3b8;}
.pase-acciones{display:flex;gap:4px;flex-shrink:0;}

.pase-detalle-head{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:linear-gradient(135deg,rgba(70,105,250,0.09),transparent 60%);}
.pase-detalle-head .tit{font-size:1rem;font-weight:700;color:var(--text);}
.pase-detalle-head .sub{font-size:0.78rem;color:var(--text-muted);}
.area-grupo{margin:0 16px 14px;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.area-grupo-head{display:flex;align-items:center;justify-content:space-between;padding:9px 14px;background:var(--bg-card-alt);border-bottom:1px solid var(--border);font-size:0.8rem;font-weight:600;color:var(--text);}
.punto-item{padding:12px 14px;border-bottom:1px solid var(--border);}
.punto-item:last-child{border-bottom:none;}
.punto-item.pendiente{border-left:3px solid #f59e0b;}
.punto-item.revisado{border-left:3px solid #06b6d4;}
.punto-item.completado{border-left:3px solid #22c55e;}
.punto-texto{font-size:0.86rem;color:var(--text);white-space:pre-wrap;word-break:break-word;}
.punto-meta{font-size:0.7rem;color:var(--text-muted);margin-top:6px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.punto-meta .autor{display:inline-flex;align-items:center;gap:4px;}
.punto-meta .autor i{color:var(--primary);}
.punto-acciones{display:flex;align-items:center;gap:6px;margin-top:8px;flex-wrap:wrap;}
.btn-sm-pase{background:transparent;border:1px solid var(--border);color:var(--text-muted);padding:4px 10px;border-radius:7px;font-size:0.72rem;display:inline-flex;align-items:center;gap:5px;cursor:pointer;transition:all 0.15s;}
.btn-sm-pase:hover{color:var(--text);border-color:var(--primary);}
.btn-sm-pase.green:hover{color:#22c55e;border-color:#22c55e;}
.btn-sm-pase.cyan:hover{color:#06b6d4;border-color:#06b6d4;}
.btn-sm-pase.red:hover{color:#ef4444;border-color:#ef4444;}
.btn-sm-pase.blue:hover{color:var(--primary);border-color:var(--primary);}
.btn-sm-pase.amber:hover{color:#f59e0b;border-color:#f59e0b;}

.comentarios-wrap{background:var(--bg-input);border-radius:8px;padding:10px 12px;margin-top:10px;}
.comentario-item{font-size:0.78rem;color:var(--text);padding:6px 0;border-bottom:1px dashed var(--border);}
.comentario-item:last-child{border-bottom:none;}
.comentario-item .autor-c{color:var(--primary);font-weight:600;}
.comentario-item .fecha-c{color:var(--text-muted);font-size:0.68rem;margin-left:6px;}
.comentario-input{display:flex;gap:6px;margin-top:8px;}

.vinculo-tarea{display:inline-flex;align-items:center;gap:6px;background:rgba(70,105,250,0.12);color:var(--primary);border:1px solid rgba(70,105,250,0.2);padding:4px 12px;border-radius:8px;font-size:0.75rem;font-weight:600;text-decoration:none;}
.vinculo-tarea:hover{background:rgba(70,105,250,0.2);color:var(--primary);}
.tabla-vacia{text-align:center;padding:40px 16px;color:var(--text-muted);}
.tabla-vacia i{font-size:2.4rem;display:block;margin-bottom:8px;opacity:0.4;}
.tabla-vacia p{margin-bottom:0;font-size:0.85rem;}
.badge-punto{font-size:0.6rem;padding:2px 9px;border-radius:12px;font-weight:600;}
.badge-punto.pendiente{background:rgba(245,158,11,0.14);color:#f59e0b;}
.badge-punto.revisado{background:rgba(6,182,212,0.14);color:#06b6d4;}
.badge-punto.completado{background:rgba(34,197,94,0.14);color:#22c55e;}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <!-- VISTA LISTA -->
    <div id="paseListView">
        <div class="ent-header">
            <div>
                <h5 class="mb-0" style="font-size:1rem;"><i class="bi bi-arrow-left-right" style="color:var(--primary);"></i> Pases de turno</h5>
                <div class="ent-sub"><i class="bi bi-shield-lock-fill"></i> Transmite la informacion y pendientes al siguiente turno</div>
            </div>
            <div class="d-flex gap-2">
                <?php if ($esAdmin): ?>
                <button class="btn btn-outline-custom btn-sm" onclick="abrirModalTurnos()">
                    <i class="bi bi-gear-fill"></i> Turnos
                </button>
                <button class="btn btn-primary-custom btn-sm" onclick="abrirModalNuevoPase()">
                    <i class="bi bi-plus-lg"></i> Nuevo pase de turno
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="pase-filtros">
            <div class="btn-chips" id="paseFiltros">
                <button class="chip-filtro active" data-estado="" onclick="cambiarFiltroPase(this)">Todos</button>
                <button class="chip-filtro" data-estado="abierto" onclick="cambiarFiltroPase(this)"><i class="bi bi-unlock"></i> Abiertos</button>
                <button class="chip-filtro" data-estado="cerrado" onclick="cambiarFiltroPase(this)"><i class="bi bi-lock"></i> Cerrados</button>
            </div>
            <div class="ms-auto">
                <button class="btn btn-outline-custom btn-sm" onclick="cargarPases()"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
        </div>

        <div id="listaPases"></div>
    </div>

    <!-- VISTA DETALLE -->
    <div id="paseDetailView" style="display:none;">
        <div class="pase-detalle-head">
            <div>
                <button class="btn btn-outline-custom btn-sm mb-2" onclick="volverALista()"><i class="bi bi-arrow-left"></i> Volver</button>
                <div class="tit" id="detTitulo"></div>
                <div class="sub" id="detMeta"></div>
            </div>
            <div class="d-flex gap-2" id="detAcciones">
                <button class="btn btn-sm btn-outline-secondary" onclick="recargarDetalle()"><i class="bi bi-arrow-clockwise"></i></button>
                <?php if ($esAdmin): ?>
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleAccionPase()"><i class="bi bi-lock-fill"></i> Cerrar / Reabrir</button>
                <button class="btn btn-sm btn-outline-danger" onclick="eliminarPase()"><i class="bi bi-trash"></i> Eliminar</button>
                <button class="btn btn-sm btn-primary-custom" onclick="abrirModalPunto(null)"><i class="bi bi-plus-lg"></i> Añadir punto</button>
                <?php endif; ?>
            </div>
        </div>
        <div id="detPuntos" style="padding:16px 0;"></div>
    </div>
</div>

<!-- Modal Nuevo Pase -->
<div class="modal fade" id="modalNuevoPase" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-arrow-left-right" style="color:var(--primary);"></i> Nuevo pase de turno</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Titulo <span class="text-muted">(opcional)</span></label>
                    <input type="text" class="form-control" id="paseTitulo" placeholder="Ej: Cierre de operaciones">
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">De turno</label>
                        <select class="form-select" id="paseDeTurno"></select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Para turno</label>
                        <select class="form-select" id="paseATurno"></select>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="paseFecha">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary-custom btn-sm" onclick="guardarPase()"><i class="bi bi-check-lg"></i> Crear pase</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Punto -->
<div class="modal fade" id="modalPunto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="puntoModalTitulo"><i class="bi bi-pin-fill" style="color:var(--primary);"></i> Nuevo punto</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="puntoId">
                <div class="mb-3">
                    <label class="form-label">Area / Departamento</label>
                    <select class="form-select" id="puntoArea">
                        <option value="">General</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contenido</label>
                    <textarea class="form-control" id="puntoContenido" rows="4" placeholder="Describe la informacion o pendiente para el siguiente turno..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary-custom btn-sm" onclick="guardarPunto()"><i class="bi bi-check-lg"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Turnos -->
<div class="modal fade" id="modalTurnos" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-gear-fill" style="color:var(--primary);"></i> Administrar turnos</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control" id="turnoNombre" placeholder="Nombre (ej: Tarde)">
                        <input type="number" class="form-control" style="max-width:70px;" id="turnoOrden" placeholder="Ord." title="Orden">
                    </div>
                    <input type="text" class="form-control mt-2" id="turnoDescripcion" placeholder="Descripcion (opcional)">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="turnoActivo" checked>
                        <label class="form-check-label" for="turnoActivo">Activo</label>
                    </div>
                    <button class="btn btn-primary-custom btn-sm mt-2 w-100" onclick="guardarTurno()"><i class="bi bi-plus-lg"></i> Agregar turno</button>
                </div>
                <div id="listaTurnos"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Convertir en tarea -->
<div class="modal fade" id="modalTareaPase" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-list-task" style="color:var(--primary);"></i> Convertir punto en tarea</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tareaPuntoId">
                <div class="mb-3">
                    <label class="form-label">Titulo de la tarea</label>
                    <input type="text" class="form-control" id="tareaTitulo">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripcion</label>
                    <textarea class="form-control" id="tareaDescripcion" rows="3"></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Prioridad</label>
                        <select class="form-select" id="tareaPrioridad">
                            <option value="media">Media</option>
                            <option value="alta">Alta</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Fecha limite <span class="text-muted">(opcional)</span></label>
                        <input type="datetime-local" class="form-control" id="tareaFechaLimite">
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">Modalidad</label>
                    <select class="form-select" id="tareaModalidad">
                        <option value="single_completes_all">Uno completa por todos</option>
                        <option value="all_must_complete">Todos deben completar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Departamentos</label>
                    <select class="form-select" id="tareaDepartamentos" multiple></select>
                    <div class="form-text">Mantén Ctrl para elegir varios.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Asignar a usuarios</label>
                    <select class="form-select" id="tareaAsignados" multiple></select>
                    <div class="form-text">Mantén Ctrl para elegir varios.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="tareaPublicada" checked>
                    <label class="form-check-label" for="tareaPublicada">Publicar la tarea</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary-custom btn-sm" onclick="convertirEnTarea()"><i class="bi bi-arrow-right-circle-fill"></i> Crear tarea</button>
            </div>
        </div>
    </div>
</div>

<script>
var esAdmin = <?= $esAdmin ? 'true' : 'false' ?>;
var paseActualId = null;
var areasCache = null;
var usuariosCache = null;
</script>