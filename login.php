<?php
session_start();

if (isset($_SESSION['user_id'])) {
  header('Location: index.php?url=dashboard');
  exit;
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/controlador/AuthController.php';
  AuthController::iniciarSesion();
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parqueadero - Iniciar Sesión</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="public/css/global.css">
</head>
<body class="login-body">
  <div class="login-container">
    <div class="login-card">
      <div class="login-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="3" y1="9" x2="21" y2="9"></line>
          <line x1="9" y1="21" x2="9" y2="9"></line>
        </svg>
      </div>
      <h1 class="login-title">Parqueadero</h1>
      <p class="login-subtitle">Panel de administración</p>

      <?php if ($error): ?>
        <div class="alert alert-danger text-center p-2 mb-3 small"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="login-form" autocomplete="off" data-no-ajax>
        <div class="form-group">
          <label for="username">Usuario</label>
          <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
        </div>
        <button type="submit" class="btn-login">Ingresar</button>
      </form>
      <p class="login-footer">Parqueadero v1.0</p>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="public/js/core/ajax-forms.js"></script>
</body>
</html>
