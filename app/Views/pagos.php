<!-- Pagos Admin View -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-credit-card-fill"></i> Pagos del Mes
        </h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-success" onclick="exportarExcel()">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="exportarPdf()">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="tablaPagosAdmin" class="table table-hover w-100">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Telefono</th>
                    <th>Mes</th>
                    <th>anio</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Enviado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tbodyPagosAdmin">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ver Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-image"></i> Comprobante de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <span class="badge bg-secondary" id="comprobanteUsuario"></span>
                    <span class="badge bg-info ms-1" id="comprobanteMes"></span>
                </div>
                <img id="comprobanteImagen" src="" alt="Comprobante" class="comprobante-preview">
                <div id="comprobanteObservacion" class="alert alert-warning mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rechazar Pago -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle text-danger"></i> Rechazar Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="textoRechazar"></p>
                <input type="hidden" id="rechazarPagoId">
                <div class="mb-3">
                    <label class="form-label">Motivo del rechazo</label>
                    <textarea class="form-control" id="observacionRechazo" rows="3"
                              placeholder="Ingrese el motivo del rechazo..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">
                    <i class="bi bi-x-lg"></i> Rechazar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial -->
<div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history"></i> <span id="historialTitulo"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Anio</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Enviado</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyHistorial">
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <strong>Total Adeudado: <span class="text-danger" id="historialTotal"></span></strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


