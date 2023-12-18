
// obtener el formulario
const form = document.querySelector('form');

// Agregar un evento de escucha para el evento de envío del formulario
form.addEventListener('submit', function(event) {
  event.preventDefault(); // Prevenir el envío del formulario

  // Obtener los valores de los campos del formulario
  const nombre = document.querySelector('#usuario').value;
  const password1 = document.querySelector('#password').value;
  const password2 = document.querySelector('#password2').value;

  // Validar los campos del formulario
  if (nombre.length < 3 || nombre.length > 8) {
    alert('El campo nombre debe tener entre 3 y 8 caracteres');
    return;
  }

  // Validar los campos del formulario
  if (password1 !== password2) {
    alert('Las contraseñas no coinciden');
    return;
  }

  // Enviar el formulario
  form.submit();
});
