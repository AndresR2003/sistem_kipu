<?php
$titulo = $titulo ?? 'Kipucloud - Gestion Empresarial';
$marcaNombre = $marcaNombre ?? 'Kipucloud';
$marcaLigo = $marcaLigo ?? '';
$logueado = !empty($logueado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo) ?></title>
    <meta name="description" content="<?= esc($marcaNombre) ?> es la plataforma de gestion empresarial para organizar tareas, entregas de turno, noticias, calendario y mas dentro de tu empresa.">
    <meta name="keywords" content="<?= esc($marcaNombre) ?>, gestion empresarial, tareas, entregas, turnos, intranet, colaboradores">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?= esc($marcaNombre) ?> - Gestion Empresarial">
    <meta property="og:description" content="Plataforma interna para organizar tareas, entregas de turno, noticias y calendario de tu equipo.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= base_url() ?>">
    <meta property="og:site_name" content="<?= esc($marcaNombre) ?>">
    <link rel="canonical" href="<?= base_url() ?>">
    <link rel="icon" type="image/svg+xml" href="<?= esc($marcaLigo) ?>">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: #4669FA;
            --primary-dark: #3651d4;
            --bg: #0f0f1a;
            --bg-card: #1a1a2e;
            --text: #e2e8f0;
            --muted: rgba(255,255,255,0.52);
            --border: rgba(255,255,255,0.07);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            background-image: radial-gradient(ellipse at 30% 10%, rgba(70,105,250,0.10) 0%, transparent 55%),
                              radial-gradient(ellipse at 75% 90%, rgba(70,105,250,0.06) 0%, transparent 55%);
        }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
        a { color: var(--primary); text-decoration: none; }

        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 32px;
            background: rgba(15,15,26,0.85);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.05rem; }
        .brand-icon {
            width: 34px; height: 34px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: inline-flex; align-items: center; justify-content: center; color: #fff;
            overflow: hidden;
        }
        .brand-icon svg { width: 1.1em; height: 1.1em; }
        .brand-icon img { width: 100%; height: 100%; object-fit: contain; padding: 5px; }
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 22px; border-radius: 10px; font-weight: 600; font-size: 0.9rem;
            transition: all 0.25s; cursor: pointer; border: none;
        }
        .btn-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(70,105,250,0.35); }
        .btn-ghost { background: rgba(255,255,255,0.05); color: var(--text); border: 1px solid var(--border); }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); }

        .hero {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 110px 24px 70px;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 50px; font-size: 0.72rem; font-weight: 600;
            background: rgba(70,105,250,0.14); color: #8aa0ff; border: 1px solid rgba(70,105,250,0.3);
            margin-bottom: 22px;
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1.15;
            letter-spacing: -1px; margin-bottom: 18px;
        }
        .hero h1 span { background: linear-gradient(135deg, #6b8aff, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { font-size: clamp(1rem, 2vw, 1.15rem); color: var(--muted); max-width: 640px; margin: 0 auto 34px; }
        .hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        .section { padding: 70px 32px; }
        .section-tag { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--primary); margin-bottom: 10px; }
        .section h2 { font-size: clamp(1.5rem, 3vw, 2.1rem); font-weight: 800; margin-bottom: 14px; }
        .section > p.lead { color: var(--muted); max-width: 620px; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 36px; }
        .feature {
            background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px;
            padding: 22px; transition: all 0.25s;
        }
        .feature:hover { transform: translateY(-4px); border-color: rgba(70,105,250,0.35); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .feature i { font-size: 1.5rem; color: var(--primary); margin-bottom: 12px; display: block; }
        .feature h3 { font-size: 1rem; font-weight: 700; margin-bottom: 6px; }
        .feature p { font-size: 0.85rem; color: var(--muted); }

        .cta {
            text-align: center; padding: 80px 24px;
            background: linear-gradient(135deg, rgba(70,105,250,0.12), rgba(167,139,250,0.08));
            border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
        }
        .cta h2 { font-size: clamp(1.6rem, 3.5vw, 2.4rem); font-weight: 800; margin-bottom: 12px; }
        .cta p { color: var(--muted); margin-bottom: 28px; }

        footer { padding: 30px 32px; text-align: center; color: var(--muted); font-size: 0.8rem; border-top: 1px solid var(--border); }
        footer .footer-links { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-bottom: 10px; }
        footer a { color: var(--muted); }
        footer a:hover { color: var(--text); }

        @media (max-width: 640px) {
            .nav { padding: 12px 18px; }
            .section { padding: 50px 18px; }
        }
    </style>
</head>
<body>

    <nav class="nav">
        <div class="nav-brand">
            <span class="brand-icon">
                <img src="<?= esc($marcaLigo) ?>" alt="Logo <?= esc($marcaNombre) ?>">
            </span>
            <?= esc($marcaNombre) ?>
        </div>
        <a class="btn <?= $logueado ? 'btn-primary' : 'btn-ghost' ?>" href="<?= $logueado ? site_url('dashboard') : site_url('login') ?>">
            <i class="bi <?= $logueado ? 'bi-speedometer2' : 'bi-box-arrow-in-right' ?>"></i>
            <?= $logueado ? 'Ir al sistema' : 'Ingresar' ?>
        </a>
    </nav>

    <section class="hero">
        <div>
            <div class="hero-badge"><i class="bi bi-lightning-charge-fill"></i> Plataforma interna de gestion</div>
            <h1>Organiza tu empresa en<br>un solo lugar <span><?= esc($marcaNombre) ?></span></h1>
            <p>Tareas, entregas de turno, noticias, calendario, manuales y mas, centralizados para que tu equipo trabaje mejor coordinado.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= site_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i> Iniciar sesion</a>
                <a class="btn btn-ghost" href="#features"><i class="bi bi-grid-3x3-gap-fill"></i> Ver modulos</a>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <div class="section-tag">Modulos</div>
        <h2>Todo lo que tu equipo necesita</h2>
        <p class="lead">Herramientas simples y rapidas para la operacion diaria de tu negocio.</p>
        <div class="features">
            <div class="feature">
                <i class="bi bi-check2-square"></i>
                <h3>Tareas</h3>
                <p>Crea tareas por departamento y usuario, con prioridades, fechas limite y seguimiento de completado.</p>
            </div>
            <div class="feature">
                <i class="bi bi-arrow-left-right"></i>
                <h3>Entregas / Pases de turno</h3>
                <p>Registra y consulta los pases de turno para que el relevo siempre este informado.</p>
            </div>
            <div class="feature">
                <i class="bi bi-newspaper"></i>
                <h3>Noticias</h3>
                <p>Publica anuncios y novedades para todo el equipo desde un panel de borradores.</p>
            </div>
            <div class="feature">
                <i class="bi bi-lightbulb-fill"></i>
                <h3>Ideas</h3>
                <p>Recopila sugerencias y propuestas de mejora de todos los colaboradores.</p>
            </div>
            <div class="feature">
                <i class="bi bi-book-fill"></i>
                <h3>Manual</h3>
                <p>Documenta procedimientos y guias de referencia siempre disponibles.</p>
            </div>
            <div class="feature">
                <i class="bi bi-calendar-fill"></i>
                <h3>Calendario</h3>
                <p>Programa eventos, fechas importantes y actividades del equipo.</p>
            </div>
            <div class="feature">
                <i class="bi bi-bell-fill"></i>
                <h3>Recordatorios</h3>
                <p>Guardate recordatorios de tareas y publicaciones para no olvidar nada.</p>
            </div>
            <div class="feature">
                <i class="bi bi-tools"></i>
                <h3>Reparaciones</h3>
                <p>Lleva el control de solicitudes de reparacion y mantenimiento.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>¿Listo para empezar?</h2>
        <p>Ingresa con tu cuenta para acceder a los modulos de <?= esc($marcaNombre) ?>.</p>
        <a class="btn btn-primary" href="<?= site_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i> Iniciar sesion</a>
    </section>

    <footer>
        <div class="footer-links">
            <a href="<?= site_url('login') ?>">Ingresar</a>
        </div>
        &copy; <?= date('Y') ?> <?= esc($marcaNombre) ?>. Todos los derechos reservados.
    </footer>

</body>
</html>
