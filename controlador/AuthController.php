<?php

require_once __DIR__ . '/../modelo/Usuario.php';

if (session_status() === PHP_SESSION_NONE) session_start();

class AuthController
{
  public static function iniciarSesion(): void
  {
    $usuario = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($usuario) || empty($password)) {
      $_SESSION['error'] = 'Ingrese usuario y contraseña';
      header('Location: login.php');
      exit;
    }

    try {
      $user = Usuario::autenticar($usuario, $password);
    } catch (Exception $e) {
      $_SESSION['error'] = 'Error de conexión con la base de datos';
      header('Location: login.php');
      exit;
    }

    if (!$user) {
      $_SESSION['error'] = 'Usuario o contraseña incorrectos';
      header('Location: login.php');
      exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['nombre'] = $user['usuario'];
    $_SESSION['rol'] = 'admin';

    unset($_SESSION['error']);
    header('Location: index.php?url=dashboard');
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
