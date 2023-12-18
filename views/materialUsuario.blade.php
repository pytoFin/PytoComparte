@extends('app')
@section('titulo', "material de usuario")

@section('cabecera')  
<div class="float-start ms-5 mt-2">
    <div class="home-icon">
        <a class="text-decoration-none text-dark" href="index.php"><i class="bi bi-house-door"></i></a>
    </div>
</div>

      

@section('contenido')
<div class="d-flex justify-content-center m-5">
    @if($usuSolicitado->getFoto_perfil() !== './asset/fotos_perfil/' &&$usuSolicitado->getFoto_perfil() !== null)
    <h3>Contenido compartido por <img src="{{ $usuSolicitado->getFoto_perfil() }}" width="25" height="25">{{ $usuSolicitado->getNombre() }}</h3>
    @else
        <h3>Contenido compartido por <i class="bi bi-person-circle me-2" style="font-size: 1.2em;"></i>{{ $usuSolicitado->getNombre() }}</h3>
    @endif    
</div>

    
    
    

    <div class="d-flex justify-content-center">
    
        
        <table class="table table-striped table-dark">
    <thead>
        <tr>
            <th scope="col" class="text-center">Imagen del contenido</th>
            <th scope="col" class="text-center">Descripción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($matsUsuario as $matUsu)
        <tr class="text-center">
           <td><img src="<?=$matUsu->getFoto()?>" width="100" height="100"></td>
           <td> {{$matUsu->getDescripcion()}}</td>
        </tr>
        @empty
    <tr>
            <td colspan="1" class="text-center">Todavía no ha publicado ningún contenido</td>
        </tr>
        @endforelse
    </tbody>
</table>
    </div>    
    
@endsection
@section ('scripts')
@endsection