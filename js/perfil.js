$(document).ready(function() {
    cargarPerfil();
});

function cambiarTabPerfil(nombre, btn) {
    $('.perfil-tab').removeClass('active');
    $(btn).addClass('active');
    $('.perfil-pane').removeClass('active');
    $('#pane' + nombre.charAt(0).toUpperCase() + nombre.slice(1)).addClass('active');
}

function cargarPerfil() {
    showLoading();
    $.ajax({
        url: BASE_URL + 'perfil/obtener',
        type: 'GET',
        dataType: 'json',
        success: function(p) {
            hideLoading();
            $('#perfilUsername').val(p.username);
            $('#perfilNombre').val(p.nombre);
            $('#perfilEmail').val(p.email);
            $('#perfilTelefono').val(p.telefono || '');
            $('#perfilPuesto').val(p.puesto || '');
            $('#perfilFechaNac').val(p.fecha_nacimiento || '');
            $('#perfilFechaCont').val(p.fecha_contratacion || '');
            $('#perfilIdioma').val(p.idioma || 'es');
            $('#perfilPassword').val('');
            $('#perfilPassword2').val('');

            // Cabecera
            $('#perfilNombreCabecera').text(p.nombre);
            $('#perfilEmailCabecera').text(p.email || '');

            var rol = p.rol === 'superadmin' ? 'Superadmin' : (p.rol === 'admin' ? 'Admin' : p.rol || '');
            $('#perfilRolCabecera').text(rol || 'Empleado');

            // Foto
            var inicial = p.nombre && p.nombre.charAt(0) ? p.nombre.charAt(0).toUpperCase() : 'A';
            if (p.foto) {
                $('#fotoPerfil').attr('src', BASE_URL + p.foto);
            } else {
                $('#fotoPerfil').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(p.nombre) + '&background=4669FA&color=fff&size=96');
            }

            // Preferencias de notificacion
            var prefs = {};
            try { prefs = JSON.parse(p.preferencias_notificacion || '{}'); } catch(e) { prefs = {}; }
            $('#notifEmail').prop('checked', prefs.email !== false);
            $('#notifAnuncios').prop('checked', prefs.anuncios !== false);
            $('#notifMensajes').prop('checked', prefs.mensajes !== false);
            $('#notifEventos').prop('checked', prefs.eventos !== false);
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error al cargar perfil.', 'error');
        }
    });
}

function subirFoto(input) {
    if (!input.files || !input.files[0]) return;

    var formData = new FormData();
    formData.append('foto', input.files[0]);

    showLoading();
    $.ajax({
        url: BASE_URL + 'perfil/subir-foto',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                $('#fotoPerfil').attr('src', response.foto + '?t=' + Date.now());
                $('.avatar img').attr('src', response.foto + '?t=' + Date.now());
                Swal.fire({ icon: 'success', title: 'Foto actualizada', timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error al subir la foto.', 'error');
        }
    });
}

function guardarPerfil(seccion) {
    var datos = {};

    if (seccion === 'datos') {
        datos.nombre = $('#perfilNombre').val().trim();
        datos.email = $('#perfilEmail').val().trim();
        datos.telefono = $('#perfilTelefono').val().trim();
        datos.puesto = $('#perfilPuesto').val().trim();
        datos.fecha_nacimiento = $('#perfilFechaNac').val();
        datos.fecha_contratacion = $('#perfilFechaCont').val();

        if (!datos.nombre) {
            Swal.fire('Validacion', 'El nombre es obligatorio.', 'warning');
            return;
        }
        if (!datos.email) {
            Swal.fire('Validacion', 'El email es obligatorio.', 'warning');
            return;
        }
    } else if (seccion === 'seguridad') {
        var pass = $('#perfilPassword').val();
        var pass2 = $('#perfilPassword2').val();
        if (!pass) {
            Swal.fire('Validacion', 'Escribe la nueva contraseña.', 'warning');
            return;
        }
        if (pass.length < 8) {
            Swal.fire('Validacion', 'La contraseña debe tener al menos 8 caracteres.', 'warning');
            return;
        }
        if (pass !== pass2) {
            Swal.fire('Validacion', 'Las contraseñas no coinciden.', 'warning');
            return;
        }
        datos.password = pass;
    } else if (seccion === 'notificaciones') {
        datos.preferencias_notificacion = {
            email: $('#notifEmail').is(':checked'),
            anuncios: $('#notifAnuncios').is(':checked'),
            mensajes: $('#notifMensajes').is(':checked'),
            eventos: $('#notifEventos').is(':checked')
        };
    } else if (seccion === 'preferencias') {
        datos.idioma = $('#perfilIdioma').val();
    }

    showLoading();
    $.ajax({
        url: BASE_URL + 'perfil/guardar',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                Swal.fire({ icon: 'success', title: 'Perfil actualizado', timer: 1500, showConfirmButton: false });
                if (seccion === 'seguridad') {
                    $('#perfilPassword').val('');
                    $('#perfilPassword2').val('');
                } else if (seccion === 'datos') {
                    $('#perfilNombreCabecera').text($('#perfilNombre').val().trim());
                    $('#perfilEmailCabecera').text($('#perfilEmail').val().trim());
                }
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
