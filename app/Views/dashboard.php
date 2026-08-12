<?php
$marcaNombre = 'Kipucloud';
try {
    $cfgMarca = model('App\Models\ConfiguracionVisualModel')->Obtener();
    if (!empty($cfgMarca['marca_activa']) && !empty($cfgMarca['marca_nombre'])) {
        $marcaNombre = $cfgMarca['marca_nombre'];
    }
} catch (\Throwable $e) {
    $marcaNombre = 'Kipucloud';
}
?>
<style>
    .dash-hero{
        position:relative;
        border-radius:var(--radius);
        padding:28px 30px;
        color:#fff;
        background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 55%,#5a2ea6 100%);
        overflow:hidden;
        margin-bottom:18px;
        box-shadow:0 10px 30px rgba(70,105,250,0.28);
    }
    .dash-hero::before{
        content:'';
        position:absolute;
        right:-40px;
        top:-60px;
        width:260px;
        height:260px;
        border-radius:50%;
        background:rgba(255,255,255,0.08);
    }
    .dash-hero::after{
        content:'';
        position:absolute;
        right:80px;
        bottom:-90px;
        width:200px;
        height:200px;
        border-radius:50%;
        background:rgba(255,255,255,0.06);
    }
    .dash-hero .hero-welcome{font-size:0.75rem;text-transform:uppercase;letter-spacing:1.6px;opacity:0.85;font-weight:600;}
    .dash-hero h2{margin:6px 0 4px;font-weight:800;font-size:1.5rem;letter-spacing:-0.5px;}
    .dash-hero p{margin:0;opacity:0.92;font-size:0.85rem;max-width:420px;}
    .dash-hero .hero-date{
        display:inline-flex;align-items:center;gap:7px;margin-top:14px;
        background:rgba(255,255,255,0.16);backdrop-filter:blur(6px);
        padding:6px 14px;border-radius:50px;font-size:0.75rem;font-weight:600;
        border:1px solid rgba(255,255,255,0.25);
    }

    .dash-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:18px;}
    .dash-stat{
        background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);
        padding:16px;position:relative;overflow:hidden;transition:all 0.25s;
    }
    .dash-stat:hover{border-color:var(--border-light);transform:translateY(-3px);box-shadow:var(--shadow);}
    .dash-stat .ds-icon{
        width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;
        font-size:1.15rem;margin-bottom:12px;
    }
    .dash-stat .ds-value{font-size:1.6rem;font-weight:800;letter-spacing:-0.5px;line-height:1.1;}
    .dash-stat .ds-label{color:var(--text-muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.7px;font-weight:600;margin-top:2px;}

    .dash-section-title{
        display:flex;align-items:center;gap:8px;margin:20px 0 12px;
        font-weight:700;font-size:0.95rem;
    }
    .dash-section-title i{color:var(--primary);}
    .dash-section-title .line{flex:1;height:1px;background:var(--border);}

    .quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;}
    .quick-card{
        background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);
        padding:16px 14px;text-align:center;cursor:pointer;transition:all 0.2s;
        text-decoration:none;display:block;position:relative;overflow:hidden;
    }
    .quick-card::before{content:'';position:absolute;top:0;left:0;width:100%;height:3px;background:var(--qcol,var(--primary-gradient));opacity:0;transition:opacity 0.25s;}
    .quick-card:hover{transform:translateY(-3px);box-shadow:var(--shadow);border-color:var(--border-light);}
    .quick-card:hover::before{opacity:1;}
    .quick-card .q-icon{
        width:44px;height:44px;border-radius:12px;margin:0 auto 10px;
        display:flex;align-items:center;justify-content:center;font-size:1.25rem;
        background:var(--qbg,rgba(70,105,250,0.12));color:var(--qcol,var(--primary));
    }
    .quick-card span{font-size:0.8rem;color:var(--text);font-weight:600;}
    .quick-card small{display:block;color:var(--text-muted);font-size:0.66rem;margin-top:2px;}

    .dash-widgets{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .dash-widget{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
    .dash-widget .dw-head{
        padding:12px 16px;border-bottom:1px solid var(--border);
        display:flex;align-items:center;justify-content:space-between;gap:8px;
    }
    .dash-widget .dw-head b{font-size:0.82rem;display:flex;align-items:center;gap:8px;}
    .dash-widget .dw-head b i{color:var(--primary);}
    .dash-widget .dw-head a{font-size:0.7rem;color:var(--primary);text-decoration:none;font-weight:600;}
    .dash-widget .dw-body{padding:6px 16px;}
    .dw-item{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px dashed var(--border);}
    .dw-item:last-child{border-bottom:none;}
    .dw-item .dw-icon{
        width:38px;height:38px;border-radius:10px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;font-size:1rem;
    }
    .dw-item .dw-txt{flex:1;min-width:0;}
    .dw-item .dw-txt b{font-size:0.8rem;font-weight:600;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .dw-item .dw-txt small{color:var(--text-muted);font-size:0.7rem;}
    .dw-item .dw-date{
        font-size:0.66rem;color:var(--text-muted);background:var(--bg-input);
        padding:3px 10px;border-radius:20px;white-space:nowrap;flex-shrink:0;
    }
    .dw-empty{color:var(--text-muted);font-size:0.78rem;padding:16px 0;text-align:center;}

    /* ===== Requiere atencion ===== */
    .alert-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;}
    .alert-card{
        position:relative;overflow:hidden;border-radius:var(--radius);
        padding:18px 18px 14px;display:flex;flex-direction:column;gap:10px;
        border:1px solid var(--alert-border,#ffffff14);
        transition:transform 0.2s,box-shadow 0.2s;
        background:var(--alert-bg);
    }
    .alert-card:hover{transform:translateY(-3px);box-shadow:var(--shadow);}
    .alert-card .ac-top{display:flex;align-items:center;justify-content:space-between;}
    .alert-card .ac-icon{
        width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;
        font-size:1.15rem;color:var(--alert-color);background:var(--alert-icon-bg);flex-shrink:0;
    }
    .alert-card .ac-badge{
        font-size:0.62rem;text-transform:uppercase;letter-spacing:0.6px;font-weight:700;
        padding:4px 10px;border-radius:20px;color:var(--alert-color);
        background:var(--alert-icon-bg);
    }
    .alert-card .ac-num{font-size:2rem;font-weight:800;line-height:1;letter-spacing:-1px;color:var(--alert-color);}
    .alert-card .ac-label{font-size:0.82rem;font-weight:600;color:var(--text);line-height:1.3;}
    .alert-card .ac-link{
        margin-top:auto;font-size:0.72rem;font-weight:600;text-decoration:none;
        color:var(--alert-color);display:inline-flex;align-items:center;gap:4px;transition:gap 0.2s;
    }
    .alert-card .ac-link:hover{gap:8px;text-decoration:none;opacity:0.85;}

    .alert-card--tareas  { --alert-color:#f87171; --alert-bg:#2a1518; --alert-border:#7f1d1d7f; --alert-icon-bg:#450a0a66; }
    .alert-card--repar   { --alert-color:#fb923c; --alert-bg:#291c12; --alert-border:#7c2d127f; --alert-icon-bg:#43140766; }
    .alert-card--petic   { --alert-color:#fbbf24; --alert-bg:#2a2410; --alert-border:#78350f7f; --alert-icon-bg:#451a0366; }
    .alert-card--turnos  { --alert-color:#60a5fa; --alert-bg:#101c2e; --alert-border:#1e3a5f7f; --alert-icon-bg:#17255466; }

    @media (max-width: 992px){
        .alert-row{grid-template-columns:repeat(2,1fr);}
    }
    @media (max-width: 576px){
        .alert-row{grid-template-columns:1fr;}
    }

    @media (max-width: 768px){
        .dash-widgets{grid-template-columns:1fr;}
        .dash-hero{padding:22px 20px;}
    }
</style>

<div class="dash-hero">
    <div style="position:relative;z-index:1;">
        <div class="hero-welcome"><i class="bi bi-building"></i> Sistema de Gestion Hotel <?= esc($marcaNombre) ?></div>
        <h2>Bienvenido, <?= esc(session()->get('admin_nombre') ?? 'Usuario') ?></h2>
        <p>Esto es lo que esta pasando hoy en tu cuenta.</p>
        <div class="hero-date"><i class="bi bi-calendar3"></i> <?= esc(fecha_es('now', 'largo')) ?></div>
    </div>
</div>

<div class="dash-stats">
    <div class="dash-stat">
        <div class="ds-icon" style="background:rgba(70,105,250,0.12);color:var(--primary);"><i class="bi bi-check2-square"></i></div>
        <div class="ds-value"><?= (int)($stats['tareas_hoy'] ?? 0) ?></div>
        <div class="ds-label">Tareas de hoy</div>
    </div>
    <div class="dash-stat">
        <div class="ds-icon" style="background:rgba(34,197,94,0.12);color:var(--success);"><i class="bi bi-check-circle"></i></div>
        <div class="ds-value"><?= (int)($stats['tareas_done'] ?? 0) ?></div>
        <div class="ds-label">Completadas hoy</div>
    </div>
    <div class="dash-stat">
        <div class="ds-icon" style="background:rgba(6,182,212,0.12);color:var(--info);"><i class="bi bi-newspaper"></i></div>
        <div class="ds-value"><?= (int)($stats['noticias'] ?? 0) ?></div>
        <div class="ds-label">Noticias</div>
    </div>
    <div class="dash-stat">
        <div class="ds-icon" style="background:rgba(245,158,11,0.12);color:var(--warning);"><i class="bi bi-bell-fill"></i></div>
        <div class="ds-value"><?= (int)($stats['recordatorios'] ?? 0) ?></div>
        <div class="ds-label">Pendientes</div>
    </div>
    <div class="dash-stat">
        <div class="ds-icon" style="background:rgba(168,85,247,0.14);color:#a855f7;"><i class="bi bi-bookmark-fill"></i></div>
        <div class="ds-value"><?= (int)($stats['marcadores'] ?? 0) ?></div>
        <div class="ds-label">Marcadores</div>
    </div>
    <div class="dash-stat">
        <div class="ds-icon" style="background:rgba(239,68,68,0.12);color:var(--danger);"><i class="bi bi-calendar-event"></i></div>
        <div class="ds-value"><?= (int)($stats['eventos'] ?? 0) ?></div>
        <div class="ds-label">Eventos proximos</div>
    </div>
</div>

<div class="dash-section-title">
    <i class="bi bi-grid-1x2-fill"></i> Accesos rapidos
    <span class="line"></span>
</div>

<div class="quick-grid">
    <a class="quick-card" style="--qcol:#4669FA;--qbg:rgba(70,105,250,0.12);" href="<?= site_url('noticias') ?>">
        <div class="q-icon"><i class="bi bi-newspaper"></i></div>
        <span>Noticias</span>
        <small>Publicaciones</small>
    </a>
    <a class="quick-card" style="--qcol:#22c55e;--qbg:rgba(34,197,94,0.12);" href="<?= site_url('tareas') ?>">
        <div class="q-icon"><i class="bi bi-check2-square"></i></div>
        <span>Tareas</span>
        <small>Diarias</small>
    </a>
    <a class="quick-card" style="--qcol:#f59e0b;--qbg:rgba(245,158,11,0.12);" href="<?= site_url('recordatorio') ?>">
        <div class="q-icon"><i class="bi bi-bell-fill"></i></div>
        <span>Recordatorios</span>
        <small>Pendientes</small>
    </a>
    <a class="quick-card" style="--qcol:#06b6d4;--qbg:rgba(6,182,212,0.12);" href="<?= site_url('calendario') ?>">
        <div class="q-icon"><i class="bi bi-calendar-fill"></i></div>
        <span>Calendario</span>
        <small>Eventos</small>
    </a>
    <a class="quick-card" style="--qcol:#a855f7;--qbg:rgba(168,85,247,0.12);" href="<?= site_url('marcadores') ?>">
        <div class="q-icon"><i class="bi bi-bookmark-fill"></i></div>
        <span>Marcadores</span>
        <small>Guardados</small>
    </a>
    <a class="quick-card" style="--qcol:#ec4899;--qbg:rgba(236,72,153,0.12);" href="<?= site_url('entregas') ?>">
        <div class="q-icon"><i class="bi bi-arrow-left-right"></i></div>
        <span>Entregas</span>
        <small>Pases de turno</small>
    </a>
    <a class="quick-card" style="--qcol:#14b8a6;--qbg:rgba(20,184,166,0.12);" href="<?= site_url('ideas') ?>">
        <div class="q-icon"><i class="bi bi-lightbulb-fill"></i></div>
        <span>Ideas</span>
        <small>Sugerencias</small>
    </a>
    <a class="quick-card" style="--qcol:#f97316;--qbg:rgba(249,115,22,0.12);" href="<?= site_url('reparaciones') ?>">
        <div class="q-icon"><i class="bi bi-tools"></i></div>
        <span>Reparaciones</span>
        <small>Mantenimiento</small>
    </a>
</div>

<div class="dash-section-title">
    <i class="bi bi-exclamation-triangle-fill" style="color:#f87171;"></i> Requiere atencion
    <span class="line"></span>
</div>

<div class="alert-row">
    <a class="alert-card alert-card--tareas" href="<?= site_url('entregas') ?>">
        <div class="ac-top">
            <div class="ac-icon"><i class="bi bi-arrow-repeat"></i></div>
            <span class="ac-badge">Tareas</span>
        </div>
        <div class="ac-num"><?= (int)($stats['tareas_vencidas'] ?? 0) ?></div>
        <div class="ac-label">Tareas vencidas</div>
        <span class="ac-link">Ver tareas <i class="bi bi-arrow-right"></i></span>
    </a>

    <a class="alert-card alert-card--repar" href="<?= site_url('reparaciones') ?>">
        <div class="ac-top">
            <div class="ac-icon"><i class="bi bi-tools"></i></div>
            <span class="ac-badge">Mantenimiento</span>
        </div>
        <div class="ac-num"><?= (int)($stats['reparaciones_pendientes'] ?? 0) ?></div>
        <div class="ac-label">Reparaciones pendientes</div>
        <span class="ac-link">Ver reparaciones <i class="bi bi-arrow-right"></i></span>
    </a>

    <a class="alert-card alert-card--petic" href="<?= site_url('peticiones') ?>">
        <div class="ac-top">
            <div class="ac-icon"><i class="bi bi-chat-dots-fill"></i></div>
            <span class="ac-badge">Comunicacion</span>
        </div>
        <div class="ac-num"><?= (int)($stats['peticiones_pendientes'] ?? 0) ?></div>
        <div class="ac-label">Peticiones sin responder</div>
        <span class="ac-link">Ver peticiones <i class="bi bi-arrow-right"></i></span>
    </a>

    <a class="alert-card alert-card--turnos" href="<?= site_url('entregas') ?>">
        <div class="ac-top">
            <div class="ac-icon"><i class="bi bi-arrow-left-right"></i></div>
            <span class="ac-badge">Operaciones</span>
        </div>
        <div class="ac-num"><?= (int)($stats['pases_pendientes'] ?? 0) ?></div>
        <div class="ac-label">Pases de turno pendientes</div>
        <span class="ac-link">Ver pases de turno <i class="bi bi-arrow-right"></i></span>
    </a>
</div>

<div class="dash-section-title">
    <i class="bi bi-activity"></i> Actividad reciente
    <span class="line"></span>
</div>

<div class="dash-widgets">
    <div class="dash-widget">
        <div class="dw-head">
            <b><i class="bi bi-newspaper"></i> Ultimas noticias</b>
            <a href="<?= site_url('noticias') ?>">Ver todo <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="dw-body">
            <?php if (empty($noticias)): ?>
                <div class="dw-empty">Aun no hay noticias publicadas.</div>
            <?php else: ?>
                <?php foreach ($noticias as $n): ?>
                <div class="dw-item">
                    <div class="dw-icon" style="background:rgba(70,105,250,0.12);color:var(--primary);"><i class="bi bi-file-text"></i></div>
                    <div class="dw-txt">
                        <b><?= esc($n['titulo'] ?? '') ?></b>
                        <small><?= esc($n['etiqueta'] ?? '') ?><?= !empty($n['etiqueta']) ? ' • ' : '' ?>Por <?= esc($n['usuario_nombre'] ?? 'Staff') ?></small>
                    </div>
                    <span class="dw-date"><?= !empty($n['updated_at']) ? esc(fecha_es($n['updated_at'], 'abreviado')) : '' ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="dash-widget">
        <div class="dw-head">
            <b><i class="bi bi-calendar-event"></i> Proximos eventos</b>
            <a href="<?= site_url('calendario') ?>">Ver todo <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="dw-body">
            <?php if (empty($eventos)): ?>
                <div class="dw-empty">No hay eventos programados.</div>
            <?php else: ?>
                <?php foreach ($eventos as $ev): ?>
                <div class="dw-item">
                    <div class="dw-icon" style="background:<?= esc($ev['color'] ?? 'rgba(70,105,250,0.12)') ?>1f;color:<?= esc($ev['color'] ?? 'var(--primary)') ?>;"><i class="bi bi-calendar-check"></i></div>
                    <div class="dw-txt">
                        <b><?= esc($ev['titulo'] ?? '') ?></b>
                        <small><?= esc($ev['descripcion'] ?? '') ?></small>
                    </div>
                    <span class="dw-date"><?= !empty($ev['fecha_inicio']) ? esc(fecha_es($ev['fecha_inicio'], 'abreviado')) : '' ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
