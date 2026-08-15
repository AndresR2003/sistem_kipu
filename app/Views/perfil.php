<style>
.perfil-fondo{background:linear-gradient(180deg, var(--primary-gradient) 0%, rgba(70,105,250,0.85) 42%, var(--bg-main) 42%);padding:28px 22px 34px;border-radius:var(--radius);min-height:calc(100vh - 140px);}

.perfil-cabecera{display:flex;align-items:center;gap:20px;color:#fff;padding:6px 8px 26px;}
.perfil-cabecera-avatar{position:relative;width:96px;height:96px;flex-shrink:0;}
.perfil-cabecera-avatar img{width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.9);box-shadow:0 6px 20px rgba(0,0,0,0.3);}
.perfil-cabecera-avatar .overlay{position:absolute;inset:0;border-radius:50%;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;cursor:pointer;}
.perfil-cabecera-avatar:hover .overlay{opacity:1;}
.perfil-cabecera-avatar .overlay i{color:#fff;font-size:1.3rem;}
.perfil-cabecera-info h4{font-weight:700;margin:0;font-size:1.35rem;text-shadow:0 2px 8px rgba(0,0,0,0.25);}
.perfil-cabecera-info .perfil-cabecera-rol{display:inline-flex;align-items:center;gap:6px;margin-top:6px;font-size:0.78rem;font-weight:600;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.35);padding:4px 12px;border-radius:20px;}
.perfil-cabecera-info .perfil-cabecera-sub{font-size:0.8rem;opacity:0.85;margin-top:6px;}

.perfil-tarjeta{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);overflow:hidden;}

.perfil-tabs{display:flex;border-bottom:1px solid var(--border);background:var(--bg-card-alt);padding:0 14px;overflow-x:auto;}
.perfil-tab{flex:1;min-width:140px;text-align:center;padding:15px 10px;cursor:pointer;border:none;background:transparent;color:var(--text-muted);font-weight:600;font-size:0.83rem;border-bottom:3px solid transparent;transition:all 0.2s;white-space:nowrap;}
.perfil-tab i{display:block;font-size:1.1rem;margin-bottom:4px;}
.perfil-tab:hover{color:var(--text);}
.perfil-tab.active{color:var(--primary);border-bottom-color:var(--primary);}

.perfil-pane{display:none;padding:24px;}
.perfil-pane.active{display:block;}

.perfil-seccion{margin-bottom:22px;}
.perfil-seccion-titulo{display:flex;align-items:center;gap:8px;font-size:0.78rem;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);margin-bottom:14px;}
.perfil-seccion-titulo i{color:var(--primary);}

.perfil-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.perfil-grid .full{grid-column:1 / -1;}

.perfil-campo label{display:block;font-size:0.72rem;font-weight:600;color:var(--text-muted);margin-bottom:5px;}
.perfil-campo input,.perfil-campo select{width:100%;}

.perfil-guardar{margin-top:10px;padding-top:16px;border-top:1px dashed var(--border);display:flex;justify-content:flex-end;}

