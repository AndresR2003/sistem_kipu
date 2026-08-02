<!-- Calendario View -->
<div class="row g-4">
    <div class="col-12">
        <div class="card calendar-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="<?= $icono ?? 'bi bi-calendar-fill' ?>"></i> <?= $titulo_seccion ?? 'Calendario' ?>
                </h5>
                <button class="btn btn-primary-custom btn-sm" onclick="abrirModalNuevo()">
                    <i class="bi bi-plus-lg"></i> Nuevo Evento
                </button>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar eventos -->
<div class="modal fade" id="modalEvento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEventoTitulo">
                    <i class="bi bi-calendar-plus"></i> Nuevo Evento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEvento" autocomplete="off">
                <input type="hidden" name="id" id="eventoId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventoTitulo" class="form-label">Titulo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="eventoTitulo" name="titulo"
                               placeholder="Nombre del evento" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventoDescripcion" class="form-label">Descripcion</label>
                        <textarea class="form-control" id="eventoDescripcion" name="descripcion"
                                  rows="3" placeholder="Descripcion opcional del evento"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="eventoFechaInicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="eventoFechaInicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="eventoFechaFin" class="form-label">Fecha Fin</label>
                            <input type="datetime-local" class="form-control" id="eventoFechaFin" name="fecha_fin">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="eventoColor" class="form-label">Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color" id="eventoColor" name="color"
                                   value="#4669FA" style="width: 60px; height: 40px; padding: 3px;">
                            <span id="eventoColorTexto" class="text-muted" style="font-size:0.85rem;">#4669FA</span>
                            <div class="d-flex gap-1 ms-2">
                                <span class="color-preset" data-color="#4669FA" style="background:#4669FA;" title="Azul"></span>
                                <span class="color-preset" data-color="#22c55e" style="background:#22c55e;" title="Verde"></span>
                                <span class="color-preset" data-color="#ef4444" style="background:#ef4444;" title="Rojo"></span>
                                <span class="color-preset" data-color="#f59e0b" style="background:#f59e0b;" title="Amarillo"></span>
                                <span class="color-preset" data-color="#a855f7" style="background:#a855f7;" title="Morado"></span>
                                <span class="color-preset" data-color="#ec4899" style="background:#ec4899;" title="Rosa"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom" id="btnGuardarEvento">
                        <i class="bi bi-check-lg"></i> Guardar Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .calendar-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .calendar-card .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border);
        padding: 16px 22px;
    }

    .calendar-card .card-body {
        padding: 22px;
    }

    /* FullCalendar Dark Theme Overrides */
    #calendar {
        min-height: 550px;
    }

    .fc {
        --fc-border-color: var(--border-light);
        --fc-button-bg-color: var(--bg-input);
        --fc-button-border-color: var(--border-light);
        --fc-button-text-color: var(--text);
        --fc-button-hover-bg-color: var(--bg-input-hover);
        --fc-button-hover-border-color: var(--border-light);
        --fc-button-active-bg-color: var(--primary);
        --fc-button-active-border-color: var(--primary);
        --fc-today-bg-color: rgba(70,105,250,0.08);
        --fc-event-bg-color: var(--primary);
        --fc-event-border-color: var(--primary);
        --fc-event-text-color: #fff;
        --fc-page-bg-color: transparent;
        --fc-neutral-bg-color: var(--bg-card-alt);
        --fc-list-event-hover-bg-color: var(--bg-input);
        --fc-highlight-color: rgba(70,105,250,0.05);
        color: var(--text);
    }

    .fc .fc-toolbar-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text);
    }

    .fc .fc-button {
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        padding: 6px 14px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .fc .fc-button-primary:disabled {
        opacity: 0.5;
    }

    .fc .fc-button-primary:hover {
        background: var(--bg-input-hover);
        border-color: var(--border-light);
    }

    .fc .fc-daygrid-day-number,
    .fc .fc-col-header-cell-cushion {
        color: var(--text);
        text-decoration: none;
        padding: 6px 8px;
        font-size: 0.85rem;
    }

    .fc .fc-col-header-cell-cushion {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: rgba(70,105,250,0.08);
    }

    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin: 4px;
        font-weight: 700;
    }

    .fc .fc-daygrid-more-link {
        color: var(--primary-light);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .fc .fc-event {
        border-radius: 6px;
        padding: 2px 6px;
        font-size: 0.8rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: transform 0.15s;
    }

    .fc .fc-event:hover {
        transform: scale(1.02);
        filter: brightness(1.15);
    }

    .fc .fc-event .fc-event-title {
        font-weight: 600;
        padding: 1px 0;
    }

    .fc .fc-timegrid-slot {
        height: 40px;
    }

    .fc .fc-timegrid-col.fc-day-today {
        background: rgba(70,105,250,0.05);
    }

    .fc .fc-list-day-cushion {
        background: var(--bg-card-alt) !important;
    }

    .fc .fc-list-event td {
        border-color: var(--border) !important;
    }

    .fc .fc-list-event:hover td {
        background: var(--bg-input) !important;
    }

    .fc .fc-popover {
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
    }

    .fc .fc-popover .fc-popover-header {
        background: var(--bg-card-alt);
        padding: 10px 14px;
        font-size: 0.85rem;
        border-bottom: 1px solid var(--border);
    }

    .fc .fc-popover .fc-popover-body {
        padding: 8px;
    }

    .fc .fc-more-popover .fc-event {
        margin: 3px 0;
    }

    .fc-theme-standard .fc-list-day-cushion .fc-list-day-text,
    .fc-theme-standard .fc-list-day-cushion .fc-list-day-side-text {
        color: var(--text);
        font-weight: 600;
    }

    .fc-theme-standard .fc-list {
        border: 1px solid var(--border);
    }

    .fc-theme-standard td,
    .fc-theme-standard th {
        border-color: var(--border);
    }

    .fc .fc-scrollgrid {
        border-color: var(--border) !important;
    }

    /* Color presets */
    .color-preset {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
        display: inline-block;
    }

    .color-preset:hover {
        transform: scale(1.15);
        border-color: var(--text-muted);
    }

    .color-preset.active {
        border-color: #fff;
        box-shadow: 0 0 0 2px var(--primary);
    }

    /* Boton eliminar en modal */
    .btn-danger-custom {
        background: rgba(239,68,68,0.12);
        border: 1px solid rgba(239,68,68,0.18);
        color: var(--danger);
        padding: 8px 18px;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.25s;
    }

    .btn-danger-custom:hover {
        background: var(--danger);
        color: #fff;
        border-color: var(--danger);
    }

    @media (max-width: 768px) {
        #calendar {
            min-height: 400px;
        }
        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 8px;
        }
        .fc .fc-toolbar-title {
            font-size: 1rem;
        }
        .calendar-card .card-body {
            padding: 12px;
        }
    }
</style>
