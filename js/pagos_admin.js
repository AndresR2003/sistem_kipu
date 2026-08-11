var tablaPagosAdmin;

$(document).ready(function() {
    cargarPagosAdmin();
});

function cargarPagosAdmin() {
    $.ajax({
        url: BASE_URL + '/api/listar-pagos',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var datos = response.data;
                var html = '';
                var estados = {
                    'PAGADO': '<span class="badge-estado badge-pagado"><i class="bi bi-check-circle"></i> Pagado</span>',
                    'PENDIENTE': '<span class="badge-estado badge-pendiente"><i class="bi bi-clock"></i> Pendiente</span>',
                    'NO_PAGADO': '<span class="badge-estado badge-no-pagado"><i class="bi bi-x-circle"></i> No Pagado</span>',
                    'RECHAZADO': '<span class="badge-estado badge-rechazado"><i class="bi bi-exclamation-circle"></i> Rechazado</span>',
                };
                var meses = {
                    1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
                    5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
                    9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre',
                };

                datos.forEach(function(pago) {
                    var mesNombre = meses[pago.mes] || '';
                    var estadoHtml = estados[pago.estado] || '';
                    var fechaEnvio = pago.fecha_envio || '-';

                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(pago.nombre) + '</strong></td>';
                    html += '<td>' + escapeHtml(pago.telefono || '-') + '</td>';
                    html += '<td>' + mesNombre + '</td>';
                    html += '<td>' + pago.anio + '</td>';
                    html += '<td>S/ ' + parseFloat(pago.monto).toFixed(2) + '</td>';
                    html += '<td>' + estadoHtml + '</td>';
                    html += '<td>' + fechaEnvio + '</td>';
                    html += '<td class="text-center">';
                    html += '<div class="d-flex gap-1 justify-content-center">';
                    if (pago.captura) {
                        html += '<button class="btn btn-sm btn-outline-info btn-sm-icon" onclick="verComprobante(' + pago.id + ')" title="Ver Comprobante"><i class="bi bi-image"></i></button>';
                    }
                    if (pago.estado === 'PENDIENTE') {
                        html += '<button class="btn btn-sm btn-outline-success btn-sm-icon" onclick="aprobarPago(' + pago.id + ')" title="Aprobar"><i class="bi bi-check-lg"></i></button>';
                        html += '<button class="btn btn-sm btn-outline-warning btn-sm-icon" onclick="rechazarPago(' + pago.id + ', \'' + escapeHtml(pago.nombre) + '\', \'' + mesNombre + '\', ' + pago.anio + ')" title="Rechazar"><i class="bi bi-x-lg"></i></button>';
                    }
                    html += '<button class="btn btn-sm btn-outline-primary btn-sm-icon" onclick="verHistorialModal(' + pago.id_usuario + ', \'' + escapeHtml(pago.nombre) + '\')" title="Historial"><i class="bi bi-clock-history"></i></button>';
                    html += '<button class="btn btn-sm btn-outline-danger btn-sm-icon" onclick="eliminarPago(' + pago.id + ')" title="Eliminar"><i class="bi bi-trash"></i></button>';
                    html += '</div>';
                    html += '</td>';
                    html += '</tr>';
                });

                if ($.fn.DataTable.isDataTable('#tablaPagosAdmin')) {
                    $('#tablaPagosAdmin').DataTable().destroy();
                }
                $('#tbodyPagosAdmin').html(html);
                tablaPagosAdmin = $('#tablaPagosAdmin').DataTable({
                    language: {
                        search: "Buscar:", lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ pagos",
                        paginate: { previous: "Anterior", next: "Siguiente" },
                        zeroRecords: "No se encontraron pagos",
                    },
                    pageLength: 10,
                    order: [[5, 'asc'], [0, 'asc']],
                });
            }
        }
    });
}

function verComprobante(idPago) {
    $.ajax({
        url: BASE_URL + '/api/ver-comprobante/' + idPago,
        type: 'POST', dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                $('#comprobanteUsuario').text(data.nombre);
                $('#comprobanteMes').text(data.mes + ' ' + data.anio + ' - ' + data.estado);
                $('#comprobanteImagen').attr('src', data.imagen);
                if (data.observacion) {
                    $('#comprobanteObservacion').html('<i class="bi bi-exclamation-triangle"></i> Motivo rechazo: ' + escapeHtml(data.observacion)).show();
                } else {
                    $('#comprobanteObservacion').hide();
                }
                $('#modalComprobante').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function aprobarPago(idPago) {
    Swal.fire({
        title: 'Aprobar pago', text: '¿Estas seguro de aprobar este pago?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#198754', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, aprobar', cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + '/api/aprobar-pago/' + idPago, type: 'POST', dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) { Swal.fire('Aprobado', response.message, 'success'); cargarPagosAdmin(); verificarNotificaciones(); }
                    else { Swal.fire('Error', response.message, 'error'); }
                },
                error: function() { hideLoading(); Swal.fire('Error', 'Error de conexion', 'error'); }
            });
        }
    });
}

