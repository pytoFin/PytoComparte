const formulario = document.getElementById('formulario');
const usuario = document.getElementById('usuario');
const email = document.getElementById('email');
const password = document.getElementById('password');
// Función que asigna un mensaje de error a un elemento del formulario.
function validarFormulario() {
  // Validar el campo nombre.
  if (!validarNombre()) {
  return false;
  }
  // Validar el campo apellidos.
  if (!validarApellidos()) {
  return false;
  }
  // Validar el campo email.
  if (!validarEmail()) {
  return false;
  }
  return true;
}
//evento que ejecuta ña funcion validarusuario cunado cambia el foco del campo usuario
usuario.addEventListener("blur", validarUsuario, true);
//evento que ejecuta la funcion validarEmail cunado cambia el foco del campo email
email.addEventListener("blur", validarEmail, true);
//evento que ejecuta la funcion validarPassword cunado cambia el foco del campo password
password.addEventListener("blur", validarPassword, true);
//evento que ejecuta la funcion validarFormulario cuando se envia el formulario
formulario.addEventListener("submit", validarFormulario, true);

function validarUsuario() {
  var patron = /^[a-zA-Z]{3,8}$/;

  usuario.className = "";
  if (patron.test(usuario.value)) {
  return true;
  } else {
  usuario.focus();
  //DEvuelve mensaje de error en el campo usuario
  asignarCampo("errorusuario", "El campo: USUARIO está incorrecto.");
  usuario.className = "error";
  return false;
  }
}


function asignarCampo(id, mensaje) {
  
  usuario.innerHTML = mensaje;
}



function validarEmail() {
  
  var patron = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
  // Eliminamos la clase error asignada al elemento hora.
  email.className = "";
  if (patron.test(email.value)) {
  return true;
  } else {
  asignarCampo("errores", "El campo: E-MAIL está incorrecto.");
  email.focus();
  email.className = "error";
  return false;
  }
}

function validarPassword() {
  var patron = /^[a-zA-Z0-9]{3,10}$/;
  // Eliminamos la clase error asignada al elemento hora.
  password.className = "";
  if (patron.test(password.value)) {
  return true;
  } else {
  asignarCampo("errores", "El campo: PASSWORD está incorrecto.");
  password.focus();
  password.className = "error";
  return false;
  }
}
