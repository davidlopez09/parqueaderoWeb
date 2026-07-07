<?php
session_start();
require_once __DIR__ . '/AuthController.php';
AuthController::verificarSesion();

require_once __DIR__ . '/../modelo/Usuario.php';

$accion = $_POST['accion'] ?? '';

try {
  match ($accion) {
    'crear' => (function () {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';
      $nombre = trim($_POST['nombre'] ?? '');
      $rol = $_POST['rol'] ?? 'operador';
      if (empty($username) || empty($password) || empty($nombre)) {
        throw new Exception('Todos los campos son obligatorios');
      }
      Usuario::crear(['username' => $username, 'password' => $password, 'nombre' => $nombre, 'rol' => $rol]);
      $_SESSION['success'] = 'Usuario creado';
    })(),
    'editar' => (function () {
      $id = $_POST['id'] ?? '';
      $username = trim($_POST['username'] ?? '');
      $nombre = trim($_POST['nombre'] ?? '');
      $rol = $_POST['rol'] ?? 'operador';
      $password = $_POST['password'] ?? '';
      if (empty($id) || empty($username) || empty($nombre)) {
        throw new Exception('Datos incompletos');
      }
      $data = ['username' => $username, 'nombre' => $nombre, 'rol' => $rol];
      if (!empty($password)) $data['password'] = $password;
      Usuario::actualizar($id, $data);
      $_SESSION['success'] = 'Usuario actualizado';
    })(),
    'eliminar' => (function () {
      $id = $_POST['id'] ?? '';
      if (empty($id)) return;
      if ($id === $_SESSION['user_id']) throw new Exception('No puede eliminarse a sí mismo');
      Usuario::eliminar($id);
      $_SESSION['success'] = 'Usuario eliminado';
    })(),
    default => throw new Exception('Acción no válida'),
  };
} catch (Exception $e) {
  $_SESSION['error'] = $e->getMessage();
}

header('Location: ../index.php?page=usuarios');
exit;
