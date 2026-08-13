<style>
    .soporte-section { margin-bottom: 24px; }
    .soporte-section-title {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
        color: var(--text-muted); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);
    }
    .soporte-card {
        background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 16px 20px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s;
    }
    .soporte-card:hover { border-color: var(--primary); transform: translateY(-1px); }
    .soporte-card-icon { font-size: 1.3rem; color: var(--primary); flex-shrink: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(70,105,250,0.08); border-radius: 10px; }
    .soporte-card-title { font-size: 0.9rem; font-weight: 600; color: var(--text); margin-bottom: 2px; }
    .soporte-card-desc { font-size: 0.78rem; color: var(--text-muted); margin: 0; }
    .soporte-card-arrow { color: var(--text-muted); font-size: 0.85rem; flex-shrink: 0; }
    .soporte-modal-body { font-size: 0.88rem; line-height: 1.7; color: var(--text); }
    .soporte-modal-body h6 { font-weight: 600; margin-top: 16px; margin-bottom: 6px; }
    .soporte-modal-body p { margin-bottom: 10px; }
    .soporte-modal-body ul { padding-left: 20px; margin-bottom: 10px; }
    .soporte-modal-body li { margin-bottom: 4px; }
    .soporte-form label { font-size: 0.8rem; font-weight: 500; color: var(--text); }
    .soporte-form input, .soporte-form textarea, .soporte-form select {
        font-size: 0.85rem; background: var(--bg-input); color: var(--text); border-color: var(--border);
    }
    .soporte-form input:focus, .soporte-form textarea:focus, .soporte-form select:focus {
        border-color: var(--primary); box-shadow: none;
    }
</style>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-question-circle-fill"></i> Soporte</h5>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="soporte-section">
                <div class="soporte-section-title"><i class="bi bi-life-preserver"></i> Ayuda y Soporte</div>

                <div class="soporte-card" onclick="abrirSoporte('centro-ayuda')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon"><i class="bi bi-book-half"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Centro de Ayuda</div>
                            <p class="soporte-card-desc">Manuales, preguntas frecuentes y tutoriales de uso.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>

                <div class="soporte-card" onclick="abrirSoporte('contactar')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(34,197,94,0.08);color:#22c55e;"><i class="bi bi-headset"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Contactar Soporte</div>
                            <p class="soporte-card-desc">Formulario para solicitar ayuda directa al equipo técnico.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>

                <div class="soporte-card" onclick="abrirSoporte('reportar')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(239,68,68,0.08);color:#ef4444;"><i class="bi bi-bug-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Reportar un Problema</div>
                            <p class="soporte-card-desc">Reporta errores técnicos indicando el módulo afectado.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="soporte-section">
                <div class="soporte-section-title"><i class="bi bi-shield-lock"></i> Legal</div>

                <div class="soporte-card" onclick="abrirSoporte('terminos')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(168,85,247,0.08);color:#a855f7;"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Términos y Condiciones</div>
                            <p class="soporte-card-desc">Condiciones de uso de la plataforma KipuCloud.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>

                <div class="soporte-card" onclick="abrirSoporte('privacidad')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(6,182,212,0.08);color:#06b6d4;"><i class="bi bi-lock-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Política de Privacidad</div>
                            <p class="soporte-card-desc">Cómo se manejan y protegen tus datos personales.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>

                <div class="soporte-card" onclick="abrirSoporte('reclamaciones')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(245,158,11,0.08);color:#f59e0b;"><i class="bi bi-journal-text"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Libro de Reclamaciones</div>
                            <p class="soporte-card-desc">Registra reclamaciones formales conforme a INDECOPI.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="soporteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);padding:16px 24px;">
                <h6 class="modal-title" id="soporteModalTitle" style="font-weight:600;"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body soporte-modal-body" id="soporteModalBody" style="padding:24px;">
            </div>
        </div>
    </div>
</div>

