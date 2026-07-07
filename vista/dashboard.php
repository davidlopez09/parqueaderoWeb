<?php
require_once __DIR__ . '/../modelo/Parqueo.php';
try {
  $stats = Parqueo::estadisticas();
  $ingresosDiarios = Parqueo::ingresosPorDia(7);
  $ingresosMensuales = Parqueo::ingresosPorMes(6);
  $conteoTipos = Parqueo::conteoPorTipo();
} catch (Exception $e) {
  $stats = ['activos' => 0, 'finalizados' => 0, 'total_ingresos' => 0, 'ingresos_hoy' => 0];
  $ingresosDiarios = ['labels' => [], 'values' => []];
  $ingresosMensuales = ['labels' => [], 'values' => []];
  $conteoTipos = ['labels' => [], 'values' => [], 'colors' => []];
  $error = 'Error al cargar datos';
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

  <div class="charts-grid">
    <div class="chart-card">
      <div class="chart-card-header">
        <h3>Ingresos últimos 7 días</h3>
      </div>
      <div class="chart-body">
        <canvas id="chartIngresosDiarios"></canvas>
      </div>
    </div>
    <div class="chart-card">
      <div class="chart-card-header">
        <h3>Ingresos por mes</h3>
      </div>
      <div class="chart-body">
        <canvas id="chartIngresosMensuales"></canvas>
      </div>
    </div>
    <div class="chart-card chart-card-full">
      <div class="chart-card-header">
        <h3>Distribución por tipo de vehículo</h3>
      </div>
      <div class="chart-body chart-body-pie">
        <canvas id="chartTipos"></canvas>
      </div>
    </div>
  </div>

  <?php if (isset($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const colores = ['#1a73e8', '#2e7d32', '#f9a825', '#7b1fa2', '#e65100', '#00acc1'];

new Chart(document.getElementById('chartIngresosDiarios'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($ingresosDiarios['labels']) ?>,
    datasets: [{
      label: 'Ingresos ($)',
      data: <?= json_encode($ingresosDiarios['values']) ?>,
      backgroundColor: 'rgba(26, 115, 232, 0.7)',
      borderColor: '#1a73e8',
      borderWidth: 2,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString('es-CO') } },
      x: { grid: { display: false } }
    }
  }
});

new Chart(document.getElementById('chartIngresosMensuales'), {
  type: 'line',
  data: {
    labels: <?= json_encode($ingresosMensuales['labels']) ?>,
    datasets: [{
      label: 'Ingresos ($)',
      data: <?= json_encode($ingresosMensuales['values']) ?>,
      borderColor: '#2e7d32',
      backgroundColor: 'rgba(46, 125, 50, 0.1)',
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointBackgroundColor: '#2e7d32',
      borderWidth: 3,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString('es-CO') } },
      x: { grid: { display: false } }
    }
  }
});

new Chart(document.getElementById('chartTipos'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($conteoTipos['labels']) ?>,
    datasets: [{
      data: <?= json_encode($conteoTipos['values']) ?>,
      backgroundColor: <?= json_encode($conteoTipos['colors']) ?>,
      borderWidth: 0,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { padding: 16, usePointStyle: true, font: { size: 13 } }
      }
    },
    cutout: '65%',
  }
});
</script>
