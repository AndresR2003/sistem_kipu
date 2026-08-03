<!DOCTYPE html>
<html lang="es" data-theme="dark" style="
<?php
try {
    $cfg = model('App\Models\ConfiguracionVisualModel')->Obtener();
    if (!empty($cfg['sidebar_bg']))        echo '--bg-sidebar:' . $cfg['sidebar_bg'] . ';';
    if (!empty($cfg['sidebar_text']))      echo '--text-nav:' . $cfg['sidebar_text'] . ';';
    if (!empty($cfg['sidebar_active_bg'])) echo '--active-bg:' . $cfg['sidebar_active_bg'] . ';';
    if (!empty($cfg['topbar_bg']))          echo '--bg-topbar:' . $cfg['topbar_bg'] . ';';
    if (!empty($cfg['topbar_text']))        echo '--topbar-text:' . $cfg['topbar_text'] . ';';
    if (!empty($cfg['primary_color']))      echo '--primary:' . $cfg['primary_color'] . ';';
    if (!empty($cfg['content_bg']))         echo '--bg-body:' . $cfg['content_bg'] . ';';
    if (!empty($cfg['card_bg']))            echo '--bg-card:' . $cfg['card_bg'] . ';';
} catch (\Throwable $e) {}
?>
">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Litio') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

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
            --bg-body: #f1f5f9;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-card-alt: #f8fafc;
            --bg-table: rgba(255,255,255,0.8);
            --bg-input: #f1f5f9;
            --bg-input-hover: #e2e8f0;
            --bg-topbar: rgba(255,255,255,0.95);
            --text: #1e293b;
            --text-muted: rgba(0,0,0,0.38);
            --text-nav: rgba(0,0,0,0.5);
            --border: rgba(0,0,0,0.06);
            --border-light: rgba(0,0,0,0.1);
            --shadow: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.06);
            --modal-bg: #ffffff;
            --swal-bg: #ffffff;
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

        .topbar-actions .dropdown-menu-custom {
            background: var(--modal-bg);
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

        .comprobante-preview {
            max-width: 100%;
            max-height: 400px;
            border-radius: var(--radius);
            border: 2px solid var(--border-light);
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
            <span class="brand-icon"><i class="bi bi-lightning-fill"></i></span>
            <div class="brand-text">
                <h5>Litio</h5>
                <small>Gestion de Asignaciones</small>
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
                    <span>Entregas / Pases de turno</span>
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
                    <span>Peticiones de huespedes</span>
                </a>
            </div>

            <div class="nav-divider"></div>
            <div class="nav-label">Mas</div>
            <div class="nav-item">
                <a class="nav-link <?= uri_string() === 'configuracion' ? 'active' : '' ?>" href="<?= site_url('configuracion') ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Configuracion</span>
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
                    <span>Cerrar Sesion</span>
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
                <h6 class="mb-0"><?= esc($titulo ?? 'Dashboard') ?></h6>
            </div>
            <div class="topbar-actions">
                <button class="topbar-btn" id="themeToggle" title="Cambiar tema">
                    <i class="bi bi-moon-fill"></i>
                </button>
                <div class="topbar-btn position-relative" id="notificationBell" title="Notificaciones" role="button">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notification-badge" id="notificationBadge" style="display:none;">0</span>
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
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesion
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            <?= $contenido ?>
        </div>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-litio"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= base_url('js/theme.js') ?>"></script>

    <script>
        var CSRF_TOKEN = '<?= csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';
        var USUARIO_ID = <?= (int) session()->get('usuario_id') ?>;

        $.ajaxSetup({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);
            }
        });

        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('show');
        }
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('show');
        }

        function verificarNotificaciones() {
            $.ajax({
                url: BASE_URL + 'api/notificaciones',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.hayPendientes) {
                        $('#notificationBadge').text(response.data.pendientes).show();
                    } else {
                        $('#notificationBadge').hide();
                    }
                }
            });
        }
        verificarNotificaciones();
        setInterval(verificarNotificaciones, 30000);
    </script>

    <?= $pageScripts ?? '' ?>
</body>
</html>
