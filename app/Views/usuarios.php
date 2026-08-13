<!-- Usuarios View -->
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-people-fill"></i> Usuarios Registrados
        </h5>
        <button class="btn btn-primary-custom" onclick="abrirModalUsuario()">
            <i class="bi bi-plus-lg"></i> Nuevo Usuario
        </button>
    </div>

    <div class="table-responsive">
        <table id="tablaUsuarios" class="table table-hover w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Telefono</th>
                    <th>Creado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tbodyUsuarios">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear/Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalUsuario">
                    <i class="bi bi-person-plus"></i> Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formUsuario">
                    <input type="hidden" id="usuarioId" value="">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="usuarioNombre"
                               placeholder="Nombre del usuario" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" class="form-control" id="usuarioTelefono"
                               placeholder="Numero de telefono" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado *</label>
                        <select class="form-select" id="usuarioActivo" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary-custom" onclick="guardarUsuario()">
                    <i class="bi bi-check-lg"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
