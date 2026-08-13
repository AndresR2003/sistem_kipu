$(document).ready(function() {
    cargarEstadisticas();
});

function cargarEstadisticas() {
    $.ajax({
        url: BASE_URL + '/api/estadisticas',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var d = response.data;
                $('#statTotalUsuarios').text(d.totalUsuarios);
            }
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
