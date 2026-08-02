<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Pago - Litio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4669FA;
            --primary-dark: #3651d4;
            --bg-dark: #0f0f1a;
            --gradient-primary: linear-gradient(135deg, #4669FA 0%, #3651d4 100%);
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f0f1a;
        background-image: radial-gradient(ellipse at 30% 20%, rgba(70,105,250,0.06) 0%, transparent 60%),
                          radial-gradient(ellipse at 70% 80%, rgba(70,105,250,0.04) 0%, transparent 60%);
            min-height: 100vh;
            color: #ffffff;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px;
        }
        .main-card {
            max-width: 600px;
            width: 100%;
            background: rgba(30,30,47,0.8);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            margin-top: 20px;
        }
        .card-header-custom {
            background: var(--gradient-primary);
            padding: 30px;
            text-align: center;
        }
        .card-header-custom h2 { margin: 0; font-weight: 700; font-size: 1.5rem; }
        .card-header-custom p { margin: 5px 0 0; opacity: 0.8; font-size: 0.9rem; }
        .card-body-custom { padding: 30px; }
        .info-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 14px;
            text-align: center;
        }
        .info-box .label {
            color: rgba(255,255,255,0.5);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .info-box .value { font-size: 1.6rem; font-weight: 700; }
        .info-box .value.text-success { color: #40c057; }
        .info-box .value.text-danger { color: #ff6b6b; }
        .badge-estado {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-pagado { background: rgba(25,135,84,0.2); color: #40c057; border: 1px solid rgba(25,135,84,0.3); }
        .badge-pendiente { background: rgba(255,193,7,0.2); color: #ffc107; border: 1px solid rgba(255,193,7,0.3); }
        .badge-no-pagado { background: rgba(220,53,69,0.2); color: #ff6b6b; border: 1px solid rgba(220,53,69,0.3); }
        .badge-rechazado { background: rgba(108,117,125,0.2); color: #adb5bd; border: 1px solid rgba(108,117,125,0.3); }

        .mes-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .mes-card:hover {
            border-color: rgba(70,105,250,0.3);
            background: rgba(255,255,255,0.06);
        }
        .mes-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .mes-card-header .mes-nombre {
            font-weight: 600;
            font-size: 1rem;
        }
        .mes-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-upload-mes {
            padding: 6px 16px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-upload-mes:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(70,105,250,0.4);
            color: white;
        }
        .btn-upload-mes:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .upload-area {
            border: 2px dashed rgba(255,255,255,0.2);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover { border-color: var(--primary); background: rgba(70,105,250,0.05); }
        .upload-area.dragover { border-color: var(--primary); background: rgba(70,105,250,0.1); }
        .upload-area i { font-size: 2rem; color: var(--primary); margin-bottom: 8px; }
        .preview-img { max-width: 100%; max-height: 180px; border-radius: 10px; margin-top: 10px; display: none; }

        .btn-send {
            width: 100%;
            padding: 14px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-send:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(70,105,250,0.4); }

        .section-divider {
            display: flex;
            align-items: center;
            margin: 28px 0 18px;
            gap: 12px;
        }
        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.1);
        }
        .section-divider span {
            color: rgba(255,255,255,0.5);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            white-space: nowrap;
        }

        .historial-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }
        .historial-card:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.1);
        }
        .historial-thumb {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: transform 0.2s;
            flex-shrink: 0;
        }
        .historial-thumb:hover {
            transform: scale(1.1);
            border-color: var(--primary);
        }
        .historial-thumb-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.2);
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .historial-info {
            flex: 1;
            min-width: 0;
        }
        .historial-info .mes-text {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        .historial-info .fecha-text {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
        }
        .historial-right {
            text-align: right;
            flex-shrink: 0;
        }
        .historial-right .monto-text {
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        .historial-observacion {
            font-size: 0.75rem;
            color: rgba(255,193,7,0.8);
            margin-top: 4px;
        }

        .modal-content {
            background: #1e1e2f;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
        }
        .modal-header { border-bottom: 1px solid rgba(255,255,255,0.08); }
        .modal-body { padding: 24px; }
        .modal-footer { border-top: 1px solid rgba(255,255,255,0.08); }
        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #ffffff;
            border-radius: 10px;
        }
        .form-control:focus { background: rgba(255,255,255,0.08); border-color: var(--primary); color: #ffffff; box-shadow: 0 0 0 3px rgba(70,105,250,0.15); }

        .comprobante-preview-modal {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.1);
        }

        .footer-text {
            text-align: center;
            padding: 18px 30px;
            color: rgba(255,255,255,0.3);
            font-size: 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        @media (max-width: 576px) {
            body { padding: 10px; align-items: flex-start; }
            .main-card { border-radius: 16px; margin-top: 10px; }
            .card-body-custom { padding: 16px; }
            .historial-card { padding: 10px; }
            .historial-thumb, .historial-thumb-placeholder { width: 40px; height: 40px; }
        }
    </style>
</head>
<body>

    <div class="main-card">
        <!-- Header -->
        <div class="card-header-custom">
                <h2><i class="bi bi-lightning-fill"></i> Hola, <?= esc($usuario['nombre']) ?></h2>
            <p>Mi pago de Litio</p>
        </div>

        <!-- Body -->
        <div class="card-body-custom">
            <!-- Mes actual -->
            <div class="info-box">
                <div class="label">Mes Actual</div>
                <div class="value" style="font-size:1.2rem;"><?= esc($mesNombre) ?> <?= esc($anioActual) ?></div>
            </div>

            <!-- Monto mensual -->
            <div class="info-box">
                <div class="label">Monto Mensual</div>
                <div class="value text-success">S/ <?= number_format($usuario['monto'], 2) ?></div>
            </div>

            <!-- Total adeudado -->
            <?php if ($totalAdeudado > 0): ?>
            <div class="info-box">
                <div class="label">Total Adeudado</div>
                <div class="value text-danger">S/ <?= number_format($totalAdeudado, 2) ?></div>
            </div>
            <?php endif; ?>

            <!-- Lista de meses pendientes con boton individual -->
            <?php if (count($mesesAdeudados) > 0): ?>
            <div class="mb-3">
                <h6 class="mb-3" style="color: rgba(255,255,255,0.7);">
                    <i class="bi bi-list-check"></i> Meses Pendientes
                </h6>
                <?php foreach ($mesesAdeudados as $mes):
                    $badgeClass = '';
                    switch ($mes['estado']) {
                        case 'PENDIENTE': $badgeClass = 'badge-pendiente'; break;
                        case 'NO_PAGADO': $badgeClass = 'badge-no-pagado'; break;
                        case 'RECHAZADO': $badgeClass = 'badge-rechazado'; break;
                        default: $badgeClass = 'badge-no-pagado';
                    }
                    $puedeSubir = ($mes['estado'] !== 'PAGADO');
                ?>
                <div class="mes-card" id="mesCard<?= $mes['id'] ?>">
                    <div class="mes-card-header">
                        <span class="mes-nombre"><?= esc($mes['mes_nombre']) ?> <?= esc($mes['anio']) ?></span>
                        <span class="badge-estado <?= $badgeClass ?>" style="font-size:0.7rem; padding:4px 10px;">
                            <?= esc($mes['estado']) ?>
                        </span>
                    </div>
                    <?php if (!empty($mes['observacion']) && $mes['estado'] === 'RECHAZADO'): ?>
                    <div style="font-size:0.8rem; color: rgba(255,255,255,0.5); margin-bottom:8px;">
                        <i class="bi bi-info-circle"></i> <?= esc($mes['observacion']) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($mes['captura'])): ?>
                    <div style="margin-bottom:8px;">
                        <img src="<?= base_url($mes['captura']) ?>" alt="Comprobante"
                             style="max-width:100%; max-height:120px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); cursor:pointer;"
                             onclick="verComprobantePublico('<?= base_url($mes['captura']) ?>', '<?= esc($mes['mes_nombre']) ?> <?= esc($mes['anio']) ?>')">
                    </div>
                    <?php endif; ?>
                    <div class="mes-card-footer">
                        <span style="font-size:0.85rem; color: rgba(255,255,255,0.5);">S/ <?= number_format($mes['monto'], 2) ?></span>
                        <?php if ($puedeSubir): ?>
                        <button class="btn-upload-mes" onclick="abrirModalPago(<?= $mes['id'] ?>, <?= $mes['mes'] ?>, <?= $mes['anio'] ?>, '<?= esc($mes['mes_nombre']) ?>')">
                            <i class="bi bi-cloud-arrow-up"></i> Subir Comprobante
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Todos pagados -->
            <div class="info-box" style="border-color: rgba(25,135,84,0.3);">
                <div style="font-size:2rem; margin-bottom:8px;">&#10003;</div>
                <div class="value text-success" style="font-size:1.2rem;">Todo al dia</div>
                <p style="color: rgba(255,255,255,0.5); margin: 6px 0 0; font-size:0.85rem;">No tienes pagos pendientes</p>
            </div>
            <?php endif; ?>

            <!-- Historial de Pagos -->
            <?php if (count($historial) > 0): ?>
            <div class="section-divider">
                <span><i class="bi bi-clock-history"></i> Historial de Pagos</span>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <select class="form-select" id="filtroAnioHistorial" style="width:auto; min-width:120px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:8px; padding:6px 12px; font-size:0.85rem;">
                    <option value="">Todos</option>
                </select>
            </div>

            <div id="listaHistorial">
                <?php foreach ($historial as $pago):
                    $badgeClass = '';
                    switch ($pago['estado']) {
                        case 'PAGADO': $badgeClass = 'badge-pagado'; break;
                        case 'PENDIENTE': $badgeClass = 'badge-pendiente'; break;
                        case 'NO_PAGADO': $badgeClass = 'badge-no-pagado'; break;
                        case 'RECHAZADO': $badgeClass = 'badge-rechazado'; break;
                        default: $badgeClass = 'badge-no-pagado';
                    }
                    $fechaEnvio = $pago['fecha_envio'] ? date('d/m/Y H:i', strtotime($pago['fecha_envio'])) : '';
                ?>
                <div class="historial-card" data-anio="<?= esc($pago['anio']) ?>">
                    <?php if (!empty($pago['captura'])): ?>
                    <img src="<?= base_url($pago['captura']) ?>" alt="Comprobante" class="historial-thumb"
                         onclick="verComprobantePublico('<?= base_url($pago['captura']) ?>', '<?= esc($pago['mes_nombre']) ?> <?= esc($pago['anio']) ?>')">
                    <?php else: ?>
                    <div class="historial-thumb-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                    <?php endif; ?>

                    <div class="historial-info">
                        <div class="mes-text"><?= esc($pago['mes_nombre']) ?> <?= esc($pago['anio']) ?></div>
                        <?php if ($fechaEnvio): ?>
                        <div class="fecha-text"><i class="bi bi-clock"></i> <?= $fechaEnvio ?></div>
                        <?php endif; ?>
                        <?php if (!empty($pago['observacion'])): ?>
                        <div class="historial-observacion"><i class="bi bi-exclamation-circle"></i> <?= esc($pago['observacion']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="historial-right">
                        <div class="monto-text">S/ <?= number_format($pago['monto'], 2) ?></div>
                        <span class="badge-estado <?= $badgeClass ?>" style="font-size:0.65rem; padding:3px 8px;">
                            <?= esc($pago['estado']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer-text">
            Litio Payment Control &copy; <?= date('Y') ?>
        </div>
    </div>

    <!-- Modal Subir Comprobante -->
    <div class="modal fade" id="modalPago" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-cloud-arrow-up"></i> Subir Comprobante
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <span class="badge bg-danger" id="modalMesLabel" style="font-size:0.9rem; padding:8px 16px;"></span>
                    </div>

                    <div class="upload-area" id="uploadAreaModal">
                        <i class="bi bi-cloud-arrow-up-fill d-block"></i>
                        <h6>Selecciona tu comprobante</h6>
                        <p class="text-muted mb-0" style="font-size:0.8rem;">Arrastra una imagen o haz clic</p>
                        <p class="text-muted" style="font-size:0.75rem;">JPG, PNG, WEBP - Max 5MB</p>
                        <input type="file" id="fileInputModal" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                    </div>

                    <img id="previewImageModal" class="preview-img" alt="Vista previa">

                    <div id="uploadBtnContainerModal" style="display:none;" class="mt-3">
                        <button class="btn-send" id="btnSubirModal" onclick="subirComprobanteModal()">
                            <i class="bi bi-upload"></i> Enviar Comprobante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Comprobante (Historial) -->
    <div class="modal fade" id="modalVerComprobante" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloComprobanteModal">
                        <i class="bi bi-image"></i> Comprobante
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenComprobanteModal" src="" alt="Comprobante" class="comprobante-preview-modal">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var BASE_URL = '<?= base_url() ?>';
        var TOKEN = '<?= esc($token) ?>';
        var ID_USUARIO = <?= $usuario['id'] ?>;
        var CSRF_TOKEN = '<?= csrf_hash() ?>';

        $.ajaxSetup({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);
            }
        });

        var modalPagoId = 0;
        var modalMes = 0;
        var modalAnio = 0;
        var archivoSeleccionado = null;

        function abrirModalPago(pagoId, mes, anio, mesNombre) {
            modalPagoId = pagoId;
            modalMes = mes;
            modalAnio = anio;
            archivoSeleccionado = null;

            $('#modalMesLabel').text(mesNombre + ' ' + anio);
            $('#previewImageModal').hide().attr('src', '');
            $('#uploadBtnContainerModal').hide();
            $('#fileInputModal').val('');

            $('#modalPago').modal('show');
        }

        function verComprobantePublico(url, titulo) {
            $('#tituloComprobanteModal').html('<i class="bi bi-image"></i> ' + titulo);
            $('#imagenComprobanteModal').attr('src', url);
            $('#modalVerComprobante').modal('show');
        }

        $(document).ready(function() {
            var uploadArea = document.getElementById('uploadAreaModal');
            var fileInput = document.getElementById('fileInputModal');

            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    seleccionarArchivoModal(e.target.files[0]);
                }
            });

            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    seleccionarArchivoModal(e.dataTransfer.files[0]);
                }
            });
        });

        function seleccionarArchivoModal(file) {
            var extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
            var ext = file.name.split('.').pop().toLowerCase();

            if (!extPermitidas.includes(ext)) {
                Swal.fire('Error', 'Solo se aceptan JPG, JPEG, PNG y WEBP', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('Error', 'Maximo 5MB. Tu archivo pesa ' + formatSize(file.size), 'error');
                return;
            }

            var mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
            if (!mimePermitidos.includes(file.type)) {
                Swal.fire('Error', 'Tipo de archivo no valido', 'error');
                return;
            }

            archivoSeleccionado = file;

            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImageModal').attr('src', e.target.result).fadeIn();
            };
            reader.readAsDataURL(file);

            $('#uploadBtnContainerModal').fadeIn();
        }

        function subirComprobanteModal() {
            if (!archivoSeleccionado) {
                Swal.fire('Error', 'Selecciona una imagen primero', 'error');
                return;
            }

            Swal.fire({
                title: 'Enviar comprobante',
                html: 'Enviar para <strong>' + $('#modalMesLabel').text() + '</strong>?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4669FA',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, enviar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append('comprobante', archivoSeleccionado);
                    formData.append('id_usuario', ID_USUARIO);
                    formData.append('mes', modalMes);
                    formData.append('anio', modalAnio);
                    formData.append('token', TOKEN);
                    formData.append('csrf_token', CSRF_TOKEN);

                    $.ajax({
                        url: BASE_URL + '/api/subir-comprobante',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        beforeSend: function() {
                            $('#btnSubirModal')
                                .html('<i class="bi bi-hourglass-split"></i> Enviando...')
                                .prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#modalPago').modal('hide');

                                var card = $('#mesCard' + modalPagoId);
                                card.find('.badge-estado')
                                    .removeClass('badge-no-pagado badge-rechazado')
                                    .addClass('badge-pendiente')
                                    .text('PENDIENTE');

                                card.find('.btn-upload-mes').remove();

                                card.find('.mes-card-footer').append(
                                    '<span style="font-size:0.8rem; color:#ffc107;"><i class="bi bi-clock"></i> Enviado</span>'
                                );

                                if (response.data && response.data.imagen) {
                                    var imgHtml = '<div style="margin-bottom:8px;"><img src="' + BASE_URL + '/' + response.data.imagen + '" alt="Comprobante" style="max-width:100%; max-height:120px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); cursor:pointer;" onclick="verComprobantePublico(\'' + BASE_URL + '/' + response.data.imagen + '\', \'' + $('#modalMesLabel').text() + '\')"></div>';
                                    card.find('.mes-card-footer').before(imgHtml);
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Comprobante enviado',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false,
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                            $('#btnSubirModal')
                                .html('<i class="bi bi-upload"></i> Enviar Comprobante')
                                .prop('disabled', false);
                        },
                        error: function() {
                            Swal.fire('Error', 'Error de conexion. Intenta de nuevo.', 'error');
                            $('#btnSubirModal')
                                .html('<i class="bi bi-upload"></i> Enviar Comprobante')
                                .prop('disabled', false);
                        }
                    });
                }
            });
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // =====================================================
        // FILTRO DE AÑO EN HISTORIAL
        // =====================================================
        $(document).ready(function() {
            var anios = {};
            $('#listaHistorial .historial-card').each(function() {
                var anio = $(this).data('anio');
                if (anio) anios[anio] = true;
            });

            var select = $('#filtroAnioHistorial');
            Object.keys(anios).sort().reverse().forEach(function(a) {
                select.append('<option value="' + a + '">' + a + '</option>');
            });

            var anioActual = new Date().getFullYear();
            if (anios[anioActual]) {
                select.val(anioActual);
                filtrarHistorial();
            }

            select.on('change', filtrarHistorial);
        });

        function filtrarHistorial() {
            var anio = $('#filtroAnioHistorial').val();
            $('#listaHistorial .historial-card').each(function() {
                if (!anio || $(this).data('anio') == anio) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    </script>
</body>
</html>
