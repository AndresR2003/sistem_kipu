<style>
.detalle-topbar{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;}
.detalle-topbar-left{display:flex;align-items:center;gap:8px;}
.detalle-topbar-right{display:flex;align-items:center;gap:8px;}
.detalle-btn{display:inline-flex;align-items:center;gap:6px;background:var(--bg-input);color:var(--text);border:1px solid var(--border);padding:7px 14px;border-radius:var(--radius-sm);font-size:0.8rem;font-weight:500;cursor:pointer;transition:all 0.15s;text-decoration:none;}
.detalle-btn:hover{background:var(--bg-input-hover);border-color:var(--primary);color:var(--primary);}
.detalle-btn.primary{background:var(--primary);color:#fff;border-color:var(--primary);}
.detalle-btn.primary:hover{opacity:0.88;color:#fff;}
.detalle-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;background:var(--bg-input);color:var(--text);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.9rem;cursor:pointer;transition:all 0.15s;}
.detalle-icon-btn:hover{background:var(--bg-input-hover);border-color:var(--primary);color:var(--primary);}

.detalle-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.detalle-head{padding:24px 28px;border-bottom:1px solid var(--border);}
.detalle-autor{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
.detalle-avatar{width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;color:#fff;}
.detalle-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.detalle-autor-info{min-width:0;}
.detalle-autor-nombre{font-size:0.9rem;font-weight:600;color:var(--text);line-height:1.2;}
.detalle-autor-rol{font-size:0.72rem;color:var(--text-muted);margin-top:2px;}
.detalle-fechahora{display:flex;flex-wrap:wrap;gap:16px;}
.detalle-fechahora-item{display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;color:var(--text-muted);}
.detalle-fechahora-item i{color:var(--primary);}
.detalle-fechahora-item b{color:var(--text);font-weight:600;}

.detalle-body{padding:28px;}
.detalle-titulo{font-size:1.25rem;font-weight:700;color:var(--text);margin-bottom:10px;line-height:1.35;}
.detalle-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px;}
.detalle-tag{display:inline-flex;align-items:center;gap:4px;font-size:0.65rem;font-weight:600;padding:3px 10px;border-radius:10px;background:rgba(70,105,250,0.12);color:var(--primary);}
.detalle-tag.fijada{background:rgba(245,158,11,0.14);color:var(--warning);}
.detalle-contenido{font-size:0.88rem;color:var(--text);line-height:1.7;white-space:pre-line;}

.detalle-acciones{display:flex;gap:6px;padding:14px 28px;border-top:1px solid var(--border);background:var(--bg-card-alt);}
.detalle-accion{background:transparent;border:none;padding:6px 12px;border-radius:var(--radius-sm);font-size:0.75rem;color:var(--text-muted);transition:all 0.15s;cursor:pointer;display:inline-flex;align-items:center;gap:5px;}
.detalle-accion:hover{background:var(--bg-input);color:var(--text);}
.detalle-accion.rec:hover{color:var(--warning);}
.detalle-accion.mar:hover{color:var(--primary);}
.detalle-accion.com:hover{color:var(--success);}
.detalle-accion .com-count{display:inline-flex;align-items:center;justify-content:center;min-width:16px;height:16px;padding:0 4px;border-radius:8px;background:var(--success);color:#fff;font-size:0.62rem;font-weight:700;line-height:1;}

.detalle-comentarios{padding:20px 28px 28px;border-top:1px solid var(--border);}
.detalle-comentarios-titulo{font-size:0.85rem;font-weight:700;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.detalle-comentarios-titulo i{color:var(--success);}
.comentarios-form{display:flex;align-items:flex-start;gap:10px;margin-bottom:18px;}
.comentarios-form .detalle-avatar{width:36px;height:36px;font-size:0.85rem;}
.comentarios-form textarea{font-size:0.82rem;background:var(--bg-input);color:var(--text);border-color:var(--border);border-radius:var(--radius-sm);resize:vertical;}
.comentarios-form textarea:focus{border-color:var(--primary);box-shadow:none;}
.comentarios-form .comentario-enviar{align-self:flex-end;background:var(--primary);color:#fff;border:none;padding:7px 16px;border-radius:var(--radius-sm);font-size:0.75rem;font-weight:600;cursor:pointer;transition:opacity 0.15s;}
.comentarios-form .comentario-enviar:hover{opacity:0.88;}
.detalle-comentarios-lista{max-height:420px;overflow-y:auto;}
.comentario-item{display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);font-size:0.8rem;}
.comentario-item:last-child{border-bottom:none;}
.comentario-avatar{flex-shrink:0;width:32px;height:32px;border-radius:50%;overflow:hidden;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;}
.comentario-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
.comentario-body{flex:1;min-width:0;}
.comentario-autor{font-weight:600;color:var(--text);font-size:0.76rem;}
.comentario-fecha{font-weight:400;color:var(--text-muted);margin-left:6px;font-size:0.66rem;}
.comentario-texto{color:var(--text);margin-top:3px;line-height:1.5;white-space:pre-line;}
.comentario-vacio{text-align:center;color:var(--text-muted);font-size:0.78rem;padding:10px 0;}

.dropdown-menu-custom a i{margin-right:6px;}
</style>

<script>
var DETALLE_PUB_ID = <?= (int) $publicacion['id'] ?>;
var DETALLE_PUB_TITULO = <?= json_encode($publicacion['titulo'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
var DETALLE_PUB_CONTENIDO = <?= json_encode($publicacion['contenido'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
var DETALLE_PUB_SECCION = <?= json_encode($publicacion['seccion'] ?? 'noticias', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<div class="detalle-topbar">
    <div class="detalle-topbar-left">
        <a href="<?= site_url('noticias') ?>" class="detalle-btn">
            <i class="bi bi-arrow-left"></i> Volver a Noticias
        </a>
    </div>
    <div class="detalle-topbar-right">
        <?php if ($esAdmin || $esAutor): ?>
        <a href="<?= site_url('borradores?select=' . (int) $publicacion['id']) ?>" class="detalle-btn primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <div class="dropdown">
            <button class="detalle-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones">
                <i class="bi bi-three-dots"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-custom dropdown-menu-end">
                <button class="dropdown-item" type="button" onclick="guardarComoDetalle('recordatorio')">
                    <i class="bi bi-bell-fill"></i> Recordatorio
                </button>
                <button class="dropdown-item" type="button" onclick="guardarComoDetalle('marcador')">
                    <i class="bi bi-bookmark-fill"></i> Marcador
                </button>
                <div class="dropdown-divider"></div>
                <?php if ($esAdmin || $esAutor): ?>
                <a class="dropdown-item" href="<?= site_url('borradores?select=' . (int) $publicacion['id']) ?>">
                    <i class="bi bi-pencil"></i> Editar borrador
                </a>
                <?php if ($esAdmin): ?>
                <button class="dropdown-item text-danger" type="button" onclick="despublicarDetalle()">
                    <i class="bi bi-x-circle"></i> Despublicar
                </button>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="detalle-card">
    <div class="detalle-head">
        <div class="detalle-autor">
            <div class="detalle-avatar">
                <?php if (!empty($publicacion['autor_foto'])): ?>
                    <img src="<?= base_url($publicacion['autor_foto']) ?>" alt="">
                <?php else: ?>
                    <?= esc(strtoupper(substr($publicacion['autor_nombre'] ?? 'A', 0, 1))) ?>
                <?php endif; ?>
            </div>
            <div class="detalle-autor-info">
                <div class="detalle-autor-nombre"><?= esc($publicacion['autor_nombre'] ?? 'Desconocido') ?></div>
                <div class="detalle-autor-rol"><?= esc($publicacion['autor_rol_legible'] ?? 'Empleado') ?></div>
            </div>
        </div>
        <div class="detalle-fechahora">
            <span class="detalle-fechahora-item">
                <i class="bi bi-calendar3"></i> Publicado el <b><?= esc($publicacion['fecha']) ?></b>
            </span>
            <span class="detalle-fechahora-item">
                <i class="bi bi-clock"></i> a las <b><?= esc($publicacion['hora']) ?></b> hrs
            </span>
        </div>
    </div>

    <div class="detalle-body">
        <h1 class="detalle-titulo"><?= esc($publicacion['titulo']) ?></h1>
        <div class="detalle-tags">
            <?php $tipo = $publicacion['destinatario_tipo'] ?? 'todos';
                  $tags = [
                      'usuarios'    => ['<i class="bi bi-person-fill"></i> Individual', ''],
                      'departamento'=> ['<i class="bi bi-people-fill"></i> Departamento', ''],
                      'multiple'    => ['<i class="bi bi-people-fill"></i> Multiple', ''],
                      'todos'       => ['<i class="bi bi-globe"></i> Todos', ''],
                  ];
                  $t = $tags[$tipo] ?? $tags['todos']; ?>
            <span class="detalle-tag"><?= $t[0] ?></span>
            <?php if ($publicacion['fijado']): ?>
            <span class="detalle-tag fijada"><i class="bi bi-pin-fill"></i> Fijada</span>
            <?php endif; ?>
        </div>
        <div class="detalle-contenido"><?= esc($publicacion['contenido'] ?? '') ?></div>
    </div>

    <div class="detalle-acciones">
        <button class="detalle-accion rec" onclick="guardarComoDetalle('recordatorio')" title="Agregar a Recordatorio">
            <i class="bi bi-bell-fill"></i> Recordatorio
        </button>
        <button class="detalle-accion mar" onclick="guardarComoDetalle('marcador')" title="Agregar a Marcadores">
            <i class="bi bi-bookmark-fill"></i> Marcador
        </button>
        <button class="detalle-accion com" onclick="toggleComentariosDetalle()" title="Ver comentarios">
            <i class="bi bi-chat-fill"></i> Comentarios
            <span class="com-count"><?= (int) ($publicacion['comentarios_count'] ?? 0) ?></span>
        </button>
    </div>

    <div class="detalle-comentarios" id="detalleComentarios" style="display:none;">
        <div class="detalle-comentarios-titulo">
            <i class="bi bi-chat-fill"></i> Comentarios
            <span class="com-count detalle-com-count" style="display:none;"></span>
        </div>
        <div class="comentarios-form">
            <div class="detalle-avatar">
                <?php $foto = session('admin_foto'); if (!empty($foto)): ?>
                    <img src="<?= base_url($foto) ?>" alt="">
                <?php else: ?>
                    <?= esc(strtoupper(substr(session('admin_nombre') ?? 'A', 0, 1))) ?>
                <?php endif; ?>
            </div>
            <textarea class="form-control" id="detalleComentarioTexto" rows="2" placeholder="Escribe un comentario..."></textarea>
            <button class="comentario-enviar" onclick="guardarComentarioDetalle(this)">Enviar</button>
        </div>
        <div class="detalle-comentarios-lista" id="detalleComentariosLista">
            <div class="comentario-vacio">Sin comentarios</div>
        </div>
    </div>
</div>