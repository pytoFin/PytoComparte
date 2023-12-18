<?php

require "../vendor/autoload.php";

//require "../src/error_handler.php";

use eftec\bladeone\BladeOne;
use App\BD\BD;
use App\Modelo\Material;
use App\DAO\MaterialDao;
use App\Modelo\Usuario;
use App\DAO\UsuarioDao;
use App\Modelo\Mensaje;
use App\DAO\MensajeDao;

session_start();

$views = __DIR__ . '/../views';
$cache = __DIR__ . '/../cache';
$blade = new BladeOne($views, $cache, BladeOne::MODE_DEBUG);

$bd = BD::getConexion();

$materialDao = new MaterialDao($bd);
$usuarioDao = new UsuarioDao($bd);
$mensajeDao = new MensajeDao($bd);
$dest  ; $msjsHilo;

function guardaFMsj (String $picture):bool{
    global $dest;
    $ruta = $_FILES['fMensaje']['tmp_name'];
    $dest = "./asset/archivos_mensajes/".$picture;
    return move_uploaded_file($ruta, $dest);
}
function guardaFResMsj (String $picture):bool{
    global $dest;
    $ruta = $_FILES['fResMensaje']['tmp_name'];
    $dest = "./asset/archivos_mensajes/".$picture;
    return move_uploaded_file($ruta, $dest);
}
function getMensajesHilo (object $msj): Array{
    global $mensajeDao;
    $hilo = $msj->getHilo();
    $idsMsjsHilo = explode(",",$hilo);
    $msjsHilo = [];
    foreach ($idsMsjsHilo as $idMsjHilo){
        $msjsHilo[]= $mensajeDao->recuperaMensajePorId((int)$idMsjHilo);
    }
    return $msjsHilo;
}
function actualizaMsjsUsuario(int $idUsuario):Array{
    global $mensajeDao;
    $msjsUsuario = $mensajeDao->recuperaMensajesPorUsuario($idUsuario);
    //formateando la fecha de envío de cada msj:
    foreach ($msjsUsuario as $msjUsu){
        $fechaRaw = new DateTime($msjUsu->getFechaEnvio());
        $fechaFormateada = $fechaRaw->format('d/m/y H:i:s');
        $msjUsu->setFechaEnvio($fechaFormateada);
    }
    return $msjsUsuario;
}
function actualizaMjsPapelera(int $idUsuario):Array{
    global $mensajeDao;
    $msjsPapelera = $mensajeDao->recuperaMsjsPorUsuarioPape($idUsuario);
    foreach ($msjsPapelera as $msjUsu){
        $fechaRaw = new DateTime($msjUsu->getFechaEnvio());
        $fechaFormateada = $fechaRaw->format('d/m/y H:i:s');
        $msjUsu->setFechaEnvio($fechaFormateada);
    }
    return  $msjsPapelera;
}

