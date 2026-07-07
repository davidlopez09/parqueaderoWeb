function editarTipo(id, nombre, precio) {
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-nombre').value = nombre;
  document.getElementById('edit-precio').value = precio;
  document.getElementById('modal-editar-tipo').style.display = 'flex';
}

function editarUsuario(id, nombre, username, rol) {
  document.getElementById('edit-user-id').value = id;
  document.getElementById('edit-user-nombre').value = nombre;
  document.getElementById('edit-user-username').value = username;
  document.getElementById('edit-user-rol').value = rol;
  document.getElementById('modal-editar-usuario').style.display = 'flex';
}

function cerrarModal(id) {
  document.getElementById(id).style.display = 'none';
}

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.style.display = 'none';
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      alert.style.opacity = '0';
      setTimeout(function () {
        alert.remove();
      }, 300);
    }, 4000);
  });
});
