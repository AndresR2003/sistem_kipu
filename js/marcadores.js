$(document).ready(function () {
  cargarMarcadores();
});

function cargarMarcadores() {
  showLoading();
  $.ajax({
    url: BASE_URL + "recordatorio/listar?tipo=marcador",
    type: "GET",
    dataType: "json",
    success: function (data) {
      if (!data || data.length === 0) {
        $("#sinMarcadores").show();
        $("#listaMarcadores").empty();
        $("#recCount").text("0 marcadores");
        hideLoading();
        return;
      }

      $("#sinMarcadores").hide();
      $("#recCount").text(data.length + " marcador" + (data.length !== 1 ? "es" : ""));

      var lista = $("#listaMarcadores");
      lista.empty();

      data.forEach(function (m) {
        lista.append(renderCardMarcador(m));
      });

      hideLoading();
    },
    error: function () {
      hideLoading();
      Swal.fire("Error", "Error al cargar marcadores.", "error");
    },
  });
}

function badgeSeccion(r) {
  var seccion = r.seccion || "";
  var origen = r.origen_tipo;
  var label, cls, icon;
  if (origen === "tarea") {
    label = "Tarea";
    cls = "background:rgba(34,197,94,0.12);color:#22c55e;";
    icon = "bi bi-check2-square";
  } else if (origen === "entrega") {
    label = "Tarea diaria";
    cls = "background:rgba(34,197,94,0.12);color:#22c55e;";
    icon = "bi bi-arrow-repeat";
  } else if (seccion === "tareas") {
    label = "Otras tareas";
    cls = "background:rgba(70,105,250,0.12);color:var(--primary);";
    icon = "bi bi-check2-square";
  } else if (seccion === "noticias") {
    label = "Noticias";
    cls = "background:rgba(6,182,212,0.12);color:#06b6d4;";
    icon = "bi bi-newspaper";
  } else if (seccion === "ideas") {
    label = "Ideas";
    cls = "background:rgba(245,158,11,0.12);color:#f59e0b;";
    icon = "bi bi-lightbulb-fill";
  } else if (seccion === "manual") {
    label = "Manual";
    cls = "background:rgba(168,85,247,0.12);color:#a855f7;";
    icon = "bi bi-book-fill";
  } else {
    label = origen === "entrega" ? "Entrega" : "Publicacion";
    cls = "";
    icon = "bi bi-file-text";
  }
  return '<span class="pub-badge" style="' + cls + '"><i class="' + icon + '"></i> ' + label + "</span>";
}

function renderCardMarcador(m) {
  var seccion = m.seccion || "";
  if (seccion === "tareas_diarias") seccion = "tareas";
  var destinoUrl = seccion && m.origen_id ? BASE_URL + seccion + "?select=" + m.origen_id : "";
  var secTitulo = seccion ? badgeSeccion({ seccion: seccion, origen_tipo: m.origen_tipo }) : "";

  var nombre = m.autor_nombre || "Desconocido";
  var avatar = avatarComentarioRec({ autor_foto: m.autor_foto }, nombre);
  var fecha = m.origen_fecha || (m.created_at ? formatearFecha(m.created_at) : "");
  var hora = m.origen_hora || "";

  var descHtml = m.descripcion
    ? '<div class="pub-contenido">' + escHtml(m.descripcion) + "</div>"
    : "";

  var origenHtml = "";
  if (m.origen_id) {
    origenHtml =
      '<div class="noticia-origen" onclick="event.stopPropagation(); abrirOrigenMarcador(' + m.id + ')" title="Ir a la publicacion original">' +
      '<i class="bi bi-link-45deg"></i> Publicacion original' +
      '<i class="bi bi-arrow-right-short"></i>' +
      "</div>";
  }

  return (
    '<div class="noticia-card" id="rec-' +
    m.id +
    '" data-origen="' +
    (m.origen_tipo === "entrega" ? "entrega" : m.origen_tipo === "tarea" ? "tarea" : "borrador") +
    '" data-origen-id="' +
    (m.origen_id || "") +
    '" data-seccion="' +
    seccion +
    '" role="button" tabindex="0" data-url="' +
    escHtml(destinoUrl) +
    '" onclick="abrirOrigenMarcador(' + m.id + ')">' +
    '<div class="noticia-main">' +
    secTitulo +
    '<div class="pub-titulo">' + escHtml(m.titulo) + "</div>" +
    descHtml +
    origenHtml +
    '<div class="pub-meta"><i class="bi bi-bookmark-fill"></i> Guardado el ' +
    (m.created_at ? formatearFechaHora(m.created_at) : "") +
    "</div>" +
    "</div>" +
    '<div class="noticia-side">' +
    '<div class="noticia-side-card">' +
    '<div class="noticia-side-label">Publicado por</div>' +
    '<div class="noticia-autor-row">' +
    '<div class="noticia-autor-avatar">' + avatar + "</div>" +
    "<div>" +
    '<div class="noticia-autor-nombre">' + escHtml(nombre) + "</div>" +
    '<div class="noticia-autor-rol">' + escHtml(m.autor_rol_legible || "") + "</div>" +
    "</div>" +
    "</div>" +
    "</div>" +
    '<div class="noticia-side-card">' +
    '<div class="noticia-side-label">Fecha de publicacion</div>' +
    '<div class="noticia-fecha-item"><i class="bi bi-calendar3"></i><span><span class="lbl">Fecha</span><span class="val"> ' + fecha + '</span></span></div>' +
    '<div class="noticia-fecha-item"><i class="bi bi-clock"></i><span><span class="lbl">Hora</span><span class="val"> ' + hora + ' hrs</span></span></div>' +
    "</div>" +
    "</div>" +
    '<div class="noticia-footer">' +
    '<div class="pub-acciones">' +
    '<button class="del" onclick="event.stopPropagation(); eliminarMarcador(' + m.id + ')" title="Eliminar"><i class="bi bi-trash"></i> Eliminar</button>' +
    (m.origen_id ? comButtonMarcador(m.comentarios_count || 0, m) : "") +
    "</div>" +
    "</div>" +
    (m.origen_id
      ? '<div class="comentarios-wrap" id="comentarios-rec-' +
        m.id +
        '" style="display:none;" onclick="event.stopPropagation();">' +
        '<div class="comentarios-lista"></div>' +
        '<div class="comentarios-form">' +
        '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
        '<button type="button" class="com-adjuntar-btn" onclick="comAbrirSelectorRec(' +
        m.id +
        ')" title="Adjuntar archivo"><i class="bi bi-paperclip"></i></button>' +
        '<input type="file" class="com-input-adjunto" style="display:none;" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.txt,.csv">' +
        '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentarioRec(' +
        m.id +
        ', this, event)">Enviar</button>' +
        '</div>' +
        '<div class="com-adjuntos-form"></div>' +
        "</div>"
      : "") +
    "</div>"
  );
}

