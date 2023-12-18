const usuario = document.querySelector("usuario");
const password = document.querySelector("password");
const email = document.querySelector("email");
const perfil = document.querySelector("fPerfil");

const setErrors = (message, field, isError = true) => {
  if (isError) {
    field.classList.add("invalid");
    field.nextElementSibling.classList.add("error");
    field.nextElementSibling.innerText = message;
  } else {
    field.classList.remove("invalid");
    field.nextElementSibling.classList.remove("error");
    field.nextElementSibling.innerText = "";
  }
}

const validarUsuario = (message, e) => {
  const field = e.target;
  const fieldValue = e.target.value;
    const regex = new RegExp(/^[a-zA-Z]{3,8}$/);
  if (fieldValue.trim().length > 0 && !regex.test(fieldValue)) {
    setErrors(message, field);
  } else {
    setErrors("", field, false);
  }
}
const validarPassword = (message, e) => {
    const field = e.target;
    const fieldValue = e.target.value;
    const regex = new RegExp(/^[a-zA-Z0-9]{3,10}$/);
    if (fieldValue.trim().length > 0 && !regex.test(fieldValue)) {
        setErrors(message, field);
    } else {
        setErrors("", field, false);
    }
    }

const validarEmail = e => {
  const field = e.target;
  const fieldValue = e.target.value;
  const regex = new RegExp(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/);
  if (fieldValue.trim().length > 5 && !regex.test(fieldValue)) {
    setErrors("Please enter a valid email", field);
  } else {
    setErrors("", field, false);
  }
}

usuario.addEventListener("blur", (e) => validarUsuario("El usuario tiene que tener una longitud entre 3 y 8", e));
password.addEventListener("blur", (e) => validarPassword("Write your password", e));
email.addEventListener("blur", (e) => validateEmptyField("Please provide an email", e));

email.addEventListener("input", validateEmailFormat);

fPerfil.addEventListener("change", (e) => {
  const field = e.target;
  const fileExt = e.target.files[0].name.split(".").pop().toLowerCase();
  const allowedExt = ["jpg", "jpeg", "png", "gif"];
  if (!allowedExt.includes(fileExt)) {
    setErrors(`The only extensions allowed are ${allowedExt.join(", ")}`, field);
  } else {
    setErrors("", field, false);
  }
});