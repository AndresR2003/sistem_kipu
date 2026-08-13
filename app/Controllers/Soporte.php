<?php

namespace App\Controllers;

class Soporte extends BaseController
{
    private function render(string $titulo, string $icono, string $grupo, string $contenido): string
    {
        return view('layout', [
            'contenido'  => view('soporte_contenido', [
                'titulo'    => $titulo,
                'icono'     => $icono,
                'grupo'     => $grupo,
                'contenido' => $contenido,
            ]),
            'titulo'      => $titulo . ' - Soporte - KipuCloud',
            'pageScripts' => '',
        ]);
    }

    public function index(): string
    {
        return view('layout', [
            'contenido'  => view('soporte'),
            'titulo'     => 'Soporte - KipuCloud',
            'pageScripts' => '',
        ]);
    }

    public function centroAyuda(): string
    {
        $html = '
        <p>Bienvenido al Centro de Ayuda de <strong>KipuCloud</strong>. Aquí encontrarás la información necesaria para sacar el máximo provecho de la plataforma.</p>

        <h6>Preguntas Frecuentes</h6>
        <ul>
            <li><strong>¿Cómo creo una publicación?</strong> — Ve a la sección correspondiente (Noticias, Ideas, Manual o Tareas) y haz clic en "Nueva publicación".</li>
            <li><strong>¿Cómo agrego un recordatorio?</strong> — Desde cualquier publicación, haz clic en el ícono de campana "Recordatorio" y selecciona la fecha.</li>
            <li><strong>¿Cómo marco una tarea como completada?</strong> — En la sección de Tareas, marca el checkbox correspondiente a la tarea.</li>
            <li><strong>¿Cómo cambio mi contraseña?</strong> — Ve a Configuración > Sesión > Cambiar contraseña.</li>
            <li><strong>¿Puedo subir archivos?</strong> — Sí, desde el módulo de Pagos puedes subir comprobantes de pago en formato Excel.</li>
        </ul>

        <h6>Tutoriales Rápidos</h6>
        <ul>
            <li>Creación y gestión de noticias del equipo.</li>
            <li>Uso del calendario para eventos y recordatorios.</li>
            <li>Gestión de tareas diarias y seguimiento de entregas.</li>
            <li>Configuración visual del sistema (colores, logo, marca).</li>
        </ul>

        <h6>Guías por Módulo</h6>
        <ul>
            <li><strong>Dashboard</strong> — Panel principal con accesos rápidos, estadísticas y resumen de actividad.</li>
            <li><strong>Noticias</strong> — Publica novedades del equipo, asigna destinatarios y gestiona comentarios.</li>
            <li><strong>Ideas</strong> — Comparte propuestas de mejora y vota las mejores ideas.</li>
            <li><strong>Manual</strong> — Documenta procesos, procedimientos y políticas de la empresa.</li>
            <li><strong>Tareas</strong> — Crea tareas diarias o puntuales, asígnalas y haz seguimiento.</li>
            <li><strong>Pagos</strong> — Registra pagos, sube comprobantes y revisa el historial.</li>
            <li><strong>Calendario</strong> — Visualiza eventos, recordatorios y fechas importantes.</li>
        </ul>

        <p class="text-muted" style="margin-top:16px;">Si necesitas ayuda adicional, contacta al equipo de soporte desde "Contactar Soporte".</p>';

        return $this->render('Centro de Ayuda', 'bi bi-book-half', 'ayuda', $html);
    }

    public function contactar(): string
    {
        $html = '
        <p>Completa el formulario y nuestro equipo técnico te responderá a la brevedad.</p>

        <form class="soporte-form" onsubmit="enviarFormulario(event)">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" placeholder="Tu nombre" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" placeholder="correo@empresa.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Departamento</label>
                    <select class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option>Recepción</option>
                        <option>Mantenimiento</option>
                        <option>Limpieza</option>
                        <option>Administración</option>
                        <option>Gerencia</option>
                        <option>Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Módulo afectado</label>
                    <select class="form-select">
                        <option value="">Ninguno en particular</option>
                        <option>Dashboard</option>
                        <option>Noticias</option>
                        <option>Ideas</option>
                        <option>Manual</option>
                        <option>Tareas</option>
                        <option>Pagos</option>
                        <option>Configuración</option>
                        <option>Otro</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Mensaje</label>
                    <textarea class="form-control" rows="4" placeholder="Describe tu consulta o solicitud..." required></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm mt-3"><i class="bi bi-send-fill"></i> Enviar solicitud</button>
        </form>';

        return $this->render('Contactar Soporte', 'bi bi-headset', 'ayuda', $html);
    }

