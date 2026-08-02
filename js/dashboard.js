var tablaPagos = null;

var meses = {
    1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
    5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
    9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre',
};

$(document).ready(function() {
    cargarFiltros();
    cargarEstadisticas();
    cargarPagos();
});

// =====================================================
// CARGAR FILTROS
// =====================================================

function cargarFiltros() {
    $.ajax({
        url: BASE_URL + '/api/listar-usuarios',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var select = $('#filtroUsuario');
                response.data.forEach(function(u) {
                    select.append('<option value="' + u.id + '">' + escapeHtml(u.nombre) + '</option>');
                });
            }
        }
    });

    $.ajax({
        url: BASE_URL + '/api/meses-registros',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var selectMes = $('#filtroMes');
                var aniosUnicos = {};

                response.data.forEach(function(m) {
                    selectMes.append('<option value="' + m.mes + '" data-anio="' + m.anio + '">' + escapeHtml(m.mes_nombre) + '</option>');
                    aniosUnicos[m.anio] = true;
                });

                var selectAnio = $('#filtroAnio');
                Object.keys(aniosUnicos).sort().reverse().forEach(function(anio) {
                    selectAnio.append('<option value="' + anio + '">' + anio + '</option>');
                });

                var anioActual = new Date().getFullYear();
                selectAnio.val(anioActual);
            }
        }
    });
}

function getFiltros() {
    var data = {};
    var usuario = $('#filtroUsuario').val();
    var mesOption = $('#filtroMes').find(':selected');
    var anio = $('#filtroAnio').val();
    var estado = $('#filtroEstado').val();

    if (usuario) data.id_usuario = usuario;
    if (mesOption.val()) {
        data.mes = mesOption.val();
        data.anio = mesOption.data('anio');
    } else if (anio) {
        data.anio = anio;
    }
    if (estado) data.estado = estado;
    return data;
}

function aplicarFiltros() {
    var partes = [];
    if ($('#filtroUsuario').val()) partes.push($('#filtroUsuario').find(':selected').text());
    if ($('#filtroAnio').val()) partes.push($('#filtroAnio').val());
    if ($('#filtroMes').val()) partes.push($('#filtroMes').find(':selected').text());
    if ($('#filtroEstado').val()) partes.push($('#filtroEstado').find(':selected').text());

    if (partes.length > 0) {
        $('#tituloTabla').html('<i class="bi bi-funnel-fill"></i> ' + escapeHtml(partes.join(' - ')) + ' - Pagos');
    } else {
        $('#tituloTabla').html('<i class="bi bi-calendar-month"></i> Todos los registros - Pagos');
    }

    cargarEstadisticas();
    cargarPagos();
}

function limpiarFiltros() {
    $('#filtroUsuario').val('');
    $('#filtroMes').val('');
    $('#filtroAnio').val(new Date().getFullYear());
    $('#filtroEstado').val('');
    $('#tituloTabla').html('<i class="bi bi-calendar-month"></i> Todos los registros - Pagos');
    cargarEstadisticas();
    cargarPagos();
}

// =====================================================
// CARGAR ESTADISTICAS
// =====================================================

function cargarEstadisticas() {
    $.ajax({
        url: BASE_URL + '/api/estadisticas',
        type: 'POST',
        data: getFiltros(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var d = response.data;
                $('#statTotalUsuarios').text(d.totalUsuarios);
                $('#statPagaronMes').text(d.pagaronMes);
                $('#statPendientes').text(d.pendientes);
                $('#statConDeuda').text(d.conDeuda);
                $('#statRecaudadoMes').text('S/ ' + d.recaudadoMes);
                $('#statDeuda').text('S/ ' + d.deuda);
            }
        }
    });
}

// =====================================================
// CARGAR PAGOS EN DATATABLE
// =====================================================

