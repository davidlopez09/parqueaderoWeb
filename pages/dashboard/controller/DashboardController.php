<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../../../modelo/Parqueo.php';

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

require_once __DIR__ . '/../view/dashboard_view.php';
