<style>
.nd {
    --nd-bg: #1C1C1C;
    --nd-bg-side: #222222;
    --nd-border: rgba(255, 255, 255, 0.08);
    --nd-text: #FFFFFF;
    --nd-text2: #A0A0A0;
    --nd-accent: #8B5CF6;
    --nd-accent2: #7C3AED;
    --nd-blue: #2563EB;
    --nd-radius: 14px;

    max-width: 1280px;
    margin: 0 auto;
    color: var(--nd-text);
}

/* ─── Top bar ─── */
.nd-topbar { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 24px; }
.nd-back {
    display: inline-flex; align-items: center; gap: 8px;
    background: transparent; border: 1px solid var(--nd-border); color: var(--nd-text2);
    padding: 8px 16px; border-radius: 10px; font-size: 0.85rem; font-weight: 500;
    cursor: pointer; text-decoration: none; transition: all 0.15s;
}
.nd-back:hover { color: var(--nd-accent); border-color: var(--nd-accent); }
.nd-back i { font-size: 1rem; }
.nd-topbar-right { display: flex; align-items: center; gap: 8px; }
.nd-btn-edit {
    display: inline-flex; align-items: center; gap: 6px; background: var(--nd-blue); color: #fff;
    border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: opacity 0.15s;
}
.nd-btn-edit:hover { opacity: 0.88; color: #fff; }
.nd-icon-btn {
    display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px;
    background: transparent; border: 1px solid var(--nd-border); color: var(--nd-text2);
    border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.15s;
}
.nd-icon-btn:hover { color: var(--nd-accent); border-color: var(--nd-accent); }

/* ─── Grid 2 columnas ─── */
.nd-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    grid-template-rows: auto;
    gap: 28px;
    grid-template-areas:
        "topbar topbar"
        "chip   side1"
        "titulo side1"
        "resumen side2"
        "contenido side2"
        "acciones side2"
        "comentarios side2";
}

.nd-chip { grid-area: chip; }
.nd-titulo { grid-area: titulo; }
.nd-resumen { grid-area: resumen; }
.nd-contenido { grid-area: contenido; }
.nd-acciones { grid-area: acciones; }
.nd-comentarios { grid-area: comentarios; }
.nd-autor { grid-area: side1; }
.nd-fecha { grid-area: side2; }