function cargarPagos() {
    $.ajax({
        url: BASE_URL + '/api/listar-pagos',
        type: 'POST',
        data: getFiltros(),
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

                datos.forEach(function(pago) {
                    var mesNombre = meses[pago.mes] || '';
                    var estadoHtml = estados[pago.estado] || '';
                    var fechaEnvio = pago.fecha_envio
                        ? new Date(pago.fecha_envio).toLocaleDateString('es-PE')
                        : '-';

                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(pago.nombre) + '</strong></td>';
                    html += '<td>' + escapeHtml(pago.telefono || '-') + '</td>';
                    html += '<td>' + mesNombre + '</td>';
                    html += '<td>' + pago.anio + '</td>';
                    html += '<td>S/ ' + parseFloat(pago.monto).toFixed(2) + '</td>';
                    html += '<td>' + estadoHtml + '</td>';
                    html += '<td>' + fechaEnvio + '</td>';
                    html += '<td class="text-center">';
                    html += '<div class="d-flex gap-1 justify-content-center flex-wrap">';

                    if (pago.captura) {
                        html += '<button class="btn btn-sm btn-outline-info btn-sm-icon" onclick="verComprobante(' + pago.id + ')" title="Ver Comprobante"><i class="bi bi-image"></i></button>';
                    }

                    if (pago.estado === 'PENDIENTE') {
                        html += '<button class="btn btn-sm btn-outline-success btn-sm-icon" onclick="aprobarPago(' + pago.id + ')" title="Aprobar"><i class="bi bi-check-lg"></i></button>';
                        html += '<button class="btn btn-sm btn-outline-warning btn-sm-icon" onclick="abrirRechazar(' + pago.id + ', \'' + escapeHtml(pago.nombre) + '\', \'' + mesNombre + '\', ' + pago.anio + ')" title="Rechazar"><i class="bi bi-x-lg"></i></button>';
                    }

                    html += '<button class="btn btn-sm btn-outline-primary btn-sm-icon" onclick="verHistorial(' + pago.id_usuario + ')" title="Historial"><i class="bi bi-clock-history"></i></button>';
                    html += '<button class="btn btn-sm btn-outline-danger btn-sm-icon" onclick="eliminarPago(' + pago.id + ')" title="Eliminar"><i class="bi bi-trash"></i></button>';

                    html += '</div>';
                    html += '</td>';
                    html += '</tr>';
                });

                if ($.fn.DataTable.isDataTable('#tablaPagos')) {
                    $('#tablaPagos').DataTable().destroy();
                }

                $('#tbodyPagos').html(html);

                tablaPagos = $('#tablaPagos').DataTable({
                    language: {
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
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

// =====================================================
// VER COMPROBANTE
// =====================================================

function verComprobante(idPago) {
    $.ajax({
        url: BASE_URL + '/api/ver-comprobante/' + idPago,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                $('#comprobanteUsuario').text(data.nombre);
                $('#comprobanteMes').text(data.mes + ' ' + data.anio + ' - ' + data.estado);
                $('#comprobanteImagen').attr('src', data.imagen);

                if (data.observacion) {
                    $('#comprobanteObservacion')
                        .html('<i class="bi bi-exclamation-triangle"></i> Motivo rechazo: ' + escapeHtml(data.observacion))
                        .show();
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

// =====================================================
// APROBAR PAGO
// =====================================================

function aprobarPago(idPago) {
    Swal.fire({
        title: 'Aprobar pago',
        text: '¿Estas seguro de aprobar este pago?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, aprobar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + '/api/aprobar-pago/' + idPago,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire('Aprobado', response.message, 'success');
                        cargarPagos();
                        cargarEstadisticas();
                        verificarNotificaciones();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Error', 'Error de conexion', 'error');
                }
            });
        }
    });
}

// =====================================================
// RECHAZAR PAGO
// =====================================================

var rechazarId = 0;

function abrirRechazar(idPago, nombre, mes, anio) {
    rechazarId = idPago;
    $('#textoRechazar').html('Rechazar pago de <strong>' + escapeHtml(nombre) + '</strong> - ' + mes + ' ' + anio);
    $('#observacionRechazo').val('');
    $('#modalRechazar').modal('show');
}

function confirmarRechazo() {
    var observacion = $('#observacionRechazo').val().trim();

    showLoading();
    $.ajax({
        url: BASE_URL + '/api/rechazar-pago/' + rechazarId,
        type: 'POST',
        data: { observacion: observacion },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                $('#modalRechazar').modal('hide');
                Swal.fire('Rechazado', response.message, 'warning');
                cargarPagos();
                cargarEstadisticas();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            Swal.fire('Error', 'Error de conexion', 'error');
        }
    });
}

// =====================================================
// ELIMINAR PAGO
// =====================================================

function eliminarPago(idPago) {
    Swal.fire({
        title: 'Eliminar pago',
        text: '¿Estas seguro de eliminar este registro?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4669FA',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: BASE_URL + '/api/eliminar-pago/' + idPago,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        cargarPagos();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Error', 'Error de conexion', 'error');
                }
            });
        }
    });
}

// =====================================================
// VER HISTORIAL (redirigir)
// =====================================================

function verHistorial(idUsuario) {
    window.location.href = BASE_URL + '/historial/' + idUsuario;
}

// =====================================================
// EXPORTAR EXCEL
// =====================================================

function exportarExcel() {
    $.ajax({
        url: BASE_URL + '/api/exportar-excel',
        type: 'POST',
        data: getFiltros(),
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                var ws = XLSX.utils.json_to_sheet(response.data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Pagos");

                ws['!cols'] = [
                    { wch: 20 },
                    { wch: 15 },
                    { wch: 15 },
                    { wch: 8 },
                    { wch: 12 },
                    { wch: 15 },
                    { wch: 20 },
                ];

                XLSX.writeFile(wb, "pagos_litio_" + new Date().toISOString().slice(0,10) + ".xlsx");
                Swal.fire('Exito', 'Archivo Excel generado correctamente', 'success');
            } else {
                Swal.fire('Info', 'No hay datos para exportar', 'info');
            }
        }
    });
}

