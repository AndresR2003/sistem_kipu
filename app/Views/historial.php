<!-- Historial View -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-clock-history"></i> <?= esc($titulo) ?>
        </h4>
        <p class="text-muted mb-0">Telefono: <?= esc($usuario['telefono'] ?? '-') ?></p>
    </div>
    <a href="<?= site_url('pagos') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<!-- Resumen -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon-box" style="background: rgba(13,202,240,0.15); color: #0dcaf0;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-value">S/ <?= number_format($totalAdeudado, 2) ?></div>
            <div class="stat-label">Total Adeudado</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon-box" style="background: rgba(25,135,84,0.15); color: #198754;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-value" id="totalPagados">-</div>
            <div class="stat-label">Meses Pagados</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon-box" style="background: rgba(220,53,69,0.15); color: #dc3545;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="stat-value" id="totalPendientes">-</div>
            <div class="stat-label">Meses Pendientes</div>
        </div>
    </div>
</div>

<!-- Tabla historial -->
<div class="table-container">
    <h5 class="mb-4"><i class="bi bi-list-ul"></i> Historial Completo</h5>
    <div class="table-responsive">
        <table class="table table-hover" id="tablaHistorial">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Anio</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha Envio</th>
                    <th>Fecha Aprobacion</th>
                    <th>Observacion</th>
                    <th class="text-center">Comprobante</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pagados = 0;
                $pendientes = 0;
                foreach ($historial as $item):
                    if ($item['estado'] === 'PAGADO') $pagados++;
                    else $pendientes++;

                    $estadoClass = '';
                    switch ($item['estado']) {
                        case 'PAGADO': $estadoClass = 'badge-pagado'; break;
                        case 'PENDIENTE': $estadoClass = 'badge-pendiente'; break;
                        case 'NO_PAGADO': $estadoClass = 'badge-no-pagado'; break;
                        case 'RECHAZADO': $estadoClass = 'badge-rechazado'; break;
                    }
                ?>
                <tr>
                    <td><strong><?= esc($item['mes_nombre']) ?></strong></td>
                    <td><?= esc($item['anio']) ?></td>
                    <td>S/ <?= number_format($item['monto'], 2) ?></td>
                    <td><span class="badge-estado <?= $estadoClass ?>"><?= esc($item['estado']) ?></span></td>
                    <td><?= $item['fecha_envio'] ?? '-' ?></td>
                    <td><?= $item['fecha_aprobacion'] ?? '-' ?></td>
                    <td><?= esc($item['observacion'] ?? '-') ?></td>
                    <td class="text-center">
                        <?php if ($item['captura']): ?>
                            <button class="btn btn-sm btn-outline-info btn-sm-icon" onclick="verComprobanteHistorial(<?= $item['id'] ?>)" title="Ver Comprobante">
                                <i class="bi bi-image"></i>
                            </button>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ver Comprobante -->
<div class="modal fade" id="modalComprobanteHistorial" tabindex="-1">
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
                    <span class="badge bg-secondary" id="compHistUsuario"></span>
                    <span class="badge bg-info ms-1" id="compHistMes"></span>
                </div>
                <img id="compHistImagen" src="" alt="Comprobante" class="comprobante-preview">
                <div id="compHistObservacion" class="alert alert-warning mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

