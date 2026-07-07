<?php

require_once __DIR__ . '/Conexion.php';

class TipoVehiculo
{
  public static function listar(): array
  {
    return Conexion::get('tipos_vehiculo', [
      'select' => '*',
      'order' => 'nombre.asc',
    ]);
  }

  public static function listarActivos(): array
  {
    return Conexion::get('tipos_vehiculo', [
      'select' => '*',
      'activo' => 'eq.true',
      'order' => 'nombre.asc',
    ]);
  }

  public static function obtener(string $id): ?array
  {
    $res = Conexion::get('tipos_vehiculo', [
      'id' => 'eq.' . $id,
      'select' => '*',
      'limit' => '1',
    ]);
    return $res[0] ?? null;
  }

  public static function crear(array $data): array
  {
    return Conexion::post('tipos_vehiculo', [
      'nombre' => $data['nombre'],
      'precio_hora' => (float) $data['precio_hora'],
      'activo' => true,
    ]);
  }

  public static function actualizar(string $id, array $data): array
  {
    $update = [];
    if (isset($data['nombre'])) $update['nombre'] = $data['nombre'];
    if (isset($data['precio_hora'])) $update['precio_hora'] = (float) $data['precio_hora'];
    if (isset($data['activo'])) $update['activo'] = $data['activo'] === 'true' || $data['activo'] === true;
    return Conexion::patch('tipos_vehiculo', $id, $update);
  }

  public static function desactivar(string $id): array
  {
    return Conexion::patch('tipos_vehiculo', $id, ['activo' => false]);
  }

  public static function activar(string $id): array
  {
    return Conexion::patch('tipos_vehiculo', $id, ['activo' => true]);
  }

  public static function eliminar(string $id): array
  {
    return Conexion::delete('tipos_vehiculo', $id);
  }
}
