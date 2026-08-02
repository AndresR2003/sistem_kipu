<style>
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
.pub-acciones button.rec:hover{color:var(--warning);}
.pub-acciones button.mar:hover{color:var(--primary);}
.pub-acciones button.com:hover{color:var(--success);}
.pub-check{display:flex;align-items:flex-start;gap:12px;}
.pub-check .form-check-input{margin-top:3px;cursor:pointer;width:18px;height:18px;flex-shrink:0;}
.pub-check .form-check-input:checked{background-color:var(--success);border-color:var(--success);}
.pub-card.completada{opacity:0.55;}
.pub-card.completada .pub-titulo{text-decoration:line-through;color:var(--text-muted);}
.pub-card.completada .pub-contenido{text-decoration:line-through;color:var(--text-muted);}
.ent-hechos{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;}
.ent-hecho{display:inline-flex;align-items:center;gap:6px;font-size:0.7rem;background:rgba(34,197,94,0.12);color:#22c55e;padding:4px 10px;border-radius:12px;}
.ent-hecho.mio{background:rgba(70,105,250,0.15);color:var(--primary);}
.comentarios-wrap{border-top:1px solid var(--border);margin-top:12px;padding-top:12px;}
.comentarios-lista{max-height:260px;overflow-y:auto;}
.comentario-item{padding:6px 0;border-bottom:1px solid var(--border);font-size:0.78rem;}
.comentario-item:last-child{border-bottom:none;}
.comentario-autor{font-weight:600;color:var(--text);font-size:0.72rem;}
.comentario-fecha{font-weight:400;color:var(--text-muted);margin-left:6px;font-size:0.65rem;}
.comentario-texto{color:var(--text);margin-top:2px;line-height:1.4;white-space:pre-line;}
.comentario-vacio{text-align:center;color:var(--text-muted);font-size:0.75rem;padding:8px 0;}
.comentarios-form textarea{font-size:0.78rem;background:var(--bg-input);color:var(--text);border-color:var(--border);}
.comentarios-form textarea:focus{border-color:var(--primary);box-shadow:none;}
</style>

<div class="table-container">
    <div class="table-header">
        <h5 class="mb-0"><i class="bi bi-<?php
            $iconos = ['noticias' => 'newspaper', 'ideas' => 'lightbulb-fill', 'manual' => 'book-fill', 'tareas' => 'check2-square'];
            echo $iconos[$seccion] ?? 'file-text';
        ?>"></i> <?= ucfirst($seccion) ?></h5>
    </div>
    <div id="publicacionesContainer" data-seccion="<?= $seccion ?>">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm"></div> Cargando...
        </div>
    </div>
</div>
