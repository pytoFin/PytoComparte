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
$nuevoUsu ;
$usuarioDao = new UsuarioDao($bd);

if (isset($_SESSION['usuario'])) {
    if (isset($_REQUEST['logout'])) {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
        echo $blade->run("login");
    } else {
        header('Location:portada.php');
    }
} else if (isset($_POST['usuario'])) {
    $nombreUsuario = trim(filter_input(INPUT_POST, 'usuario', FILTER_UNSAFE_RAW));
    $pass = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
    $usuario = $usuarioDao->recuperaPorCredencial($nombreUsuario, $pass);
    $valid = !(is_null($usuario));
    if ($valid) {
        $_SESSION['usuario'] = $usuario;
    }
    $response['login'] = $valid;
    header('Content-type: application/json');
    echo json_encode($response);
    die;
}
else if(isset ($_REQUEST['registro']))
    echo $blade->run("registro");
else {
    if(isset($_REQUEST['nuevoUsuario'])){
        $nuevoUsu= $_REQUEST['nuevoUsuario'];
        echo $blade->run("login", compact('nuevoUsu'));
    } else {
            echo $blade->run("login");
        }    
}
