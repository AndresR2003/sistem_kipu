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
                <div style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;background:var(--bg-body);">
                    <div id="previewTopbar" style="background:rgba(15,15,26,0.92);padding:10px 14px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
                        <span id="previewTopbarText" style="color:#e2e8f0;font-size:0.8rem;font-weight:600;"><i class="bi bi-grid-1x2-fill"></i> Dashboard</span>
                        <span style="display:flex;align-items:center;gap:10px;">
                            <span id="previewTopbarIcon" style="color:#e2e8f0;font-size:0.85rem;"><i class="bi bi-bell-fill"></i></span>
                            <span id="previewAvatar" style="width:22px;height:22px;border-radius:50%;background:var(--primary-gradient);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:0.6rem;">A</span>
                        </span>
                    </div>
                    <div style="display:flex;">
                        <div id="previewSidebar" style="background:#13131f;width:56px;padding:12px 8px;display:flex;flex-direction:column;align-items:center;gap:12px;border-right:1px solid var(--border);">
                            <div id="previewLogo" style="width:28px;height:28px;border-radius:6px;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.75rem;">
                                <i class="bi bi-lightning-fill"></i>
                            </div>
                            <div id="previewSidebarIcon1" style="color:rgba(255,255,255,0.55);font-size:0.9rem;"><i class="bi bi-grid-1x2-fill"></i></div>
                            <div id="previewSidebarIcon2" style="color:rgba(255,255,255,0.55);font-size:0.9rem;"><i class="bi bi-people-fill"></i></div>
                            <div id="previewSidebarActive" style="width:100%;border-radius:6px;background:#4669FA;display:flex;align-items:center;justify-content:center;padding:6px;color:#fff;font-size:0.9rem;">
                                <i class="bi bi-credit-card-fill"></i>
                            </div>
                        </div>
                        <div style="flex:1;background:var(--bg-body);padding:14px;">
                            <div style="display:flex;gap:8px;margin-bottom:10px;">
                                <div style="flex:1;padding:10px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-sm);">
                                    <div style="height:7px;width:55%;background:var(--border-light);border-radius:4px;margin-bottom:6px;"></div>
                                    <div style="height:7px;width:35%;background:var(--border);border-radius:4px;"></div>
                                </div>
                                <div style="flex:1;padding:10px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-sm);">
                                    <div style="height:7px;width:55%;background:var(--border-light);border-radius:4px;margin-bottom:6px;"></div>
                                    <div style="height:7px;width:35%;background:var(--border);border-radius:4px;"></div>
                                </div>
                            </div>
                            <div style="height:7px;width:70%;background:var(--border-light);border-radius:4px;margin-bottom:6px;"></div>
                            <div style="height:7px;width:45%;background:var(--border);border-radius:4px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
