<?php
require_once __DIR__ . '/../modelo/Parqueo.php';
try {
  $stats = Parqueo::estadisticas();
} catch (Exception $e) {
  $stats = ['activos' => 0, 'finalizados' => 0, 'total_ingresos' => 0, 'ingresos_hoy' => 0];
  $error = 'Error al cargar estadísticas: ' . $e->getMessage();
}
?>
<div class="page-container">
  <div class="page-header">
    <h2>Dashboard</h2>
    <p class="page-subtitle">Resumen general del parqueadero</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card stat-blue">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= $stats['activos'] ?></span>
        <span class="stat-label">Vehículos activos</span>
      </div>
    </div>

    <div class="stat-card stat-green">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value"><?= $stats['finalizados'] ?></span>
        <span class="stat-label">Finalizados hoy</span>
      </div>
    </div>

    <div class="stat-card stat-purple">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value">$<?= number_format($stats['ingresos_hoy'], 0, ',', '.') ?></span>
        <span class="stat-label">Ingresos hoy</span>
      </div>
    </div>

    <div class="stat-card stat-orange">
      <div class="stat-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
      </div>
      <div class="stat-info">
        <span class="stat-value">$<?= number_format($stats['total_ingresos'], 0, ',', '.') ?></span>
        <span class="stat-label">Ingresos totales</span>
      </div>
    </div>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
</div>
