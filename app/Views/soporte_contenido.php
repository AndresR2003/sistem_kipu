<style>
    .soporte-back {
        display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 500;
        color: var(--text-muted); text-decoration: none; margin-bottom: 16px; transition: color 0.15s;
    }
    .soporte-back:hover { color: var(--primary); text-decoration: none; }
    .soporte-content { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 28px 32px; }
    .soporte-content h5 { font-size: 1.05rem; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .soporte-content h6 { font-size: 0.88rem; font-weight: 600; margin-top: 18px; margin-bottom: 8px; color: var(--text); }
    .soporte-content p { font-size: 0.85rem; color: var(--text); line-height: 1.7; margin-bottom: 10px; }
    .soporte-content ul, .soporte-content ol { font-size: 0.85rem; color: var(--text); line-height: 1.7; padding-left: 20px; margin-bottom: 10px; }
    .soporte-content li { margin-bottom: 4px; }
    .soporte-content .text-muted { font-size: 0.78rem; }
    .soporte-form label { font-size: 0.8rem; font-weight: 500; color: var(--text); }
    .soporte-form input, .soporte-form textarea, .soporte-form select {
        font-size: 0.85rem; background: var(--bg-input); color: var(--text); border-color: var(--border);
    }
    .soporte-form input:focus, .soporte-form textarea:focus, .soporte-form select:focus {
        border-color: var(--primary); box-shadow: none;
    }
    .soporte-badge {
        display: inline-block; font-size: 0.65rem; padding: 2px 10px; border-radius: 10px;
        font-weight: 600; margin-left: 10px;
    }
    .soporte-badge.ayuda { background: rgba(70,105,250,0.12); color: var(--primary); }
    .soporte-badge.legal { background: rgba(168,85,247,0.12); color: #a855f7; }
</style>

<a href="<?= site_url('soporte') ?>" class="soporte-back">
    <i class="bi bi-arrow-left"></i> Volver a Soporte
</a>

<div class="soporte-content">
    <h5>
        <i class="<?= $icono ?>"></i> <?= $titulo ?>
        <span class="soporte-badge <?= $grupo ?>"><?= $grupo === 'ayuda' ? 'Ayuda y Soporte' : 'Legal' ?></span>
    </h5>
    <?= $contenido ?>
</div>
