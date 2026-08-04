<style>
.rec-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border);}
.rec-header h5{margin:0;font-size:1rem;font-weight:600;display:flex;align-items:center;gap:8px;}
.rec-empty{text-align:center;padding:60px 20px;color:var(--text-muted);}
.rec-empty i{font-size:2.5rem;display:block;margin-bottom:12px;opacity:0.3;}
.rec-empty p{margin:0;font-size:0.85rem;}

/* ===== Estilos compartidos del feed de publicaciones ===== */
.pub-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px 24px;margin-bottom:14px;transition:border-color 0.2s;}
.pub-card:hover{border-color:var(--primary);}
.pub-card .pub-titulo{font-size:1rem;font-weight:600;color:var(--text);margin-bottom:6px;}
.pub-card .pub-contenido{font-size:0.85rem;color:var(--text-muted);white-space:pre-line;line-height:1.5;}
.pub-card .pub-meta{font-size:0.7rem;color:var(--text-muted);margin-top:10px;}
.pub-card .pub-badge{display:inline-block;font-size:0.6rem;padding:2px 8px;border-radius:10px;background:rgba(70,105,250,0.12);color:var(--primary);margin-right:6px;}
.pub-card .pub-contenido{margin-bottom:8px;}
.pub-acciones{display:flex;gap:4px;margin-top:10px;}
.pub-acciones button{background:transparent;border:none;padding:3px 8px;border-radius:5px;font-size:0.7rem;color:var(--text-muted);transition:all 0.15s;}
.pub-acciones button:hover{background:var(--bg-input);color:var(--text);}
.pub-acciones button.del:hover{color:var(--danger);}
.pub-acciones button .com-count{display:inline-flex;align-items:center;justify-content:center;min-width:15px;height:15px;padding:0 4px;margin-left:3px;border-radius:8px;background:var(--success);color:#fff;font-size:0.6rem;font-weight:700;line-height:1;}
.comentarios-wrap{border-top:1px solid var(--border);margin-top:12px;padding-top:12px;}
.comentarios-lista{max-height:260px;overflow-y:auto;}
.comentario-item{display:flex;align-items:flex-start;gap:8px;padding:7px 0;border-bottom:1px solid var(--border);font-size:0.78rem;}
.comentario-item:last-child{border-bottom:none;}
.comentario-avatar{flex-shrink:0;width:28px;height:28px;border-radius:50%;overflow:hidden;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;}
.comentario-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.comentario-body{flex:1;min-width:0;}
.comentario-autor{font-weight:600;color:var(--text);font-size:0.72rem;}
.comentario-fecha{font-weight:400;color:var(--text-muted);margin-left:6px;font-size:0.65rem;}
.comentario-texto{color:var(--text);margin-top:2px;line-height:1.4;white-space:pre-line;}
.comentario-vacio{text-align:center;color:var(--text-muted);font-size:0.75rem;padding:8px 0;}
.comentarios-form textarea{font-size:0.78rem;background:var(--bg-input);color:var(--text);border-color:var(--border);}
.comentarios-form textarea:focus{border-color:var(--primary);box-shadow:none;}
</style>

<div class="table-container" style="padding:0;overflow:hidden;">
    <div class="rec-header">
        <h5><i class="bi bi-bookmark-fill" style="color:var(--primary);"></i> Marcadores</h5>
        <span class="text-muted" style="font-size:0.75rem;" id="recCount">0 marcadores</span>
    </div>
    <div id="sinMarcadores" class="rec-empty" style="display:none;">
        <i class="bi bi-bookmark"></i>
        <p>No tienes marcadores.<br>Los marcadores se crean desde las publicaciones.</p>
    </div>
    <div id="listaMarcadores">
    </div>
</div>