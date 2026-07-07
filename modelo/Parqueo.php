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

  public static function ingresosPorDia(int $dias = 7): array
  {
    $fin = new DateTime('now', new DateTimeZone('UTC'));
    $inicio = (clone $fin)->modify("-{$dias} days");
    $filtros = [
      'fecha_inicio' => $inicio->format('Y-m-d') . 'T00:00:00',
      'fecha_fin' => $fin->format('Y-m-d') . 'T23:59:59',
    ];
    $registros = self::listar($filtros);

    $diasData = [];
    for ($i = $dias; $i >= 0; $i--) {
      $f = (clone $fin)->modify("-{$i} days")->format('Y-m-d');
      $diasData[$f] = 0;
    }

    foreach ($registros as $r) {
      if (!$r['fecha_salida']) continue;
      $fecha = (new DateTime($r['fecha_salida']))->format('Y-m-d');
      if (isset($diasData[$fecha])) {
        $diasData[$fecha] += (float) ($r['total'] ?? 0);
      }
    }

    $labels = [];
    $values = [];
    foreach ($diasData as $f => $v) {
      $labels[] = (new DateTime($f))->format('d/m');
      $values[] = $v;
    }

    return ['labels' => $labels, 'values' => $values];
  }

  public static function ingresosPorMes(int $meses = 6): array
  {
    $fin = new DateTime('now', new DateTimeZone('UTC'));
    $inicio = (clone $fin)->modify("-{$meses} months");
    $filtros = [
      'fecha_inicio' => $inicio->format('Y-m-d') . 'T00:00:00',
      'fecha_fin' => $fin->format('Y-m-d') . 'T23:59:59',
    ];
    $registros = self::listar($filtros);

    $mesesData = [];
    for ($i = $meses; $i >= 0; $i--) {
      $f = (clone $fin)->modify("-{$i} months")->format('Y-m');
      $mesesData[$f] = 0;
    }

    foreach ($registros as $r) {
      if (!$r['fecha_salida']) continue;
      $fecha = (new DateTime($r['fecha_salida']))->format('Y-m');
      if (isset($mesesData[$fecha])) {
        $mesesData[$fecha] += (float) ($r['total'] ?? 0);
      }
    }

    $labels = [];
    $values = [];
    foreach ($mesesData as $f => $v) {
      $labels[] = (new DateTime($f . '-01'))->format('M Y');
      $values[] = $v;
    }

    return ['labels' => $labels, 'values' => $values];
  }

  public static function conteoPorTipo(): array
  {
    $registros = self::listar();
    $tipos = Conexion::get('tipos_vehiculo', ['select' => '*']);
    $mapaTipos = [];
    foreach ($tipos as $t) {
      $mapaTipos[$t['id']] = $t['nombre'];
    }

    $conteo = [];
    foreach ($registros as $r) {
      $tipo = $mapaTipos[$r['tipo_vehiculo_id']] ?? 'Desconocido';
      if (!isset($conteo[$tipo])) $conteo[$tipo] = 0;
      $conteo[$tipo]++;
    }

    $labels = array_keys($conteo);
    $values = array_values($conteo);
    $colors = ['#1a73e8', '#2e7d32', '#f9a825', '#7b1fa2', '#e65100', '#00acc1'];

    return ['labels' => $labels, 'values' => $values, 'colors' => array_slice($colors, 0, count($labels))];
  }
}
