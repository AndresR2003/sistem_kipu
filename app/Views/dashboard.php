<style>
.welcome-section{text-align:center;padding:28px 16px;}
.welcome-icon{font-size:2.8rem;color:var(--primary);margin-bottom:10px;}
.welcome-title{font-size:1.5rem;font-weight:700;color:var(--text);margin-bottom:4px;}
.welcome-sub{font-size:0.9rem;color:var(--text-muted);margin-bottom:18px;}
.quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;max-width:640px;margin:0 auto;}
.quick-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:14px;text-align:center;cursor:pointer;transition:all 0.2s;text-decoration:none;display:block;}
.quick-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,0.15);}
.quick-card i{font-size:1.3rem;color:var(--primary);margin-bottom:6px;display:block;}
.quick-card span{font-size:0.8rem;color:var(--text);font-weight:500;}
</style>

<div class="welcome-section">
    <div class="welcome-icon"><i class="bi bi-building"></i></div>
    <div class="welcome-title">Bienvenido, <?= session()->get('admin_nombre') ?? 'Usuario' ?></div>
    <div class="welcome-sub">Sistema de Gestion Hotel Litio</div>

    <div class="quick-grid">
        <a class="quick-card" href="<?= site_url('noticias') ?>">
            <i class="bi bi-newspaper"></i>
            <span>Noticias</span>
        </a>
        <a class="quick-card" href="<?= site_url('tareas') ?>">
            <i class="bi bi-check2-square"></i>
            <span>Tareas</span>
        </a>
        <a class="quick-card" href="<?= site_url('recordatorio') ?>">
            <i class="bi bi-bell-fill"></i>
            <span>Recordatorios</span>
        </a>
        <a class="quick-card" href="<?= site_url('calendario') ?>">
            <i class="bi bi-calendar-fill"></i>
            <span>Calendario</span>
        </a>
        <?php if (in_array(session()->get('admin_rol'), ['admin', 'superadmin'])): ?>
        <a class="quick-card" href="<?= site_url('pagos') ?>">
            <i class="bi bi-credit-card-fill"></i>
            <span>Pagos</span>
        </a>
        <a class="quick-card" href="<?= site_url('colaboradores') ?>">
            <i class="bi bi-person-badge-fill"></i>
            <span>Personal</span>
        </a>
        <?php endif; ?>
    </div>
</div>
