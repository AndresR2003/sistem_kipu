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
  var origen = r.origen_tipo === "entrega" ? "entrega" : "borrador";
  var label, cls, icon;
  if (seccion === "tareas_diarias") {
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
  var fecha = m.created_at ? formatearFecha(m.created_at) : "";
  var descHtml = m.descripcion
    ? '<div class="pub-contenido">' + escHtml(m.descripcion) + "</div>"
    : "";
  var secTitulo = m.seccion ? badgeSeccion(m) : "";

  return (
    '<div class="pub-card" id="rec-' +
    m.id +
    '" data-origen="' +
    (m.origen_tipo === "entrega" ? "entrega" : "borrador") +
    '" data-origen-id="' +
    (m.origen_id || "") +
    '">' +
    secTitulo +
    '<div class="pub-titulo">' +
    escHtml(m.titulo) +
    "</div>" +
    descHtml +
    '<div class="pub-meta"><i class="bi bi-clock"></i> ' +
    fecha +
    "</div>" +
    '<div class="pub-acciones">' +
    '<button class="del" onclick="eliminarMarcador(' +
    m.id +
    ')" title="Eliminar"><i class="bi bi-trash"></i> Eliminar</button>' +
    (m.origen_id ? comButtonMarcador(m.comentarios_count || 0, m) : "") +
    "</div>" +
    (m.origen_id
      ? '<div class="comentarios-wrap" id="comentarios-rec-' +
        m.id +
        '" style="display:none;">' +
        '<div class="comentarios-lista"></div>' +
        '<div class="comentarios-form">' +
        '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
        '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentarioRec(' +
        m.id +
        ', this)">Enviar</button>' +
        "</div></div>"
      : "") +
    "</div>"
  );
}

function comButtonMarcador(count, m) {
  return (
    '<button class="com" onclick="toggleComentariosRec(' +
    m.id +
    ')" title="Ver comentarios"><i class="bi bi-chat-fill"></i> Comentarios' +
    (count > 0 ? '<span class="com-count">' + count + "</span>" : "") +
    "</button>"
  );
}

function setComentariosCountRec(id, count) {
  var btn = $("#comentarios-rec-" + id).closest(".pub-card").find(".com");
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

function cargarComentariosRec(id) {
  var card = $("#rec-" + id);
  var lista = $("#comentarios-rec-" + id + " .comentarios-lista");
  var origenTipo = card.data("origen") || "borrador";
  var origenId = card.data("origen-id");

  var url = BASE_URL + (origenTipo === "entrega"
    ? "entregas/comentarios/" + origenId
    : "borradores/listar-comentarios/" + origenId);

  $.ajax({
    url: url,
    type: "GET",
    dataType: "json",
    success: function (data) {
      lista.empty();
      setComentariosCountRec(id, data ? data.length : 0);
      if (!data || data.length === 0) {
        lista.html('<div class="comentario-vacio">Sin comentarios</div>');
        return;
      }
      data.forEach(function (c) {
        var nombre = c.autor_nombre || "Desconocido";
        var fecha = c.created_at ? formatearFechaHora(c.created_at) : "";
        lista.append(
          '<div class="comentario-item">' +
            '<div class="comentario-avatar">' + avatarComentarioRec(c, nombre) + "</div>" +
            '<div class="comentario-body">' +
            '<div class="comentario-autor">' + escHtml(nombre) + ' <span class="comentario-fecha">' + fecha + "</span></div>" +
            '<div class="comentario-texto">' + escHtml(c.comentario) + "</div>" +
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

function guardarComentarioRec(id, btn) {
  var wrap = $("#comentarios-rec-" + id);
  var ta = wrap.find("textarea");
  var texto = ta.val().trim();
  if (!texto) return;

  var card = $("#rec-" + id);
  var origenTipo = card.data("origen") || "borrador";
  var origenId = card.data("origen-id");
  var payload = origenTipo === "entrega"
    ? { entrega_id: origenId, comentario: texto }
    : { borrador_id: origenId, comentario: texto };
  var url = BASE_URL + (origenTipo === "entrega" ? "entregas/comentario" : "borradores/guardar-comentario");

  $(btn).prop("disabled", true);
  $.ajax({
    url: url,
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify(payload),
    dataType: "json",
    success: function (response) {
      if (response.success) {
        ta.val("");
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