<style>
.rec-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border);}
.rec-header h5{margin:0;font-size:1rem;font-weight:600;display:flex;align-items:center;gap:8px;}
.rec-empty{text-align:center;padding:60px 20px;color:var(--text-muted);}
.rec-empty i{font-size:2.5rem;display:block;margin-bottom:12px;opacity:0.3;}
.rec-empty p{margin:0;font-size:0.85rem;}
.rec-table{width:100%;border-collapse:collapse;}
.rec-table thead th{padding:10px 16px;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);border-bottom:1px solid var(--border);text-align:left;background:var(--bg-card);}
.rec-table tbody tr{border-bottom:1px solid var(--border);transition:background 0.12s;}
.rec-table tbody tr:hover{background:var(--bg-input);}
.rec-table tbody tr.completado{opacity:0.45;}
.rec-table tbody td{padding:12px 16px;font-size:0.82rem;vertical-align:middle;}
.rec-table .rec-check{width:32px;}
.rec-table .rec-titulo{font-weight:500;color:var(--text);}
.rec-table .rec-desc{font-size:0.75rem;color:var(--text-muted);margin-top:2px;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.rec-table .rec-fecha{font-size:0.78rem;color:var(--text-muted);white-space:nowrap;}
.rec-table .rec-prio{text-align:center;}
.badge-prio{display:inline-block;padding:2px 10px;border-radius:10px;font-size:0.65rem;font-weight:600;}
.badge-prio.alta{background:rgba(239,68,68,0.12);color:#ef4444;}
.badge-prio.media{background:rgba(234,179,8,0.12);color:#eab308;}
.badge-prio.baja{background:rgba(107,114,128,0.12);color:#6b7280;}
.rec-table .rec-acciones{text-align:right;white-space:nowrap;}
.rec-table .rec-acciones button{background:none;border:none;padding:4px 8px;border-radius:4px;color:var(--text-muted);transition:all 0.12s;}
.rec-table .rec-acciones button:hover{background:var(--bg-input);color:var(--danger);}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div class="rec-header">
        <h5><i class="bi bi-bell-fill" style="color:var(--primary);"></i> Recordatorios</h5>
        <span class="text-muted" style="font-size:0.75rem;" id="recCount">0 recordatorios</span>
    </div>
    <div id="sinRecordatorios" class="rec-empty" style="display:none;">
        <i class="bi bi-inbox"></i>
        <p>No hay recordatorios.<br>Los recordatorios se crean desde las publicaciones.</p>
    </div>
    <div id="listaRecordatoriosWrap">
        <table class="rec-table" id="recTable">
            <thead>
                <tr>
                    <th class="rec-check"></th>
                    <th>Titulo</th>
                    <th>Fecha</th>
                    <th class="rec-prio">Prioridad</th>
                    <th class="rec-acciones"></th>
                </tr>
            </thead>
            <tbody id="recTbody"></tbody>
        </table>
    </div>
</div>
