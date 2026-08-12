<div class="table-container">
    <style>
        .configuracion-tabs { border-bottom: 1px solid var(--border) !important; }
        .configuracion-tabs .nav-link {
            color: var(--text-muted);
            border: 1px solid transparent;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 8px 16px;
        }
        .configuracion-tabs .nav-link:hover { color: var(--text); border-color: var(--border-light); }
        .configuracion-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
            border-color: var(--border-light) var(--border-light) var(--bg-card);
            border-bottom-color: var(--bg-card);
            font-weight: 600;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-gear-fill"></i> Configuracion Visual
        </h5>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:10px 24px 24px;">
                <ul class="nav nav-tabs configuracion-tabs mt-2 mb-3" style="border-bottom:1px solid var(--border);gap:2px;" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabColores" type="button" role="tab">
                            <i class="bi bi-palette-fill"></i> Colores
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMarca" type="button" role="tab">
                            <i class="bi bi-briefcase-fill"></i> Marca de la Empresa
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabColores" role="tabpanel">
                        <h6 class="mb-3"><i class="bi bi-palette-fill"></i> Colores del Sistema</h6>

                        <form id="formColores">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Fondo del Sidebar</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="sidebar_bg" value="#13131f" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="sidebar_bg_text" value="#13131f" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Texto del Sidebar</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="sidebar_text" value="rgba(255,255,255,0.55)" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="sidebar_text_text" value="rgba(255,255,255,0.55)" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fondo Activo Sidebar</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="sidebar_active_bg" value="#4669FA" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="sidebar_active_bg_text" value="#4669FA" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Color Principal</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="primary_color" value="#4669FA" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="primary_color_text" value="#4669FA" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fondo del Topbar</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="topbar_bg" value="rgba(15,15,26,0.92)" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="topbar_bg_text" value="rgba(15,15,26,0.92)" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Texto del Topbar</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="topbar_text" value="#e2e8f0" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="topbar_text_text" value="#e2e8f0" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fondo del Contenido</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="content_bg" value="#0f0f1a" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="content_bg_text" value="#0f0f1a" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fondo de Tarjetas</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="color" class="form-control form-control-color" id="card_bg" value="#1a1a2e" style="width:50px;height:38px;padding:2px;">
                                        <input type="text" class="form-control" id="card_bg_text" value="#1a1a2e" maxlength="20">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="button" class="btn btn-primary-custom" onclick="guardarColores()">
                                    <i class="bi bi-check-lg"></i> Guardar Cambios
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="restaurarColores()">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restaurar Default
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tabMarca" role="tabpanel">
                        <h6 class="mb-3"><i class="bi bi-briefcase-fill"></i> Marca de la Empresa Cliente</h6>

                        <form id="formMarca">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="marca_activa" role="switch" style="cursor:pointer;border-color:var(--border-light);">
                                <label class="form-check-label" style="color:var(--text);font-size:0.85rem;cursor:pointer;" for="marca_activa">
                                    Permitir reemplazar el logo y el nombre "KipuCloud" por el logo y/o nombre de la empresa cliente.
                                </label>
                            </div>

                            <div id="marcaCampos" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label">Nombre de la empresa</label>
                                    <input type="text" class="form-control" id="marca_nombre" placeholder="Ej: Hotel Gran Palma" maxlength="120">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Logo de la empresa</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div id="logoPreview" style="width:56px;height:56px;border-radius:10px;overflow:hidden;background:var(--bg-input);border:1px solid var(--border-light);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:var(--text-muted);flex-shrink:0;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control" id="logo_input" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                            <input type="hidden" id="marca_logo">
                                            <small class="d-block mt-1" style="color:var(--text-muted);">PNG, JPG, WebP o SVG. Max 2MB.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn btn-primary-custom" onclick="guardarMarca()">
                                    <i class="bi bi-check-lg"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;position:sticky;top:84px;">
                <h6 class="mb-3"><i class="bi bi-eye-fill"></i> Vista Previa</h6>
                <style>
                    #previewShell {
                        --pv-primary: #4669FA;
                        --pv-sidebar: #13131f;
                        --pv-sidebar-text: rgba(255,255,255,0.55);
                        --pv-sidebar-active: #4669FA;
                        --pv-topbar: rgba(15,15,26,0.92);
                        --pv-topbar-text: #e2e8f0;
                        --pv-body: #0f0f1a;
                        --pv-card: #1a1a2e;
                        border: 1px solid var(--border);
                        border-radius: var(--radius-sm);
                        overflow: hidden;
                        background: var(--pv-body);
                    }
                    .pv-body { display: flex; align-items: stretch; }
                    .pv-sidebar {
                        width: 108px; flex-shrink: 0; padding: 10px 9px; display: flex; flex-direction: column;
                        gap: 7px; background: var(--pv-sidebar); border-right: 1px solid var(--border);
                        align-self: stretch;
                    }
                    .pv-brand {
                        display: flex; align-items: center; gap: 7px; padding-bottom: 8px;
                        border-bottom: 1px solid var(--border); margin-bottom: 4px;
                    }
                    .pv-logo {
                        width: 22px; height: 22px; border-radius: 4px; flex-shrink: 0;
                        background: linear-gradient(135deg, var(--pv-primary) 0%, #3651d4 100%);
                        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.6rem;
                    }
                    .pv-brand-txt { min-width: 0; }
                    .pv-brand-txt b { display: block; font-size: 0.62rem; font-weight: 800; color: var(--text); line-height: 1.1; }
                    .pv-brand-txt small { display: block; font-size: 0.4rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
                    .pv-nav {
                        display: flex; align-items: center; gap: 7px; padding: 5px 6px; border-radius: 5px;
                        color: var(--pv-sidebar-text); font-size: 0.58rem; line-height: 1; white-space: nowrap;
                    }
                    .pv-nav i { font-size: 0.68rem; flex-shrink: 0; width: 14px; text-align: center; }
                    .pv-nav.active {
                        background: var(--pv-sidebar-active); color: #fff; font-weight: 600;
                    }
                    .pv-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
                    .pv-topbar {
                        display: flex; justify-content: space-between; align-items: center;
                        background: var(--pv-topbar); padding: 8px 12px;
                        border-bottom: 1px solid var(--border); flex-shrink: 0;
                    }
                    .pv-topbar-title { color: var(--pv-topbar-text); font-size: 0.72rem; font-weight: 600; display: flex; align-items: center; gap: 6px; }
                    .pv-topbar-actions { display: flex; align-items: center; gap: 8px; }
                    .pv-bell { color: var(--pv-topbar-text); font-size: 0.8rem; }
                    .pv-avatar {
                        width: 20px; height: 20px; border-radius: 50%;
                        background: linear-gradient(135deg, var(--pv-primary) 0%, #3651d4 100%);
                        color: #fff; font-size: 0.55rem; font-weight: 700;
                        display: inline-flex; align-items: center; justify-content: center;
                    }
                    .pv-content { flex: 1; padding: 10px; min-width: 0; }
                    .pv-section-t {
                        display: flex; align-items: center; gap: 5px; margin: 12px 0 7px;
                        font-size: 0.6rem; font-weight: 700; color: var(--text);
                    }
                    .pv-section-t i { color: var(--pv-primary); }
                    .pv-section-t .ln { flex: 1; height: 1px; background: var(--border); }
                    .pv-hero {
                        border-radius: 8px; padding: 12px 14px; color: #fff; margin-bottom: 10px;
                        background: linear-gradient(135deg, var(--pv-primary) 0%, #3651d4 55%, #5a2ea6 100%);
                    }
                    .pv-hero-label { font-size: 0.5rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.85; font-weight: 600; }
                    .pv-hero-title { font-size: 0.8rem; font-weight: 800; margin: 3px 0 1px; }
                    .pv-hero-sub { font-size: 0.6rem; opacity: 0.9; }
                    .pv-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 10px; }
                    .pv-stat { background: var(--pv-card); border: 1px solid var(--border); border-radius: 7px; padding: 8px; }
                    .pv-stat .si { width: 20px; height: 20px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.62rem; margin-bottom: 5px; background: color-mix(in srgb, var(--pv-primary) 12%, transparent); color: var(--pv-primary); }
                    .pv-stat .sv { font-size: 0.85rem; font-weight: 800; line-height: 1; }
                    .pv-stat .sl { font-size: 0.5rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-top: 2px; }
                    .pv-quick { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-bottom: 10px; }
                    .pv-q { background: var(--pv-card); border: 1px solid var(--border); border-radius: 7px; padding: 7px 4px; text-align: center; }
                    .pv-q i { display: block; font-size: 0.75rem; color: var(--pv-primary); margin-bottom: 3px; }
                    .pv-q span { font-size: 0.5rem; color: var(--text); font-weight: 600; }
                </style>
                <div id="previewShell">
                    <div class="pv-body">
                        <div id="previewSidebar" class="pv-sidebar">
                            <div class="pv-brand">
                                <div id="previewLogo" class="pv-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="width:1em;height:1em;" aria-hidden="true"><path d="M3 4.5h18"/><path d="M7 4.5v8"/><circle cx="7" cy="12.5" r="1.9"/><path d="M12 4.5v12"/><circle cx="12" cy="9" r="1.9"/><circle cx="12" cy="15" r="1.9"/><path d="M17 4.5v6"/><circle cx="17" cy="7.5" r="1.9"/></svg></div>
                                <div class="pv-brand-txt"><b id="previewBrandName">Kipucloud</b><small id="previewBrandSub">Gestion</small></div>
                            </div>
                            <div id="previewSidebarIcon1" class="pv-nav"><i class="bi bi-house-fill"></i> Inicio</div>
                            <div id="previewSidebarIcon2" class="pv-nav"><i class="bi bi-newspaper"></i> Noticias</div>
                            <div id="previewSidebarActive" class="pv-nav active"><i class="bi bi-check2-square"></i> Tareas</div>
                        </div>
                        <div class="pv-main">
                            <div id="previewTopbar" class="pv-topbar">
                                <span id="previewTopbarText" class="pv-topbar-title"><i class="bi bi-house-fill"></i> Dashboard - <span id="previewTopbarName">Kipucloud</span></span>
                                <span class="pv-topbar-actions">
                                    <span id="previewTopbarIcon" class="pv-bell"><i class="bi bi-bell-fill"></i></span>
                                    <span id="previewAvatar" class="pv-avatar">A</span>
                                </span>
                            </div>
                            <div id="previewContent" class="pv-content">
                            <div class="pv-hero">
                                <div class="pv-hero-label" id="previewHeroLabel">Sistema de Gestion Hotel Kipucloud</div>
                                <div class="pv-hero-title">Bienvenido, Usuario</div>
                                <div class="pv-hero-sub">Esto es lo que esta pasando hoy en tu cuenta</div>
                            </div>
                            <div class="pv-stats">
                                <div class="pv-stat"><div class="si"><i class="bi bi-check2-square"></i></div><div class="sv">12</div><div class="sl">Tareas</div></div>
                                <div class="pv-stat"><div class="si" style="background:rgba(34,197,94,0.12);color:#22c55e;"><i class="bi bi-check-circle"></i></div><div class="sv">8</div><div class="sl">Listas</div></div>
                                <div class="pv-stat"><div class="si" style="background:rgba(6,182,212,0.12);color:#06b6d4;"><i class="bi bi-newspaper"></i></div><div class="sv">3</div><div class="sl">Noticias</div></div>
                                <div class="pv-stat"><div class="si" style="background:rgba(245,158,11,0.12);color:var(--warning);"><i class="bi bi-bell-fill"></i></div><div class="sv">5</div><div class="sl">Pend.</div></div>
                                <div class="pv-stat"><div class="si" style="background:rgba(168,85,247,0.14);color:#a855f7;"><i class="bi bi-bookmark-fill"></i></div><div class="sv">2</div><div class="sl">Marc.</div></div>
                                <div class="pv-stat"><div class="si" style="background:rgba(239,68,68,0.12);color:var(--danger);"><i class="bi bi-calendar-event"></i></div><div class="sv">1</div><div class="sl">Eventos</div></div>
                            </div>
                            <div class="pv-section-t"><i class="bi bi-grid-1x2-fill"></i> Accesos rapidos <span class="ln"></span></div>
                            <div class="pv-quick">
                                <div class="pv-q"><i class="bi bi-newspaper"></i><span>Noticias</span></div>
                                <div class="pv-q"><i class="bi bi-check2-square"></i><span>Tareas</span></div>
                                <div class="pv-q"><i class="bi bi-bell-fill"></i><span>Record.</span></div>
                                <div class="pv-q"><i class="bi bi-calendar-fill"></i><span>Calend.</span></div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
