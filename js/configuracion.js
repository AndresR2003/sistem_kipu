$(document).ready(function() {
    cargarColores();

    $('input[type="color"]').each(function() {
        var textId = this.id + '_text';
        var $text = $('#' + textId);
        if ($text.length) {
            $(this).on('input', function() {
                $text.val(this.value);
                actualizarPreview(this.id, this.value);
            });
            $text.on('input', function() {
                var val = this.value;
                var colorInput = $('#' + this.id.replace('_text', ''));
                if (/^#[0-9a-f]{6}$/i.test(val)) {
                    colorInput.val(val);
                }
                actualizarPreview(this.id.replace('_text', ''), val);
            });
        }
    });
});

function actualizarPreview(campo, valor) {
    var shell = document.getElementById('previewShell');
    var cssVar = {
        sidebar_bg: '--pv-sidebar',
        sidebar_text: '--pv-sidebar-text',
        sidebar_active_bg: '--pv-sidebar-active',
        topbar_bg: '--pv-topbar',
        topbar_text: '--pv-topbar-text',
        primary_color: '--pv-primary',
        content_bg: '--pv-body',
        card_bg: '--pv-card',
    }[campo];
    if (shell && cssVar) {
        shell.style.setProperty(cssVar, valor);
    }
}

function cargarColores() {
    $.ajax({
        url: BASE_URL + 'configuracion/obtener',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var campos = ['sidebar_bg', 'sidebar_text', 'sidebar_active_bg', 'topbar_bg', 'topbar_text', 'primary_color', 'content_bg', 'card_bg'];
            campos.forEach(function(c) {
                if (data[c]) {
                    $('#' + c + '_text').val(data[c]);
                    if (/^#[0-9a-f]{6}$/i.test(data[c])) {
                        $('#' + c).val(data[c]);
                    }
                    actualizarPreview(c, data[c]);
                }
            });
        }
    });
}

function guardarColores() {
    var datos = {
        sidebar_bg: $('#sidebar_bg_text').val(),
        sidebar_text: $('#sidebar_text_text').val(),
        sidebar_active_bg: $('#sidebar_active_bg_text').val(),
        topbar_bg: $('#topbar_bg_text').val(),
        topbar_text: $('#topbar_text_text').val(),
        primary_color: $('#primary_color_text').val(),
        content_bg: $('#content_bg_text').val(),
        card_bg: $('#card_bg_text').val(),
    };

    showLoading();
    $.ajax({
        url: BASE_URL + 'configuracion/guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false,
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion.', 'error');
        }
    });
}

function restaurarColores() {
    Swal.fire({
        title: 'Restaurar colores',
        text: 'Se restableceran los colores por defecto.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4669FA',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, restaurar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (result.isConfirmed) {
            var defaults = {
                sidebar_bg: '#13131f',
                sidebar_text: 'rgba(255,255,255,0.55)',
                sidebar_active_bg: '#4669FA',
                topbar_bg: 'rgba(15,15,26,0.92)',
                topbar_text: '#e2e8f0',
                primary_color: '#4669FA',
                content_bg: '#0f0f1a',
                card_bg: '#1a1a2e',
            };
            Object.keys(defaults).forEach(function(c) {
                var val = defaults[c];
                if (/^#[0-9a-f]{6}$/i.test(val)) {
                    $('#' + c).val(val);
                }
                $('#' + c + '_text').val(val);
                actualizarPreview(c, val);
            });
            guardarColores();
        }
    });
}
