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
        '.com-lightbox-img{border-radius:8px;}' +
        '.pub-like{display:inline-flex;align-items:center;gap:6px;background:transparent;border:1px solid var(--border,#d0d0d0);border-radius:var(--radius,6px);padding:6px 10px;font-size:0.72rem;color:var(--text,#333);cursor:pointer;transition:all .15s;white-space:nowrap;}' +
        '.pub-like:hover{background:var(--bg-hover,#e9ecef);border-color:var(--primary,#0d6efd);color:var(--primary,#0d6efd);}' +
        '.pub-like.activo{background:rgba(13,110,253,.1);border-color:var(--primary,#0d6efd);color:var(--primary,#0d6efd);}' +
        '.pub-like .lbl{display:none;}' +
        '.com-like{display:inline-flex;align-items:center;gap:5px;background:transparent;border:none;color:var(--text-muted,#888);font-size:0.72rem;cursor:pointer;padding:2px 4px;border-radius:4px;transition:all .15s;}' +
        '.com-like:hover{color:#0dcaf0;background:rgba(13,202,240,.08);}' +
        '.com-like.activo{color:#0d6efd;}' +
        '.com-like .lbl{display:none;}' +
        '.com-like-count{font-weight:700;}' +
        '.comentario-acciones{display:flex;align-items:center;gap:4px;margin-top:4px;}' +
        '.visto-avatar{width:38px;height:38px;border-radius:50%;object-fit:cover;display:inline-flex;align-items:center;justify-content:center;background:var(--primary,#0d6efd);color:#fff;font-weight:700;}' +
        '.visto-lista{max-height:320px;overflow:auto;text-align:left;}' +
        '.visto-item{display:flex;align-items:center;gap:10px;padding:8px 4px;border-bottom:1px solid var(--border,#eee);}' +
        '.visto-item:last-child{border-bottom:none;}' +
        '.pub-menu-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:transparent;border:1px solid var(--border,#d0d0d0);border-radius:var(--radius,6px);color:var(--text,#333);cursor:pointer;transition:all .15s;}' +
        '.pub-menu-btn:hover{background:var(--bg-input-hover,#e9ecef);border-color:var(--primary,#0d6efd);color:var(--primary,#0d6efd);}' +
        '.pub-menu-btn.pub-menu-der{margin-left:auto;}' +
        '.com-admin-menu{position:fixed;z-index:20000;background:var(--bg-card,#fff);border:1px solid var(--border,#d0d0d0);border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.2);min-width:210px;padding:6px;display:none;}' +
        '.com-admin-menu button{display:flex;align-items:center;gap:10px;width:100%;background:transparent;border:none;padding:9px 12px;border-radius:8px;font-size:0.82rem;color:var(--text,#333);cursor:pointer;text-align:left;}' +
        '.com-admin-menu button:hover{background:var(--bg-input-hover,#f1f3f5);color:var(--primary,#0d6efd);}' +
        '.com-admin-menu .badge-n{margin-left:auto;background:var(--primary,#0d6efd);color:#fff;border-radius:999px;padding:1px 8px;font-size:0.68rem;font-weight:700;}';
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
    var archivos = c && c.archivos;
    if (typeof archivos === 'string') {
        try { archivos = JSON.parse(archivos); } catch (e) { archivos = []; }
    }
    if (!Array.isArray(archivos) || !archivos.length) return '';
    var html = '<div class="com-adjuntos">';
    archivos.forEach(function (a) {
        var ext = (a.extension || (a.nombre || '').split('.').pop() || '').toLowerCase();
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

// HTML de previsualizacion de un archivo elegido en el input (aun sin subir).
function comPreviewArchivoHtml(file) {
    var ext = (file.name.split('.').pop() || '').toLowerCase();
    var nombre = comEscHtml(file.name);
    var icono;
    if (COM_EXT_IMAGEN.indexOf(ext) !== -1) {
        var nombreJs = nombre.replace(/(['"\\])/g, '\\$1');
        icono = '<img src="' + URL.createObjectURL(file) + '" alt="' + nombre + '" title="Ver imagen" ' +
            'onclick="comAbrirImagen(this.src, \'' + nombreJs + '\')">';
    } else {
        icono = comIconoArchivo(ext);
    }
    return '<div class="com-adjunto-preview">' + icono +
        '<span>' + nombre + '</span>' +
        '<i class="bi bi-x-circle" onclick="comQuitarArchivo(this)"></i>' +
        '</div>';
}

// Quita una previsualizacion de la cola de adjuntos aun sin enviar.
function comQuitarArchivo(icono) {
    $(icono).closest('.com-adjunto-preview').remove();
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

// ─────────────── Me gusta y Vistos ───────────────

function comRutaFoto(u) {
    if (!u || !u.foto) return '';
    if (u.foto.indexOf('http') === 0) return u.foto;
    var base = BASE_URL.charAt(BASE_URL.length - 1) === '/' ? BASE_URL : BASE_URL + '/';
    return base + u.foto;
}

function comAvatarVisto(u) {
    var nombre = u.nombre || 'Desconocido';
    var inicial = nombre.charAt(0) ? nombre.charAt(0).toUpperCase() : 'A';
    if (u.foto) {
        return '<img class="visto-avatar" src="' + comRutaFoto(u) + '?t=' + Date.now() + '" alt="" onerror="this.outerHTML=\'<span class=visto-avatar>\' + \'' + comEscHtml(inicial) + '\' + \'</span>\';">';
    }
    return '<span class="visto-avatar">' + comEscHtml(inicial) + '</span>';
}

function comEsAdmin() {
    return typeof USUARIO_ROL !== 'undefined' && (USUARIO_ROL === 'admin' || USUARIO_ROL === 'superadmin');
}

function pubLikeButton(p, extraClase) {
    var activo = p.me_gusta ? ' activo' : '';
    return '<button type="button" class="pub-like' + activo + ' ' + (extraClase || '') + '" onclick="toggleLikePublicacion(' + p.id + ', this)" title="Me gusta"><i class="bi bi-hand-thumbs-up' + (p.me_gusta ? '-fill' : '') + '"></i><span class="lbl">Me gusta</span>' + (parseInt(p.likes_count) > 0 ? '<span class="com-count">' + p.likes_count + '</span>' : '') + '</button>';
}

function pubAdminMenuButton(p) {
    if (!comEsAdmin()) return '';
    var likes = parseInt(p.likes_count) || 0;
    var vistos = parseInt(p.vistos_count) || 0;
    return '<button type="button" class="pub-menu-btn pub-menu-der" onclick="comMenuAdmin(event,' + p.id + ',' + likes + ',' + vistos + ')" title="Mas opciones"><i class="bi bi-three-dots-vertical"></i></button>';
}

function comMenuAdmin(ev, id, likes, vistos) {
    ev.stopPropagation();
    var menu = document.getElementById('comAdminMenu');
    if (!menu) {
        menu = document.createElement('div');
        menu.id = 'comAdminMenu';
        menu.className = 'com-admin-menu';
        document.body.appendChild(menu);
        document.addEventListener('click', function (e) {
            if (e.target === menu || menu.contains(e.target)) return;
            menu.style.display = 'none';
        });
        window.addEventListener('resize', function () { menu.style.display = 'none'; });
        window.addEventListener('scroll', function () { menu.style.display = 'none'; }, true);
    }
    if (menu.style.display === 'block') { menu.style.display = 'none'; return; }

    menu.innerHTML =
        '<button type="button" onclick="comCerrarMenuAdmin();verLikesPublicacion(' + id + ')"><i class="bi bi-hand-thumbs-up"></i> Quienes dieron me gusta<span class="badge-n">' + likes + '</span></button>' +
        '<button type="button" onclick="comCerrarMenuAdmin();verVistosPublicacion(' + id + ')"><i class="bi bi-eye"></i> Quienes vieron<span class="badge-n">' + vistos + '</span></button>';

    menu.style.display = 'block';
    menu.style.left = '0px';
    menu.style.top = '0px';
    var rect = ev.currentTarget.getBoundingClientRect();
    var mw = menu.offsetWidth;
    var mh = menu.offsetHeight;
    var left = rect.right - mw;
    if (left < 8) left = 8;
    if (left + mw > window.innerWidth - 8) left = window.innerWidth - mw - 8;
    var top = rect.bottom + 6;
    if (top + mh > window.innerHeight - 8) top = rect.top - mh - 6;
    if (top < 8) top = 8;
    menu.style.left = left + 'px';
    menu.style.top = top + 'px';
}

function comCerrarMenuAdmin() {
    var menu = document.getElementById('comAdminMenu');
    if (menu) menu.style.display = 'none';
}

function toggleLikePublicacion(id, btn) {
    $.ajax({
        url: BASE_URL + 'borradores/toggle-like/' + id,
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            if (!res.success) return;
            $(btn).toggleClass('activo', !!res.me_gusta);
            $(btn).find('.bi').removeClass('bi-hand-thumbs-up bi-hand-thumbs-up-fill').addClass(res.me_gusta ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up');
            $(btn).find('.com-count').remove();
            if (res.likes > 0) $(btn).append('<span class="com-count">' + res.likes + '</span>');
        }
    });
}

function verVistosPublicacion(id) {
    $.ajax({
        url: BASE_URL + 'borradores/vistos/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.success) return;
            var html = '<div class="visto-lista">';
            if (!res.data.length) {
                html += '<div class="text-muted text-center py-3"><i class="bi bi-eye-slash"></i> Aun nadie ha visto esta publicacion</div>';
            } else {
                res.data.forEach(function (u) {
                    html += '<div class="visto-item">' + comAvatarVisto(u) +
                        '<div class="flex-grow-1"><div class="fw-semibold">' + comEscHtml(u.nombre) + ' <span class="text-muted small">' + (u.rol_legible || '') + '</span></div>' +
                        '<div class="small text-muted">Visto el ' + (u.fecha || '') + ' a las ' + (u.hora || '') + '</div></div></div>';
                });
            }
            html += '</div>';
            Swal.fire({
                title: 'Visto por (' + res.data.length + ')',
                html: html,
                width: 460,
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    });
}

function verLikesPublicacion(id) {
    $.ajax({
        url: BASE_URL + 'borradores/likes/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.success) return;
            var html = '<div class="visto-lista">';
            if (!res.data.length) {
                html += '<div class="text-muted text-center py-3"><i class="bi bi-hand-thumbs-up"></i> Aun nadie ha dado me gusta</div>';
            } else {
                res.data.forEach(function (u) {
                    html += '<div class="visto-item">' + comAvatarVisto(u) +
                        '<div class="flex-grow-1"><div class="fw-semibold">' + comEscHtml(u.nombre) + ' <span class="text-muted small">' + (u.rol_legible || '') + '</span></div>' +
                        '<div class="small text-muted">Dio me gusta el ' + (u.fecha || '') + ' a las ' + (u.hora || '') + '</div></div></div>';
                });
            }
            html += '</div>';
            Swal.fire({
                title: 'Me gusta (' + res.data.length + ')',
                html: html,
                width: 460,
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    });
}

function comLikeButton(c, esPase) {
    var activo = c.me_gusta ? ' activo' : '';
    var onclick = esPase ? 'toggleLikePaseComentario(' + c.id + ', this)' : 'toggleLikeComentario(' + c.id + ', this)';
    var icono = esPase ? 'bi bi-hand-thumbs-up' : 'bi bi-hand-thumbs-up';
    return '<button type="button" class="com-like' + activo + '" onclick="' + onclick + '" title="Me gusta"><i class="' + icono + (c.me_gusta ? '-fill' : '') + '"></i><span class="lbl">Me gusta</span>' + (parseInt(c.likes_count) > 0 ? '<span class="com-like-count">' + c.likes_count + '</span>' : '') + '</button>';
}

function toggleLikeComentario(id, btn) {
    $.ajax({
        url: BASE_URL + 'comentarios/toggle-like/' + id,
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            if (!res.success) return;
            $(btn).toggleClass('activo', !!res.me_gusta);
            $(btn).find('.bi').removeClass('bi-hand-thumbs-up bi-hand-thumbs-up-fill').addClass(res.me_gusta ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up');
            $(btn).find('.com-like-count').remove();
            if (res.likes > 0) $(btn).append('<span class="com-like-count">' + res.likes + '</span>');
        }
    });
}

function toggleLikePaseComentario(id, btn) {
    $.ajax({
        url: BASE_URL + 'comentarios/toggle-like-pase/' + id,
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            if (!res.success) return;
            $(btn).toggleClass('activo', !!res.me_gusta);
            $(btn).find('.bi').removeClass('bi-hand-thumbs-up bi-hand-thumbs-up-fill').addClass(res.me_gusta ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up');
            $(btn).find('.com-like-count').remove();
            if (res.likes > 0) $(btn).append('<span class="com-like-count">' + res.likes + '</span>');
        }
    });
}