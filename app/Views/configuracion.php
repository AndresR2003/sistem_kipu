<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-gear-fill"></i> Configuracion Visual
        </h5>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
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
                    .pv-topbar {
                        display: flex; justify-content: space-between; align-items: center;
                        background: var(--pv-topbar); padding: 8px 12px;
                        border-bottom: 1px solid var(--border);
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
                    .pv-body { display: flex; }
                    .pv-sidebar {
                        width: 42px; padding: 10px 7px; display: flex; flex-direction: column;
                        align-items: center; gap: 9px; background: var(--pv-sidebar); border-right: 1px solid var(--border);
                    }
                    .pv-logo {
                        width: 22px; height: 22px; border-radius: 5px;
                        background: linear-gradient(135deg, var(--pv-primary) 0%, #3651d4 100%);
                        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.6rem;
                    }
                    .pv-nav { color: var(--pv-sidebar-text); font-size: 0.72rem; line-height: 1; }
                    .pv-nav.active {
                        width: 100%; border-radius: 5px; background: var(--pv-sidebar-active);
                        display: flex; align-items: center; justify-content: center; padding: 5px; color: #fff; font-size: 0.72rem;
                    }
                    .pv-content { flex: 1; padding: 10px; min-width: 0; }
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
                    .pv-widgets { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
                    .pv-widget { background: var(--pv-card); border: 1px solid var(--border); border-radius: 7px; overflow: hidden; }
                    .pv-whead { padding: 6px 8px; border-bottom: 1px solid var(--border); font-size: 0.55rem; font-weight: 700; display: flex; align-items: center; gap: 5px; color: var(--text); }
                    .pv-whead i { color: var(--pv-primary); }
                    .pv-wbody { padding: 5px 8px; }
                    .pv-witem { display: flex; align-items: center; gap: 6px; padding: 5px 0; border-bottom: 1px dashed var(--border); }
                    .pv-witem:last-child { border-bottom: none; }
                    .pv-witem .wi { width: 18px; height: 18px; border-radius: 5px; background: color-mix(in srgb, var(--pv-primary) 12%, transparent); color: var(--pv-primary); display: flex; align-items: center; justify-content: center; font-size: 0.55rem; flex-shrink: 0; }
                    .pv-witem .wt { flex: 1; min-width: 0; }
                    .pv-witem .wt b { display: block; font-size: 0.52rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                    .pv-witem .wt small { font-size: 0.45rem; color: var(--text-muted); }
                    .pv-witem .wd { font-size: 0.45rem; color: var(--text-muted); background: var(--bg-input); padding: 2px 6px; border-radius: 8px; white-space: nowrap; }
                </style>
                <div id="previewShell">
                    <div id="previewTopbar" class="pv-topbar">
                        <span id="previewTopbarText" class="pv-topbar-title"><i class="bi bi-house-fill"></i> Dashboard - Litio</span>
                        <span class="pv-topbar-actions">
                            <span id="previewTopbarIcon" class="pv-bell"><i class="bi bi-bell-fill"></i></span>
                            <span id="previewAvatar" class="pv-avatar">A</span>
                        </span>
                    </div>
                    <div class="pv-body">
                        <div id="previewSidebar" class="pv-sidebar">
                            <div id="previewLogo" class="pv-logo"><i class="bi bi-lightning-fill"></i></div>
                            <div id="previewSidebarIcon1" class="pv-nav"><i class="bi bi-house-fill"></i></div>
                            <div id="previewSidebarIcon2" class="pv-nav"><i class="bi bi-bell-fill"></i></div>
                            <div id="previewSidebarActive" class="pv-nav active"><i class="bi bi-newspaper"></i></div>
                        </div>
                        <div id="previewContent" class="pv-content">
                            <div class="pv-hero">
                                <div class="pv-hero-label">Sistema de Gestion Hotel Litio</div>
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
                            <div class="pv-quick">
                                <div class="pv-q"><i class="bi bi-newspaper"></i><span>Noticias</span></div>
                                <div class="pv-q"><i class="bi bi-check2-square"></i><span>Tareas</span></div>
                                <div class="pv-q"><i class="bi bi-bell-fill"></i><span>Record.</span></div>
                                <div class="pv-q"><i class="bi bi-calendar-fill"></i><span>Calend.</span></div>
                            </div>
                            <div class="pv-widgets">
                                <div class="pv-widget">
                                    <div class="pv-whead"><i class="bi bi-newspaper"></i> Ultimas noticias</div>
                                    <div class="pv-wbody">
                                        <div class="pv-witem"><div class="wi"><i class="bi bi-file-text"></i></div><div class="wt"><b>Titulo de la noticia</b><small>Por Usuario</small></div><span class="wd">02 ago</span></div>
                                        <div class="pv-witem"><div class="wi"><i class="bi bi-file-text"></i></div><div class="wt"><b>Otra noticia</b><small>Por Staff</small></div><span class="wd">01 ago</span></div>
                                    </div>
                                </div>
                                <div class="pv-widget">
                                    <div class="pv-whead"><i class="bi bi-calendar-event"></i> Proximos eventos</div>
                                    <div class="pv-wbody">
                                        <div class="pv-witem"><div class="wi"><i class="bi bi-calendar-check"></i></div><div class="wt"><b>Evento proximo</b><small>Descripcion</small></div><span class="wd">05 ago</span></div>
                                        <div class="pv-witem"><div class="wi"><i class="bi bi-calendar-check"></i></div><div class="wt"><b>Otro evento</b><small>Detalle</small></div><span class="wd">08 ago</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