    public function reportar(): string
    {
        $html = '
        <p>Si encontraste un error técnico, descríbelo lo más detalladamente posible para que podamos resolverlo rápido.</p>

        <form class="soporte-form" onsubmit="enviarFormulario(event)">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" placeholder="Tu nombre" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" placeholder="correo@empresa.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Módulo afectado</label>
                    <select class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option>Dashboard</option>
                        <option>Noticias</option>
                        <option>Ideas</option>
                        <option>Manual</option>
                        <option>Tareas</option>
                        <option>Recordatorios</option>
                        <option>Marcadores</option>
                        <option>Pagos</option>
                        <option>Configuración</option>
                        <option>Calendario</option>
                        <option>Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Severidad</label>
                    <select class="form-select" required>
                        <option value="baja">Baja — No me impide usar el sistema</option>
                        <option value="media" selected>Media — Afecta alguna funcionalidad</option>
                        <option value="alta">Alta — No puedo usar el sistema</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Descripción del problema</label>
                    <textarea class="form-control" rows="4" placeholder="Describe los pasos para reproducir el error..." required></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Adjuntar captura (opcional)</label>
                    <input type="file" class="form-control" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn btn-danger btn-sm mt-3"><i class="bi bi-bug-fill"></i> Reportar problema</button>
        </form>';

        return $this->render('Reportar un Problema', 'bi bi-bug-fill', 'ayuda', $html);
    }

    public function terminos(): string
    {
        $html = '
        <p><strong>Última actualización:</strong> 12 de agosto de 2026</p>

        <h6>1. Acceptance of Terms</h6>
        <p>Al acceder y utilizar KipuCloud ("la Plataforma"), el usuario acepta estos Términos y Condiciones. Si no está de acuerdo con alguno de los siguientes puntos, no debe utilizar el servicio.</p>

        <h6>2. Description of Service</h6>
        <p>KipuCloud es una plataforma de gestión interna empresarial que incluye módulos de noticias, ideas, manual de procedimientos, tareas, pagos, calendario y soporte técnico. Está diseñada para facilitar la comunicación y productividad dentro de la organización.</p>

        <h6>3. User Responsibilities</h6>
        <ul>
            <li>El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso.</li>
            <li>Se compromete a utilizar la Plataforma de manera ética y conforme a las leyes aplicables.</li>
            <li>No deberá publicar contenido ofensivo, ilegal o que vulnere derechos de terceros.</li>
            <li>El usuario es responsable de toda actividad que se realice bajo su cuenta.</li>
        </ul>

        <h6>4. Intellectual Property</h6>
        <p>Todo el contenido generado dentro de la Plataforma (publicaciones, documentos, imágenes) es propiedad de la empresa que utiliza KipuCloud. El código fuente y la tecnología de la plataforma son propiedad de KipuCloud.</p>

        <h6>5. Limitation of Liability</h6>
        <p>KipuCloud se proporciona "tal cual", sin garantías expresas o implícitas. No será responsable por pérdidas de datos, interrupciones del servicio, daños indirectos o consecuentes derivados del uso de la plataforma.</p>

        <h6>6. Data and Privacy</h6>
        <p>El manejo de datos personales se rige por nuestra Política de Privacidad, que forma parte integral de estos Términos. Al utilizar la plataforma, el usuario consiente el tratamiento de sus datos conforme a dicha política.</p>

        <h6>7. Modifications</h6>
        <p>La empresa se reserva el derecho de modificar estos términos en cualquier momento. Los usuarios serán notificados de cambios significativos a través de la plataforma o por correo electrónico.</p>

        <h6>8. Governing Law</h6>
        <p>Estos términos se rigen por las leyes de la República del Perú. Cualquier controversia será sometida a los tribunales competentes de Lima.</p>';

        return $this->render('Términos y Condiciones', 'bi bi-file-earmark-text-fill', 'legal', $html);
    }

