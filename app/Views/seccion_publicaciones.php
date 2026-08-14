<style>
.pub-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:20px 24px;margin-bottom:14px;transition:border-color 0.2s;}
.pub-card:hover{border-color:var(--primary);}
.pub-autor{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
.pub-autor-avatar{width:38px;height:38px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:#fff;}
.pub-autor-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.pub-autor-info{min-width:0;}
.pub-autor-nombre{font-size:0.8rem;font-weight:600;color:var(--text);line-height:1.2;}
.pub-autor-rol{font-size:0.68rem;color:var(--text-muted);margin-top:2px;}
.pub-card .pub-titulo{font-size:1rem;font-weight:600;color:var(--text);margin-bottom:6px;}
.pub-card .pub-contenido{font-size:0.85rem;color:var(--text-muted);white-space:pre-line;line-height:1.5;}
.pub-card .pub-meta{font-size:0.7rem;color:var(--text-muted);margin-top:10px;}
.pub-card .pub-badge{display:inline-block;font-size:0.6rem;padding:2px 8px;border-radius:10px;background:rgba(70,105,250,0.12);color:var(--primary);margin-right:6px;}
.pub-card .pub-contenido{margin-bottom:8px;}
.pub-acciones{display:flex;gap:4px;margin-top:10px;}
.pub-acciones button{background:transparent;border:none;padding:3px 8px;border-radius:5px;font-size:0.7rem;color:var(--text-muted);transition:all 0.15s;}
.pub-acciones button:hover{background:var(--bg-input);color:var(--text);}
.pub-acciones button.rec:hover{color:var(--warning);}
.pub-acciones button.mar:hover{color:var(--primary);}
.pub-acciones button.com:hover{color:var(--success);}
.pub-acciones button .com-count{display:inline-flex;align-items:center;justify-content:center;min-width:15px;height:15px;padding:0 4px;margin-left:3px;border-radius:8px;background:var(--success);color:#fff;font-size:0.6rem;font-weight:700;line-height:1;}
.pub-check{display:flex;align-items:flex-start;gap:12px;}
.pub-check .form-check-input{margin-top:3px;cursor:pointer;width:18px;height:18px;flex-shrink:0;}
.pub-check .form-check-input:checked{background-color:var(--success);border-color:var(--success);}
.pub-card.completada{opacity:0.55;}
.pub-card.completada .pub-titulo{text-decoration:line-through;color:var(--text-muted);}
.pub-card.completada .pub-contenido{text-decoration:line-through;color:var(--text-muted);}
.ent-hechos{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;}
.ent-hecho{display:inline-flex;align-items:center;gap:6px;font-size:0.7rem;background:rgba(34,197,94,0.12);color:#22c55e;padding:4px 10px;border-radius:12px;}
.ent-hecho.mio{background:rgba(70,105,250,0.15);color:var(--primary);}
.tareas-diarias-titulo{display:flex;align-items:center;gap:8px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#22c55e;padding:14px 4px 10px;border-bottom:1px dashed var(--border);margin-bottom:14px;}
.tareas-diarias-titulo span{color:var(--text-muted);font-weight:500;text-transform:none;letter-spacing:0;}
.pub-card.diaria{border-left:3px solid #22c55e;}
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

/* ===== Organizacion compacta seccion Tareas ===== */
.tareas-grupo{margin-bottom:14px;border:1px solid var(--border);border-radius:6px;overflow:hidden;}
.tareas-grupo-titulo{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:7px 12px;border-radius:0;background:var(--bg-card-alt);border:none;border-bottom:1px solid var(--border);font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text);}
.tareas-grupo-titulo.diaria{color:#22c55e;}
.tareas-grupo-titulo i{font-size:0.85rem;}
.tareas-grupo-titulo .tareas-fecha{color:var(--text-muted);text-transform:none;font-weight:500;letter-spacing:0;}
.tareas-grupo-titulo .tareas-count{background:var(--bg-input);padding:2px 9px;border-radius:12px;font-size:0.65rem;color:var(--text-muted);font-weight:600;}
.tareas-progreso{height:4px;width:110px;background:var(--bg-input);border-radius:2px;overflow:hidden;}
.tareas-progreso span{display:block;height:100%;background:var(--success);border-radius:2px;}
.tareas-vacio{text-align:center;padding:22px 12px;color:var(--text-muted);font-size:0.8rem;}
.tareas-vacio i{font-size:1.4rem;display:block;margin-bottom:6px;opacity:0.4;}

/* Tarjetas compactas dentro de Tareas */
.pub-compact .pub-card{padding:10px 12px;margin:0;border-radius:0;border:none;border-bottom:1px solid var(--border);}
.pub-compact .pub-card:last-child{border-bottom:none;}
.pub-compact .pub-card:hover{background:var(--bg-input);border-color:var(--border);border-left-color:#22c55e;}
.pub-compact .pub-card.diaria{border-left:3px solid #22c55e;}
.pub-compact .pub-card .pub-titulo{font-size:0.85rem;margin:0;}
.pub-compact .pub-card .pub-contenido{font-size:0.78rem;margin:0;line-height:1.4;}
.pub-compact .pub-card .pub-meta{font-size:0.65rem;margin:0;}
.pub-compact .pub-card .pub-badge{font-size:0.55rem;padding:2px 7px;margin:0;}
.pub-compact .pub-autor{gap:8px;margin-bottom:8px;}
.pub-compact .pub-autor-avatar{width:30px;height:30px;font-size:0.7rem;}
.pub-compact .pub-autor-nombre{font-size:0.74rem;}
.pub-compact .pub-autor-rol{font-size:0.62rem;}
.pub-compact .pub-check{gap:10px;justify-content:space-between;}
.pub-compact .pub-check .form-check-input{width:16px;height:16px;margin-top:2px;order:2;}
.pub-compact .pub-acciones{margin-top:2px;opacity:0;transition:opacity 0.15s;}
.pub-compact .pub-card:hover .pub-acciones{opacity:1;}
.pub-compact .pub-acciones button{font-size:0.65rem;padding:2px 7px;}
.pub-compact .ent-hechos{margin-top:6px;gap:5px;}
.pub-compact .ent-hecho{font-size:0.62rem;padding:3px 8px;}
.pub-compact .comentarios-wrap{margin-top:8px;padding-top:8px;}
.pub-compact .comentarios-lista{max-height:200px;}

/* Lista con contenedor para secciones sin grupos (noticias, manual) */
.publicaciones-lista{border:1px solid var(--border);border-radius:6px;overflow:hidden;}
.publicaciones-lista .pub-card:last-child{border-bottom:none;}
</style>

<div class="table-container">
    <div class="table-header">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
            <div>
                <h5 class="mb-0"><i class="bi bi-<?php
                    $iconos = ['noticias' => 'newspaper', 'ideas' => 'lightbulb-fill', 'manual' => 'book-fill', 'tareas' => 'check2-square'];
                    echo $iconos[$seccion] ?? 'file-text';
                ?>"></i> <?= ucfirst($seccion) ?></h5>
                <?php if ($seccion === 'tareas'): ?>
                <small class="text-muted">Marca las tareas que vayas realizando</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="publicacionesContainer" data-seccion="<?= $seccion ?>" class="<?= in_array($seccion, ['tareas', 'noticias', 'manual']) ? 'pub-compact' : '' ?>">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm"></div> Cargando...
        </div>
    </div>
</div>
