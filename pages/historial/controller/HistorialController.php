<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../../../modelo/Parqueo.php';
require_once __DIR__ . '/../../../app/AjaxResponse.php';

$isAjax = !empty($_POST['accion']);

if ($isAjax) {
  $accion = $_POST['accion'];
  try {
    match ($accion) {
      'listar' => (function () {
        $filtroFecha = $_POST['periodo'] ?? 'hoy';
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        $filtroPlaca = $_POST['placa'] ?? '';
        $filtroEstado = $_POST['estado'] ?? '';
        $filtroTipo = $_POST['tipo_vehiculo_id'] ?? '';

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
        if ($filtroTipo) $filtros['tipo_vehiculo_id'] = $filtroTipo;

        $registros = Parqueo::listarConTipo($filtros);
        $stats = Parqueo::estadisticas($filtros);

        echo json_encode([
          'registros' => $registros,
          'stats' => $stats,
        ]);
        exit;
      })(),
      default => throw new Exception('Acción no válida'),
    };
  } catch (Exception $e) {
    ajax_respond(false, $e->getMessage());
  }
  exit;
}

try {
  $registros = Parqueo::listarConTipo();
  $stats = Parqueo::estadisticas();
} catch (Exception $e) {
  $registros = [];
  $stats = ['activos' => 0, 'finalizados' => 0, 'total_ingresos' => 0, 'ingresos_hoy' => 0];
  $error = $e->getMessage();
}

try {
  $tipos = Conexion::get('tipos_vehiculo', ['select' => '*', 'order' => 'nombre.asc']);
} catch (Exception $e) {
  $tipos = [];
}
require_once __DIR__ . '/../view/historial_view.php';