/* ─── Chip ─── */
.nd-chip {
    display: inline-flex; align-items: center; gap: 6px; justify-self: start;
    height: 30px; padding: 0 16px; border-radius: 999px;
    background: rgba(124, 58, 237, 0.18); color: var(--nd-accent);
    font-size: 0.75rem; font-weight: 600; margin-bottom: 18px;
}
.nd-chip.fijada { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

/* ─── Titulo / resumen / contenido ─── */
.nd-titulo {
    font-size: clamp(26px, 3vw, 34px); font-weight: 700; line-height: 1.25;
    color: var(--nd-text); margin: 0 0 16px;
}
.nd-resumen {
    font-size: 1.05rem; font-weight: 400; color: #d1d1d1; line-height: 1.6;
    border-left: 3px solid var(--nd-accent); padding-left: 16px; margin: 0 0 20px;
}
.nd-contenido {
    background: var(--nd-bg); border: 1px solid var(--nd-border); border-radius: var(--nd-radius);
    padding: 24px 28px; font-size: 15px; line-height: 1.75; color: var(--nd-text);
    white-space: pre-line;
}

/* ─── Barra de acciones ─── */
.nd-acciones { display: flex; flex-wrap: wrap; gap: 8px; margin: 22px 0 0; }
.nd-accion {
    display: inline-flex; align-items: center; gap: 7px;
    background: transparent; border: 1px solid var(--nd-border); color: var(--nd-text2);
    padding: 8px 16px; border-radius: 10px; font-size: 0.8rem; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
}
.nd-accion:hover { background: rgba(255, 255, 255, 0.05); color: var(--nd-text); }
.nd-accion i { font-size: 0.95rem; }
.nd-accion.rec:hover { color: var(--nd-accent); border-color: var(--nd-accent); }
.nd-accion.mar:hover { color: var(--nd-accent); border-color: var(--nd-accent); }
.nd-accion.com:hover { color: var(--nd-blue); border-color: var(--nd-blue); }
.nd-accion .com-count {
    display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px;
    padding: 0 5px; border-radius: 999px; background: var(--nd-blue); color: #fff;
    font-size: 0.65rem; font-weight: 700; line-height: 1;
}

/* ─── Separador ─── */
.nd-sep { height: 1px; background: var(--nd-border); margin: 26px 0; }

/* ─── Comentarios ─── */
.nd-comentarios-titulo {
    font-size: 0.95rem; font-weight: 700; color: var(--nd-text); margin: 0 0 16px;
    display: flex; align-items: center; gap: 8px;
}
.nd-comentarios-titulo i { color: var(--nd-blue); }
.nd-comentarios-form { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.nd-comentarios-form .nd-avatar { width: 40px; height: 40px; flex-shrink: 0; }
.nd-comentarios-form textarea {
    flex: 1; min-height: 46px; max-height: 120px; resize: vertical;
    background: var(--nd-bg-side); border: 1px solid var(--nd-border); color: var(--nd-text);
    border-radius: 10px; padding: 10px 14px; font-size: 0.85rem; line-height: 1.5;
}
.nd-comentarios-form textarea:focus { border-color: var(--nd-accent); box-shadow: none; }
.nd-comentarios-form textarea::placeholder { color: var(--nd-text2); }
.nd-comentarios-form .comentario-enviar {
    align-self: center; background: var(--nd-blue); color: #fff; border: none;
    height: 44px; padding: 0 22px; border-radius: 8px; font-size: 0.82rem; font-weight: 600;
    cursor: pointer; white-space: nowrap; transition: opacity 0.15s;
}
.nd-comentarios-form .comentario-enviar:hover { opacity: 0.88; }

.nd-comentarios-lista { max-height: 420px; overflow-y: auto; }
.comentario-item { display: flex; align-items: flex-start; gap: 10px; padding: 12px 0; border-bottom: 1px solid var(--nd-border); font-size: 0.82rem; }
.comentario-item:last-child { border-bottom: none; }
.comentario-avatar { flex-shrink: 0; width: 34px; height: 34px; border-radius: 50%; overflow: hidden; background: var(--nd-accent2); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #fff; }
.comentario-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.comentario-body { flex: 1; min-width: 0; }
.comentario-autor { font-weight: 600; color: var(--nd-text); font-size: 0.78rem; }
.comentario-fecha { font-weight: 400; color: var(--nd-text2); margin-left: 8px; font-size: 0.68rem; }
.comentario-texto { color: #d1d1d1; margin-top: 3px; line-height: 1.55; white-space: pre-line; }
.comentario-vacio { text-align: center; color: var(--nd-text2); font-size: 0.8rem; padding: 12px 0; }

/* ─── Tarjetas laterales ─── */
.nd-card {
    background: var(--nd-bg-side); border: 1px solid var(--nd-border); border-radius: var(--nd-radius);
    padding: 18px 20px; box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
}
.nd-autor { margin-bottom: 24px; align-self: start; }
.nd-card-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.6px; color: var(--nd-text2); margin-bottom: 12px; font-weight: 600; }
.nd-avatar {
    width: 56px; height: 56px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
    background: linear-gradient(135deg, var(--nd-accent2), var(--nd-accent));
    display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 700; color: #fff;
}
.nd-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.nd-autor-row { display: flex; align-items: center; gap: 12px; }
.nd-autor-nombre { font-size: 0.95rem; font-weight: 600; color: var(--nd-text); line-height: 1.3; }
.nd-autor-rol { font-size: 0.8rem; color: var(--nd-text2); margin-top: 2px; }

.nd-fecha-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; }
.nd-fecha-item:first-child { padding-top: 0; }
.nd-fecha-item:last-child { padding-bottom: 0; }
.nd-fecha-item i { font-size: 1.05rem; color: var(--nd-accent); width: 20px; text-align: center; }
.nd-fecha-label { font-size: 0.68rem; color: var(--nd-text2); display: block; }
.nd-fecha-valor { font-size: 0.85rem; font-weight: 600; color: var(--nd-text); }

.dropdown-menu-custom a i { margin-right: 6px; }

/* ─── Responsive ─── */
@media (max-width: 1024px) {
    .nd-grid { grid-template-columns: minmax(0, 1fr) 260px; gap: 24px; }
}

@media (max-width: 900px) {
    .nd-grid {
        grid-template-columns: 1fr;
        grid-template-areas:
            "topbar"
            "chip"
            "titulo"
            "autor"
            "fecha"
            "resumen"
            "contenido"
            "acciones"
            "comentarios";
        gap: 20px;
    }
    .nd-autor { margin-bottom: 0; }
}
</style>

<script>
var DETALLE_PUB_ID = <?= (int) $publicacion['id'] ?>;
var DETALLE_PUB_TITULO = <?= json_encode($publicacion['titulo'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
var DETALLE_PUB_CONTENIDO = <?= json_encode($publicacion['contenido'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
var DETALLE_PUB_SECCION = <?= json_encode($publicacion['seccion'] ?? 'noticias', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<?php
$contenido = $publicacion['contenido'] ?? '';
$partes = preg_split('/\R\R+/', trim($contenido), 2);
$resumen = $partes[0] ?? '';
$autorFoto = !empty($publicacion['autor_foto']) ? base_url($publicacion['autor_foto']) : '';
$autorInicial = strtoupper(substr($publicacion['autor_nombre'] ?? 'A', 0, 1));
$tipo = $publicacion['destinatario_tipo'] ?? 'todos';
$chipMapa = [
    'usuarios'    => 'Individual',
    'departamento'=> 'Departamento',
    'multiple'    => 'Multiple',
    'todos'       => 'Todos',
];
$chipTexto = $chipMapa[$tipo] ?? 'Todos';
$fotoSesion = session('admin_foto');
?>

<div class="nd">
    <div class="nd-topbar">
        <a href="<?= site_url('noticias') ?>" class="nd-back">
            <i class="bi bi-arrow-left"></i> Volver a noticias
        </a>
        <?php if ($esAdmin || $esAutor): ?>
        <div class="nd-topbar-right">
            <a href="<?= site_url('borradores?select=' . (int) $publicacion['id']) ?>" class="nd-btn-edit">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <div class="dropdown">
                <button class="nd-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Mas opciones">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-custom dropdown-menu-end">
                    <button class="dropdown-item" type="button" onclick="guardarComoDetalle('recordatorio')">
                        <i class="bi bi-bell"></i> Recordatorio
                    </button>
                    <button class="dropdown-item" type="button" onclick="guardarComoDetalle('marcador')">
                        <i class="bi bi-bookmark"></i> Marcador
                    </button>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= site_url('borradores?select=' . (int) $publicacion['id']) ?>">
                        <i class="bi bi-pencil"></i> Editar borrador
                    </a>
                    <?php if ($esAdmin): ?>
                    <button class="dropdown-item text-danger" type="button" onclick="despublicarDetalle()">
                        <i class="bi bi-x-circle"></i> Despublicar
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="nd-grid">
        <span class="nd-chip">
            <i class="bi bi-<?= $tipo === 'usuarios' ? 'person-fill' : ($tipo === 'departamento' || $tipo === 'multiple' ? 'people-fill' : 'globe') ?>"></i>
            <?= esc($chipTexto) ?>
        </span>
        <span class="nd-chip fijada" <?= $publicacion['fijado'] ? '' : 'style="display:none;"' ?>>
            <i class="bi bi-pin-fill"></i> Fijada
        </span>

        <h1 class="nd-titulo"><?= esc($publicacion['titulo']) ?></h1>

        <?php if (!empty($resumen) && $resumen !== trim($contenido)): ?>
        <p class="nd-resumen"><?= esc($resumen) ?></p>
        <?php endif; ?>

        <div class="nd-contenido"><?= esc($contenido) ?></div>

        <div class="nd-acciones">
            <button class="nd-accion rec" onclick="guardarComoDetalle('recordatorio')" title="Agregar a Recordatorio">
                <i class="bi bi-bell"></i> Recordatorio
            </button>
            <button class="nd-accion mar" onclick="guardarComoDetalle('marcador')" title="Agregar a Marcadores">
                <i class="bi bi-bookmark"></i> Marcador
            </button>
            <button class="nd-accion com" onclick="toggleComentariosDetalle()" title="Ver comentarios">
                <i class="bi bi-chat"></i> Comentarios
                <span class="com-count" <?= ((int) ($publicacion['comentarios_count'] ?? 0)) > 0 ? '' : 'style="display:none;"' ?>><?= (int) ($publicacion['comentarios_count'] ?? 0) ?></span>
            </button>
        </div>

        <div class="nd-sep"></div>

        <div class="nd-comentarios" id="detalleComentarios" style="display:none;">
            <div class="nd-comentarios-titulo">
                <i class="bi bi-chat"></i> Comentarios
            </div>
            <div class="nd-comentarios-form">
                <div class="nd-avatar">
                    <?php if (!empty($fotoSesion)): ?>
                        <img src="<?= base_url($fotoSesion) ?>" alt="">
                    <?php else: ?>
                        <?= esc(strtoupper(substr(session('admin_nombre') ?? 'A', 0, 1))) ?>
                    <?php endif; ?>
                </div>
                <textarea id="detalleComentarioTexto" rows="2" placeholder="Escribe un comentario..."></textarea>
                <button class="comentario-enviar" onclick="guardarComentarioDetalle(this)">Enviar</button>
            </div>
            <div class="nd-comentarios-lista" id="detalleComentariosLista">
                <div class="comentario-vacio">Sin comentarios</div>
            </div>
        </div>

        <div class="nd-card nd-autor">
            <div class="nd-card-label">Publicado por</div>
            <div class="nd-autor-row">
                <div class="nd-avatar">
                    <?php if (!empty($autorFoto)): ?>
                        <img src="<?= $autorFoto ?>" alt="">
                    <?php else: ?>
                        <?= esc($autorInicial) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="nd-autor-nombre"><?= esc($publicacion['autor_nombre'] ?? 'Desconocido') ?></div>
                    <div class="nd-autor-rol"><?= esc($publicacion['autor_rol_legible'] ?? 'Empleado') ?></div>
                </div>
            </div>
        </div>

        <div class="nd-card nd-fecha">
            <div class="nd-card-label">Fecha de publicacion</div>
            <div class="nd-fecha-item">
                <i class="bi bi-calendar3"></i>
                <div>
                    <span class="nd-fecha-label">Fecha</span>
                    <span class="nd-fecha-valor"><?= esc($publicacion['fecha']) ?></span>
                </div>
            </div>
            <div class="nd-fecha-item">
                <i class="bi bi-clock"></i>
                <div>
                    <span class="nd-fecha-label">Hora</span>
                    <span class="nd-fecha-valor"><?= esc($publicacion['hora']) ?> hrs</span>
                </div>
            </div>
        </div>
    </div>
</div>