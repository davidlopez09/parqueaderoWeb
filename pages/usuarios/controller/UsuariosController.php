<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../../../modelo/Usuario.php';
require_once __DIR__ . '/../../../app/AjaxResponse.php';

$isAjax = !empty($_POST['accion']);

if ($isAjax) {
  $accion = $_POST['accion'];
  try {
    match ($accion) {
      'listar' => (function () {
        $usuarios = Usuario::listar();
        echo json_encode($usuarios);
        exit;
      })(),
      'crear' => (function () {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        if (empty($usuario) || empty($password)) {
          throw new Exception('Usuario y contraseña son obligatorios');
        }
        Usuario::crear(['usuario' => $usuario, 'password' => $password]);
        ajax_respond(true, 'Usuario creado');
      })(),
      'editar' => (function () {
        $id = $_POST['id'] ?? '';
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        if (empty($id) || empty($usuario)) throw new Exception('Datos incompletos');
        $data = ['usuario' => $usuario];
        if (!empty($password)) $data['password'] = $password;
        Usuario::actualizar($id, $data);
        ajax_respond(true, 'Usuario actualizado');
      })(),
      'eliminar' => (function () {
        $id = $_POST['id'] ?? '';
        if (empty($id)) throw new Exception('ID no proporcionado');
        if ($id === $_SESSION['user_id']) throw new Exception('No puede eliminarse a sí mismo');
        Usuario::eliminar($id);
        ajax_respond(true, 'Usuario eliminado');
      })(),
      default => throw new Exception('Acción no válida'),
    };
  } catch (Exception $e) {
    ajax_respond(false, $e->getMessage());
  }
  exit;
}

try {
  $usuarios = Usuario::listar();
} catch (Exception $e) {
  $usuarios = [];
  $error = $e->getMessage();
}
require_once __DIR__ . '/../view/usuarios_view.php';
