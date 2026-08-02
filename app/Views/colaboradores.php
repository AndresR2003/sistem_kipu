<style>
#tablaColaboradores th { font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
.badge-rol { font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; }
.badge-rol.superadmin { background: rgba(245,158,11,0.15); color: #f59e0b; }
.badge-rol.admin { background: rgba(70,105,250,0.15); color: var(--primary); }
.badge-rol.empleado { background: rgba(34,197,94,0.15); color: #22c55e; }
.badge-rol.soporte { background: rgba(99,102,241,0.15); color: #6366f1; }
.badge-rol.vendedor { background: rgba(236,72,153,0.15); color: #ec4899; }
.badge-rol.tecnico { background: rgba(251,146,60,0.15); color: #fb923c; }
</style>

<div class="table-container">
    <div class="table-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <h5 class="mb-0"><i class="bi bi-person-badge-fill"></i> Personal / Empleados</h5>
                <small class="text-muted">Gestion de empleados del sistema</small>
            </div>
            <button class="btn btn-primary-custom btn-sm" onclick="nuevoColaborador()">
                <i class="bi bi-plus-lg"></i> Nuevo
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table id="tablaColaboradores" class="table table-sm">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Departamento</th>
                    <th>Puesto</th>
                    <th>Rol</th>
                    <th>Telefono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalColaborador" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-person-badge"></i> <span id="modalTitle">Nuevo Empleado</span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="colaboradorId">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label small">Usuario *</label>
                            <input type="text" class="form-control" id="colUsername" placeholder="username">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label small">Password <span id="passLabel" class="text-muted">(dejar vacio para generar)</span></label>
                            <input type="password" class="form-control" id="colPassword" placeholder="••••••">
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label small">Nombre *</label>
                            <input type="text" class="form-control" id="colNombre" placeholder="Nombre completo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label small">Email *</label>
                            <input type="email" class="form-control" id="colEmail" placeholder="email@ejemplo.com">
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label small">Rol</label>
                            <select class="form-select" id="colRol">
                                <option value="empleado">Empleado</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="soporte">Soporte</option>
                                <option value="vendedor">Vendedor</option>
                                <option value="tecnico">Tecnico</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label small">Departamento</label>
                            <select class="form-select" id="colDepto">
                                <option value="">Sin departamento</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label small">Estado</label>
                            <select class="form-select" id="colActivo">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label small">Puesto</label>
                            <input type="text" class="form-control" id="colPuesto" placeholder="Ej: Analista">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label small">Telefono</label>
                            <input type="text" class="form-control" id="colTelefono" placeholder="000-000-0000">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label small">Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="colFechaNac">
                        </div>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label small">Fecha Contratacion</label>
                            <input type="date" class="form-control" id="colFechaCont">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary-custom btn-sm" onclick="guardarColaborador()">Guardar</button>
            </div>
        </div>
    </div>
</div>
