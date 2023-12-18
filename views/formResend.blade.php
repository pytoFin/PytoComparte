@extends('app')
@section('titulo', "Reenvio Mensaje")

@section('cabecera')  
<div class="float-start ms-5 mt-2">
    <div class="home-icon">
        <a class="text-decoration-none text-dark" href="index.php"><i class="bi bi-house-door"></i></a>
    </div>
</div>

      

@section('contenido')
<div class="container mt-5">
<div class="row">
<div class="col-md-3">
<div class="d-flex flex-column">
    <a class="text-decoration-none text-dark" href="mensajes.php?buzonEnt">Buzón de entrada</a>
    <a class="text-decoration-none text-dark" href="mensajes.php?redactar">Redactar mensaje</a>
<!--    <a class="text-decoration-none text-dark" href="mensajes.php?menEnviados">Mensajes enviados</a> -->
    <a class="text-decoration-none text-dark" href="mensajes.php?papelera">Papelera</a> 

</div>
</div>
    
    
    

    <div class="col-md-9">   

    <form method="POST" action="mensajes.php" name="formResend" novalidate="true">
        <input type="hidden" name="idMsjOrigen" value="{{$msjOrigen->getId()}}">
        <div class="form-group mt-2">
            <label for="asunto">Asunto: </label>
            <input  class="form-control" type="text" id='asunto' name="asunto"
                    value="{{$msjOrigen->getAsunto()}}" disabled>
        </div>
        <div class="form-group mt-2">
            <label for="dest" class="d-block">Destinatari@s: </label>
            <input  class="<?= "form-control" . ((isset($destVacio)) || (isset($destInexis))) ? ($destVacio || $destInexis) ? "is-invalid" : "is-valid" : "" ?>"
                    id='destino' name="destino" value="{{$dest??''}}">
            <div class=" invalid-feedback">
            @if(isset($destVacio)&&$destVacio)
            
            Destinatario no puede estar vacío
            @elseif (isset($destInexis)&&$destInexis)
            Destinatario indicado no existente
            @endif
            </div>
        </div>
        
        
        <div class="form-group mt-2">
            <label for="txt">Texto: </label>
            <textarea  class="form-control" 
                       rows="4" cols="50" id='txt' name="txt" disabled="true"><?= $msjOrigen->getTexto()?></textarea>
                
        </div>
        <div class="form-group mt-3">
            <label for="fMensaje"><i class="bi bi-paperclip"></i></label>
            @if($msjOrigen->getAdjunto() !== "./asset/archivos_mensajes/")
            <p class="mt-2">
               @if (pathinfo($msjOrigen->getAdjunto(), PATHINFO_EXTENSION) == 'pdf')
                    <a href="{{$msjOrigen->getAdjunto()}}" target="_blank">Ver PDF</a>
               @elseif (pathinfo($msjOrigen->getAdjunto(), PATHINFO_EXTENSION) == 'docx')
                     <a href="{{$msjOrigen->getAdjunto()}}" download>Descargar DOCX</a>
               @else
                     <img src="{{$msjOrigen->getAdjunto()}}" width="100" height="100">
               @endif
            </p>
            @endif
        </div>
        <div class="d-flex justify-content-end mt-3">
            <input type="submit" value="Enviar" class="btn float-right btn-success" name="reenvMsj">
        </div>

    </form>

 </div>    
</div>
</div>   
@endsection
@section ('scripts')

@endsection