// =====================================================
// EXPORTAR PDF
// =====================================================

function exportarPdf() {
    $.ajax({
        url: BASE_URL + '/api/exportar-pdf',
        type: 'POST',
        data: getFiltros(),
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const { jsPDF } = window.jspdf;
                var doc = new jsPDF();

                doc.setFontSize(18);
                doc.setTextColor(229, 9, 20);
                doc.text("Reporte de Pagos - Litio", 14, 20);

                doc.setFontSize(10);
                doc.setTextColor(128, 128, 128);
                doc.text("Generado: " + new Date().toLocaleDateString('es-PE') + " " + new Date().toLocaleTimeString('es-PE'), 14, 28);

                var columns = [
                    { header: 'Usuario', dataKey: 'Usuario' },
                    { header: 'Mes', dataKey: 'Mes' },
                    { header: 'Anio', dataKey: 'Anio' },
                    { header: 'Monto', dataKey: 'Monto' },
                    { header: 'Estado', dataKey: 'Estado' },
                ];

                doc.autoTable({
                    columns: columns,
                    body: response.data,
                    startY: 35,
                    styles: { fontSize: 9, cellPadding: 3 },
                    headStyles: { fillColor: [229, 9, 20], textColor: [255, 255, 255] },
                    alternateRowStyles: { fillColor: [245, 245, 245] },
                    columnStyles: {
                        0: { cellWidth: 40 },
                        2: { cellWidth: 20 },
                        3: { cellWidth: 25 },
                        4: { cellWidth: 35 },
                    },
                });

                doc.save("pagos_litio_" + new Date().toISOString().slice(0,10) + ".pdf");
                Swal.fire('Exito', 'Archivo PDF generado correctamente', 'success');
            } else {
                Swal.fire('Info', 'No hay datos para exportar', 'info');
            }
        }
    });
}

// =====================================================
// UTILIDADES
// =====================================================

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
