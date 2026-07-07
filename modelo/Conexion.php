<?php

class Conexion
{
  private static string $url = 'https://onrndhxrurmaeupjvqcy.supabase.co';
  private static string $key = 'sb_publishable_ZWJojvnijYHZ-oOM-dzyDQ_tobwqYP0';

  public static function get(string $tabla, array $params = []): array
  {
    $parts = [];
    foreach ($params as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $v) {
          $parts[] = rawurlencode($key) . '=' . rawurlencode($v);
        }
      } else {
        $parts[] = rawurlencode($key) . '=' . rawurlencode($value);
      }
    }
    $query = implode('&', $parts);
    $path = $tabla . ($query ? '?' . $query : '');
    return self::request('GET', $path);
  }

  public static function post(string $tabla, array $data): array
  {
    return self::request('POST', $tabla, $data);
  }

  public static function patch(string $tabla, string $id, array $data): array
  {
    return self::request('PATCH', "$tabla?id=eq.$id", $data);
  }

  public static function delete(string $tabla, string $id): array
  {
    return self::request('DELETE', "$tabla?id=eq.$id");
  }

  private static function request(string $method, string $path, array $data = []): array
  {
    $url = self::$url . '/rest/v1/' . $path;

    $headers = [
      'apikey: ' . self::$key,
      'Authorization: Bearer ' . self::$key,
      'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($method === 'POST' || $method === 'PATCH') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      $headers[] = 'Prefer: return=representation';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    if ($method === 'PATCH') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    }

    if ($method === 'DELETE') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
      throw new Exception('Error de conexión con Supabase');
    }

    if ($httpCode >= 400) {
      $body = json_decode($response, true);
      $msg = $body['message'] ?? 'Error desconocido';
      throw new Exception($msg);
    }

    $decoded = json_decode($response, true);
    return $decoded ?? [];
  }
}
