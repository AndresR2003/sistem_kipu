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

      var listaWrap = $("#recTbody");
      listaWrap.find(".rec-card").remove();

      data.forEach(function (r) {
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
          ? '<div class="rec-desc">' + escHtml(r.descripcion) + "</div>"
          : "";
        var completadoClass = r.completado ? "completado" : "";
        var checked = r.completado ? "checked" : "";

        var card =
          '<div class="rec-card ' +
          completadoClass +
          '">' +
          '<div class="rec-check">' +
          '<input type="checkbox" class="form-check-input" ' +
          checked +
          ' onchange="toggleCompletado(' +
          r.id +
          ', this)">' +
          '<div class="rec-cuerpo">' +
          '<div class="rec-titulo">' +
          escHtml(r.titulo) +
          "</div>" +
          descHtml +
          '<div class="rec-meta">' +
          '<span><i class="bi bi-clock" style="margin-right:4px;"></i>' +
          fecha +
          (hora ? ' <span style="opacity:0.6;">' + hora + "</span>" : "") +
          "</span>" +
          badgePrio +
          '<span class="rec-acciones">' +
          '<button onclick="eliminarRecordatorio(' +
          r.id +
          ')" title="Eliminar"><i class="bi bi-trash"></i> Eliminar</button>' +
          "</span>" +
          "</div>" +
          "</div>" +
          "</div>" +
          "</div>";

        tbody.append(card);
      });

      hideLoading();
    },
    error: function () {
      hideLoading();
      Swal.fire("Error", "Error al cargar recordatorios.", "error");
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

function escHtml(str) {
  if (!str) return "";
  return $("<div>").text(str).html();
}
