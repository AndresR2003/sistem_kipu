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

/* ===== Tarjeta tipo Ideas (dos columnas) ===== */
.noticia-card{display:grid;grid-template-columns:minmax(0,1fr) 280px;gap:16px 24px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:22px 24px;margin-bottom:14px;cursor:pointer;transition:border-color 0.2s;}
.noticia-card:hover{border-color:var(--primary);}
.noticia-card .noticia-main{min-width:0;}
.noticia-card .noticia-side{display:flex;flex-direction:column;gap:12px;}
.noticia-card .pub-titulo{font-size:1.1rem;font-weight:700;color:var(--text);margin-bottom:8px;}
.noticia-card .pub-contenido{font-size:0.85rem;color:var(--text-muted);white-space:pre-line;line-height:1.55;}
.noticia-card .pub-meta{font-size:0.7rem;color:var(--text-muted);margin-top:10px;display:flex;align-items:center;flex-wrap:wrap;gap:4px;}
.noticia-card .pub-badge{margin-top:8px;display:inline-block;}
.noticia-footer{grid-column:1 / -1;border-top:1px solid var(--border);margin-top:4px;padding-top:10px;}
.noticia-footer .pub-acciones{display:flex;gap:4px;margin-top:0;}
.noticia-footer .pub-acciones button{background:transparent;border:none;padding:4px 9px;border-radius:6px;font-size:0.72rem;color:var(--text-muted);transition:all 0.15s;}
.noticia-footer .pub-acciones button:hover{background:var(--bg-input);color:var(--text);}
.noticia-footer .pub-acciones button.del:hover{color:var(--danger);}
.noticia-footer .pub-acciones button.com:hover{color:var(--success);}
.noticia-card .comentarios-wrap{grid-column:1 / -1;border-top:1px solid var(--border);margin-top:14px;padding-top:14px;}
.noticia-origen{margin-top:10px;padding:10px 12px;border:1px dashed var(--border);border-radius:8px;background:var(--bg-card-alt);}
.noticia-origen-lbl{font-size:0.62rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:700;margin-bottom:4px;}
.noticia-origen-titulo{font-size:0.83rem;font-weight:600;color:var(--text);margin-bottom:2px;}
.noticia-origen .pub-contenido{margin-bottom:0;}
.noticia-side-card{background:transparent;border:none;border-radius:var(--radius);padding:2px 0;font-size:0.9em;}
.noticia-side-label{font-size:0.66rem;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);font-weight:600;margin-bottom:10px;}
.noticia-autor-row{display:flex;align-items:center;gap:10px;}
.noticia-autor-avatar{width:42px;height:42px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:700;color:#fff;}
.noticia-autor-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.noticia-autor-nombre{font-size:0.82rem;font-weight:600;color:var(--text);line-height:1.25;}
.noticia-autor-rol{font-size:0.7rem;color:var(--text-muted);margin-top:2px;}
.noticia-fecha-item{display:flex;align-items:center;gap:8px;font-size:0.76rem;color:var(--text);padding:4px 0;}
.noticia-fecha-item i{color:var(--primary);}
.noticia-fecha-item .lbl{color:var(--text-muted);font-size:0.66rem;display:block;}
.noticia-fecha-item .val{font-weight:600;}
@media (max-width:900px){.noticia-card{grid-template-columns:1fr;gap:16px;}}
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