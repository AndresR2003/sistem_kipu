/* Helpers reutilizables para adjuntos en comentarios (PDF, Word, imágenes). */

(function () {
    var css = '.com-adjuntos{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;}' +
        '.com-adjunto{border:1px solid var(--border,#d0d0d0);border-radius:6px;background:var(--bg-input,#f8f9fa);}' +
        '.com-adjunto-img{padding:3px;width:120px;height:120px;overflow:hidden;display:flex;align-items:center;justify-content:center;cursor:pointer;}' +
        '.com-adjunto-img img{max-width:100%;max-height:100%;border-radius:4px;object-fit:cover;display:block;}' +
        '.com-adjunto-img img:hover{opacity:.85;}' +
        '.com-adjunto-doc{display:flex;align-items:center;gap:8px;padding:6px 10px;max-width:260px;}' +
        '.com-adjunto-icono{font-size:1.3rem;color:#e23636;display:inline-flex;align-items:center;}' +
        '.com-adjunto-nombre{font-size:0.78rem;color:var(--primary,#0d6efd);text-decoration:none;font-weight:600;word-break:break-word;}' +
        '.com-adjunto-nombre:hover{text-decoration:underline;}' +
        '.com-adjunto-meta{font-size:0.7rem;color:var(--text-muted,#888);white-space:nowrap;}' +
        '.com-adjuntar-btn{display:inline-flex;align-items:center;gap:5px;background:transparent;border:1px solid var(--border,#d0d0d0);border-radius:var(--radius,6px);padding:6px 10px;font-size:0.72rem;color:var(--text,#333);cursor:pointer;transition:all .15s;white-space:nowrap;}' +
        '.com-adjuntar-btn:hover{background:var(--bg-hover,#e9ecef);border-color:var(--primary,#0d6efd);color:var(--primary,#0d6efd);}' +
        '.com-adjuntar-btn:disabled{opacity:.6;cursor:not-allowed;}' +
        '.comentarios-form .com-adjuntar-btn{align-self:stretch;}' +
        '.comentarios-form{display:flex;align-items:stretch;gap:8px;}' +
        '.comentarios-form textarea{flex:1;}' +
        '.com-adjuntos-form{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;}' +
        '.com-adjunto-preview{position:relative;display:flex;align-items:center;gap:8px;border:1px solid var(--border,#d0d0d0);border-radius:6px;padding:5px 8px;background:var(--bg-input,#f8f9fa);}' +
        '.com-adjunto-preview .bi-x-circle{position:absolute;top:-7px;right:-7px;background:#dc3545;color:#fff;border-radius:50%;font-size:0.9rem;cursor:pointer;}' +
        '.com-adjunto-preview img{width:34px;height:34px;object-fit:cover;border-radius:4px;}' +
        '.com-adjunto-preview span{font-size:0.72rem;color:var(--text,#333);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}' +
        '.com-lightbox-img{border-radius:8px;}';
    var style = document.createElement('style');
    style.type = 'text/css';
    if (style.styleSheet) { style.styleSheet.cssText = css; } else { style.appendChild(document.createTextNode(css)); }
    document.head.appendChild(style);
})();

var COM_EXT_IMAGEN = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

function comEscHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, function (ch) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
}

// Renderiza los adjuntos de un comentario (array de metadata).
function comRenderAdjuntos(c) {
    if (!c.archivos || !c.archivos.length) return '';
    var html = '<div class="com-adjuntos">';
    c.archivos.forEach(function (a) {
        var ext = (a.extension || a.nombre.split('.').pop() || '').toLowerCase();
        html += comHtmlAdjunto(a, ext);
    });
    html += '</div>';
    return html;
}

function comHtmlAdjunto(a, ext) {
    if (COM_EXT_IMAGEN.indexOf(ext) !== -1) {
        var url = a.url || BASE_URL + a.ruta;
        var nombre = comEscHtml(a.nombre);
        var nombreJs = nombre.replace(/(['"\\])/g, '\\$1');
        return '<div class="com-adjunto com-adjunto-img">' +
            '<img src="' + url + '" alt="' + nombre + '" onclick="comAbrirImagen(\'' + url + '\', \'' + nombreJs + '\')" title="' + nombre + '">' +
            '</div>';
    }
    var icono = comIconoArchivo(ext);
    return '<div class="com-adjunto com-adjunto-doc">' +
        '<span class="com-adjunto-icono">' + icono + '</span>' +
        '<a class="com-adjunto-nombre" href="' + (a.url || BASE_URL + a.ruta) + '" target="_blank" rel="noopener" download>' + comEscHtml(a.nombre) + '</a>' +
        '<span class="com-adjunto-meta">' + comTamano(a.tamano) + '</span>' +
        '</div>';
}

function comIconoArchivo(ext) {
    if (ext === 'pdf') return '<i class="bi bi-file-earmark-pdf-fill"></i>';
    if (['doc', 'docx'].indexOf(ext) !== -1) return '<i class="bi bi-file-earmark-word-fill"></i>';
    if (['xls', 'xlsx'].indexOf(ext) !== -1) return '<i class="bi bi-file-earmark-excel-fill"></i>';
    return '<i class="bi bi-file-earmark-fill"></i>';
}

function comTamano(bytes) {
    if (!bytes) return '';
    var units = ['B', 'KB', 'MB', 'GB'];
    var i = 0;
    var n = bytes;
    while (n >= 1024 && i < units.length - 1) { n = n / 1024; i++; }
    return n.toFixed(n >= 10 || i === 0 ? 0 : 1) + ' ' + units[i];
}

// Previsualizacion de imagenes seleccionadas (lightbox simple).
function comAbrirImagen(url, nombre) {
    Swal.fire({
        title: nombre || 'Imagen',
        imageUrl: url,
        imageAlt: nombre || 'Imagen',
        width: 700,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: { image: 'com-lightbox-img' }
    });
}

// Convierte un archivo del input en metadata (sin subir, solo preview).
// Prepara el FormData necesario para subirlo.
function comPrepararSubida(input, callback) {
    var files = input.files || [];
    if (!files.length) { callback([]); return; }

    var result = [];
    var pendientes = files.length;
    Array.prototype.forEach.call(files, function (file) {
        var fd = new FormData();
        fd.append('archivo', file);
        $.ajax({
            url: BASE_URL + 'comentarios/subir-archivo',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.success && res.archivo) {
                    result.push(res.archivo);
                    result[result.length - 1].nombreOriginal = file.name;
                } else {
                    Swal.fire('Error', (res.message || 'No se pudo subir un archivo.'), 'warning');
                }
            },
            error: function (xhr) {
                var msg = 'Error de conexion.';
                try { var r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; } catch (e) {}
                Swal.fire('Error', msg, 'warning');
            },
            complete: function () {
                pendientes--;
                if (pendientes === 0) callback(result);
            }
        });
    });
}