<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) return;
?>
<!-- Mobile Header Bar -->
<div class="mobile-topbar">
  <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Abrir menú">
    <i class="fas fa-bars"></i>
  </button>
  <div class="mobile-topbar-title">Parqueadero</div>
  <div class="mobile-topbar-user">
    <div class="user-avatar-small"><?= strtoupper(substr($_SESSION['usuario'] ?? 'U', 0, 1)) ?></div>
  </div>
</div>

<!-- Overlay for sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="3" y1="9" x2="21" y2="9"></line>
        <line x1="9" y1="21" x2="9" y2="9"></line>
      </svg>
      <span>Parqueadero</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="<?= BASE_URL ?>/index.php?url=dashboard" class="nav-item <?= ($_GET['url'] ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="fas fa-th-large fa-fw"></i>
      <span>Dashboard</span>
    </a>
    <a href="<?= BASE_URL ?>/index.php?url=tipos" class="nav-item <?= ($_GET['url'] ?? '') === 'tipos' ? 'active' : '' ?>">
      <i class="fas fa-cogs fa-fw"></i>
      <span>Tipos de Vehículo</span>
    </a>
    <a href="<?= BASE_URL ?>/index.php?url=historial" class="nav-item <?= ($_GET['url'] ?? '') === 'historial' ? 'active' : '' ?>">
      <i class="fas fa-history fa-fw"></i>
      <span>Historial</span>
    </a>
    <a href="<?= BASE_URL ?>/index.php?url=usuarios" class="nav-item <?= ($_GET['url'] ?? '') === 'usuarios' ? 'active' : '' ?>">
      <i class="fas fa-users fa-fw"></i>
      <span>Usuarios</span>
    </a>
  </nav>

  <div class="sidebar-user">
    <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario'] ?? 'U', 0, 1)) ?></div>
    <div class="user-info">
      <span class="user-name"><?= htmlspecialchars($_SESSION['usuario'] ?? '') ?></span>
      <span class="user-rol"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></span>
    </div>
    <a href="<?= BASE_URL ?>/index.php?url=logout" class="btn-logout" title="Cerrar sesión">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('mobileMenuBtn');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if(btn && sidebar && overlay) {
    btn.addEventListener('click', function() {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', function() {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }
});
</script>
