@extends('app')
@section('titulo', "Respuesta Mensaje")

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
    <a class="text-decoration-none text-dark" href="mensajes.php?menEnviados">Mensajes enviados</a> 
    <a class="text-decoration-none text-dark" href="mensajes.php?papelera">Papelera</a> 

</div>
</div>
    
    
    

    <div class="col-md-9">   

    <form method="POST" action="mensajes.php" name="formResponse" enctype="multipart/form-data" novalidate="true">
        <div class="form-group mt-2">
            <label for="asunto">Asunto: </label>
            <input  class="form-control" type="text" id='asunto' name="asunto"
                    value="{{$msjOrigen->getAsunto()}}" disabled>
        </div>
        <div class="form-group mt-2">
            <label for="dest" class="d-block">Destinatari@: </label>
            <input  class="form-control" type="text"
                    id='dest' name="dest" value="{{$msjOrigen->getNomRemi()}}" disabled="true">
            
        </div>
        
        
        <div class="form-group mt-2">
            <label for="txt">Texto: </label>
            <textarea  class="form-control" 
                       rows="4" cols="50" id='txt' name="txt"></textarea>
                
        </div>
        <input type="hidden" name="msjOrigen" value="{{base64_encode(serialize($msjOrigen))}}">
        <div class="form-group mt-2">
            <label for="fResMensaje"><i class="bi bi-paperclip"></i></label>
            <input type="file" class="form-control mt-2" id="fResMensaje" name="fResMensaje">
        </div>
        <div class="d-flex justify-content-end mt-3">
            <input type="submit" value="Enviar" class="btn float-right btn-success" name="envResMsj">
        </div>

    </form>

 </div>    
</div>
</div>   
@endsection
@section ('scripts')

@endsection