function comButtonMarcador(count, m) {
  return (
    '<button class="com" onclick="event.stopPropagation(); toggleComentariosRec(' +
    m.id +
    ')" title="Ver comentarios"><i class="bi bi-chat-fill"></i> Comentarios' +
    (count > 0 ? '<span class="com-count">' + count + "</span>" : "") +
    "</button>"
  );
}

function setComentariosCountRec(id, count) {
  var btn = $("#comentarios-rec-" + id).closest(".noticia-card, .pub-card").find(".com");
  if (!btn.length) return;
  btn.find(".com-count").remove();
  if (count > 0) {
    btn.append('<span class="com-count">' + count + "</span>");
  }
}

function toggleComentariosRec(id) {
  var wrap = $("#comentarios-rec-" + id);
  var visible = wrap.is(":visible");
  wrap.slideToggle(200);
  if (!visible) cargarComentariosRec(id);
}

function comAbrirSelectorRec(id) {
  $("#comentarios-rec-" + id + " .com-input-adjunto").trigger("click");
}

function comVinculaInputRec(id, wrap) {
  var input = wrap.find(".com-input-adjunto");
  if (input.data("com-vinculado")) return;
  input.data("com-vinculado", true);
  input.on("change", function () {
    var preview = wrap.find(".com-adjuntos-form");
    preview.empty();
    var files = this.files || [];
    Array.prototype.forEach.call(files, function (file) {
      $(comPreviewArchivoHtml(file)).appendTo(preview);
    });
  });
}

function cargarComentariosRec(id) {
  var card = $("#rec-" + id);
  var wrap = $("#comentarios-rec-" + id);
  var lista = wrap.find(".comentarios-lista");
  comVinculaInputRec(id, wrap);
  var origenTipo = card.data("origen") || "borrador";
  var origenId = card.data("origen-id");

  var url, metodo;
  if (origenTipo === "entrega") {
    url = BASE_URL + "entregas/comentarios/" + origenId;
    metodo = "GET";
  } else if (origenTipo === "tarea") {
    url = BASE_URL + "tareas/listar-comentarios/" + origenId;
    metodo = "POST";
  } else {
    url = BASE_URL + "borradores/listar-comentarios/" + origenId;
    metodo = "GET";
  }

  $.ajax({
    url: url,
    type: metodo,
    dataType: "json",
    success: function (data) {
      var items = data && data.data ? data.data : data;
      lista.empty();
      setComentariosCountRec(id, items ? items.length : 0);
      if (!items || items.length === 0) {
        lista.html('<div class="comentario-vacio">Sin comentarios</div>');
        return;
      }
      items.forEach(function (c) {
        var nombre = c.autor_nombre || "Desconocido";
        var fecha = c.created_at ? formatearFechaHora(c.created_at) : "";
        lista.append(
          '<div class="comentario-item">' +
            '<div class="comentario-avatar">' + avatarComentarioRec(c, nombre) + "</div>" +
            '<div class="comentario-body">' +
            '<div class="comentario-autor">' + escHtml(nombre) + ' <span class="comentario-fecha">' + fecha + "</span></div>" +
            '<div class="comentario-texto">' + escHtml(c.comentario) + "</div>" +
            comRenderAdjuntos(c) +
            '<div class="comentario-acciones">' + comLikeButton(c, origenTipo === "entrega") + '</div>' +
            "</div></div>",
        );
      });
    },
  });
}

