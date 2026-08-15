var tablaColaboradores = null;
var esAdminColaboradores = typeof USUARIO_ROL !== "undefined" && (USUARIO_ROL === "admin" || USUARIO_ROL === "superadmin");
var fotoColaboradorPendiente = null;

$(document).ready(function () {
  if (!esAdminColaboradores) {
    $("#btnNuevoColaborador").hide();
  }
  cargarColaboradores();
});

function cargarColaboradores() {
  if (tablaColaboradores) {
    tablaColaboradores.destroy();
  }

  showLoading();
  $.ajax({
    url: BASE_URL + "colaboradores/listar",
    type: "GET",
    dataType: "json",
    success: function (data) {
      var tbody = $("#tablaColaboradores tbody");
      tbody.empty();
      hideLoading();

      if (!data || data.length === 0) {
        tbody.append(
          '<tr><td colspan="9" class="text-center text-muted">No hay personal</td></tr>',
        );
        return;
      }

      data.forEach(function (c) {
        var estado = c.activo
          ? '<span class="badge" style="background:rgba(34,197,94,0.15);color:#22c55e;font-size:0.7rem;">Activo</span>'
          : '<span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.7rem;">Inactivo</span>';

        var acciones = '<span class="text-muted">-</span>';
        if (c.puede_gestionar) {
          var btnEliminar =
            c.id == USUARIO_ID
              ? '<button class="btn-sm-icon" disabled style="opacity:0.3;" title="No puedes eliminarte"><i class="bi bi-trash"></i></button>'
              : '<button class="btn-sm-icon" onclick="eliminarColaborador(' +
                c.id +
                ')" title="Eliminar"><i class="bi bi-trash" style="color:var(--danger);"></i></button>';
          acciones =
            '<button class="btn-sm-icon" onclick="editarColaborador(' +
            c.id +
            ')" title="Editar"><i class="bi bi-pencil-fill"></i></button> ' +
            btnEliminar;
        }

        var tr =
          "<tr>" +
          '<td><code style="font-size:0.8rem;">' +
          escHtml(c.username) +
          "</code></td>" +
          '<td><strong style="font-size:0.85rem;">' +
          escHtml(c.nombre) +
          "</strong></td>" +
          "<td><small>" +
          escHtml(c.email) +
          "</small></td>" +
          "<td><small>" +
          escHtml(c.dep_des) +
          "</small></td>" +
          "<td><small>" +
          escHtml(c.puesto) +
          "</small></td>" +
          "<td><small>" +
          escHtml(c.telefono) +
          "</small></td>" +
          "<td>" +
          estado +
          "</td>" +
          "<td>" +
          acciones +
          "</td>" +
          "</tr>";
        tbody.append(tr);
      });

      tablaColaboradores = $("#tablaColaboradores").DataTable({
        language: {
          url: BASE_URL + 'js/datatables_es-ES.json',
        },
        pageLength: 25,
        order: [[1, "asc"]],
        columnDefs: [
          { orderable: false, targets: [7] }
        ],
        createdRow: function (row) {
          if (!esAdminColaboradores) {
            $("td:nth-child(8)", row).hide();
          }
        }
      });

      if (!esAdminColaboradores) {
        $("#tablaColaboradores th:last-child").hide();
        tablaColaboradores.columns(7).visible(false);
      }
    },
    error: function () {
      hideLoading();
      Swal.fire("Error", "Error al cargar personal.", "error");
    },
  });
}

function cargarDepartamentos() {
  $.ajax({
    url: BASE_URL + "colaboradores/departamentos",
    type: "GET",
    dataType: "json",
    success: function (data) {
      var sel = $("#colDepto");
      sel.find("option:gt(0)").remove();
      if (data) {
        data.forEach(function (d) {
          sel.append('<option value="' + d.id + '">' + escHtml(d.descripcion) + "</option>");
        });
      }
    },
  });
}

function previsualizarFotoColaborador(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      var preview = $("#colFotoPreview");
      preview.empty();
      var img = $('<img alt="Foto">').attr("src", e.target.result);
      preview.append(img);
    };
    reader.readAsDataURL(input.files[0]);
    fotoColaboradorPendiente = input.files[0];
  }
}

function nuevoColaborador() {
  $("#colaboradorId").val("");
  $("#colUsername").val("").prop("readonly", false);
  $("#colNombre").val("");
  $("#colEmail").val("");
  $("#colPassword").val("");
  $("#colRol").val("empleado");
  $("#colDepto").val("");
  $("#colActivo").val("1");
  $("#colPuesto").val("");
  $("#colTelefono").val("");
  $("#colFechaNac").val("");
  $("#colFechaCont").val("");
  $("#colFoto").val("");
  fotoColaboradorPendiente = null;
  $("#colFotoPreview").empty().html('<i class="bi bi-person-fill"></i>');
  $("#modalTitle").text("Nuevo Empleado");
  $("#passLabel").text("(dejar vacio = se genera automatica)");
  cargarDepartamentos();
  $("#modalColaborador").modal("show");
}