<script>
var soporteContenidos = {
    'centro-ayuda': {
        titulo: '<i class="bi bi-book-half"></i> Centro de Ayuda',
        html: '<p>Bienvenido al Centro de Ayuda de <strong>KipuCloud</strong>. Aquí encontrarás la información necesaria para sacar el máximo provecho de la plataforma.</p>' +
            '<h6>Preguntas Frecuentes</h6>' +
            '<ul>' +
            '<li><strong>¿Cómo creo una publicación?</strong> — Ve a la sección correspondiente (Noticias, Ideas, Manual o Tareas) y haz clic en "Nueva publicación".</li>' +
            '<li><strong>¿Cómo agrego un recordatorio?</strong> — Desde cualquier publicación, haz clic en el ícono de campana "Recordatorio" y seleccione la fecha.</li>' +
            '<li><strong>¿Cómo marco una tarea como completada?</strong> — En la sección de Tareas, marca el checkbox correspondiente a la tarea.</li>' +
            '<li><strong>¿Cómo cambio mi contraseña?</strong> — Ve a Configuración > Sesión > Cambiar contraseña.</li>' +
            '<li><strong>¿Puedo subir archivos?</strong> — Sí, desde el módulo de Pagos puedes subir comprobantes de pago en formato Excel.</li>' +
            '</ul>' +
            '<h6>Tutoriales Rápidos</h6>' +
            '<ul>' +
            '<li>Creación y gestión de noticias del equipo.</li>' +
            '<li>Uso del calendario para eventos y recordatorios.</li>' +
            '<li>Gestión de tareas diarias y seguimiento de entregas.</li>' +
            '<li>Configuración visual del sistema (colores, logo, marca).</li>' +
            '</ul>' +
            '<p class="text-muted" style="font-size:0.8rem;margin-top:16px;">Si necesitas ayuda adicional, contacta al equipo de soporte desde "Contactar Soporte".</p>'
    },
    'contactar': {
        titulo: '<i class="bi bi-headset"></i> Contactar Soporte',
        html: '<p>Completa el formulario y nuestro equipo técnico te responderá a la brevedad.</p>' +
            '<form class="soporte-form" onsubmit="enviarSoporte(event)">' +
            '<div class="mb-3"><label class="form-label">Nombre completo</label><input type="text" class="form-control" placeholder="Tu nombre" required></div>' +
            '<div class="mb-3"><label class="form-label">Correo electrónico</label><input type="email" class="form-control" placeholder="correo@empresa.com" required></div>' +
            '<div class="mb-3"><label class="form-label">Módulo afectado</label><select class="form-select"><option value="">Seleccionar...</option><option>Dashboard</option><option>Noticias</option><option>Ideas</option><option>Manual</option><option>Tareas</option><option>Pagos</option><option>Configuración</option><option>Otro</option></select></div>' +
            '<div class="mb-3"><label class="form-label">Mensaje</label><textarea class="form-control" rows="4" placeholder="Describe tu consulta o problema..." required></textarea></div>' +
            '<button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send-fill"></i> Enviar solicitud</button>' +
            '</form>'
    },
    'reportar': {
        titulo: '<i class="bi bi-bug-fill"></i> Reportar un Problema',
        html: '<p>Si encontraste un error técnico, descríbelo lo más detallada posible para que podamos resolverlo rápido.</p>' +
            '<form class="soporte-form" onsubmit="enviarSoporte(event)">' +
            '<div class="mb-3"><label class="form-label">Nombre</label><input type="text" class="form-control" placeholder="Tu nombre" required></div>' +
            '<div class="mb-3"><label class="form-label">Correo electrónico</label><input type="email" class="form-control" placeholder="correo@empresa.com" required></div>' +
            '<div class="mb-3"><label class="form-label">Módulo afectado</label><select class="form-select" required><option value="">Seleccionar...</option><option>Dashboard</option><option>Noticias</option><option>Ideas</option><option>Manual</option><option>Tareas</option><option>Recordatorios</option><option>Marcadores</option><option>Pagos</option><option>Configuración</option><option>Calendario</option><option>Otro</option></select></div>' +
            '<div class="mb-3"><label class="form-label">Severidad</label><select class="form-select"><option value="baja">Baja — No me impide usar el sistema</option><option value="media" selected>Media — Afecta alguna funcionalidad</option><option value="alta">Alta — No puedo usar el sistema</option></select></div>' +
            '<div class="mb-3"><label class="form-label">Descripción del problema</label><textarea class="form-control" rows="4" placeholder="Describe los pasos para reproducir el error..." required></textarea></div>' +
            '<div class="mb-3"><label class="form-label">Adjuntar captura (opcional)</label><input type="file" class="form-control" accept="image/*"></div>' +
            '<button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-bug-fill"></i> Reportar problema</button>' +
            '</form>'
    },
    'terminos': {
        titulo: '<i class="bi bi-file-earmark-text-fill"></i> Términos y Condiciones',
        html: '<p><strong>Última actualización:</strong> 12 de agosto de 2026</p>' +
            '<h6>1. Acceptance of Terms</h6>' +
            '<p>Al acceder y utilizar KipuCloud ("la Plataforma"), el usuario acepta estos Términos y Condiciones. Si no está de acuerdo, no debe utilizar el servicio.</p>' +
            '<h6>2. Description of Service</h6>' +
            '<p>KipuCloud es una plataforma de gestión interna empresarial que incluye módulos de noticias, ideas, manual de procedimientos, tareas, pagos, calendario y soporte técnico.</p>' +
            '<h6>3. User Responsibilities</h6>' +
            '<ul>' +
            '<li>El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso.</li>' +
            '<li>Se compromete a utilizar la Plataforma de manera ética y conforme a las leyes aplicables.</li>' +
            '<li>No deberá publicar contenido ofensivo, ilegal o que vulnere derechos de terceros.</li>' +
            '</ul>' +
            '<h6>4. Intellectual Property</h6>' +
            '<p>Todo el contenido generado dentro de la Plataforma (publicaciones, documentos, imágenes) es propiedad de la empresa que utiliza KipuCloud.</p>' +
            '<h6>5. Limitation of Liability</h6>' +
            '<p>KipuCloud se proporcion tal cual, sin garantías expresas. No será responsable por pérdidas de datos, interrupciones del servicio o daños indirectos.</p>' +
            '<h6>6. Modifications</h6>' +
            '<p>La empresa se reserva el derecho de modificar estos términos en cualquier momento. Los usuarios serán notificados de cambios significativos.</p>'
    },
    'privacidad': {
        titulo: '<i class="bi bi-lock-fill"></i> Política de Privacidad',
        html: '<p><strong>Última actualización:</strong> 12 de agosto de 2026</p>' +
            '<h6>1. Datos Recopilados</h6>' +
            '<p>KipuCloud recopila la siguiente información personal de los usuarios:</p>' +
            '<ul>' +
            '<li>Nombre completo y correo electrónico</li>' +
            '<li>Fotografía de perfil (opcional)</li>' +
            '<li>Rol y departamento dentro de la organización</li>' +
            '<li>Actividad dentro de la plataforma (publicaciones, comentarios, tareas completadas)</li>' +
            '</ul>' +
            '<h6>2. Uso de los Datos</h6>' +
            '<p>Los datos se utilizan exclusivamente para:</p>' +
            '<ul>' +
            '<li>Funcionamiento interno de la plataforma</li>' +
            '<li>Generación de reportes y estadísticas de productividad</li>' +
            '<li>Gestión de recordatorios, tareas y notificaciones</li>' +
            '</ul>' +
            '<h6>3. Almacenamiento y Seguridad</h6>' +
            '<p>Los datos se almacenan en servidores seguros con cifrado SSL. Se implementan medidas de seguridad razonables para proteger la información contra accesos no autorizados.</p>' +
            '<h6>4. Compartición de Datos</h6>' +
            '<p>KipuCloud no vende ni comparte datos personales con terceros, salvo requerimiento legal o autorización expresa del usuario.</p>' +
            '<h6>5. Derechos del Usuario</h6>' +
            '<ul>' +
            '<li>Acceder a sus datos personales</li>' +
            '<li>Solicitar rectificación de datos inexactos</li>' +
            '<li>Solicitar eliminación de sus datos (derecho al olvido)</li>' +
            '</ul>' +
            '<h6>6. Contacto</h6>' +
            '<p>Para consultas sobre privacidad, contactar a través del formulario de "Contactar Soporte".</p>'
    },
    'reclamaciones': {
        titulo: '<i class="bi bi-journal-text"></i> Libro de Reclamaciones',
        html: '<p>Conforme a la normativa vigente de <strong>INDECOPI</strong>, pones a disposición el siguiente Libro de Reclamaciones.</p>' +
            '<h6>Datos del Proveedor</h6>' +
            '<ul>' +
            '<li><strong>Razón Social:</strong> KipuCloud S.A.C.</li>' +
            '<li><strong>RUC:</strong> 20512345678</li>' +
            '<li><strong>Domicilio:</strong> Av. Principal 123, Oficina 401, Lima, Perú</li>' +
            '<li><strong>Correo:</strong> soporte@kipucloud.com</li>' +
            '</ul>' +
            '<h6>Proceso de Reclamación</h6>' +
            '<ol>' +
            '<li>El consumidor puede registrar su reclamación a través de este formulario o presencialmente.</li>' +
            '<li>Se emitirá un código de seguimiento para cada reclamación registrada.</li>' +
            '<li>El plazo de respuesta es de hasta 15 días hábiles desde la fecha de registro.</li>' +
            '<li>Si la reclamación no es resuelta satisfactoriamente, el consumidor puede acudir a INDECOPI.</li>' +
            '</ol>' +
            '<h6>Registrar Reclamación</h6>' +
            '<form class="soporte-form" onsubmit="enviarSoporte(event)">' +
            '<div class="mb-3"><label class="form-label">Nombre completo del reclamante</label><input type="text" class="form-control" required></div>' +
            '<div class="mb-3"><label class="form-label">DNI / Documento de identidad</label><input type="text" class="form-control" required></div>' +
            '<div class="mb-3"><label class="form-label">Correo electrónico</label><input type="email" class="form-control" required></div>' +
            '<div class="mb-3"><label class="form-label">Teléfono de contacto</label><input type="tel" class="form-control"></div>' +
            '<div class="mb-3"><label class="form-label">Detalle de la reclamación</label><textarea class="form-control" rows="4" placeholder="Describe detalladamente tu reclamación..." required></textarea></div>' +
            '<button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-journal-text"></i> Registrar reclamación</button>' +
            '</form>'
    }
};

function abrirSoporte(clave) {
    var data = soporteContenidos[clave];
    if (!data) return;
    document.getElementById('soporteModalTitle').innerHTML = data.titulo;
    document.getElementById('soporteModalBody').innerHTML = data.html;
    var modal = new bootstrap.Modal(document.getElementById('soporteModal'));
    modal.show();
}

function enviarSoporte(e) {
    e.preventDefault();
    Swal.fire({ icon: 'success', title: 'Enviado', text: 'Tu solicitud fue registrada correctamente. Te contactaremos pronto.', timer: 2500, showConfirmButton: false });
    bootstrap.Modal.getInstance(document.getElementById('soporteModal')).hide();
}
</script>
