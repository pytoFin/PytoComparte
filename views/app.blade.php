<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <!-- Bootstrap Font Icon CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <!-- archivo css -->
        <link rel="stylesheet" href="../asset/css/styles.css">
        <title>@yield('titulo')</title>
    </head>
    <body style="background-image: url('./fotosJPG/fotografia2-e1537978531632.jpg')">

        @section('cabecera')
        @if(isset($usuario))
        
            <div class="email-icon"></div>
        <div class="float float-end d-inline-flex m-3" style="display: flex; align-items: center;">
            @if($usuario->getFoto_perfil() !== "./asset/fotos_perfil/" && $usuario->getFoto_perfil() !== null&& $usuario->getFoto_perfil() !== null)
            <div class="profile-picture m-2">
                <img src="<?=$usuario->getFoto_perfil()?>" width="25" height="25">
            </div>    
            @else
            <i class="bi bi-person-circle me-2" style="font-size: 1.2em;"></i>
            @endif
            <input type="text" size='10px' value="{{$usuario->getNombre()}}"
                   class="form-control me-2 bg-transparent text-white font-weight-bold" disabled>
            <a href="index.php?logout" class="btn btn-warning mr-2">Salir</a>
        </div>
        
        @endif
        <br><br><br>
        @show 
        <div class="container mt-3">
            <h3 class="text-center mt-3 mb-3">@yield('encabezado')</h3>
            @yield('contenido')
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    @yield('scripts')
</html>
