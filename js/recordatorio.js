$(document).ready(function () {
  cargarRecordatorios();
});

function cargarRecordatorios() {
  showLoading();
  $.ajax({
    url: BASE_URL + "recordatorio/listar",
    type: "GET",
    dataType: "json",
    success: function (data) {
      if (!data || data.length === 0) {
        $("#sinRecordatorios").show();
        $("#listaRecordatoriosWrap").hide();
        $("#recCount").text("0 recordatorios");
        hideLoading();
        return;
      }

      $("#sinRecordatorios").hide();
      $("#listaRecordatoriosWrap").show();
      $("#recCount").text(
        data.length + " recordatorio" + (data.length !== 1 ? "s" : ""),
      );

      var tbody = $("#recTbody");
      tbody.empty();

      data.forEach(function (r) {
        tbody.append(renderCardRec(r));
      });

      hideLoading();
    },
    error: function () {
      hideLoading();
      Swal.fire("Error", "Error al cargar recordatorios.", "error");
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

function renderCardRec(r) {
  var fecha = r.fecha ? r.fecha.split(" ")[0] : "";
  var hora = r.fecha ? r.fecha.split(" ")[1]?.slice(0, 5) : "";

  var badgePrio = "";
  switch (r.prioridad) {
    case "alta":
      badgePrio = '<span class="badge-prio alta">Alta</span>';
      break;
    case "media":
      badgePrio = '<span class="badge-prio media">Media</span>';
      break;
    default:
      badgePrio = '<span class="badge-prio baja">Baja</span>';
  }

  var descHtml = r.descripcion
    ? '<div class="pub-contenido">' + escHtml(r.descripcion) + "</div>"
    : "";
  var origenTitulo = r.origen_titulo ? escHtml(r.origen_titulo) : '';
  var origenContenido = r.origen_contenido ? escHtml((r.origen_contenido || '').replace(/<[^>]*>/g, '').slice(0, 180)) : '';
  var origenMeta = r.origen_meta ? formatearFechaHora(r.origen_meta) : '';
  var completadoClass = r.completado ? "completado" : "";
  var checked = r.completado ? "checked" : "";
  var secTitulo = r.seccion ? badgeSeccion(r) : "";
  var tipoTitulo = r.tipo === "marcador" ? "Marcador" : "Recordatorio";

  return (
    '<div class="pub-card ' +
    completadoClass +
    '" id="rec-' +
    r.id +
    '" data-origen="' +
    (r.origen_tipo === "entrega" ? "entrega" : "borrador") +
    '" data-origen-id="' +
    (r.origen_id || "") +
    '" role="button" tabindex="0" onclick="abrirOrigenRec(' + r.id + ')">' +
    secTitulo +
    '<div class="pub-titulo">' +
    escHtml(r.titulo) +
    "</div>" +
    (origenTitulo ? '<div class="pub-meta" style="margin-top:0;">' +
      '<i class="bi bi-link-45deg"></i> <strong>Origen:</strong> ' + origenTitulo +
      '</div>' : '') +
    (origenContenido ? '<div class="pub-contenido" style="margin-top:6px;">' + origenContenido + '</div>' : '') +
    (origenMeta ? '<div class="pub-meta"><i class="bi bi-clock"></i> ' + origenMeta + '</div>' : '') +
    descHtml +
    '<div class="pub-meta"><i class="bi bi-clock"></i> ' +
    fecha +
    (hora ? " " + hora : "") +
    " &nbsp;|&nbsp; <i class='bi bi-check2-circle'></i> " +
    tipoTitulo +
    " &nbsp; " +
    badgePrio +
    "</div>" +
    '<div class="pub-acciones">' +
    '<button type="button" class="del" onclick="event.stopPropagation(); eliminarRecordatorio(' +
    r.id +
    ')" title="Eliminar"><i class="bi bi-trash"></i> Eliminar</button>' +
    (r.origen_id
    ? comButtonRec(r.comentarios_count || 0, r)
      : "") +
    "</div>" +
    (r.origen_id
      ? '<div class="comentarios-wrap" id="comentarios-rec-' +
        r.id +
        '" style="display:none;">' +
        '<div class="comentarios-lista"></div>' +
        '<div class="comentarios-form">' +
        '<textarea class="form-control form-control-sm" rows="2" placeholder="Escribe un comentario..."></textarea>' +
        '<button class="btn btn-primary btn-sm mt-1" onclick="guardarComentarioRec(' +
        r.id +
        ', this, event)">Enviar</button>' +
        "</div></div>"
      : "") +
    '</div>'
  );
}

function abrirOrigenRec(id) {
  var card = $('#rec-' + id);
  var origenTipo = card.data('origen') || 'borrador';
  var origenId = card.data('origen-id');
  if (!origenId) return;
  var url = origenTipo === 'entrega'
    ? BASE_URL + 'entregas?select=' + origenId
    : BASE_URL + 'borradores?select=' + origenId;
  window.location.href = url;
}

function comButtonRec(count, r) {
    return (
    '<button type="button" class="com" onclick="event.stopPropagation(); toggleComentariosRec(' +
    r.id +
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

function guardarComentarioRec(id, btn, ev) {
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

  if (ev && typeof ev.stopPropagation === 'function') ev.stopPropagation();
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

function eliminarRecordatorio(id) {
  Swal.fire({
    title: "Eliminar recordatorio",
    text: "Esta seguro de eliminar este recordatorio?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#4669FA",
    cancelButtonColor: "#6c757d",
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
            Swal.fire({
              icon: "success",
              title: "Eliminado",
              text: response.message,
              timer: 2000,
              showConfirmButton: false,
            });
            cargarRecordatorios();
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

function toggleCompletado(id, checkbox) {
  showLoading();
  $.ajax({
    url: BASE_URL + "recordatorio/completar/" + id,
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({ completado: checkbox.checked ? 1 : 0 }),
    dataType: "json",
    success: function (response) {
      hideLoading();
      if (response.success) {
        cargarRecordatorios();
      }
    },
    error: function () {
      hideLoading();
      checkbox.checked = !checkbox.checked;
      Swal.fire("Error", "Error al actualizar.", "error");
    },
  });
}

function formatearFechaHora(f) {
  if (!f) return "";
  var d = new Date(f.replace(" ", "T"));
  if (isNaN(d.getTime())) return f.slice(0, 10);
  var dd = ("0" + d.getDate()).slice(-2), mm = ("0" + (d.getMonth() + 1)).slice(-2), yy = d.getFullYear().toString().slice(-2);
  var hh = ("0" + d.getHours()).slice(-2), mi = ("0" + d.getMinutes()).slice(-2);
  return dd + "/" + mm + "/" + yy + " " + hh + ":" + mi;
}

function escHtml(str) {
  if (!str) return "";
  return $("<div>").text(str).html();
}
