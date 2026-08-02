$(document).ready(function() {
    cargarPerfil();
});

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
            $('#perfilRol').val(p.rol === 'superadmin' ? 'Superadmin' : 'Admin');

            if (p.foto) {
                $('#fotoPerfil').attr('src', BASE_URL + p.foto);
            } else {
                $('#fotoPerfil').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(p.nombre) + '&background=4669FA&color=fff&size=120');
            }
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

function guardarPerfil() {
    var datos = {
        nombre: $('#perfilNombre').val().trim(),
        email: $('#perfilEmail').val().trim(),
    };

    var pass = $('#perfilPassword').val();
    if (pass) {
        datos.password = pass;
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