if(isset($_SESSION['usuario'])){
    $usuario =$_SESSION['usuario'];
    $msjsUsuario = actualizaMsjsUsuario((int)$usuario->getId());
    
    
  if (isset($_REQUEST['redactar'])) {   // si se ha pulsado el enlace redactar mensaje desde v mensajesUsuario.blade
        
        echo $blade->run("formMensaje", compact('usuario'));
        die;
    }
    else if (isset ($_POST['envMsj'])) { //pulsado el btn enviar del formulario de msj en v. formMensaje.blade
    $asunto = trim(filter_input(INPUT_POST, 'asunto', FILTER_UNSAFE_RAW));
    $destino = trim(filter_input(INPUT_POST, 'dest', FILTER_UNSAFE_RAW));
    $destVacio = empty($destino);
    $destInexis = !$usuarioDao->existe_nombreUsuario($destino);
    $texto = trim(filter_input(INPUT_POST, 'txt', FILTER_UNSAFE_RAW));
    $adjunto = trim($_FILES['fMensaje']['name']);

    if($destVacio || $destInexis){
        echo $blade->run("formMensaje", compact('usuario','asunto','destino','destVacio','destInexis','texto'));
        die;
    }
    else {      //guarda msj
        if($adjunto !== ""){
            guardaFMsj($adjunto);
        }
        else{
            $dest="./asset/archivos_mensajes/";
        }
        $usuDest = $usuarioDao->recuperaUsuarioPorNombre($destino);
        $fEnvio = new DateTime();
        $mensaje = new Mensaje($asunto,$texto,(int)$usuDest->getId(),$destino,(int)$usuario->getId(),$usuario->getNombre(),
                $dest,$fEnvio);
        $mensajeDao->creaMensaje($mensaje);
        $msjEnviado = true;
        echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario','msjEnviado'));
        die;
    }    
    
    }
    else if(isset ($_POST['reenvMsj'])){  //pulsado el btn enviar en formResend.blade
        $idMsjOriginal = $_POST['idMsjOrigen'];
        $msjOrigen = $mensajeDao->recuperaMensajePorId((int)$idMsjOriginal);
        $destino = trim(filter_input(INPUT_POST, 'destino', FILTER_UNSAFE_RAW));
        $destVacio = empty($destino);
        $arrDestinos = explode(",", $destino);
        $destInexis;
        foreach ($arrDestinos as $destin){
            $destInexis = !$usuarioDao->existe_nombreUsuario(trim($destin));
            if($destInexis)                break;
        }
        if($destVacio || $destInexis){
            echo $blade->run("formResend", compact('usuario','msjOrigen','destVacio','destInexis'));
            die;
        }
        else{         //envía msjOrigen a cada destinatario
            
            foreach ($arrDestinos as $destine){
                $destine = trim($destine);
                $usuDest = $usuarioDao->recuperaUsuarioPorNombre($destine);
                $mensaje = new Mensaje($msjOrigen->getAsunto(),$msjOrigen->getTexto(),(int)$usuDest->getId(),
                $destine,(int)$usuario->getId(),$usuario->getNombre(),$msjOrigen->getAdjunto(),
                new DateTime($msjOrigen->getFechaEnvio()));
                $mensajeDao->creaMensaje($mensaje);
                if($msjOrigen->getHilo()!== null)
                    $mensajeDao->modificaHiloMensaje ($mensaje, (int)$mensaje->getId ());
            }
            $msjEnviado = true;
             echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario','msjEnviado'));
             die;
            
        }
    }
    else if(isset ($_REQUEST['papelera'])||isset ($_REQUEST['mostrarPapeUsuario'])){  //pulsado el enlace a la papelera desde cualquier menú lateral de las v. de msj's
        $msjsPapelera = actualizaMjsPapelera((int)$usuario->getId());                  //u opción de volver a pape desde v. mensajePapeSelecc.blade  
        echo $blade->run("mensajesPapelera", compact('usuario','msjsPapelera'));    
        die; 
    }
    else if(isset ($_REQUEST['reenMsjEntrada'])){ //desde v. mensajePapeSelecc.blade se ha pulsado reenviar a bandeja de entrada
        $idMsjAEntrada = $_REQUEST['idReenMsjPape'];
        $msjSelecc = $mensajeDao->recuperaMensajePapePorId((int)$idMsjAEntrada);
        if($mensajeDao->mueveMsjAEntrada($msjSelecc)){
            $msjsPapelera = actualizaMjsPapelera((int)$usuario->getId());
        echo $blade->run("mensajesPapelera", compact('usuario','msjsPapelera'));
        die();
        }
        
    }
    else if(isset ($_REQUEST['msjPapeShow'])){ //pulsado en ausnto del msj desde v. mensajesPapelera.blade
        $idMsj = $_REQUEST['idMsj'];
        $msjSelecc = $mensajeDao->recuperaMensajePapePorId((int)$idMsj);
        if($msjSelecc->getHilo()== null){
        echo $blade->run("mensajePapeSelecc", compact('usuario','msjSelecc'));
        die();
        }else {
          $msjsHilo = getMensajesHilo($msjSelecc); 
          echo $blade->run("mensajePapeSelecc", compact('usuario','msjSelecc','msjsHilo'));
        die();
        }
    }
    else if(isset ($_REQUEST['vaciaPape'])){      //pulsado btn vaciar papelera desde v. mensajesPapelera.blade
        $mensajeDao->vaciaPapeleraUsuario((int)$usuario->getId());
        $msjsPapelera = actualizaMjsPapelera((int)$usuario->getId());                  
        echo $blade->run("mensajesPapelera", compact('usuario','msjsPapelera'));    
        die;
    }
    else if(isset ($_REQUEST['msjShow'])){ //pulsado sobre el asunto del msj desde v. mensajesUsuario.blade
        $idMsj = $_REQUEST['idMsj'];
        $msjSelecc = $mensajeDao->recuperaMensajePorId((int)$idMsj);
        $msjSelecc->setLeido(true);
        $mensajeDao->modificaMensaje($msjSelecc,(int)$idMsj);
        if($msjSelecc->getHilo()== null){
        echo $blade->run("mensajeSelecc", compact('usuario','msjSelecc'));
        die();
        }else {
          $msjsHilo = getMensajesHilo($msjSelecc); 
          echo $blade->run("mensajeSelecc", compact('usuario','msjSelecc','msjsHilo'));
        die();
        }
        
    }
    else if(isset ($_REQUEST['responMsj'])){ //pulsado el btn responder desde la vista mensajeSelecc.blade.php
        $idMsjOrigen = $_REQUEST['idResMsj'];
        $msjOrigen = $mensajeDao->recuperaMensajePorId((int)$idMsjOrigen);
        
        echo $blade->run("formResponse", compact('usuario','msjOrigen'));
        die();
    }
    else if(isset($_REQUEST['reenMsj'])){   //pulsado el btn reenviar desde mensajeSelecc.blade
        $idMsjOrigen = $_REQUEST['idReenMsj'];
        $msjOrigen = $mensajeDao->recuperaMensajePorId((int)$idMsjOrigen);
        
        echo $blade->run("formResend", compact('usuario','msjOrigen'));
        die();
    }
    else if(isset ($_REQUEST['papeMsj'])){ //pulsado el btn icono-papelera desde la vista mensajeSelecc.blade.php
        $idMsjOrigen = $_REQUEST['idPapMsj'];
        $msjOrigen = $mensajeDao->recuperaMensajePorId((int)$idMsjOrigen);
        if($mensajeDao->mueveMsjAPapelera($msjOrigen)){
           $msjsUsuario = actualizaMsjsUsuario((int)$usuario->getId()); 
        echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario'));
        die();
        }
    }
    else if(isset ($_POST['envResMsj'])){  // enviado formResponse.blade
        $msjOrigenRes = unserialize(base64_decode($_POST['msjOrigen']));
        $asuntoRes = $msjOrigenRes->getAsunto();
        $textoRes = trim(filter_input(INPUT_POST, 'txt', FILTER_UNSAFE_RAW));
        $adjuntoRes = trim($_FILES['fResMensaje']['name']);
        if($adjuntoRes !== "")
            guardaFResMsj($adjuntoRes);
        else
            $dest="./asset/archivos_mensajes/";
        
        $fEnvioRes = new DateTime();
        $newMsj = new Mensaje($asuntoRes,$textoRes,(int)$msjOrigenRes->getIdRemi(),$msjOrigenRes->getNomRemi(),(int)$usuario->getId(),$usuario->getNombre(),
                $dest,$fEnvioRes);
        //comprobar si el mensaje original tiene hilo (registrado en bbdd):
        
        if($msjOrigenRes->getHilo()!== null){ //ya tiene hilo
            $mensajeDao->creaMensaje($newMsj);
            $idNewMsj = $mensajeDao->obtenerUltimoIdInsertado();
            $newMsjBB=$mensajeDao->recuperaMensajePorId($idNewMsj);
            $newMsjBB->setHilo($msjOrigenRes->getHilo().",".$msjOrigenRes->getId()); //concatena lo anterior con el último msj
            $mensajeDao->modificaHiloMensaje($newMsjBB, (int)$newMsjBB->getId());
            $msjEnviado = true;
            echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario','msjEnviado'));
            die;
                
        }
        else{                    //se le establece el hilo del msjOrigenRes al nuevo mensaje
            
            $mensajeDao->creaMensaje($newMsj);
            $idNewMsj = $mensajeDao->obtenerUltimoIdInsertado();
            $newMsjBB=$mensajeDao->recuperaMensajePorId($idNewMsj);
            $newMsjBB->setHilo($msjOrigenRes->getId());
            $mensajeDao->modificaHiloMensaje($newMsjBB, (int)$newMsjBB->getId());
            $msjEnviado = true;
            echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario','msjEnviado'));
            die;
        }      
        
    }
    
    else { //si se pulsado el botón de correo desde v portada.blade 
        echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario'));
        die; 
    }
}else{
    echo $blade->run('login');
}