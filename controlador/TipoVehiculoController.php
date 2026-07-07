<?php
session_start();
require_once __DIR__ . '/AuthController.php';
AuthController::verificarSesion();

require_once __DIR__ . '/../modelo/TipoVehiculo.php';

$accion = $_POST['accion'] ?? '';

try {
  match ($accion) {
    'crear' => (function () {
      $nombre = trim($_POST['nombre'] ?? '');
      $precio = $_POST['precio_hora'] ?? 0;
      if (empty($nombre)) throw new Exception('El nombre es obligatorio');
      if ($precio <= 0) throw new Exception('El precio debe ser mayor a 0');
      TipoVehiculo::crear(['nombre' => $nombre, 'precio_hora' => $precio]);
      $_SESSION['success'] = 'Tipo de vehículo creado';
    })(),
    'editar' => (function () {
      $id = $_POST['id'] ?? '';
      $nombre = trim($_POST['nombre'] ?? '');
      $precio = $_POST['precio_hora'] ?? 0;
      if (empty($id) || empty($nombre)) throw new Exception('Datos incompletos');
      if ($precio <= 0) throw new Exception('El precio debe ser mayor a 0');
      TipoVehiculo::actualizar($id, ['nombre' => $nombre, 'precio_hora' => $precio]);
      $_SESSION['success'] = 'Tipo de vehículo actualizado';
    })(),
    'desactivar' => (function () {
      $id = $_POST['id'] ?? '';
      if (empty($id)) return;
      TipoVehiculo::desactivar($id);
      $_SESSION['success'] = 'Tipo de vehículo desactivado';
    })(),
    'activar' => (function () {
      $id = $_POST['id'] ?? '';
      if (empty($id)) return;
      TipoVehiculo::activar($id);
      $_SESSION['success'] = 'Tipo de vehículo activado';
    })(),
    default => throw new Exception('Acción no válida'),
  };
} catch (Exception $e) {
  $_SESSION['error'] = $e->getMessage();
}

header('Location: ../index.php?page=tipos');
exit;
