$(document).ready(function() {
    cargarMarcadores();
});

function cargarMarcadores() {
    showLoading();
    $.ajax({
        url: BASE_URL + 'recordatorio/listar?tipo=marcador',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var lista = $('#listaMarcadores');
            lista.empty();

            if (!data || data.length === 0) {
                $('#sinMarcadores').show();
                hideLoading();
                return;
            }

            $('#sinMarcadores').hide();

            data.forEach(function(m) {
                var fecha = m.created_at ? formatearFecha(m.created_at) : '';
                var card = '<div class="marcador-item">' +
                    '<div class="marc-cuerpo" style="flex:1;min-width:0;">' +
                    '<div class="marc-titulo">' + escHtml(m.titulo) + '</div>' +
                    (m.descripcion ? '<div class="marc-contenido">' + escHtml(m.descripcion) + '</div>' : '') +
                    '<div class="marc-fecha"><i class="bi bi-clock"></i> ' + fecha + '</div>' +
                    '</div>' +
                    '<div class="marcador-acciones">' +
                    '<button class="btn-sm-icon" onclick="eliminarMarcador(' + m.id + ')" title="Eliminar" style="color:var(--danger);"><i class="bi bi-trash"></i></button>' +
                    '</div>' +
                    '</div>';
                lista.append(card);
            });
            hideLoading();
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error al cargar marcadores.', 'error');
        }
    });
}

function eliminarMarcador(id) {
    Swal.fire({
        title: 'Eliminar marcador',
        text: 'Se eliminara este marcador.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(function(result) {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + 'recordatorio/eliminar/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
                        cargarMarcadores();
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
    });
}

function formatearFecha(f) {
    if (!f) return '';
    var d = new Date(f.replace(' ', 'T'));
    if (isNaN(d.getTime())) return f.slice(0, 10);
    var dd = ('0' + d.getDate()).slice(-2), mm = ('0' + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
    return dd + '/' + mm + '/' + yy;
}

function escHtml(s) {
    if (!s) return '';
    return $('<div>').text(s).html();
}
