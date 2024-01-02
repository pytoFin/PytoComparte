
function confirmaBorrado() {
    var id = this.getAttribute('data-enviado');
    if(confirm('¿Borrar mensaje seleccionado?')){
     window.location.href = 'mensajes.php?eliminaMensajeEnviado&idEnv='+id;   
    }
};
document.addEventListener('DOMContentLoaded', function(){
    var buttons = document.querySelectorAll('button[data-enviado]');
    buttons.forEach(function(button){
        button.addEventListener('click',confirmaBorrado);
    })
})