function avatarComentarioRec(c, nombre) {
  if (c.autor_foto) {
    return '<img src="' + BASE_URL + c.autor_foto + '" alt="">';
  }
  var inicial = nombre && nombre.charAt(0) ? nombre.charAt(0).toUpperCase() : "A";
  return "<span>" + escHtml(inicial) + "</span>";
}

function guardarComentarioRec(id, btn, ev) {
  var wrap = $("#comentarios-rec-" + id);
  var ta = wrap.find("textarea");
  var texto = ta.val().trim();
  var input = wrap.find(".com-input-adjunto");
  if (!texto && (!input[0] || !input[0].files.length)) return;

  var card = $("#rec-" + id);
  var origenTipo = card.data("origen") || "borrador";
  var origenId = card.data("origen-id");

  if (ev && typeof ev.stopPropagation === "function") ev.stopPropagation();
  $(btn).prop("disabled", true);
  var subir = function (archivos) {
    var payload, url;
    if (origenTipo === "entrega") {
      payload = { entrega_id: origenId, comentario: texto, archivos: archivos };
      url = BASE_URL + "entregas/comentario";
    } else if (origenTipo === "tarea") {
      payload = { tarea_id: origenId, comentario: texto, archivos: archivos };
      url = BASE_URL + "tareas/guardar-comentario";
    } else {
      payload = { borrador_id: origenId, comentario: texto, archivos: archivos };
      url = BASE_URL + "borradores/guardar-comentario";
    }
    $.ajax({
      url: url,
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(payload),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          ta.val("");
          if (input[0]) { input[0].value = ""; }
          wrap.find(".com-adjuntos-form").empty();
          cargarComentariosRec(id);
        } else {
          Swal.fire("Error", response.message, "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Error de conexion.", "error");
      },
      complete: function () {
        $(btn).prop("disabled", false);
      },
    });
  };
  if (input[0] && input[0].files.length) {
    comPrepararSubida(input[0], function (archivos) {
      subir(archivos);
      input[0].value = "";
    });
  } else {
    subir([]);
  }
}

function eliminarMarcador(id) {
  Swal.fire({
    title: "Eliminar marcador",
    text: "Se eliminara este marcador.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#ef4444",
    confirmButtonText: "Si, eliminar",
    cancelButtonText: "Cancelar",
  }).then(function (result) {
    if (result.isConfirmed) {
      showLoading();
      $.ajax({
        url: BASE_URL + "recordatorio/eliminar/" + id,
        type: "POST",
        dataType: "json",
        success: function (response) {
          hideLoading();
          if (response.success) {
            Swal.fire({ icon: "success", title: "Eliminado", timer: 1500, showConfirmButton: false });
            cargarMarcadores();
          } else {
            Swal.fire("Error", response.message, "error");
          }
        },
        error: function () {
          hideLoading();
          Swal.fire("Error", "Error de conexion.", "error");
        },
      });
    }
  });
}

function formatearFecha(f) {
  if (!f) return "";
  var d = new Date(f.replace(" ", "T"));
  if (isNaN(d.getTime())) return f.slice(0, 10);
  var dd = ("0" + d.getDate()).slice(-2), mm = ("0" + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
  return dd + "/" + mm + "/" + yy;
}

function formatearFechaHora(f) {
  if (!f) return "";
  var d = new Date(f.replace(" ", "T"));
  if (isNaN(d.getTime())) return f.slice(0, 10);
  var dd = ("0" + d.getDate()).slice(-2), mm = ("0" + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
  var hh = ("0" + d.getHours()).slice(-2), mi = ("0" + d.getMinutes()).slice(-2);
  return dd + "/" + mm + "/" + yy + " " + hh + ":" + mi;
}

function escHtml(s) {
  if (!s) return "";
  return $("<div>").text(s).html();
}

function abrirOrigenMarcador(id) {
  var card = $("#rec-" + id);
  var url = card.data("url") || "";
  if (!url) {
    Swal.fire("Sin destino", "Este marcador no tiene una seccion asociada.", "info");
    return;
  }
  window.location.href = url;
}