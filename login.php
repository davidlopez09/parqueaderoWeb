<?php
session_start();

if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
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
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="login-form" autocomplete="off">
        <div class="form-group">
          <label for="username">Usuario</label>
          <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
            <button type="button" class="toggle-password" onclick="togglePassword()" tabindex="-1">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-login">Ingresar</button>
      </form>
      <p class="login-footer">Parqueadero v1.0</p>
    </div>
  </div>

  <script>
    function togglePassword() {
      const pwd = document.getElementById('password');
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>
