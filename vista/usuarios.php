<?php
require_once __DIR__ . '/../modelo/Usuario.php';
try {
  $usuarios = Usuario::listar();
} catch (Exception $e) {
  $usuarios = [];
  $_SESSION['error'] = 'Error al cargar usuarios: ' . $e->getMessage();
}
?>
<div class="page-container">
  <div class="page-header">
    <h2>Usuarios</h2>
    <p class="page-subtitle">Gestión de usuarios del sistema</p>
  </div>

  <div class="section-card">
    <div class="section-card-header">
      <h3>Nuevo usuario</h3>
    </div>
    <form method="POST" action="controlador/UsuarioController.php" class="inline-form">
      <input type="hidden" name="accion" value="crear">
      <div class="form-row">
        <div class="form-group">
          <label for="user-usuario">Usuario</label>
          <input type="text" id="user-usuario" name="usuario" placeholder="Nombre de usuario" required>
        </div>
        <div class="form-group">
          <label for="user-password">Contraseña</label>
          <input type="password" id="user-password" name="password" placeholder="Contraseña" required>
        </div>
        <div class="form-group form-group-btn">
          <button type="submit" class="btn btn-primary">Crear</button>
        </div>
      </div>
    </form>
  </div>

  <div class="section-card">
    <div class="section-card-header">
      <h3>Usuarios registrados</h3>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($usuarios)): ?>
            <tr><td colspan="2" class="empty-table">No hay usuarios registrados</td></tr>
          <?php else: ?>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td class="td-name"><?= htmlspecialchars($u['usuario']) ?></td>
                <td class="td-actions">
                  <button class="btn btn-sm btn-outline" onclick="editarUsuario('<?= $u['id'] ?>', '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>')">
                    Editar
                  </button>
                  <form method="POST" action="controlador/UsuarioController.php" style="display:inline" onsubmit="return confirm('¿Eliminar usuario?')">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-editar-usuario" style="display:none">
  <div class="modal">
    <div class="modal-header">
      <h3>Editar usuario</h3>
      <button type="button" class="modal-close" onclick="cerrarModal('modal-editar-usuario')">&times;</button>
    </div>
    <form method="POST" action="controlador/UsuarioController.php">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id" id="edit-user-id">
      <div class="modal-body">
        <div class="form-group">
          <label for="edit-user-usuario">Usuario</label>
          <input type="text" id="edit-user-usuario" name="usuario" required>
        </div>
        <div class="form-group">
          <label for="edit-user-password">Nueva contraseña (dejar vacío para no cambiar)</label>
          <input type="password" id="edit-user-password" name="password" placeholder="••••••••">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="cerrarModal('modal-editar-usuario')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
function editarUsuario(id, usuario) {
  document.getElementById('edit-user-id').value = id;
  document.getElementById('edit-user-usuario').value = usuario;
  document.getElementById('modal-editar-usuario').style.display = 'flex';
}
</script>
