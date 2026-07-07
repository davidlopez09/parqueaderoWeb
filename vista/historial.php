<?php
require_once __DIR__ . '/../modelo/Parqueo.php';

$filtroFecha = $_GET['periodo'] ?? 'hoy';
$fechaInicio = $_GET['fecha_inicio'] ?? '';
$fechaFin = $_GET['fecha_fin'] ?? '';
$filtroPlaca = $_GET['placa'] ?? '';
$filtroEstado = $_GET['estado'] ?? '';

$ahora = new DateTime('now', new DateTimeZone('UTC'));

switch ($filtroFecha) {
  case 'hoy':
    $fechaInicio = $ahora->format('Y-m-d') . 'T00:00:00';
    $fechaFin = $ahora->format('Y-m-d') . 'T23:59:59';
    break;
  case 'semana':
    $inicio = (clone $ahora)->modify('monday this week');
    $fin = (clone $ahora)->modify('sunday this week');
    $fechaInicio = $inicio->format('Y-m-d') . 'T00:00:00';
    $fechaFin = $fin->format('Y-m-d') . 'T23:59:59';
    break;
  case 'mes':
    $fechaInicio = $ahora->format('Y-m') . '-01T00:00:00';
    $fechaFin = $ahora->format('Y-m-t') . 'T23:59:59';
    break;
  case 'anio':
    $fechaInicio = $ahora->format('Y') . '-01-01T00:00:00';
    $fechaFin = $ahora->format('Y') . '-12-31T23:59:59';
    break;
  case 'rango':
    if ($fechaInicio) $fechaInicio .= 'T00:00:00';
    if ($fechaFin) $fechaFin .= 'T23:59:59';
    break;
}

$filtros = [];
if ($fechaInicio) $filtros['fecha_inicio'] = $fechaInicio;
if ($fechaFin) $filtros['fecha_fin'] = $fechaFin;
if ($filtroPlaca) $filtros['placa'] = $filtroPlaca;
if ($filtroEstado) $filtros['estado'] = $filtroEstado;

try {
  $registros = Parqueo::listarConTipo($filtros);
  $stats = Parqueo::estadisticas($filtros);
} catch (Exception $e) {
  $registros = [];
  $stats = ['activos' => 0, 'finalizados' => 0, 'total_ingresos' => 0, 'ingresos_hoy' => 0];
  $error = 'Error al cargar historial: ' . $e->getMessage();
}
?>
<div class="page-container">
  <div class="page-header">
    <h2>Historial</h2>
    <p class="page-subtitle">Consulte la trazabilidad de ingresos y salidas</p>
  </div>

  <div class="section-card">
    <div class="section-card-header">
      <h3>Filtros</h3>
    </div>
    <form method="GET" class="filter-form">
      <input type="hidden" name="page" value="historial">
      <div class="filter-row">
        <div class="filter-group">
          <label>Período rápido</label>
          <select name="periodo" onchange="this.form.submit()">
            <option value="hoy" <?= $filtroFecha === 'hoy' ? 'selected' : '' ?>>Hoy</option>
            <option value="semana" <?= $filtroFecha === 'semana' ? 'selected' : '' ?>>Esta semana</option>
            <option value="mes" <?= $filtroFecha === 'mes' ? 'selected' : '' ?>>Este mes</option>
            <option value="anio" <?= $filtroFecha === 'anio' ? 'selected' : '' ?>>Este año</option>
            <option value="rango" <?= $filtroFecha === 'rango' ? 'selected' : '' ?>>Rango personalizado</option>
          </select>
        </div>
        <div class="filter-group <?= $filtroFecha !== 'rango' ? 'hidden' : '' ?>" id="rango-fechas">
          <label>Fecha inicio</label>
          <input type="date" name="fecha_inicio" value="<?= htmlspecialchars(substr($fechaInicio, 0, 10)) ?>">
        </div>
        <div class="filter-group <?= $filtroFecha !== 'rango' ? 'hidden' : '' ?>" id="rango-fechas-fin">
          <label>Fecha fin</label>
          <input type="date" name="fecha_fin" value="<?= htmlspecialchars(substr($fechaFin, 0, 10)) ?>">
        </div>
        <div class="filter-group">
          <label>Placa</label>
          <input type="text" name="placa" placeholder="Buscar placa" value="<?= htmlspecialchars($filtroPlaca) ?>">
        </div>
        <div class="filter-group">
          <label>Estado</label>
          <select name="estado">
            <option value="">Todos</option>
            <option value="activo" <?= $filtroEstado === 'activo' ? 'selected' : '' ?>>Activos</option>
            <option value="finalizado" <?= $filtroEstado === 'finalizado' ? 'selected' : '' ?>>Finalizados</option>
          </select>
        </div>
        <div class="filter-group filter-group-btn">
          <label>&nbsp;</label>
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
      </div>
    </form>
  </div>

  <div class="stats-grid stats-grid-sm">
    <div class="stat-card stat-blue">
      <div class="stat-info">
        <span class="stat-value"><?= $stats['activos'] ?></span>
        <span class="stat-label">Activos</span>
      </div>
    </div>
    <div class="stat-card stat-green">
      <div class="stat-info">
        <span class="stat-value"><?= $stats['finalizados'] ?></span>
        <span class="stat-label">Finalizados</span>
      </div>
    </div>
    <div class="stat-card stat-purple">
      <div class="stat-info">
        <span class="stat-value">$<?= number_format($stats['ingresos_hoy'], 0, ',', '.') ?></span>
        <span class="stat-label">Ingresos del período</span>
      </div>
    </div>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="section-card">
    <div class="section-card-header">
      <h3>Registros (<?= count($registros) ?>)</h3>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Placa</th>
            <th>Tipo</th>
            <th>Ingreso</th>
            <th>Salida</th>
            <th>Duración</th>
            <th>Total</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($registros)): ?>
            <tr><td colspan="7" class="empty-table">No hay registros en este período</td></tr>
          <?php else: ?>
            <?php foreach ($registros as $r): ?>
              <tr>
                <td class="td-placa"><?= htmlspecialchars($r['placa']) ?></td>
                <td><?= htmlspecialchars($r['tipo_nombre']) ?></td>
                <td class="td-date"><?= htmlspecialchars($r['fecha_entrada_fmt']) ?></td>
                <td class="td-date"><?= htmlspecialchars($r['fecha_salida_fmt']) ?></td>
                <td><?= htmlspecialchars($r['duracion']) ?></td>
                <td class="td-total">
                  <?= $r['total'] !== null ? '$' . number_format($r['total'], 0, ',', '.') : '—' ?>
                </td>
                <td>
                  <?php if ($r['fecha_salida'] === null): ?>
                    <span class="badge badge-active">Activo</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Finalizado</span>
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

<script>
document.querySelector('select[name="periodo"]')?.addEventListener('change', function() {
  const rango = document.getElementById('rango-fechas');
  const rango2 = document.getElementById('rango-fechas-fin');
  if (this.value === 'rango') {
    rango.classList.remove('hidden');
    rango2.classList.remove('hidden');
  } else {
    rango.classList.add('hidden');
    rango2.classList.add('hidden');
  }
});
</script>