    public function privacidad(): string
    {
        $html = '
        <p><strong>Última actualización:</strong> 12 de agosto de 2026</p>

        <h6>1. Datos Recopilados</h6>
        <p>KipuCloud recopila la siguiente información personal de los usuarios:</p>
        <ul>
            <li>Nombre completo y correo electrónico</li>
            <li>Fotografía de perfil (opcional)</li>
            <li>Rol y departamento dentro de la organización</li>
            <li>Actividad dentro de la plataforma (publicaciones, comentarios, tareas completadas)</li>
        </ul>

        <h6>2. Uso de los Datos</h6>
        <p>Los datos se utilizan exclusivamente para:</p>
        <ul>
            <li>Funcionamiento interno de la plataforma</li>
            <li>Generación de reportes y estadísticas de productividad</li>
            <li>Gestión de recordatorios, tareas y notificaciones</li>
            <li>Comunicación interna entre miembros de la organización</li>
        </ul>

        <h6>3. Almacenamiento y Seguridad</h6>
        <p>Los datos se almacenan en servidores seguros con cifrado SSL/TLS. Se implementan medidas de seguridad razonables para proteger la información contra accesos no autorizados, pérdida o alteración.</p>

        <h6>4. Compartición de Datos</h6>
        <p>KipuCloud no vende ni comparte datos personales con terceros, salvo:</p>
        <ul>
            <li>Requerimiento legal o judicial</li>
            <li>Autorización expresa del usuario</li>
            <li>Proveedores de servicios que prestan soporte técnico (bajo acuerdos de confidencialidad)</li>
        </ul>

        <h6>5. Retención de Datos</h6>
        <p>Los datos personales se conservan mientras la cuenta del usuario esté activa. Tras la eliminación de la cuenta, los datos se eliminan de forma permanente en un plazo máximo de 30 días.</p>

        <h6>6. Derechos del Usuario</h6>
        <ul>
            <li><strong>Acceso:</strong> Solicitar una copia de todos los datos personales almacenados.</li>
            <li><strong>Rectificación:</strong> Solicitar la corrección de datos inexactos.</li>
            <li><strong>Eliminación:</strong> Solicitar la eliminación de datos personales (derecho al olvido).</li>
            <li><strong>Portabilidad:</strong> Solicitar los datos en un formato estructurado y de uso común.</li>
        </ul>

        <h6>7. Cookies</h6>
        <p>KipuCloud utiliza cookies estrictamente necesarias para el funcionamiento de la plataforma (sesión de usuario, preferencias). No se utilizan cookies de rastreo o publicitarias.</p>

        <h6>8. Contacto</h6>
        <p>Para consultas sobre privacidad o ejercer tus derechos, contacta a través del formulario de "Contactar Soporte".</p>';

        return $this->render('Política de Privacidad', 'bi bi-lock-fill', 'legal', $html);
    }

    public function reclamaciones(): string
    {
        $html = '
        <p>Conforme a la normativa vigente de <strong>INDECOPI</strong>, ponemos a disposición el siguiente Libro de Reclamaciones.</p>

        <h6>Datos del Proveedor</h6>
        <ul>
            <li><strong>Razón Social:</strong> KipuCloud S.A.C.</li>
            <li><strong>RUC:</strong> 20512345678</li>
            <li><strong>Domicilio:</strong> Av. Principal 123, Oficina 401, Lima, Perú</li>
            <li><strong>Correo:</strong> soporte@kipucloud.com</li>
            <li><strong>Teléfono:</strong> +51 (01) 555-1234</li>
        </ul>

        <h6>Proceso de Reclamación</h6>
        <ol>
            <li>El consumidor puede registrar su reclamación a través de este formulario o presencialmente en nuestras oficinas.</li>
            <li>Se emitirá un código de seguimiento para cada reclamación registrada.</li>
            <li>El plazo de respuesta es de hasta <strong>15 días hábiles</strong> desde la fecha de registro.</li>
            <li>Si la reclamación no es resuelta satisfactoriamente, el consumidor puede acudir a INDECOPI.</li>
        </ol>

        <p class="text-muted" style="margin-top:12px;">INDECOPI pone a tu disposición su herramienta oficial para registro de reclamaciones: <a href="https://consultas.indecopi.gob.pe" target="_blank" rel="noopener">consultas.indecopi.gob.pe</a></p>

        <h6>Registrar Reclamación</h6>
        <form class="soporte-form" onsubmit="enviarFormulario(event)">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre completo del reclamante</label>
                    <input type="text" class="form-control" placeholder="Nombres y apellidos" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">DNI / Documento de identidad</label>
                    <input type="text" class="form-control" placeholder="Número de documento" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" placeholder="correo@empresa.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono de contacto</label>
                    <input type="tel" class="form-control" placeholder="+51 999 999 999">
                </div>
                <div class="col-12">
                    <label class="form-label">Detalle de la reclamación</label>
                    <textarea class="form-control" rows="5" placeholder="Describe detalladamente tu reclamación, indicando fecha, producto o servicio, y monto en caso aplique..." required></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-warning btn-sm mt-3"><i class="bi bi-journal-text"></i> Registrar reclamación</button>
        </form>';

        return $this->render('Libro de Reclamaciones', 'bi bi-journal-text', 'legal', $html);
    }
}
