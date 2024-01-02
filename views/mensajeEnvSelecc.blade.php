@extends('app')
@section('titulo', "lectura de mensaje enviado")

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
        <div class="card">
        <div class="card-header">Detalles del mensaje enviado</div>
        <div class="card-body">
            <h5 class="card-title">Asunto: {{ $msjSelecc->getAsunto() }}</h5>
            <p class="card-text">Para: {{ $msjSelecc->getNomDesti() }}</p>
            <p class="card-text">Fecha: {{ $msjSelecc->getFechaEnvio() }}</p>
            <p class="card-text">Contenido: {{ $msjSelecc->getTexto() }}</p>
            <p class="card-text">fichero Adjunto:
                @if($msjSelecc->getAdjunto() !== "./asset/archivos_enviados/")
                <img src="<?=$msjSelecc->getAdjunto()?>" width="100" height="100">
                @endif
            </p>
            <!-- eliminar msj enviado -->
            <button data-enviado="{{$msjSelecc->getId()}}" name="borraEnv" id="borraEnv" ><i class="bi bi-trash"></i></button>
        </div>
    </div>
        @if(isset($msjsHilo))
        <h5 class="mt-2">Histórico de mensajes</h5>
            @foreach(array_reverse($msjsHilo) as $msjHilo)
           
            @if($msjHilo !== null)
            <p class="card-text">De: {{ $msjHilo->getNomRemi() }}</p>
            <p class="card-text">Fecha: {{ $msjHilo->getFechaEnvio() }}</p>
            <p class="card-text">Contenido: {{ $msjHilo->getTexto() }}</p>
            <p class="card-text">fichero Adjunto:
                @if($msjHilo->getAdjunto() !== "./asset/archivos_mensajes/")
                <img src="<?=$msjHilo->getAdjunto()?>" width="100" height="100">
                @endif
            </p>
            @else
            <p class="card-title">Mensaje eliminado por su remitente</p>
            @endif
            <hr>
            @endforeach
        @endif
        
    </div>    
</div>
</div>
@endsection
@section ('scripts')
<script src="./js/confEliminaEnv.js"></script>
@endsection