function editarColaborador(id) {
  showLoading();
  $.ajax({
    url: BASE_URL + "colaboradores/obtener/" + id,
    type: "GET",
    dataType: "json",
    success: function (c) {
      hideLoading();
      $("#colaboradorId").val(c.id);
      $("#colUsername").val(c.username).prop("readonly", true);
      $("#colNombre").val(c.nombre);
      $("#colEmail").val(c.email);
      $("#colPassword").val("");
      $("#colRol").val(c.rol);
      $("#colDepto").val(c.id_departamento || "");
      $("#colActivo").val(c.activo);
      $("#colPuesto").val(c.puesto || "");
      $("#colTelefono").val(c.telefono || "");
      $("#colFechaNac").val(c.fecha_nacimiento || "");
      $("#colFechaCont").val(c.fecha_contratacion || "");
      $("#colFoto").val("");
      fotoColaboradorPendiente = null;
      var preview = $("#colFotoPreview");
      preview.empty();
      if (c.foto) {
        preview.append($('<img alt="Foto">').attr("src", BASE_URL + c.foto));
      } else {
        preview.html('<i class="bi bi-person-fill"></i>');
      }
      $("#modalTitle").text("Editar Empleado");
      $("#passLabel").text("(dejar vacio para mantener)");
      cargarDepartamentos();
      $("#modalColaborador").modal("show");
    },
    error: function () {
      hideLoading();
      Swal.fire("Error", "Error al cargar datos.", "error");
    },
  });
}

function guardarColaborador() {
  var id = $("#colaboradorId").val();
  var username = $("#colUsername").val().trim();
  var nombre = $("#colNombre").val().trim();
  var email = $("#colEmail").val().trim();
  var password = $("#colPassword").val();

  if (!username) {
    Swal.fire("Validacion", "El usuario es obligatorio.", "warning");
    return;
  }
  if (!nombre) {
    Swal.fire("Validacion", "El nombre es obligatorio.", "warning");
    return;
  }
  if (!email) {
    Swal.fire("Validacion", "El email es obligatorio.", "warning");
    return;
  }

  var datos = {
    id: id ? parseInt(id) : null,
    username: username,
    nombre: nombre,
    email: email,
    rol: $("#colRol").val(),
    id_departamento: $("#colDepto").val() || null,
    activo: parseInt($("#colActivo").val()),
    puesto: $("#colPuesto").val().trim() || null,
    telefono: $("#colTelefono").val().trim() || null,
    fecha_nacimiento: $("#colFechaNac").val() || null,
    fecha_contratacion: $("#colFechaCont").val() || null,
  };

  if (password) {
    datos.password = password;
  }

  showLoading();
  $.ajax({
    url: BASE_URL + "colaboradores/guardar",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify(datos),
    dataType: "json",
    success: function (response) {
      if (response.success) {
        var nuevoId = response.id || null;
        if (fotoColaboradorPendiente) {
          subirFotoColaborador(nuevoId, response.message);
        } else {
          hideLoading();
          $("#modalColaborador").modal("hide");
          Swal.fire({
            icon: "success",
            title: "Guardado",
            text: response.message,
            timer: 1500,
            showConfirmButton: false,
          });
          cargarColaboradores();
        }
      } else {
        hideLoading();
        Swal.fire("Error", response.message, "error");
      }
    },
    error: function () {
      hideLoading();
      Swal.fire("Error", "Error de conexion.", "error");
    },
  });
}

function subirFotoColaborador(id, mensaje) {
  var formData = new FormData();
  formData.append("foto", fotoColaboradorPendiente);

  $.ajax({
    url: BASE_URL + "colaboradores/subir-foto/" + (id || 0),
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      hideLoading();
      if (response.success) {
        $("#modalColaborador").modal("hide");
        Swal.fire({
          icon: "success",
          title: "Guardado",
          text: mensaje + " Foto subida.",
          timer: 1500,
          showConfirmButton: false,
        });
        cargarColaboradores();
      } else {
        Swal.fire("Advertencia", "Colaborador guardado, pero la foto fallo: " + response.message, "warning");
        cargarColaboradores();
      }
    },
    error: function () {
      hideLoading();
      Swal.fire("Advertencia", "Colaborador guardado, pero la foto no pudo subirse.", "warning");
      cargarColaboradores();
    },
  });
}

function eliminarColaborador(id) {
  Swal.fire({
    title: "Eliminar empleado",
    text: "Este usuario perdera acceso al sistema.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Si, eliminar",
    cancelButtonText: "Cancelar",
  }).then(function (result) {
    if (result.isConfirmed) {
      showLoading();
      $.ajax({
        url: BASE_URL + "colaboradores/eliminar/" + id,
        type: "POST",
        dataType: "json",
        success: function (response) {
          hideLoading();
          if (response.success) {
            Swal.fire({
              icon: "success",
              title: "Eliminado",
              timer: 1500,
              showConfirmButton: false,
            });
            cargarColaboradores();
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

function formatearFechaHora(fecha) {
  if (!fecha) return "";
  var d = new Date(fecha.replace(" ", "T"));
  if (isNaN(d.getTime())) return fecha.slice(0, 10);
  var dd = ("0" + d.getDate()).slice(-2);
  var mm = ("0" + (d.getMonth() + 1)).slice(-2);
  var yy = d.getFullYear().toString().slice(-2);
  var hh = ("0" + d.getHours()).slice(-2);
  var min = ("0" + d.getMinutes()).slice(-2);
  return dd + "/" + mm + "/" + yy + " " + hh + ":" + min;
}

function escHtml(str) {
  if (!str) return "";
  return $("<div>").text(str).html();
}
