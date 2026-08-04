<style>
.rec-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border);}
.rec-header h5{margin:0;font-size:1rem;font-weight:600;display:flex;align-items:center;gap:8px;}
.rec-empty{text-align:center;padding:60px 20px;color:var(--text-muted);}
.rec-empty i{font-size:2.5rem;display:block;margin-bottom:12px;opacity:0.3;}
.rec-empty p{margin:0;font-size:0.85rem;}

/* ===== Tarjetas tipo publicacion ===== */
.rec-lista{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.rec-card{background:var(--bg-card);border-bottom:1px solid var(--border);padding:16px 20px;transition:background 0.15s;}
.rec-card:last-child{border-bottom:none;}
.rec-card:hover{background:var(--bg-input);}
.rec-card.completado{opacity:0.5;}
.rec-card.completado .rec-titulo{text-decoration:line-through;color:var(--text-muted);}
.rec-check{display:flex;align-items:flex-start;gap:12px;}
.rec-check .form-check-input{margin-top:3px;cursor:pointer;width:18px;height:18px;flex-shrink:0;}
.rec-check .form-check-input:checked{background-color:var(--success);border-color:var(--success);}
.rec-cuerpo{flex:1;min-width:0;}
.rec-titulo{font-size:0.9rem;font-weight:600;color:var(--text);margin-bottom:4px;}
.rec-desc{font-size:0.8rem;color:var(--text-muted);white-space:pre-line;line-height:1.4;}
.rec-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:10px;font-size:0.7rem;color:var(--text-muted);}
.badge-prio{display:inline-block;padding:2px 10px;border-radius:10px;font-size:0.65rem;font-weight:600;}
.badge-prio.alta{background:rgba(239,68,68,0.12);color:#ef4444;}
.badge-prio.media{background:rgba(234,179,8,0.12);color:#eab308;}
.badge-prio.baja{background:rgba(107,114,128,0.12);color:#6b7280;}
.rec-acciones{display:flex;gap:4px;}
.rec-acciones button{background:transparent;border:none;padding:4px 8px;border-radius:5px;color:var(--text-muted);font-size:0.7rem;transition:all 0.12s;}
.rec-acciones button:hover{background:var(--bg-input);color:var(--danger);}
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
        <div class="rec-lista" id="recTbody"></div>
    </div>
</div>