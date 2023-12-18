@extends('app')
@section('titulo', "Portada")

@section('cabecera')  
<div class="float-start ms-5 mt-2">
    <div class="email-icon">
        <a class="text-decoration-none text-dark" href="mensajes.php"><i class="bi bi-envelope h1"></i></a>
    </div>
</div>

      

@section('contenido')
@if(isset($actualizado)&&$actualizado)
<div class="alert alert-success mt-3">
    <p>contenido actualizado</p>
</div>
@endif
@if(isset($eliminado)&&$eliminado)
<div class="alert alert-success mt-3">
    <p>contenido eliminado</p>
</div>
@endif
<div class="row m-5">
    <div class="col-md-6">
<table class="table table-striped table-dark">
    <thead>
        <tr>
            <th scope="col" class="text-center">Usuarios Registrados</th>
            
        </tr>
    </thead>
    <tbody>
        @forelse($restoUsu as $usu)
        <tr class="text-center">
           <td><img src="{{ $usu->getFoto_perfil() !== null ? $usu->getFoto_perfil() : './asset/fotos_perfil/'}}" width="100" height="100"></td>
           <td><a href="materiales.php?matUsu&idUsu=<?=$usu->getId()?>" class="text-decoration-none text-white">{{$usu->getNombre()}}</a></td>
            
        </tr>
        @empty
    <tr>
            <td colspan="1" class="text-center">Todavía no hay usuarios registrados</td>
        </tr>
        @endforelse
    </tbody>
</table>
    </div>
    

    <div class="col-md-6">
    <a href="materiales.php" class="btn btn-success mb-3">Nuevo Material</a>
        
        <table class="table table-striped table-dark">
    <thead>
        <tr>
            <th scope="col" class="text-center">Mis Publicaciones</th>
            
        </tr>
    </thead>
    <tbody>
        @forelse($materiales as $material)
        <tr class="text-center">
           <td><img src="<?=$material->getFoto()?>" width="100" height="100"></td>
           <td><a href="materiales.php?edicion&id=<?=$material->getId()?>" class="text-decoration-none text-white">{{$material->getDescripcion()}}</a></td>
           <td><button data-material="{{$material->getId()}}" name="borraCont" id="borraCont" ><i class="bi bi-trash"></i></button></td> 
        </tr>
        @empty
    <tr>
            <td colspan="1" class="text-center">Todavía no has publicado nigún contenido</td>
        </tr>
        @endforelse
    </tbody>
</table>
    </div>    

</div>    
@endsection
@section ('scripts')
<script src="./js/confEliminaMat.js"></script>
@endsection