<?php

require_once __DIR__ . '/../modelo/Usuario.php';

session_start();

class AuthController
{
  public static function iniciarSesion(): void
  {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
      $_SESSION['error'] = 'Ingrese usuario y contraseña';
      header('Location: login.php');
      exit;
    }

    try {
      $usuario = Usuario::autenticar($username, $password);
    } catch (Exception $e) {
      $_SESSION['error'] = 'Error de conexión con la base de datos';
      header('Location: login.php');
      exit;
    }

    if (!$usuario) {
      $_SESSION['error'] = 'Usuario o contraseña incorrectos';
      header('Location: login.php');
      exit;
    }

    if (!$usuario['activo']) {
      $_SESSION['error'] = 'Usuario desactivado';
      header('Location: login.php');
      exit;
    }

    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

    unset($_SESSION['error']);
    header('Location: index.php');
    exit;
  }

  public static function cerrarSesion(): void
  {
    session_destroy();
    header('Location: login.php');
    exit;
  }

  public static function verificarSesion(): void
  {
    if (!isset($_SESSION['user_id'])) {
      header('Location: login.php');
      exit;
    }
  }
}
