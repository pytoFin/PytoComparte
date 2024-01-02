@extends('app')
@section('titulo', "mensajes enviados")

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
          <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Mensajes Enviados</h3>
                

          </div>
        
        <table class="table table-striped table-dark">
    <thead>
        <tr>
            <th scope="col" class="text-center">Destinatari@</th>
            <th scope="col" class="text-center">Asunto</th>
            <th scope="col" class="text-center">Fecha de envío</th>
            <th scope="col" class="text-center">Fichero adjunto</th>
        </tr>
    </thead>
    <tbody>
        @forelse($msjsEnviados as $msjUsu)
        <tr class="text-center">
           <td>{{$msjUsu->getNomDesti()}}</td>
           <td><a href="mensajes.php?msjEnvShow&idMsj={{$msjUsu->getId()}}" class="text-decoration-none text-white">{{$msjUsu->getAsunto()}}</a></td>
           <td> {{$msjUsu->getFechaEnvio()}}</td>
           @if($msjUsu->getAdjunto()!== "./asset/archivos_mensajes/")
           <td><i class="bi bi-paperclip"></i></td>
           @else
           <td></td>
           @endif
        </tr>
        @empty
    <tr>
            <td colspan="4" class="text-center">no has enviado mensajes todavía</td>
        </tr>
        @endforelse
    </tbody>
</table>
    </div>    
</div>
</div>
@endsection
@section ('scripts')
@endsection