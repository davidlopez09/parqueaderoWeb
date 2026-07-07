<?php
require_once __DIR__ . '/../modelo/TipoVehiculo.php';
try {
  $tipos = TipoVehiculo::listar();
} catch (Exception $e) {
  $tipos = [];
  $_SESSION['error'] = 'Error al cargar tipos: ' . $e->getMessage();
}
?>
<div class="page-container">
  <div class="page-header">
    <h2>Tipos de Vehículo</h2>
    <p class="page-subtitle">Configure los tipos de vehículo y sus tarifas por hora</p>
  </div>

  <div class="section-card">
    <div class="section-card-header">
      <h3>Nuevo tipo</h3>
    </div>
    <form method="POST" action="controlador/TipoVehiculoController.php" class="inline-form">
      <input type="hidden" name="accion" value="crear">
      <div class="form-row">
        <div class="form-group">
          <label for="tipo-nombre">Nombre</label>
          <input type="text" id="tipo-nombre" name="nombre" placeholder="Ej: Moto, Carro, Camión" required>
        </div>
        <div class="form-group">
          <label for="tipo-precio">Precio por hora ($)</label>
          <input type="number" id="tipo-precio" name="precio_hora" min="1" step="1" placeholder="2000" required>
        </div>
        <div class="form-group form-group-btn">
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
      </div>
    </form>
  </div>

  <div class="section-card">
    <div class="section-card-header">
      <h3>Tipos registrados</h3>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Precio / hora</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($tipos)): ?>
            <tr><td colspan="4" class="empty-table">No hay tipos de vehículo registrados</td></tr>
          <?php else: ?>
            <?php foreach ($tipos as $t): ?>
              <tr>
                <td class="td-name"><?= htmlspecialchars($t['nombre']) ?></td>
                <td><strong>$<?= number_format($t['precio_hora'], 0, ',', '.') ?></strong> / hora</td>
                <td>
                  <?php if ($t['activo']): ?>
                    <span class="badge badge-active">Activo</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="td-actions">
                  <button class="btn btn-sm btn-outline" onclick="editarTipo('<?= $t['id'] ?>', '<?= htmlspecialchars($t['nombre'], ENT_QUOTES) ?>', '<?= $t['precio_hora'] ?>')">
                    Editar
                  </button>
                  <?php if ($t['activo']): ?>
                    <form method="POST" action="controlador/TipoVehiculoController.php" style="display:inline">
                      <input type="hidden" name="accion" value="desactivar">
                      <input type="hidden" name="id" value="<?= $t['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-danger">Desactivar</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="controlador/TipoVehiculoController.php" style="display:inline">
                      <input type="hidden" name="accion" value="activar">
                      <input type="hidden" name="id" value="<?= $t['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-success">Activar</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-editar-tipo" style="display:none">
  <div class="modal">
    <div class="modal-header">
      <h3>Editar tipo de vehículo</h3>
      <button type="button" class="modal-close" onclick="cerrarModal('modal-editar-tipo')">&times;</button>
    </div>
    <form method="POST" action="controlador/TipoVehiculoController.php">
      <input type="hidden" name="accion" value="editar">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-body">
        <div class="form-group">
          <label for="edit-nombre">Nombre</label>
          <input type="text" id="edit-nombre" name="nombre" required>
        </div>
        <div class="form-group">
          <label for="edit-precio">Precio por hora ($)</label>
          <input type="number" id="edit-precio" name="precio_hora" min="1" step="1" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="cerrarModal('modal-editar-tipo')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
