
function confirmaBorrado() {
    var id = this.getAttribute('data-material');
    if(confirm('¿Borrar contenido seleccionado?')){
     window.location.href = 'portada.php?eliminaContenido&idEli='+id;   
    }
};
function eliminarCuenta() {
     var idUsuario = document.getElementById('eliminarCuenta').getAttribute('data-id-usuario');
    if (confirm('¿Estás seguro de que quieres eliminar tu cuenta?')) {
        window.location.href = 'portada.php?eliminaUsuario&idUs='+idUsuario;
    }
}
document.addEventListener('DOMContentLoaded', function(){
    var buttons = document.querySelectorAll('button[data-material]');
    buttons.forEach(function(button){
        button.addEventListener('click',confirmaBorrado);
    })
    // Manejar botón de eliminar cuenta
    var botonEliminarCuenta = document.getElementById('eliminarCuenta');
    if (botonEliminarCuenta) {
        botonEliminarCuenta.addEventListener('click', eliminarCuenta);
    }
});