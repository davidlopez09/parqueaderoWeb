<?php

require_once __DIR__ . '/Conexion.php';

class Usuario
{
  public static function autenticar(string $username, string $password): ?array
  {
    $usuarios = Conexion::get('usuarios', [
      'username' => 'eq.' . $username,
      'select' => '*',
      'limit' => '1',
    ]);

    if (empty($usuarios)) return null;

    $user = $usuarios[0];

    if (!password_verify($password, $user['password_hash'])) return null;

    return $user;
  }

  public static function listar(): array
  {
    return Conexion::get('usuarios', [
      'select' => '*',
      'order' => 'nombre.asc',
    ]);
  }

  public static function obtener(string $id): ?array
  {
    $res = Conexion::get('usuarios', [
      'id' => 'eq.' . $id,
      'select' => '*',
      'limit' => '1',
    ]);
    return $res[0] ?? null;
  }

  public static function crear(array $data): array
  {
    $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
    unset($data['password']);
    return Conexion::post('usuarios', $data);
  }

  public static function actualizar(string $id, array $data): array
  {
    if (isset($data['password'])) {
      $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
      unset($data['password']);
    }
    return Conexion::patch('usuarios', $id, $data);
  }

  public static function eliminar(string $id): array
  {
    return Conexion::delete('usuarios', $id);
  }
}
