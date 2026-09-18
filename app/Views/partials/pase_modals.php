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