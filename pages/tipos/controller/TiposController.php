<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../../../modelo/TipoVehiculo.php';
require_once __DIR__ . '/../../../app/AjaxResponse.php';

$isAjax = !empty($_POST['accion']);

if ($isAjax) {
  $accion = $_POST['accion'];
  try {
    match ($accion) {
      'listar' => (function () {
        $tipos = TipoVehiculo::listar();
        echo json_encode($tipos);
        exit;
      })(),
      'crear' => (function () {
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = $_POST['precio_hora'] ?? 0;
        if (empty($nombre)) throw new Exception('El nombre es obligatorio');
        if ($precio <= 0) throw new Exception('El precio debe ser mayor a 0');
        TipoVehiculo::crear(['nombre' => $nombre, 'precio_hora' => $precio]);
        ajax_respond(true, 'Tipo de vehículo creado');
      })(),
      'editar' => (function () {
        $id = $_POST['id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = $_POST['precio_hora'] ?? 0;
        if (empty($id) || empty($nombre)) throw new Exception('Datos incompletos');
        if ($precio <= 0) throw new Exception('El precio debe ser mayor a 0');
        TipoVehiculo::actualizar($id, ['nombre' => $nombre, 'precio_hora' => $precio]);
        ajax_respond(true, 'Tipo de vehículo actualizado');
      })(),
      'eliminar' => (function () {
        $id = $_POST['id'] ?? '';
        if (empty($id)) throw new Exception('ID no proporcionado');
        TipoVehiculo::eliminar($id);
        ajax_respond(true, 'Tipo de vehículo eliminado');
      })(),
      default => throw new Exception('Acción no válida'),
    };
  } catch (Exception $e) {
    ajax_respond(false, $e->getMessage());
  }
  exit;
}

try {
  $tipos = TipoVehiculo::listar();
} catch (Exception $e) {
  $tipos = [];
  $error = $e->getMessage();
}
require_once __DIR__ . '/../view/tipos_view.php';
