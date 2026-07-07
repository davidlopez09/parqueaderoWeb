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
          <label for="user-nombre">Nombre</label>
          <input type="text" id="user-nombre" name="nombre" placeholder="Nombre completo" required>
        </div>
        <div class="form-group">
          <label for="user-username">Usuario</label>
          <input type="text" id="user-username" name="username" placeholder="Nombre de usuario" required>
        </div>
        <div class="form-group">
          <label for="user-password">Contraseña</label>
          <input type="password" id="user-password" name="password" placeholder="Contraseña" required>
        </div>
        <div class="form-group">
          <label for="user-rol">Rol</label>
          <select id="user-rol" name="rol">
            <option value="operador">Operador</option>
            <option value="admin">Administrador</option>
          </select>
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
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($usuarios)): ?>
            <tr><td colspan="5" class="empty-table">No hay usuarios registrados</td></tr>
          <?php else: ?>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td class="td-name"><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td>
                  <span class="badge <?= $u['rol'] === 'admin' ? 'badge-active' : 'badge-inactive' ?>">
                    <?= htmlspecialchars($u['rol']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($u['activo']): ?>
                    <span class="badge badge-active">Activo</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="td-actions">
                  <button class="btn btn-sm btn-outline" onclick="editarUsuario('<?= $u['id'] ?>', '<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>', '<?= $u['rol'] ?>')">
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
          <label for="edit-user-nombre">Nombre</label>
          <input type="text" id="edit-user-nombre" name="nombre" required>
        </div>
        <div class="form-group">
          <label for="edit-user-username">Usuario</label>
          <input type="text" id="edit-user-username" name="username" required>
        </div>
        <div class="form-group">
          <label for="edit-user-password">Nueva contraseña (dejar vacío para no cambiar)</label>
          <input type="password" id="edit-user-password" name="password" placeholder="••••••••">
        </div>
        <div class="form-group">
          <label for="edit-user-rol">Rol</label>
          <select id="edit-user-rol" name="rol">
            <option value="operador">Operador</option>
            <option value="admin">Administrador</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="cerrarModal('modal-editar-usuario')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
