<!DOCTYPE html>
<html lang="es">
<head>
  <?php require_once __DIR__ . '/../../../shared/head.php'; ?>
  <title>Tipos de Vehículo - Parqueadero</title>
</head>
<body>
  <div class="app-layout">
    <?php require_once __DIR__ . '/../../../shared/nav.php'; ?>
    <main class="main-content">
      <div class="page-container">
        <div class="page-header">
          <h2>Tipos de Vehículo</h2>
          <p class="page-subtitle">Configure los tipos de vehículo y sus tarifas por hora</p>
        </div>

        <div class="section-card">
          <div class="section-card-header"><h3>Nuevo tipo</h3></div>
          <form class="inline-form" data-action="tipos">
            <input type="hidden" name="accion" value="crear">
            <div class="form-row">
              <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Moto, Carro, Camión" required>
              </div>
              <div class="form-group">
                <label>Precio por hora ($)</label>
                <input type="number" name="precio_hora" class="form-control" min="1" step="1" placeholder="2000" required>
              </div>
              <div class="form-group form-group-btn">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Agregar</button>
              </div>
            </div>
          </form>
        </div>

        <div class="section-card">
          <div class="section-card-header"><h3>Tipos registrados</h3></div>
          <div class="table-responsive">
            <table class="table" id="tabla-tipos">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Precio / hora</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tbody-tipos">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div class="modal fade" id="modalEditarTipo" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form data-action="tipos">
          <input type="hidden" name="accion" value="editar">
          <input type="hidden" name="id" id="edit-id">
          <div class="modal-header">
            <h5 class="modal-title">Editar tipo de vehículo</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre</label>
              <input type="text" id="edit-nombre" name="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Precio por hora ($)</label>
              <input type="number" id="edit-precio" name="precio_hora" class="form-control" min="1" step="1" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/../../../shared/foot.php'; ?>
  <script>
    function cargarTipos() {
      $.post('index.php?url=tipos', { accion: 'listar' }, function (data) {
        var tbody = $('#tbody-tipos').empty();
        if (!data || data.length === 0) {
          tbody.append('<tr><td colspan="3" class="empty-table">No hay tipos de vehículo registrados</td></tr>');
          return;
        }
        data.forEach(function (t) {
          var tr = '<tr>' +
            '<td class="td-name">' + $('<span>').text(t.nombre).html() + '</td>' +
            '<td><strong>$' + Number(t.precio_hora).toLocaleString('es-CO') + '</strong> / hora</td>' +
            '<td class="td-actions">' +
              '<button class="btn btn-sm btn-outline" onclick="editarTipo(\'' + t.id + '\',\'' + $('<span>').text(t.nombre).html() + '\',' + t.precio_hora + ')">Editar</button>' +
              '<button class="btn btn-sm btn-danger" onclick="eliminarTipo(\'' + t.id + '\',\'' + $('<span>').text(t.nombre).html() + '\')">Eliminar</button>' +
            '</td>' +
            '</tr>';
          tbody.append(tr);
        });
      }, 'json').fail(function () {
        $('#tbody-tipos').html('<tr><td colspan="4" class="empty-table">Error al cargar datos</td></tr>');
      });
    }

    function eliminarTipo(id, nombre) {
      Swal.fire({
        title: '¿Estás seguro de eliminar?',
        text: 'Se eliminará el tipo "' + nombre + '"',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
      }).then(function (result) {
        if (!result.isConfirmed) return;
        $.post('index.php?url=tipos', { accion: 'eliminar', id: id }, function (data) {
          var item = Array.isArray(data) ? data[0] : data;
          if (String(item.S_1) === '1') {
            Swal.fire({ title: 'Eliminado', text: item.S_2, icon: 'success', confirmButtonText: 'Aceptar' }).then(cargarTipos);
          } else {
            Swal.fire({ title: 'Error', text: item.S_2 || 'Error', icon: 'error', confirmButtonText: 'Aceptar' });
          }
        }, 'json');
      });
    }

    function editarTipo(id, nombre, precio) {
      $('#edit-id').val(id);
      $('#edit-nombre').val(nombre);
      $('#edit-precio').val(precio);
      $('#modalEditarTipo').modal('show');
    }

    $(function () {
      cargarTipos();

      $('form[data-action="tipos"]').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        if (!form.checkValidity()) { form.reportValidity(); return; }
        var formData = new FormData(form);
        $.ajax({
          type: 'POST',
          url: 'index.php?url=tipos',
          data: formData,
          dataType: 'json',
          cache: false,
          contentType: false,
          processData: false,
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          success: function (data) {
            var item = Array.isArray(data) ? data[0] : data;
            if (String(item.S_1) === '1') {
              $('#modalEditarTipo').modal('hide');
              form.reset();
              Swal.fire({ title: 'Éxito', text: item.S_2, icon: 'success', confirmButtonText: 'Aceptar' }).then(cargarTipos);
            } else {
              Swal.fire({ title: 'Error', text: item.S_2 || 'Error', icon: 'error', confirmButtonText: 'Aceptar' });
            }
          },
          error: function () {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo completar la operación', icon: 'error', confirmButtonText: 'Aceptar' });
          }
        });
      });
    });
  </script>
</body>
</html>
