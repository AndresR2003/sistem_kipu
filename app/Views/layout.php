<!DOCTYPE html>
<html lang="es" data-theme="dark">
<?php
try {
    $cfg = model('App\Models\ConfiguracionVisualModel')->Obtener();
    $anuncioActivo = service('cache')->get('ultimo_anuncio');
    if ($anuncioActivo === null) {
        $anuncioActivo = model('App\Models\BorradorModel')->ObtenerUltimoAnuncio();
        service('cache')->save('ultimo_anuncio', $anuncioActivo, 60);
    }
} catch (\Throwable $e) {
    $cfg = [];
    $anuncioActivo = null;
}

$marcaActiva = !empty($cfg['marca_activa']);
$marcaNombre = ($marcaActiva && !empty($cfg['marca_nombre'])) ? $cfg['marca_nombre'] : 'Kipucloud';
$marcaLigo   = ($marcaActiva && !empty($cfg['marca_logo'])) ? base_url($cfg['marca_logo']) : '';
$idleMinutes = max(1, (int) ($cfg['session_idle_minutes'] ?? 15));
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(str_replace('Kipucloud', $marcaNombre, $titulo ?? 'Kipucloud')) ?></title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dataTables.bootstrap5.min.css') ?>" rel="stylesheet">

    <style>
        :root {
            --primary: #4669FA;
            --primary-dark: #3651d4;
            --primary-light: #6b8aff;
            --primary-gradient: linear-gradient(135deg, var(--primary) 0%, #3651d4 100%);
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --sidebar-width: 226px;
            --sidebar-collapsed: 62px;
            --topbar-height: 52px;
            --radius: 12px;
            --radius-sm: 8px;
            --transition: 0.25s ease;
        }

        [data-theme="dark"] {
            --bg-body: #0f0f1a;
            --bg-sidebar: #13131f;
            --bg-card: #1a1a2e;
            --bg-card-alt: #1e1e33;
            --bg-table: rgba(26,26,46,0.6);
            --bg-input: rgba(255,255,255,0.04);
            --bg-input-hover: rgba(255,255,255,0.07);
            --bg-topbar: rgba(15,15,26,0.92);
            --text: #e2e8f0;
            --text-muted: rgba(255,255,255,0.52);
            --text-nav: rgba(255,255,255,0.55);
            --border: rgba(255,255,255,0.05);
            --border-light: rgba(255,255,255,0.1);
            --shadow: 0 4px 20px rgba(0,0,0,0.3);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.4);
            --modal-bg: #1a1a2e;
            --swal-bg: #1a1a2e;
        }

        [data-theme="light"] {
            --bg-body: #e4e9f1;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-card-alt: #f0f4fa;
            --bg-table: rgba(255,255,255,0.92);
            --bg-input: #eef2f7;
            --bg-input-hover: #e2e8f0;
            --bg-topbar: rgba(255,255,255,0.95);
            --text: #1e293b;
            --text-muted: rgba(0,0,0,0.45);
            --text-nav: rgba(0,0,0,0.58);
            --border: rgba(15,23,42,0.14);
            --border-light: rgba(15,23,42,0.22);
            --shadow: 0 3px 8px rgba(15,23,42,0.08);
            --shadow-lg: 0 12px 32px rgba(15,23,42,0.12);
            --modal-bg: #ffffff;
            --swal-bg: #ffffff;
        }

        [data-theme="dark"] {
            <?php if (!empty($cfg['sidebar_bg']))        echo '--bg-sidebar:' . $cfg['sidebar_bg'] . ';'; ?>
            <?php if (!empty($cfg['sidebar_text']))      echo '--text-nav:' . $cfg['sidebar_text'] . ';'; ?>
            <?php if (!empty($cfg['sidebar_active_bg'])) echo '--active-bg:' . $cfg['sidebar_active_bg'] . ';'; ?>
            <?php if (!empty($cfg['topbar_bg']))          echo '--bg-topbar:' . $cfg['topbar_bg'] . ';'; ?>
            <?php if (!empty($cfg['topbar_text']))        echo '--topbar-text:' . $cfg['topbar_text'] . ';'; ?>
            <?php if (!empty($cfg['primary_color']))      echo '--primary:' . $cfg['primary_color'] . ';'; ?>
            <?php if (!empty($cfg['content_bg']))         echo '--bg-body:' . $cfg['content_bg'] . ';'; ?>
            <?php if (!empty($cfg['card_bg']))            echo '--bg-card:' . $cfg['card_bg'] . ';'; ?>
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text);
            min-height: 100vh;
            font-size: 0.83rem;
            line-height: 1.45;
        }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-light); border-radius: 10px; }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: width var(--transition);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        [data-sidebar="collapsed"] .sidebar { width: var(--sidebar-collapsed); }

        .sidebar-brand {
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 52px;
            flex-shrink: 0;
            position: relative;
        }

        .sidebar-brand .brand-icon {
            width: 30px;
            height: 30px;
            border-radius: var(--radius-sm);
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            overflow: hidden;
            white-space: nowrap;
            transition: opacity var(--transition);
        }

        .sidebar-brand .brand-text h5 {
            font-weight: 800;
            font-size: 0.95rem;
            margin: 0;
            color: var(--text);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .sidebar-brand .brand-text small {
            color: var(--text-muted);
            font-size: 0.55rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            display: block;
        }

        [data-sidebar="collapsed"] .sidebar-brand .brand-text {
            opacity: 0;
            width: 0;
            margin: 0;
        }

        [data-sidebar="collapsed"] .sidebar-brand {
            padding: 16px 5px;
            gap: 4px;
        }

        .sidebar-toggle-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1rem;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s;
            flex-shrink: 0;
            margin-left: auto;
        }

        .sidebar-toggle-btn:hover { color: var(--text); background: var(--bg-input); }

        [data-sidebar="collapsed"] .sidebar-toggle-btn {
            padding: 4px 2px;
            margin-left: auto;
            transform: rotate(180deg);
        }

        [data-sidebar="collapsed"] .sidebar-toggle-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .sidebar-nav {
            padding: 8px 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav .nav-label {
            padding: 6px 16px 2px;
            font-size: 0.55rem;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        [data-sidebar="collapsed"] .sidebar-nav .nav-label { opacity: 0; }

        .sidebar-nav .nav-item { margin: 1px 8px; }

        .sidebar-nav .nav-link {
            color: var(--text-nav);
            padding: 7px 10px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
            position: relative;
        }

        .sidebar-nav .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-nav .nav-link span {
            overflow: hidden;
            transition: opacity var(--transition);
        }

        [data-sidebar="collapsed"] .sidebar-nav .nav-link span { opacity: 0; width: 0; }

        .sidebar-nav .nav-link:hover {
            background: color-mix(in srgb, var(--primary) 8%, transparent);
            color: var(--text);
        }

        .sidebar-nav .nav-link.active {
            background: var(--active-bg, var(--primary));
            color: #fff;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--primary) 35%, transparent);
        }

        .sidebar-nav .nav-divider {
            height: 1px;
            background: var(--border);
            margin: 6px 14px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left var(--transition);
        }

        [data-sidebar="collapsed"] .main-content { margin-left: var(--sidebar-collapsed); }

        .top-navbar {
            background: var(--bg-topbar);
            color: var(--topbar-text, var(--text));
            backdrop-filter: blur(16px);
            padding: 0 28px;
            height: var(--topbar-height);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-navbar h6 {
            margin: 0;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--topbar-text, var(--text));
        }

        .topbar-anuncio {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 10px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 600;
            background: color-mix(in srgb, var(--primary) 18%, transparent);
            color: var(--topbar-text, var(--text));
            border: 1px solid color-mix(in srgb, var(--primary) 35%, transparent);
            white-space: nowrap;
            max-width: 340px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-anuncio i { color: var(--warning); }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .topbar-btn {
            background: none;
            border: none;
            color: var(--topbar-text, var(--text-muted));
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .topbar-btn:hover { background: var(--bg-input); color: var(--topbar-text, var(--text)); }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: #fff;
            border-radius: 50%;
            width: 17px;
            height: 17px;
            font-size: 0.55rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 2px solid var(--bg-topbar);
        }

        .notif-panel {
            width: 320px;
            padding: 0;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            max-height: 420px;
        }

        .notif-panel.show {
            display: flex;
            flex-direction: column;
        }

        .notif-titulo {
            margin: 0;
            padding: 12px 16px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        .notif-titulo i { color: var(--primary); margin-right: 6px; }

        .notif-lista {
            overflow-y: auto;
            flex: 1;
        }

        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .notif-item:hover { background: var(--bg-input); }

        .notif-item .notif-icon {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            background: rgba(245,158,11,0.14);
            color: var(--warning);
        }

        .notif-item .notif-txt { flex: 1; min-width: 0; }
        .notif-item .notif-txt .t { font-size: 0.78rem; font-weight: 600; color: var(--text); line-height: 1.35; }
        .notif-item .notif-txt .s { font-size: 0.68rem; color: var(--text-muted); margin-top: 2px; }

        .notif-vacio {
            padding: 24px 16px;
            text-align: center;
            font-size: 0.75rem;
        }

        .notif-vacio i { font-size: 1.4rem; opacity: 0.4; display: block; margin-bottom: 6px; }

        .topbar-actions .dropdown-menu-custom {
            background: var(--modal-bg);
        }

        /* Chat grupal flotante */
        .chat-fab {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 1040;
            width: 56px;
            height: 56px;
            border: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            color: #fff;
            font-size: 1.35rem;
            box-shadow: 0 8px 22px rgba(70,105,250,0.35);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .chat-fab:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(70,105,250,0.45); }
        .chat-fab .chat-fab-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 19px;
            height: 19px;
            padding: 0 5px;
            border: 2px solid var(--bg-body);
            border-radius: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            background: var(--danger);
            color: #fff;
            font-size: 0.58rem;
            font-weight: 800;
        }

        .chat-panel {
            position: fixed;
            right: 24px;
            bottom: 92px;
            z-index: 1040;
            width: 700px;
            height: min(590px, calc(100vh - 120px));
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--border-light);
            border-radius: 16px;
            background: var(--modal-bg);
            color: var(--text);
            box-shadow: 0 18px 55px rgba(0,0,0,0.28);
        }

        .chat-panel.is-open { display: flex; }
        .chat-layout { display: flex; flex: 1; min-height: 0; }
        .chat-sidebar { width: 230px; flex-shrink: 0; overflow-y: auto; border-right: 1px solid var(--border); background: var(--modal-bg); }
        .chat-sidebar-title { padding: 14px 15px 10px; color: var(--text); font-size: 0.82rem; font-weight: 800; }
        .chat-conversations { padding: 0 7px 8px; }
        .chat-conversation { display: flex; align-items: center; gap: 9px; width: 100%; padding: 9px 8px; border: 0; border-radius: 9px; background: transparent; color: var(--text); text-align: left; cursor: pointer; }
        .chat-conversation:hover { background: var(--bg-input); }
        .chat-conversation.active { background: color-mix(in srgb, var(--primary) 12%, var(--bg-card)); }
        .chat-conversation-avatar { width: 36px; height: 36px; flex: 0 0 36px; overflow: hidden; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--primary) 18%, var(--bg-card)); color: var(--primary); font-size: 0.75rem; font-weight: 800; }
        .chat-conversation-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .chat-conversation-body { min-width: 0; flex: 1; }
        .chat-conversation-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text); font-size: 0.72rem; font-weight: 700; }
        .chat-conversation-preview { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 2px; color: var(--text-muted); font-size: 0.63rem; }
        .chat-conversation-time { align-self: flex-start; color: var(--text-muted); font-size: 0.56rem; white-space: nowrap; }
        .chat-conversation-unread { display: block; min-width: 17px; margin-top: 5px; padding: 2px 4px; border-radius: 10px; background: var(--primary); color: #fff; font-size: 0.54rem; text-align: center; }
        .chat-main { display: flex; flex: 1; min-width: 0; flex-direction: column; }
        .chat-main .chat-header { flex-shrink: 0; }
        .chat-back { display: none; border: 0; background: transparent; color: #fff; font-size: 1rem; cursor: pointer; }
        .chat-header {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            color: #fff;
            background: var(--primary-gradient);
        }

        .chat-header-title { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .chat-header-title > i { font-size: 1.3rem; }
        .chat-header-title strong { display: block; font-size: 0.9rem; }
        .chat-header-title small { display: block; margin-top: 1px; opacity: 0.8; font-size: 0.67rem; }
        .chat-header-close { border: 0; background: transparent; color: #fff; opacity: 0.8; font-size: 1.1rem; cursor: pointer; }
        .chat-header-close:hover { opacity: 1; }
        .chat-mode-switch { display: flex; gap: 5px; padding: 8px 10px 0; background: var(--modal-bg); }
        .chat-mode-button { flex: 1; padding: 7px 8px; border: 1px solid var(--border); border-radius: 8px; background: transparent; color: var(--text-muted); font-size: 0.68rem; font-weight: 700; cursor: pointer; }
        .chat-mode-button.active { border-color: color-mix(in srgb, var(--primary) 45%, var(--border)); background: color-mix(in srgb, var(--primary) 12%, var(--bg-card)); color: var(--primary); }
        .chat-recipient { display: none; padding: 8px 10px 0; background: var(--modal-bg); }
        .chat-recipient.show { display: block; }
        .chat-recipient select { width: 100%; padding: 8px 10px; border: 1px solid var(--border-light); border-radius: 8px; outline: none; background: var(--bg-input); color: var(--text); font: inherit; font-size: 0.7rem; }
        .chat-recipient select:focus { border-color: var(--primary); }

        .chat-messages {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 14px 12px;
            background: var(--bg-body);
        }

        .chat-empty { padding: 40px 20px; color: var(--text-muted); text-align: center; font-size: 0.76rem; }
        .chat-empty i { display: block; margin-bottom: 8px; font-size: 1.8rem; opacity: 0.45; }
        .chat-message { display: flex; align-items: flex-end; gap: 7px; margin-bottom: 11px; }
        .chat-message.mine { flex-direction: row-reverse; }
        .chat-avatar {
            flex: 0 0 28px;
            width: 28px;
            height: 28px;
            overflow: hidden;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--primary) 18%, var(--bg-card));
            color: var(--primary);
            font-size: 0.65rem;
            font-weight: 800;
        }
        .chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .chat-bubble { max-width: 78%; padding: 8px 10px; border: 1px solid var(--border); border-radius: 13px 13px 13px 4px; background: var(--bg-card); }
        .chat-message.mine .chat-bubble { border-color: color-mix(in srgb, var(--primary) 22%, var(--border)); border-radius: 13px 13px 4px 13px; background: color-mix(in srgb, var(--primary) 10%, var(--bg-card)); }
        .chat-author { margin-bottom: 3px; color: var(--primary); font-size: 0.65rem; font-weight: 700; }
        .chat-text { white-space: pre-wrap; overflow-wrap: anywhere; color: var(--text); font-size: 0.77rem; line-height: 1.4; }
        .chat-meta { margin-top: 4px; color: var(--text-muted); font-size: 0.58rem; text-align: right; }
        .chat-checks { margin-left: 5px; color: var(--text-muted); font-size: 0.72rem; letter-spacing: -2px; }
        .chat-checks.read { color: #2196f3; }
        .chat-attachment { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; margin-top: 6px; padding: 6px 8px; border-radius: 8px; background: var(--bg-input); color: var(--primary); font-size: 0.68rem; text-decoration: none; }
        .chat-attachment span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .chat-attachment:hover { color: var(--primary-light); }
        .chat-image-link { display: block; max-width: 220px; margin-top: 6px; overflow: hidden; border-radius: 9px; }
        .chat-image { display: block; width: 100%; max-height: 220px; object-fit: cover; transition: opacity 0.2s; }
        .chat-image:hover { opacity: 0.85; }
        .chat-audio { display: block; width: 220px; max-width: 100%; height: 34px; margin-top: 6px; }

        .chat-composer { position: relative; flex-shrink: 0; padding: 10px; border-top: 1px solid var(--border); background: var(--modal-bg); }
        .chat-attachment-preview { display: none; align-items: center; gap: 7px; margin-bottom: 7px; padding: 6px 8px; border-radius: 7px; background: var(--bg-input); color: var(--text-muted); font-size: 0.68rem; }
        .chat-attachment-preview.show { display: flex; }
        .chat-attachment-preview span { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .chat-attachment-remove { border: 0; background: transparent; color: var(--danger); cursor: pointer; }
        .chat-input-row { display: flex; align-items: flex-end; gap: 6px; }
        .chat-input { flex: 1; min-height: 38px; max-height: 100px; resize: none; padding: 9px 10px; border: 1px solid var(--border-light); border-radius: 10px; outline: none; background: var(--bg-input); color: var(--text); font: inherit; font-size: 0.76rem; }
        .chat-input:focus { border-color: var(--primary); }
        .chat-tool { width: 32px; height: 34px; padding: 0; border: 0; border-radius: 8px; background: transparent; color: var(--text-muted); cursor: pointer; }
        .chat-tool:hover { background: var(--bg-input); color: var(--primary); }
        .chat-tool.recording { background: color-mix(in srgb, var(--danger) 14%, var(--bg-input)); color: var(--danger); animation: chat-recording-pulse 1.2s infinite; }
        @keyframes chat-recording-pulse { 50% { opacity: 0.45; } }
        .chat-send { width: 36px; height: 34px; border: 0; border-radius: 9px; background: var(--primary); color: #fff; cursor: pointer; }
        .chat-send:disabled { opacity: 0.55; cursor: wait; }
        .chat-emoji-picker { position: absolute; right: 48px; bottom: 56px; display: none; width: 220px; padding: 9px; border: 1px solid var(--border-light); border-radius: 10px; background: var(--modal-bg); box-shadow: var(--shadow-lg); }
        .chat-emoji-picker.show { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
        .chat-emoji-picker button { padding: 5px 2px; border: 0; border-radius: 6px; background: transparent; font-size: 1.1rem; cursor: pointer; }
        .chat-emoji-picker button:hover { background: var(--bg-input); }

        @media (max-width: 576px) {
            .chat-fab { right: 16px; bottom: 16px; }
            .chat-panel { right: 10px; bottom: 82px; width: calc(100vw - 20px); height: min(600px, calc(100vh - 100px)); }
            .chat-sidebar { width: 100%; border-right: 0; }
            .chat-main { display: none; }
            .chat-panel.room-open .chat-sidebar { display: none; }
            .chat-panel.room-open .chat-main { display: flex; }
            .chat-panel.room-open .chat-back { display: inline-block; }
        }

        @media (min-width: 577px) and (max-width: 760px) {
            .chat-panel { right: 12px; width: calc(100vw - 24px); }
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.2s;
            color: var(--topbar-text, var(--text));
            text-decoration: none;
            font-size: 0.85rem;
            margin-left: 6px;
            border: 1px solid transparent;
        }

        .user-dropdown:hover { background: var(--bg-input); border-color: var(--border); }

        .user-dropdown .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-gradient);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-dropdown .user-info { line-height: 1.2; }
        .user-dropdown .user-info .name { font-weight: 600; font-size: 0.8rem; }
        .user-dropdown .user-info .role {
            color: var(--text-muted);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-area { padding: 16px 20px; }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 13px 16px;
            transition: all 0.25s;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .stat-card:hover { border-color: color-mix(in srgb, var(--primary) 15%, transparent); }
        .stat-card:hover::before { opacity: 1; }

        .stat-card .icon-box {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            margin-bottom: 8px;
        }

        .stat-card .stat-value {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 1px;
            letter-spacing: -0.5px;
        }

        .stat-card .stat-label {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 500;
        }

        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
        }

        .table {
            --bs-table-bg: transparent !important;
            --bs-table-striped-bg: rgba(128,128,128,0.02) !important;
            --bs-table-hover-bg: rgba(70,105,250,0.03) !important;
            --bs-table-color: var(--text) !important;
            --bs-table-border-color: var(--border) !important;
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .table thead th {
            background: rgba(70,105,250,0.06);
            border-bottom: 2px solid rgba(70,105,250,0.12);
            color: var(--text);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 0.5px;
            padding: 8px 12px;
        }

        .table tbody td {
            border-bottom: 1px solid var(--border);
            padding: 8px 12px;
            vertical-align: middle;
            color: var(--text);
        }

        .table tbody tr:hover { background: rgba(70,105,250,0.02); }

        .badge-estado {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-pagado { background: rgba(34,197,94,0.12); color: var(--success); border: 1px solid rgba(34,197,94,0.18); }
        .badge-pendiente { background: rgba(245,158,11,0.12); color: var(--warning); border: 1px solid rgba(245,158,11,0.18); }
        .badge-no-pagado { background: rgba(239,68,68,0.12); color: var(--danger); border: 1px solid rgba(239,68,68,0.18); }
        .badge-rechazado { background: rgba(148,163,184,0.12); color: #94a3b8; border: 1px solid rgba(148,163,184,0.18); }

        .btn-primary-custom {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
            padding: 8px 18px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.25s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(70,105,250,0.35);
            color: #fff;
        }

        .btn-sm-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            border: none;
            transition: all 0.2s;
            background: var(--bg-input);
            color: var(--text-muted);
        }

        .btn-sm-icon:hover { background: var(--bg-input-hover); color: var(--text); }

        .modal-content {
            background: var(--modal-bg);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 12px 18px;
        }

        .modal-header .btn-close { filter: invert(0.6); }
        .modal-body { padding: 18px; }
        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 10px 18px;
        }

        .form-control, .form-select {
            background: var(--bg-input);
            border: 1px solid var(--border-light);
            color: var(--text);
            border-radius: var(--radius-sm);
            padding: 7px 12px;
            font-size: 0.83rem;
        }

        .form-control:focus, .form-select:focus {
            background: var(--bg-input-hover);
            border-color: var(--primary);
            color: var(--text);
            box-shadow: 0 0 0 3px rgba(70,105,250,0.1);
        }

        .form-control::placeholder { color: var(--text-muted); }
        .form-select option { background: var(--modal-bg); color: var(--text); }
        .form-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.8rem;
            margin-bottom: 4px;
        }

        .text-muted { color: var(--text-muted) !important; }

        div.dataTables_wrapper { background: transparent !important; }

        table.dataTable, table.dataTable thead, table.dataTable tbody,
        table.dataTable td, table.dataTable th {
            background: transparent !important;
            color: var(--text) !important;
        }

        table.dataTable thead th {
            background: rgba(70,105,250,0.06) !important;
            border-bottom: 2px solid rgba(70,105,250,0.12) !important;
        }

        table.dataTable tbody td { border-bottom: 1px solid var(--border) !important; }
        table.dataTable.no-footer { border-bottom: 1px solid var(--border) !important; }

        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter,
        div.dataTables_wrapper div.dataTables_info,
        div.dataTables_wrapper div.dataTables_processing {
            color: var(--text-muted) !important;
            font-size: 0.8rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            background: var(--bg-input);
            border: 1px solid var(--border-light);
            color: var(--text);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus { border-color: var(--primary); outline: none; }

        .dataTables_wrapper .dataTables_length select {
            background: var(--bg-input);
            border: 1px solid var(--border-light);
            color: var(--text);
            border-radius: var(--radius-sm);
            padding: 4px 8px;
            font-size: 0.85rem;
        }

        .dataTables_wrapper .dataTables_length select option { background: var(--modal-bg); color: var(--text); }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: transparent !important;
            border: 1px solid var(--border-light) !important;
            color: var(--text-muted) !important;
            border-radius: var(--radius-sm) !important;
            margin: 0 2px;
            font-size: 0.8rem;
            padding: 4px 10px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(70,105,250,0.12) !important;
            border-color: rgba(70,105,250,0.25) !important;
            color: var(--text) !important;
        }

        .filter-bar {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 14px;
            padding: 12px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .filter-bar .form-select,
        .filter-bar .form-control {
            width: auto;
            min-width: 150px;
        }

        .filter-bar label {
            color: var(--text-muted);
            font-size: 0.78rem;
            font-weight: 500;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 4px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .swal2-popup {
            background: var(--swal-bg) !important;
            border: 1px solid var(--border-light) !important;
            border-radius: var(--radius) !important;
            color: var(--text) !important;
        }

        .swal2-title { color: var(--text) !important; font-size: 1.1rem !important; }
        .swal2-html-container { color: var(--text-muted) !important; font-size: 0.9rem !important; }
        .swal2-confirm { font-size: 0.85rem !important; border-radius: var(--radius-sm) !important; }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show { display: flex; }

        .spinner-litio {
            width: 38px; height: 38px;
            border: 3px solid rgba(255,255,255,0.08);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }
            .sidebar.show { transform: translateX(0); }
            [data-sidebar="collapsed"] .sidebar { width: var(--sidebar-width) !important; }
            [data-sidebar="collapsed"] .sidebar .brand-text,
            [data-sidebar="collapsed"] .sidebar .nav-link span { opacity: 1; width: auto; }
            [data-sidebar="collapsed"] .sidebar-toggle-btn { transform: none; margin-left: auto; }
            [data-sidebar="collapsed"] .main-content { margin-left: 0; }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0 !important; }
            .mobile-toggle { display: block; }
            .content-area { padding: 16px; }
            .top-navbar { padding: 0 16px; }
            .filter-bar .form-select,
            .filter-bar .form-control { min-width: 120px; width: 100%; }
            .table-container { padding: 14px; overflow-x: auto; }
            .stat-card { margin-bottom: 10px; }
        }

        @media (max-width: 576px) {
            .user-dropdown .user-info { display: none; }
            .content-area { padding: 12px; }
            .top-navbar { padding: 0 12px; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar .form-select,
            .filter-bar .form-control { min-width: 100%; }
        }

        .dropdown-menu-custom {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 6px;
            min-width: 190px;
        }

        .dropdown-menu-custom .dropdown-item {
            color: var(--text);
            font-size: 0.85rem;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-menu-custom .dropdown-item:hover { background: var(--bg-input); color: var(--text); }
        .dropdown-menu-custom .dropdown-item i { font-size: 1rem; width: 18px; text-align: center; }
        .dropdown-menu-custom .dropdown-divider { border-top: 1px solid var(--border); margin: 4px 0; }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <?php if ($marcaActiva && $marcaLigo): ?>
                <span class="brand-icon" style="overflow:hidden;background:transparent;"><img src="<?= esc($marcaLigo) ?>" alt="logo" style="width:100%;height:100%;object-fit:contain;padding:4px;"></span>
            <?php else: ?>
                <span class="brand-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="width:1em;height:1em;" aria-hidden="true"><path d="M3 4.5h18"/><path d="M7 4.5v8"/><circle cx="7" cy="12.5" r="1.9"/><path d="M12 4.5v12"/><circle cx="12" cy="9" r="1.9"/><circle cx="12" cy="15" r="1.9"/><path d="M17 4.5v6"/><circle cx="17" cy="7.5" r="1.9"/></svg></span>
            <?php endif; ?>
            <div class="brand-text">
                <h5><?= esc($marcaNombre) ?></h5>
                <small>Gestión de Asignaciones</small>
            </div>
            <button class="sidebar-toggle-btn" id="sidebarToggle" title="Minimizar sidebar">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu principal</div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === '/' || uri_string() === 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
                    <i class="bi bi-house-fill"></i>
                    <span>Inicio</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'recordatorio' ? 'active' : '' ?>" href="<?= site_url('recordatorio') ?>">
                    <i class="bi bi-bell-fill"></i>
                    <span>Recordatorio</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'marcadores' ? 'active' : '' ?>" href="<?= site_url('marcadores') ?>">
                    <i class="bi bi-bookmark-fill"></i>
                    <span>Marcadores</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'borradores' ? 'active' : '' ?>" href="<?= site_url('borradores') ?>">
                    <i class="bi bi-pencil-fill"></i>
                    <span>Borradores</span>
                </a>
            </div>

            <div class="nav-divider"></div>
            <div class="nav-label">Herramientas</div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'entregas' ? 'active' : '' ?>" href="<?= site_url('entregas') ?>">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Pases de turno</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'noticias' ? 'active' : '' ?>" href="<?= site_url('noticias') ?>">
                    <i class="bi bi-newspaper"></i>
                    <span>Noticias</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'ideas' ? 'active' : '' ?>" href="<?= site_url('ideas') ?>">
                    <i class="bi bi-lightbulb-fill"></i>
                    <span>Ideas</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'manual' ? 'active' : '' ?>" href="<?= site_url('manual') ?>">
                    <i class="bi bi-book-fill"></i>
                    <span>Manual</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'tareas' ? 'active' : '' ?>" href="<?= site_url('tareas') ?>">
                    <i class="bi bi-check2-square"></i>
                    <span>Tareas</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'calendario' ? 'active' : '' ?>" href="<?= site_url('calendario') ?>">
                    <i class="bi bi-calendar-fill"></i>
                    <span>Calendario</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'reparaciones' ? 'active' : '' ?>" href="<?= site_url('reparaciones') ?>">
                    <i class="bi bi-tools"></i>
                    <span>Reparaciones</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'peticiones' ? 'active' : '' ?>" href="<?= site_url('peticiones') ?>">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Peticiones de huéspedes</span>
                </a>
            </div>

            <div class="nav-divider"></div>
            <div class="nav-label">Más</div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'configuracion' ? 'active' : '' ?>" href="<?= site_url('configuracion') ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Configuración</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'colaboradores' ? 'active' : '' ?>" href="<?= site_url('colaboradores') ?>">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Colaboradores / Personal</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'soporte' ? 'active' : '' ?>" href="<?= site_url('soporte') ?>">
                    <i class="bi bi-question-circle-fill"></i>
                    <span>Soporte</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="<?= site_url('logout') ?>">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </nav>
    </aside>

    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h6 class="mb-0"><?= esc(str_replace('Kipucloud', $marcaNombre, $titulo ?? 'Dashboard')) ?></h6>
                <?php if (!empty($anuncioActivo['titulo'])): ?>
                <span class="topbar-anuncio"><i class="bi bi-megaphone-fill"></i> <?= esc($anuncioActivo['titulo']) ?></span>
                <?php endif; ?>
            </div>
            <div class="topbar-actions">
                <button class="topbar-btn" id="themeToggle" title="Cambiar tema">
                    <i class="bi bi-moon-fill"></i>
                </button>
                <div class="dropdown">
                    <div class="topbar-btn position-relative" id="notificationBell" title="Notificaciones" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span class="notification-badge" id="notificationBadge" style="display:none;">0</span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-custom dropdown-menu-end notif-panel" id="notifPanel">
                        <h6 class="notif-titulo"><i class="bi bi-bell-fill"></i> Notificaciones</h6>
                        <div id="notifLista" class="notif-lista">
                            <div class="notif-vacio text-muted small"><i class="bi bi-inbox"></i> Cargando...</div>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar">
                            <?php $foto = session('admin_foto'); if (!empty($foto)): ?>
                                <img src="<?= base_url($foto) ?>" alt="foto" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <?= strtoupper(substr(session('admin_nombre') ?? 'A', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="user-info">
                            <div class="name"><?= esc(session('admin_nombre') ?? 'Admin') ?></div>
                            <div class="role"><?= esc(session('admin_rol') ?? 'admin') ?></div>
                        </div>
                        <i class="bi bi-chevron-down" style="font-size:0.7rem;color:var(--text-muted);margin-left:2px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-custom dropdown-menu-end">
                        <span class="dropdown-item-text" style="font-size:0.75rem;color:var(--text-muted);padding:6px 14px;">
                            <?php if (!empty($foto)): ?>
                                <img src="<?= base_url($foto) ?>" alt="foto" style="width:18px;height:18px;border-radius:50%;object-fit:cover;vertical-align:middle;">
                            <?php else: ?>
                                <i class="bi bi-person-circle"></i>
                            <?php endif; ?>
                            <?= esc(session('admin_username') ?? 'admin') ?>
                        </span>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= site_url('perfil') ?>">
                            <i class="bi bi-person-gear"></i> Mi Perfil
                        </a>
                        <a class="dropdown-item" href="<?= site_url('logout') ?>">
                            <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <?= $contenido ?>
        </div>
    </div>

    <button class="chat-fab" id="chatToggle" type="button" aria-label="Abrir chat grupal" title="Chat grupal">
        <i class="bi bi-chat-dots-fill"></i>
        <span class="chat-fab-badge" id="chatBadge">0</span>
    </button>

    <section class="chat-panel" id="chatPanel" aria-label="Chat grupal">
        <div class="chat-layout">
            <aside class="chat-sidebar" id="chatSidebar">
                <div class="chat-sidebar-title"><i class="bi bi-chat-square-text"></i> Conversaciones</div>
                <div class="chat-conversations" id="chatConversations">
                    <div class="chat-empty"><i class="bi bi-hourglass-split"></i>Cargando...</div>
                </div>
            </aside>
            <div class="chat-main" id="chatMain">
                <div class="chat-header">
                    <button class="chat-back" id="chatBack" type="button" aria-label="Volver a conversaciones"><i class="bi bi-arrow-left"></i></button>
                    <div class="chat-header-title">
                        <i class="bi bi-people-fill" id="chatHeaderIcon"></i>
                        <div><strong id="chatHeaderName">Chat grupal</strong><small id="chatHeaderStatus">Comunicación del equipo</small></div>
                    </div>
                    <button class="chat-header-close" id="chatClose" type="button" aria-label="Cerrar chat"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <div class="chat-empty"><i class="bi bi-chat-square-text"></i>Cargando mensajes...</div>
                </div>
                <div class="chat-composer">
                    <div class="chat-attachment-preview" id="chatAttachmentPreview">
                        <i class="bi bi-paperclip"></i><span id="chatAttachmentName"></span>
                        <button class="chat-attachment-remove" id="chatAttachmentRemove" type="button" aria-label="Quitar archivo"><i class="bi bi-x"></i></button>
                    </div>
                    <div class="chat-emoji-picker" id="chatEmojiPicker" aria-label="Emojis">
                        <?php foreach (['😀','😂','😍','😎','🤔','😅','🙌','👏','👍','👎','❤️','🔥','✅','🎉','🚀','💡','👋','🙏','😴','😢','😡','💬','📌','⭐','🎯','💪','🤝','📎'] as $emoji): ?>
                            <button type="button" class="chat-emoji" data-emoji="<?= $emoji ?>"><?= $emoji ?></button>
                        <?php endforeach; ?>
                    </div>
                    <form id="chatForm" enctype="multipart/form-data">
                        <div class="chat-input-row">
                            <button class="chat-tool" id="chatEmojiButton" type="button" title="Agregar emoji" aria-label="Agregar emoji"><i class="bi bi-emoji-smile"></i></button>
                            <button class="chat-tool" id="chatFileButton" type="button" title="Adjuntar archivo" aria-label="Adjuntar archivo"><i class="bi bi-paperclip"></i></button>
                            <button class="chat-tool" id="chatRecordButton" type="button" title="Grabar audio" aria-label="Grabar audio"><i class="bi bi-mic-fill"></i></button>
                            <input type="file" id="chatFile" name="archivo" accept="image/*,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.mp3,.ogg,.wav,.webm,.m4a,.mp4" hidden>
                            <textarea class="chat-input" id="chatInput" name="mensaje" rows="1" maxlength="2000" placeholder="Escribe un mensaje..."></textarea>
                            <button class="chat-send" id="chatSend" type="submit" title="Enviar mensaje" aria-label="Enviar mensaje"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-litio"></div>
    </div>

    <script src="<?= base_url('assets/js/jquery-3.7.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
        <script src="<?= base_url('js/theme.js') ?>"></script>
        <script src="<?= base_url('js/chat.js') ?>?v=<?= filemtime(FCPATH . 'js/chat.js') ?>"></script>

    <script>
        var CSRF_TOKEN = '<?= csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';
        var USUARIO_ID = <?= (int) session()->get('usuario_id') ?>;

        $.ajaxSetup({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);
            }
        });

        var loadingTimer = null;

        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('show');
            clearTimeout(loadingTimer);
            loadingTimer = setTimeout(hideLoading, 15000);
        }
        function hideLoading() {
            clearTimeout(loadingTimer);
            document.getElementById('loadingOverlay').classList.remove('show');
        }

        var SESSION_IDLE_MINUTES = <?= (int) $idleMinutes ?>;

        // ===== Cierre de sesion por inactividad =====
        (function() {
            var INACTIVIDAD_MS = SESSION_IDLE_MINUTES * 60 * 1000;
            var temporizador = null;
            var logoutEnCurso = false;

            function reiniciarTemporizador() {
                clearTimeout(temporizador);
                if (logoutEnCurso) return;
                temporizador = setTimeout(cerrarSesion, INACTIVIDAD_MS);
            }

            function cerrarSesion() {
                if (logoutEnCurso) return;
                logoutEnCurso = true;
                window.location.href = BASE_URL + 'logout';
            }

            ['click', 'keydown', 'mousemove', 'mousedown', 'scroll', 'touchstart'].forEach(function(ev) {
                document.addEventListener(ev, reiniciarTemporizador, { passive: true });
            });

            reiniciarTemporizador();
        })();

        function verificarNotificaciones() {
            $.ajax({
                url: BASE_URL + 'api/notificaciones',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (!response.success) return;
                    var d = response.data || {};
                    var total = 0;
                    var html = '';

                    if (d.anuncio && d.anuncio.titulo) {
                        total++;
                        html += '<div class="notif-item">' +
                            '<div class="notif-icon"><i class="bi bi-megaphone-fill"></i></div>' +
                            '<div class="notif-txt">' +
                            '<div class="t">' + $('<div>').text(d.anuncio.titulo).html() + '</div>' +
                            '<div class="s"><i class="bi bi-newspaper"></i> Anuncio de Noticias</div>' +
                            '</div></div>';
                    }

                    if (total > 0) {
                        $('#notificationBadge').text(total).show();
                    } else {
                        $('#notificationBadge').hide();
                    }

                    if (!html) {
                        html = '<div class="notif-vacio"><i class="bi bi-inbox"></i> Sin notificaciones</div>';
                    }
                    $('#notifLista').html(html);
                },
                error: function() {
                    $('#notifLista').html('<div class="notif-vacio"><i class="bi bi-exclamation-triangle"></i> Error al cargar</div>');
                }
            });
        }
        verificarNotificaciones();
        setInterval(verificarNotificaciones, 30000);
    </script>

    <?= $pageScripts ?? '' ?>
</body>
</html>
