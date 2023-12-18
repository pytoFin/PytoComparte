<?php

require "../vendor/autoload.php";

//require "../src/error_handler.php";

use eftec\bladeone\BladeOne;
use App\BD\BD;
use App\Modelo\Usuario;
use App\DAO\UsuarioDao;

session_start();

$views = __DIR__ . '/../views';
$cache = __DIR__ . '/../cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

$bd = BD::getConexion();

$usuarioDao = new UsuarioDao($bd);
$dest;
function validaNombre(String $nombre): bool {
    if (preg_match('/^\w{3,12}$/', $nombre)) {
        return true;
    }
    return false;
}

function validaPass(String $pass): bool {
    if (preg_match('/^(?=.*\W).{6,}$/', $pass))
        return true;
    return false;
}

function validaEmail(String $email): bool {
    if (preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email))
        return true;
    return false;
}
function validaNombre_FPerfil (String $foto):bool{
    $dir_fotosPerfil = "./asset/fotos_perfil";
    $nombresFotos = scandir($dir_fotosPerfil);
    foreach ($nombresFotos as $nomFoto){
        if($nomFoto == $foto)
            return false;
        
    }
    return true;
}
function guardaFoto (String $picture):bool{
    global $dest;
    $ruta = $_FILES['fPerfil']['tmp_name'];
    $dest = "./asset/fotos_perfil/".$picture;
    return move_uploaded_file($ruta, $dest);
}

//si hay un usuario logeado se cierra la sesión
if (isset($_SESSION['usuario'])) {
    session_unset();
    session_destroy();
    setcookie(session_name(), '', 0, '/');
    echo $blade->run("login");
} else if (isset($_POST['usuario'])) {
    $nombreUsuario = trim(filter_input(INPUT_POST, 'usuario', FILTER_UNSAFE_RAW));
    $errorNombre = !( validaNombre($nombreUsuario));
    $nombreRepe = $usuarioDao->existe_nombreUsuario($nombreUsuario);
    $pass = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
    $errorPass = !(validaPass($pass));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
    $errorEmail = ($email !== "") ? (validaEmail($email) ? false : true) : false;
    $foto = trim($_FILES['fPerfil']['name']);
    $errorFoto = ($foto !== "") ? (validaNombre_FPerfil($foto) ? false : true) : false;
    
    $errRegistro = $errorNombre || $errorPass || $errorEmail || $nombreRepe || $errorFoto;
    if(!$errorFoto && !$errRegistro && ($foto != ""))
        guardaFoto($foto);
    if ($errRegistro) {
        echo $blade->run("registro", compact("errorNombre", "errorPass", "errorEmail", "nombreRepe",
                "errRegistro","errorFoto" ,"nombreUsuario", "pass", "email", "foto"));
        die;
    } else {
        if($foto == ""){
            $dest = null;
        }
        $usuario = new Usuario($nombreUsuario,$pass,$email,$dest);
        $usuarioDao->creaUsuario($usuario);
        header('Location:index.php?nuevoUsuario');
 //       echo $blade->run("login");
    }
}