function rechazarPago(idPago, nombre, mes, anio) {
    $('#rechazarPagoId').val(idPago);
    $('#textoRechazar').html('Rechazar pago de <strong>' + escapeHtml(nombre) + '</strong> - ' + mes + ' ' + anio);
    $('#observacionRechazo').val('');
    $('#modalRechazar').modal('show');
}

function confirmarRechazo() {
    var idPago = $('#rechazarPagoId').val();
    var observacion = $('#observacionRechazo').val().trim();
    showLoading();
    $.ajax({
        url: BASE_URL + '/api/rechazar-pago/' + idPago, type: 'POST',
        data: { observacion: observacion }, dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) { $('#modalRechazar').modal('hide'); Swal.fire('Rechazado', response.message, 'warning'); cargarPagosAdmin(); }
            else { Swal.fire('Error', response.message, 'error'); }
        },
        error: function() { hideLoading(); Swal.fire('Error', 'Error de conexion', 'error'); }
    });
}

function eliminarPago(idPago) {
    Swal.fire({
        title: 'Eliminar pago', text: '¿Estas seguro de eliminar este registro?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#4669FA', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar', cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + '/api/eliminar-pago/' + idPago, type: 'POST', dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) { Swal.fire('Eliminado', response.message, 'success'); cargarPagosAdmin(); }
                    else { Swal.fire('Error', response.message, 'error'); }
                },
                error: function() { hideLoading(); Swal.fire('Error', 'Error de conexion', 'error'); }
            });
        }
    });
}

function verHistorialModal(idUsuario, nombre) {
    $.ajax({
        url: BASE_URL + '/api/historial-usuario/' + idUsuario, type: 'POST', dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                var html = '';
                var estados = { 'PAGADO': '<span class="badge-estado badge-pagado">Pagado</span>', 'PENDIENTE': '<span class="badge-estado badge-pendiente">Pendiente</span>', 'NO_PAGADO': '<span class="badge-estado badge-no-pagado">No Pagado</span>', 'RECHAZADO': '<span class="badge-estado badge-rechazado">Rechazado</span>' };
                data.historial.forEach(function(item) {
                    html += '<tr><td>' + item.mes_nombre + '</td><td>' + item.anio + '</td><td>S/ ' + parseFloat(item.monto).toFixed(2) + '</td><td>' + (estados[item.estado] || '') + '</td><td>' + (item.fecha_envio || '-') + '</td></tr>';
                });
                $('#historialTitulo').text('Historial de ' + nombre);
                $('#tbodyHistorial').html(html);
                $('#historialTotal').text('S/ ' + parseFloat(data.totalAdeudado).toFixed(2));
                $('#modalHistorial').modal('show');
            }
        }
    });
}

function exportarExcel() {
    $.ajax({
        url: BASE_URL + '/api/exportar-excel', type: 'POST', dataType: 'json',
        success: function(response) {
            if (response.success) {
                var ws = XLSX.utils.json_to_sheet(response.data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Pagos");
                XLSX.writeFile(wb, "pagos_Kipucloud.xlsx");
                Swal.fire('Exito', 'Archivo Excel generado', 'success');
            }
        }
    });
}

function exportarPdf() {
    $.ajax({
        url: BASE_URL + '/api/exportar-pdf', type: 'POST', dataType: 'json',
        success: function(response) {
            if (response.success) {
                const { jsPDF } = window.jspdf;
                var doc = new jsPDF();
                doc.setFontSize(18); doc.text("Reporte de Pagos - Kipucloud", 14, 22);
                doc.setFontSize(11); doc.text("Fecha: " + new Date().toLocaleDateString('es-PE'), 14, 30);
                var columns = [{ header: 'Usuario', dataKey: 'Usuario' }, { header: 'Mes', dataKey: 'Mes' }, { header: 'Anio', dataKey: 'Anio' }, { header: 'Monto', dataKey: 'Monto' }, { header: 'Estado', dataKey: 'Estado' }];
                doc.autoTable({ columns: columns, body: response.data, startY: 35, styles: { fontSize: 9 }, headStyles: { fillColor: [229, 9, 20] } });
                doc.save("pagos_Kipucloud.pdf");
                Swal.fire('Exito', 'Archivo PDF generado', 'success');
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