.notif-opcion{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:13px 16px;border:1px solid var(--border);border-radius:var(--radius);background:var(--bg-card-alt);margin-bottom:10px;}
.notif-opcion-info{display:flex;align-items:center;gap:12px;}
.notif-opcion-icono{width:38px;height:38px;border-radius:9px;background:var(--primary-gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.95rem;flex-shrink:0;}
.notif-opcion-txt{font-size:0.84rem;font-weight:600;color:var(--text);}
.notif-opcion-desc{font-size:0.72rem;color:var(--text-muted);margin-top:1px;}
.form-check-input{cursor:pointer;width:38px;height:19px;margin:0;}
.form-check-input:checked{background-color:var(--success);border-color:var(--success);}

.seguridad-info{background:var(--bg-card-alt);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;margin-bottom:14px;display:flex;align-items:center;gap:12px;}
.seguridad-info i{font-size:1.2rem;color:var(--success);}
.seguridad-info .txt{font-size:0.78rem;color:var(--text-muted);}
.seguridad-info .txt b{color:var(--text);}

@media (max-width: 576px){
    .perfil-grid{grid-template-columns:1fr;}
    .perfil-cabecera{flex-direction:column;text-align:center;gap:12px;}
}
</style>

<div class="perfil-fondo">
    <div class="perfil-cabecera">
        <div class="perfil-cabecera-avatar" onclick="document.getElementById('fotoInput').click()">
            <img id="fotoPerfil" src="" alt="Foto">
            <div class="overlay"><i class="bi bi-camera-fill"></i></div>
        </div>
        <input type="file" id="fotoInput" accept="image/*" style="display:none;" onchange="subirFoto(this)">
        <div class="perfil-cabecera-info">
            <h4 id="perfilNombreCabecera">Cargando...</h4>
            <div><span class="perfil-cabecera-rol"><i class="bi bi-shield-fill-check"></i> <span id="perfilRolCabecera">-</span></span></div>
            <div class="perfil-cabecera-sub" id="perfilEmailCabecera"></div>
        </div>
    </div>

    <div class="perfil-tarjeta">
        <div class="perfil-tabs">
            <button type="button" class="perfil-tab active" data-tab="datos" onclick="cambiarTabPerfil('datos', this)"><i class="bi bi-person-fill"></i> Datos personales</button>
            <button type="button" class="perfil-tab" data-tab="seguridad" onclick="cambiarTabPerfil('seguridad', this)"><i class="bi bi-shield-lock-fill"></i> Contraseña / Seguridad</button>
            <button type="button" class="perfil-tab" data-tab="notificaciones" onclick="cambiarTabPerfil('notificaciones', this)"><i class="bi bi-bell-fill"></i> Notificaciones</button>
            <button type="button" class="perfil-tab" data-tab="preferencias" onclick="cambiarTabPerfil('preferencias', this)"><i class="bi bi-sliders"></i> Preferencias</button>
        </div>

        <!-- PESTAÑA: DATOS PERSONALES -->
        <div class="perfil-pane active" id="paneDatos">
            <div class="perfil-seccion">
                <div class="perfil-seccion-titulo"><i class="bi bi-person-vcard"></i> Informacion basica</div>
                <div class="perfil-grid">
                    <div class="perfil-campo">
                        <label>Usuario</label>
                        <input type="text" class="form-control" id="perfilUsername" readonly style="background:var(--bg-input);">
                    </div>
                    <div class="perfil-campo">
                        <label>Nombre completo</label>
                        <input type="text" class="form-control" id="perfilNombre">
                    </div>
                    <div class="perfil-campo">
                        <label>Email</label>
                        <input type="email" class="form-control" id="perfilEmail">
                    </div>
                    <div class="perfil-campo">
                        <label>Telefono</label>
                        <input type="text" class="form-control" id="perfilTelefono" placeholder="000-000-0000">
                    </div>
                    <div class="perfil-campo">
                        <label>Puesto</label>
                        <input type="text" class="form-control" id="perfilPuesto" placeholder="Ej: Analista">
                    </div>
                    <div class="perfil-campo">
                        <label>Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="perfilFechaNac">
                    </div>
                    <div class="perfil-campo full">
                        <label>Fecha de contratacion</label>
                        <input type="date" class="form-control" id="perfilFechaCont">
                    </div>
                </div>
                <div class="perfil-guardar">
                    <button class="btn btn-primary-custom btn-sm" onclick="guardarPerfil('datos')">
                        <i class="bi bi-save"></i> Guardar datos
                    </button>
                </div>
            </div>
        </div>

        <!-- PESTAÑA: CONTRASEÑA / SEGURIDAD -->
        <div class="perfil-pane" id="paneSeguridad">
            <div class="perfil-seccion">
                <div class="perfil-seccion-titulo"><i class="bi bi-shield-lock"></i> Seguridad</div>
                <div class="seguridad-info">
                    <i class="bi bi-check-circle-fill"></i>
                    <div class="txt">Tu cuenta esta protegida. Cambia tu contraseña regularmente y no la compartas con nadie. La nueva contraseña debe tener al menos <b>8 caracteres</b>.</div>
                </div>
                <div class="perfil-grid">
                    <div class="perfil-campo">
                        <label>Nueva contraseña</label>
                        <input type="password" class="form-control" id="perfilPassword" placeholder="Minimo 8 caracteres">
                    </div>
                    <div class="perfil-campo">
                        <label>Confirmar nueva contraseña</label>
                        <input type="password" class="form-control" id="perfilPassword2" placeholder="Repite la contraseña">
                    </div>
                </div>
                <div class="perfil-guardar">
                    <button class="btn btn-primary-custom btn-sm" onclick="guardarPerfil('seguridad')">
                        <i class="bi bi-shield-check"></i> Actualizar contraseña
                    </button>
                </div>
            </div>
        </div>

        <!-- PESTAÑA: NOTIFICACIONES -->
        <div class="perfil-pane" id="paneNotificaciones">
            <div class="perfil-seccion">
                <div class="perfil-seccion-titulo"><i class="bi bi-bell"></i> Preferencias de notificacion</div>
                <div class="notif-opcion">
                    <div class="notif-opcion-info">
                        <div class="notif-opcion-icono"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <div class="notif-opcion-txt">Notificaciones por email</div>
                            <div class="notif-opcion-desc">Recibir avisos importantes a tu correo</div>
                        </div>
                    </div>
                    <input type="checkbox" class="form-check-input" id="notifEmail">
                </div>
                <div class="notif-opcion">
                    <div class="notif-opcion-info">
                        <div class="notif-opcion-icono"><i class="bi bi-megaphone-fill"></i></div>
                        <div>
                            <div class="notif-opcion-txt">Avisos y anuncios</div>
                            <div class="notif-opcion-desc">Nuevos anuncios de la organizacion</div>
                        </div>
                    </div>
                    <input type="checkbox" class="form-check-input" id="notifAnuncios">
                </div>
                <div class="notif-opcion">
                    <div class="notif-opcion-info">
                        <div class="notif-opcion-icono"><i class="bi bi-chat-dots-fill"></i></div>
                        <div>
                            <div class="notif-opcion-txt">Mensajes y comentarios</div>
                            <div class="notif-opcion-desc">Cuando te mencionen o respondan</div>
                        </div>
                    </div>
                    <input type="checkbox" class="form-check-input" id="notifMensajes">
                </div>
                <div class="notif-opcion">
                    <div class="notif-opcion-info">
                        <div class="notif-opcion-icono"><i class="bi bi-calendar-event-fill"></i></div>
                        <div>
                            <div class="notif-opcion-txt">Recordatorios de eventos</div>
                            <div class="notif-opcion-desc">Recordatorios de tareas y eventos</div>
                        </div>
                    </div>
                    <input type="checkbox" class="form-check-input" id="notifEventos">
                </div>
                <div class="perfil-guardar">
                    <button class="btn btn-primary-custom btn-sm" onclick="guardarPerfil('notificaciones')">
                        <i class="bi bi-save"></i> Guardar preferencias
                    </button>
                </div>
            </div>
        </div>

        <!-- PESTAÑA: PREFERENCIAS -->
        <div class="perfil-pane" id="panePreferencias">
            <div class="perfil-seccion">
                <div class="perfil-seccion-titulo"><i class="bi bi-sliders"></i> Preferencias generales</div>
                <div class="perfil-grid">
                    <div class="perfil-campo full">
                        <label>Idioma</label>
                        <select class="form-select" id="perfilIdioma">
                            <option value="es">Español</option>
                            <option value="en">Ingles</option>
                        </select>
                    </div>
                </div>
                <div class="perfil-guardar">
                    <button class="btn btn-primary-custom btn-sm" onclick="guardarPerfil('preferencias')">
                        <i class="bi bi-save"></i> Guardar preferencias
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
