<style>
    .soporte-section { margin-bottom: 24px; }
    .soporte-section-title {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
        color: var(--text-muted); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);
    }
    .soporte-card {
        background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius);
        padding: 16px 20px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s;
        text-decoration: none; display: block; color: inherit;
    }
    .soporte-card:hover { border-color: var(--primary); transform: translateY(-1px); text-decoration: none; color: inherit; }
    .soporte-card-icon { font-size: 1.3rem; flex-shrink: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
    .soporte-card-title { font-size: 0.9rem; font-weight: 600; color: var(--text); margin-bottom: 2px; }
    .soporte-card-desc { font-size: 0.78rem; color: var(--text-muted); margin: 0; }
    .soporte-card-arrow { color: var(--text-muted); font-size: 0.85rem; flex-shrink: 0; }
</style>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-question-circle-fill"></i> Soporte</h5>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="soporte-section">
                <div class="soporte-section-title"><i class="bi bi-life-preserver"></i> Ayuda y Soporte</div>

                <a href="<?= site_url('soporte/centro-ayuda') ?>" class="soporte-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(70,105,250,0.08);color:var(--primary);"><i class="bi bi-book-half"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Centro de Ayuda</div>
                            <p class="soporte-card-desc">Manuales, preguntas frecuentes y tutoriales de uso.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </a>

                <a href="<?= site_url('soporte/contactar') ?>" class="soporte-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(34,197,94,0.08);color:#22c55e;"><i class="bi bi-headset"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Contactar Soporte</div>
                            <p class="soporte-card-desc">Formulario para solicitar ayuda directa al equipo técnico.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </a>

                <a href="<?= site_url('soporte/reportar') ?>" class="soporte-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(239,68,68,0.08);color:#ef4444;"><i class="bi bi-bug-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Reportar un Problema</div>
                            <p class="soporte-card-desc">Reporta errores técnicos indicando el módulo afectado.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="soporte-section">
                <div class="soporte-section-title"><i class="bi bi-shield-lock"></i> Legal</div>

                <a href="<?= site_url('soporte/terminos') ?>" class="soporte-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(168,85,247,0.08);color:#a855f7;"><i class="bi bi-file-earmark-text-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Términos y Condiciones</div>
                            <p class="soporte-card-desc">Condiciones de uso de la plataforma KipuCloud.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </a>

                <a href="<?= site_url('soporte/privacidad') ?>" class="soporte-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(6,182,212,0.08);color:#06b6d4;"><i class="bi bi-lock-fill"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Política de Privacidad</div>
                            <p class="soporte-card-desc">Cómo se manejan y protegen tus datos personales.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </a>

                <a href="<?= site_url('soporte/reclamaciones') ?>" class="soporte-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soporte-card-icon" style="background:rgba(245,158,11,0.08);color:#f59e0b;"><i class="bi bi-journal-text"></i></div>
                        <div class="flex-grow-1">
                            <div class="soporte-card-title">Libro de Reclamaciones</div>
                            <p class="soporte-card-desc">Registra reclamaciones formales conforme a INDECOPI.</p>
                        </div>
                        <div class="soporte-card-arrow"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function enviarFormulario(e) {
    e.preventDefault();
    Swal.fire({ icon: 'success', title: 'Enviado', text: 'Tu solicitud fue registrada correctamente. Te contactaremos pronto.', timer: 2500, showConfirmButton: false });
}
</script>
