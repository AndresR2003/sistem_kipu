<style>
.marcador-item{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;margin-bottom:10px;transition:border-color 0.2s;display:flex;justify-content:space-between;align-items:flex-start;gap:12px;}
.marcador-item:hover{border-color:var(--primary);}
.marcador-item .marc-titulo{font-size:0.9rem;font-weight:600;color:var(--text);margin-bottom:4px;}
.marcador-item .marc-contenido{font-size:0.8rem;color:var(--text-muted);white-space:pre-line;line-height:1.4;}
.marcador-item .marc-fecha{font-size:0.65rem;color:var(--text-muted);margin-top:6px;}
.marcador-acciones{display:flex;gap:4px;flex-shrink:0;}
.btn-sm-icon{background:transparent;border:none;padding:4px 8px;color:var(--text);border-radius:6px;font-size:0.85rem;transition:background 0.15s;}
.btn-sm-icon:hover{background:var(--bg-input);}
.btn-primary-custom{background:var(--primary);color:#fff;border:none;}
.btn-primary-custom:hover{background:var(--primary-dark);color:#fff;}
</style>

<div class="table-container">
    <div class="table-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <h5 class="mb-0"><i class="bi bi-bookmark-fill"></i> Marcadores</h5>
                <small class="text-muted">Tus marcadores guardados</small>
            </div>
        </div>
    </div>
    <div id="listaMarcadores" style="padding:16px;"></div>
    <div id="sinMarcadores" class="text-center py-5" style="display:none;">
        <i class="bi bi-bookmark" style="font-size:2.5rem;color:var(--text-muted);"></i>
        <p class="text-muted mt-2 mb-0 small">No tienes marcadores</p>
    </div>
</div>
