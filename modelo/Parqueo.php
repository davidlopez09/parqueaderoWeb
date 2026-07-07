<?php

require_once __DIR__ . '/Conexion.php';

class Parqueo
{
  public static function listar(array $filtros = []): array
  {
    $params = [
      'select' => '*',
      'order' => 'fecha_entrada.desc',
      'limit' => '200',
    ];

    if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
      $params['fecha_entrada'] = ['gte.' . $filtros['fecha_inicio'], 'lte.' . $filtros['fecha_fin']];
    } elseif (!empty($filtros['fecha_inicio'])) {
      $params['fecha_entrada'] = 'gte.' . $filtros['fecha_inicio'];
    } elseif (!empty($filtros['fecha_fin'])) {
      $params['fecha_entrada'] = 'lte.' . $filtros['fecha_fin'];
    }
    if (!empty($filtros['placa'])) {
      $params['placa'] = 'ilike.*' . strtoupper($filtros['placa']) . '*';
    }
    if (!empty($filtros['estado'])) {
      if ($filtros['estado'] === 'activo') {
        $params['fecha_salida'] = 'is.null';
      } elseif ($filtros['estado'] === 'finalizado') {
        $params['fecha_salida'] = 'not.is.null';
      }
    }

    return Conexion::get('parqueos', $params);
  }

  public static function listarConTipo(array $filtros = []): array
  {
    $registros = self::listar($filtros);
    $tipos = Conexion::get('tipos_vehiculo', ['select' => '*']);
    $mapaTipos = [];
    foreach ($tipos as $t) {
      $mapaTipos[$t['id']] = $t['nombre'];
    }

    foreach ($registros as &$r) {
      $r['tipo_nombre'] = $mapaTipos[$r['tipo_vehiculo_id']] ?? 'Desconocido';
      if ($r['fecha_salida']) {
        $entrada = new DateTime($r['fecha_entrada']);
        $salida = new DateTime($r['fecha_salida']);
        $diff = $entrada->diff($salida);
        $r['duracion'] = $diff->h . 'h ' . $diff->i . 'm';
      } else {
        $entrada = new DateTime($r['fecha_entrada']);
        $ahora = new DateTime('now', new DateTimeZone('UTC'));
        $diff = $entrada->diff($ahora);
        $r['duracion'] = $diff->h . 'h ' . $diff->i . 'm';
      }
      $r['fecha_entrada_fmt'] = (new DateTime($r['fecha_entrada']))->format('d/m/Y H:i');
      $r['fecha_salida_fmt'] = $r['fecha_salida']
        ? (new DateTime($r['fecha_salida']))->format('d/m/Y H:i')
        : '—';
    }

    return $registros;
  }

  public static function estadisticas(array $filtros = []): array
  {
    $registros = self::listar($filtros);

    $activos = 0;
    $finalizados = 0;
    $totalIngresos = 0;

    foreach ($registros as $r) {
      if ($r['fecha_salida'] === null) {
        $activos++;
      } else {
        $finalizados++;
        $totalIngresos += (float) ($r['total'] ?? 0);
      }
    }

    $hoy = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d');
    $ingresosHoy = 0;
    foreach ($registros as $r) {
      if ($r['fecha_salida']) {
        $fechaSalida = (new DateTime($r['fecha_salida']))->format('Y-m-d');
        if ($fechaSalida === $hoy) {
          $ingresosHoy += (float) ($r['total'] ?? 0);
        }
      }
    }

    return [
      'activos' => $activos,
      'finalizados' => $finalizados,
      'total_ingresos' => $totalIngresos,
      'ingresos_hoy' => $ingresosHoy,
    ];
  }

  public static function ingresosPorPeriodo(string $tipo, string $fechaInicio = null, string $fechaFin = null): array
  {
    $filtros = [];
    if ($fechaInicio) $filtros['fecha_inicio'] = $fechaInicio;
    if ($fechaFin) $filtros['fecha_fin'] = $fechaFin;

    $registros = self::listar($filtros);
    $ingresos = [];

    foreach ($registros as $r) {
      if (!$r['fecha_salida']) continue;
      $fecha = new DateTime($r['fecha_salida']);
      switch ($tipo) {
        case 'hora':
          $key = $fecha->format('Y-m-d H:00');
          break;
        case 'dia':
          $key = $fecha->format('Y-m-d');
          break;
        case 'semana':
          $key = $fecha->format('Y-\WW');
          break;
        case 'mes':
          $key = $fecha->format('Y-m');
          break;
        case 'anio':
          $key = $fecha->format('Y');
          break;
        default:
          $key = $fecha->format('Y-m-d');
      }
      if (!isset($ingresos[$key])) $ingresos[$key] = 0;
      $ingresos[$key] += (float) ($r['total'] ?? 0);
    }

    return $ingresos;
  }
}
