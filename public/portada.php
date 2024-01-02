<?php

require "../vendor/autoload.php";
//require "../src/error_handler.php";

use eftec\bladeone\BladeOne;
use App\BD\BD;
use App\DAO\UsuarioDao;
use App\Modelo\Usuario;
use App\DAO\MaterialDao;
use App\DAO\MensajeDao;
//use App\Modelo\Material;

session_start();

$views = __DIR__ . '/../views';
$cache = __DIR__ . '/../cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

$bd = BD::getConexion();
$usuarioDao = new UsuarioDao($bd);
$materialDao = new MaterialDao($bd);
$mensajeDao = new MensajeDao($bd);

if (isset($_SESSION['usuario']) && $_SESSION['usuario'] instanceof Usuario) {
    $usuario = $_SESSION['usuario'];
 //   $miNombre = $usuario->getNombre();
//    $fotoPer = $usuario->getFoto_perfil();
    $restoUsu = $usuarioDao->recuperaRestoUsuarios($usuario->getNombre());
    $materiales = $materialDao->recuperaMaterialesPorUsuario($usuario->getId());
    if(isset($_REQUEST['mateAdded'])){
        $mateAdded = true;
        echo $blade->run('portada', compact('usuario','restoUsu','materiales','mateAdded'));
        die();
    }
    elseif(isset($_REQUEST['actualizado'])){
        $actualizado = true;
        echo $blade->run('portada', compact('usuario','restoUsu','materiales','actualizado'));
        die();
    }
    elseif (isset ($_REQUEST['eliminaContenido'])) {
        $idEli=$_REQUEST['idEli'];
        $matEli = $materialDao->recuperaMaterialPorId((int)$idEli);
        $fotoEli = $matEli->getFoto();
        $fotoEli !== "./asset/fotos_material/"? unlink($fotoEli):""; //elimina foto almacenada
        $materialDao->eliminaMaterial((int)$idEli);
        
        $materiales = $materialDao->recuperaMaterialesPorUsuario($usuario->getId());
        $eliminado = true;
        echo $blade->run('portada', compact('usuario','restoUsu','materiales','eliminado'));
        die();
                
    }
    elseif (isset ($_REQUEST['eliminaUsuario'])) {   //bton elimina cuenta de la v portada.blade.(a través de confEliminaMat.js)
        $idUsuario = $_REQUEST['idUs'];
        $usuario = $usuarioDao->recuperaUsuarioPorId((int)$idUsuario);
        $mensajeDao->eliminaMsjsUsuario((int)$idUsuario);
        $materialDao->eliminaMaterialesPorUsuario($idUsuario);
        $usuario->getFoto_perfil()!== null? unlink($usuario->getFoto_perfil()):"";
        $usuarioDao->elimina($idUsuario);
        session_unset();
        session_destroy();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
        );
     //       echo $blade->run('login');
     header('location: index.php');
            die();
    }
        echo $blade->run('portada', compact('usuario','restoUsu','materiales'));
        die();
    
} else {
    echo $blade->run('login');
    die();
}    