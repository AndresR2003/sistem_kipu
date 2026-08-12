<style>
.avatar-upload{position:relative;width:120px;height:120px;margin:0 auto;}
.avatar-upload img{width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid var(--primary);}
.avatar-upload .overlay{position:absolute;inset:0;border-radius:50%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;cursor:pointer;}
.avatar-upload:hover .overlay{opacity:1;}
.avatar-upload .overlay i{color:#fff;font-size:1.5rem;}
</style>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="table-container" style="max-width:600px;margin:0 auto;">
            <div class="table-header">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Mi Perfil</h5>
            </div>
            <div style="padding:28px;text-align:center;">
                <div class="avatar-upload mb-3" onclick="document.getElementById('fotoInput').click()">
                    <img id="fotoPerfil" src="" alt="Foto">
                    <div class="overlay"><i class="bi bi-camera-fill"></i></div>
                </div>
                <input type="file" id="fotoInput" accept="image/*" style="display:none;" onchange="subirFoto(this)">
                <small class="text-muted d-block mb-3">Haz clic para cambiar foto</small>

                <div style="text-align:left;">
                    <div class="mb-2">
                        <label class="form-label small">Usuario</label>
                        <input type="text" class="form-control" id="perfilUsername" readonly style="background:var(--bg-input);">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Nombre</label>
                        <input type="text" class="form-control" id="perfilNombre">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Email</label>
                        <input type="email" class="form-control" id="perfilEmail">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Rol</label>
                        <input type="text" class="form-control" id="perfilRol" readonly style="background:var(--bg-input);">
                    </div>
                    <hr style="border-color:var(--border);">
                    <div class="mb-2">
                        <label class="form-label small">Nueva contraseña <span class="text-muted">(dejar vacío para mantener)</span></label>
                        <input type="password" class="form-control" id="perfilPassword" placeholder="••••••">
                    </div>
                </div>
                <button class="btn btn-primary-custom btn-sm mt-3 w-100" onclick="guardarPerfil()">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
