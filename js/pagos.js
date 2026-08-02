/**
 * Pagos.js - Funciones AJAX para la vista publica del usuario
 * Maneja: subir comprobante, vista previa, drag and drop, validaciones
 */

$(document).ready(function() {
    // Verificar que las variables globales existen
    if (typeof BASE_URL === 'undefined') {
        console.error('BASE_URL no definido');
        return;
    }

    initUploadArea();
});

// =====================================================
// INICIALIZAR AREA DE CARGA
// =====================================================

function initUploadArea() {
    var uploadArea = document.getElementById('uploadArea');
    var fileInput = document.getElementById('fileInput');

    if (!uploadArea || !fileInput) return;

    // Click en el area de carga
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Cambio de archivo
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            seleccionarArchivo(e.target.files[0]);
        }
    });

    // Drag and Drop
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
            seleccionarArchivo(e.dataTransfer.files[0]);
        }
    });
}

// =====================================================
// SELECCIONAR ARCHIVO Y VALIDAR
// =====================================================

var archivoSeleccionado = null;

function seleccionarArchivo(file) {
    // Extensiones permitidas
    var extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    var extension = file.name.split('.').pop().toLowerCase();

    if (!extensionesPermitidas.includes(extension)) {
        Swal.fire({
            icon: 'error',
            title: 'Extension no permitida',
            text: 'Solo se aceptan archivos JPG, JPEG, PNG y WEBP',
        });
        return;
    }

    // Validar tamaño (5MB maximo)
    var tamanoMaximo = 5 * 1024 * 1024;
    if (file.size > tamanoMaximo) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'El tamaño maximo es 5MB. Tu archivo pesa ' + formatSize(file.size),
        });
        return;
    }

    // Validar MIME type
    var mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    if (!mimePermitidos.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Tipo de archivo no valido',
            text: 'El archivo debe ser una imagen valida',
        });
        return;
    }

    archivoSeleccionado = file;

    // Mostrar vista previa
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#previewImage').attr('src', e.target.result).fadeIn();
    };
    reader.readAsDataURL(file);

    // Mostrar boton de envio
    $('#uploadBtnContainer').fadeIn();
}

// =====================================================
// SUBIR COMPROBANTE
// =====================================================

function subirComprobante() {
    if (!archivoSeleccionado) {
        Swal.fire('Error', 'Selecciona una imagen primero', 'error');
        return;
    }

    Swal.fire({
        title: 'Enviar comprobante',
        html: '¿Estas seguro de enviar este comprobante?<br><small class="text-muted">El administrador lo revisara y aprobara</small>',
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
            formData.append('mes', MES_ACTUAL);
            formData.append('anio', ANIO_ACTUAL);
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
                    $('#btnSubir')
                        .html('<i class="bi bi-hourglass-split"></i> Enviando...')
                        .prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        // Ocultar area de carga y mostrar mensaje de exito
                        $('#uploadArea').slideUp();
                        $('#previewImage').slideUp();
                        $('#uploadBtnContainer').slideUp();
                        $('#successMessage').slideDown();

                        Swal.fire({
                            icon: 'success',
                            title: 'Comprobante enviado',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        $('#btnSubir')
                            .html('<i class="bi bi-upload"></i> Enviar Comprobante')
                            .prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Error de conexion. Intenta de nuevo.', 'error');
                    $('#btnSubir')
                        .html('<i class="bi bi-upload"></i> Enviar Comprobante')
                        .prop('disabled', false);
                }
            });
        }
    });
}

// =====================================================
// UTILIDADES
// =====================================================

function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}
