<!DOCTYPE html>
<html lang="es">
<head>
  <?php require_once __DIR__ . '/../../../shared/head.php'; ?>
  <title>Usuarios - Parqueadero</title>
</head>
<body>
  <div class="app-layout">
    <?php require_once __DIR__ . '/../../../shared/nav.php'; ?>
    <main class="main-content">
      <div class="page-container">
        <div class="page-header">
          <h2>Usuarios</h2>
          <p class="page-subtitle">Gestión de usuarios del sistema</p>
        </div>

        <div class="section-card">
          <div class="section-card-header"><h3>Nuevo usuario</h3></div>
          <form class="inline-form" id="form-crear-usuario">
            <input type="hidden" name="accion" value="crear">
            <div class="form-row">
              <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
              </div>
              <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
              </div>
              <div class="form-group form-group-btn">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Agregar</button>
              </div>
            </div>
          </form>
        </div>

        <div class="section-card">
          <div class="section-card-header"><h3>Usuarios registrados</h3></div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tbody-usuarios"></tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="form-editar-usuario">
          <input type="hidden" name="accion" value="editar">
          <input type="hidden" name="id" id="edit-id">
          <div class="modal-header">
            <h5 class="modal-title">Editar usuario</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Usuario</label>
              <input type="text" id="edit-usuario" name="usuario" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Nueva contraseña <small>(dejar vacío para mantener)</small></label>
              <input type="password" id="edit-password" name="password" class="form-control" placeholder="Ingrese nueva contraseña">
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
    function cargarUsuarios() {
      $.ajax({
        type: 'POST',
        url: 'index.php?url=usuarios',
        data: { accion: 'listar' },
        dataType: 'json',
        success: function (data) {
          var tbody = $('#tbody-usuarios').empty();
          if (!data || data.length === 0) {
            tbody.append('<tr><td colspan="2" class="empty-table">No hay usuarios registrados</td></tr>');
            return;
          }
          data.forEach(function (u) {
            var tr = '<tr>' +
              '<td>' + $('<span>').text(u.usuario).html() + '</td>' +
              '<td class="td-actions">' +
                '<button class="btn btn-sm btn-outline" onclick="editarUsuario(\'' + u.id + '\',\'' + $('<span>').text(u.usuario).html() + '\')">Editar</button>' +
                '<button class="btn btn-sm btn-danger" onclick="eliminarUsuario(\'' + u.id + '\')">Eliminar</button>' +
              '</td>' +
              '</tr>';
            tbody.append(tr);
          });
        },
        error: function () {
          $('#tbody-usuarios').html('<tr><td colspan="2" class="empty-table">Error al cargar datos</td></tr>');
        }
      });
    }

    function editarUsuario(id, usuario) {
      $('#edit-id').val(id);
      $('#edit-usuario').val(usuario);
      $('#edit-password').val('');
      $('#modalEditarUsuario').modal('show');
    }

    function eliminarUsuario(id) {
      Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
      }).then(function (result) {
        if (!result.isConfirmed) return;
        $.ajax({
          type: 'POST',
          url: 'index.php?url=usuarios',
          data: { accion: 'eliminar', id: id },
          dataType: 'json',
          success: function (data) {
            var item = Array.isArray(data) ? data[0] : data;
            if (String(item.S_1) === '1') {
              Swal.fire({ title: 'Éxito', text: item.S_2, icon: 'success', confirmButtonText: 'Aceptar' }).then(cargarUsuarios);
            } else {
              Swal.fire({ title: 'Error', text: item.S_2 || 'Error', icon: 'error', confirmButtonText: 'Aceptar' });
            }
          },
          error: function () {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo completar la operación', icon: 'error', confirmButtonText: 'Aceptar' });
          }
        });
      });
    }

    $(function () {
      cargarUsuarios();

      $('#form-crear-usuario').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        if (!form.checkValidity()) { form.reportValidity(); return; }
        var formData = new FormData(form);
        $.ajax({
          type: 'POST',
          url: 'index.php?url=usuarios',
          data: formData,
          dataType: 'json',
          cache: false,
          contentType: false,
          processData: false,
          success: function (data) {
            var item = Array.isArray(data) ? data[0] : data;
            if (String(item.S_1) === '1') {
              form.reset();
              Swal.fire({ title: 'Éxito', text: item.S_2, icon: 'success', confirmButtonText: 'Aceptar' }).then(cargarUsuarios);
            } else {
              Swal.fire({ title: 'Error', text: item.S_2 || 'Error', icon: 'error', confirmButtonText: 'Aceptar' });
            }
          },
          error: function () {
            Swal.fire({ title: 'Error de conexión', text: 'No se pudo completar la operación', icon: 'error', confirmButtonText: 'Aceptar' });
          }
        });
      });

      $('#form-editar-usuario').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        if (!form.checkValidity()) { form.reportValidity(); return; }
        var formData = new FormData(form);
        $.ajax({
          type: 'POST',
          url: 'index.php?url=usuarios',
          data: formData,
          dataType: 'json',
          cache: false,
          contentType: false,
          processData: false,
          success: function (data) {
            var item = Array.isArray(data) ? data[0] : data;
            if (String(item.S_1) === '1') {
              $('#modalEditarUsuario').modal('hide');
              Swal.fire({ title: 'Éxito', text: item.S_2, icon: 'success', confirmButtonText: 'Aceptar' }).then(cargarUsuarios);
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
