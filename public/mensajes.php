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
$dest; $dest2  ; $msjsHilo; $destEnv;$nombreUnico; $nombreUnico2;$arrContent = array();

function guardaFMsj (String $picture):bool{
    global $nombreUnico;    global $nombreUnico2; global $arrContent;
    $ruta = $_FILES['fMensaje']['tmp_name']; 
    $content = false;
    
    
    if (file_exists($ruta)) {
        // En la primera iteración, $ruta es válido, lee desde el archivo
        $content = file_get_contents($ruta);
        $arrContent[]=$content;
    } else if(!empty ($arrContent)) {
        // En iteraciones posteriores, $ruta no es válido, lee desde $picture
        $content = $arrContent[0];
    }
    
    $cargaMen2 = false;
    
    $dirDestino = "./asset/archivos_mensajes/";
    $dirDestino2 = "./asset/archivos_enviados/";
    
    $infoArchivo = pathinfo($picture);
    $nombreUnico = $dirDestino . 
                    $infoArchivo['filename']. '_' . 
                    uniqid() . '.' . 
                    $infoArchivo['extension'];
    $nombreUnico2 = $dirDestino2 .  '/' . 
                    $infoArchivo['filename']. '_' . 
                    uniqid() . '.' . 
                    $infoArchivo['extension'];
    $cargaEnv = file_put_contents($nombreUnico2, $content);
    

    $cargaMen = move_uploaded_file($ruta, $nombreUnico)? '' :$cargaMen2 = 
 file_put_contents($nombreUnico, $content) ;
    
    return (($cargaMen||$cargaMen2) && $cargaEnv);
}
function guardaFResMsj (String $picture):bool{
    global $dest; global $dest2; $content = false;$msjCargado = false;
    global $nombreUnico;    global $nombreUnico2;
    $ruta = $_FILES['fResMensaje']['tmp_name'];
    $content = file_get_contents($ruta);
    $dest = "./asset/archivos_mensajes/";
    $dest2 = "./asset/archivos_enviados/";
    
    $infoArchivo = pathinfo($picture);
    $nombreUnico = $dest . 
                    $infoArchivo['filename']. '_' . 
                    uniqid() . '.' . 
                    $infoArchivo['extension'];
    $nombreUnico2 = $dest2 . 
                    $infoArchivo['filename']. '_' . 
                    uniqid() . '.' . 
                    $infoArchivo['extension'];
    move_uploaded_file($ruta, $nombreUnico)?$msjCargado=true :$msjCargado=false;
    $cargaEnv = file_put_contents($nombreUnico2, $content);
    
    return $msjCargado&& $cargaEnv;
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
function actualizaMsjsEnviados(int $idUsuario):Array{ //********ENVIADOS***********************
    global $mensajeDao;
    $msjsEnviados = $mensajeDao->recuperaMsjsPorUsuarioEnv($idUsuario);
    foreach ($msjsEnviados as $msjUsu){
        $fechaRaw = new DateTime($msjUsu->getFechaEnvio());
        $fechaFormateada = $fechaRaw->format('d/m/y H:i:s');
        $msjUsu->setFechaEnvio($fechaFormateada);
    }
    return $msjsEnviados;
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
    $destInexis;
    $texto = trim(filter_input(INPUT_POST, 'txt', FILTER_UNSAFE_RAW));
    $adjunto = trim($_FILES['fMensaje']['name']);

    $arrDestinos = explode(",", $destino);
    foreach ($arrDestinos as $destin){
            $destInexis = !$usuarioDao->existe_nombreUsuario(trim($destin));
            if($destInexis)                break;
        }
    if($destVacio || $destInexis){
        echo $blade->run("formMensaje", compact('usuario','asunto','destino','destVacio','destInexis','texto'));
        die;
    }
    else {      //envia msjs a destinatarios
        foreach ($arrDestinos as $destine){
                $destine = trim($destine);
                $usuDest = $usuarioDao->recuperaUsuarioPorNombre($destine);
                if ($adjunto!== "" ){
                    guardaFMsj($adjunto);
                    

                    $mensaje = new Mensaje($asunto,
                    $texto,
                    (int)$usuDest->getId(),
                     $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $nombreUnico,
                    new DateTime());
                    
                    $mensajeEnvRe = new Mensaje($asunto,
                    $texto,
                    (int)$usuDest->getId(),
                    $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $nombreUnico2,
                    new DateTime());
                    
                    $mensajeDao->creaMensaje($mensaje);
                    $mensajeDao->creaEnviado($mensajeEnvRe);  //***guarda msj en enviados***

                    
                }
                else{
                    $dirDestino = "./asset/archivos_mensajes/";
                    $dirDestino2 = "./asset/archivos_enviados/";
                    $mensaje = new Mensaje($asunto,
                    $texto,
                    (int)$usuDest->getId(),
                     $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $dirDestino,
                    new DateTime());
                    
                    $mensajeEnvRe = new Mensaje($asunto,
                    $texto,
                    (int)$usuDest->getId(),
                    $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $dirDestino2,
                    new DateTime());
                    
                    $mensajeDao->creaMensaje($mensaje);
                    $mensajeDao->creaEnviado($mensajeEnvRe);  //***guarda msj en enviados***
                    
                }
            }
        $msjEnviado = true;
        $arrContent = [];
        $msjsUsuario = actualizaMsjsUsuario((int)$usuario->getId());
        
 //       actualizaMsjsEnviados((int)$usuario->getId());
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
            echo $blade->run("formResend", compact('usuario','msjOrigen','destVacio','destInexis','destino'));
            die;
        }
        else{         //envía msjOrigen a cada destinatario
            foreach ($arrDestinos as $destine){
                $destine = trim($destine);
                $usuDest = $usuarioDao->recuperaUsuarioPorNombre($destine);
                if ($msjOrigen->getAdjunto()!== "./asset/archivos_mensajes/" ){
                    $arOriginal = $msjOrigen->getAdjunto();
                    $dirDestino2 = "./asset/archivos_enviados/";

                    $nombreUnico = pathinfo($msjOrigen->getAdjunto(), PATHINFO_DIRNAME) . '/' . 
                            pathinfo($msjOrigen->getAdjunto(), PATHINFO_FILENAME) . '_' . 
                            uniqid() . '.' . 
                            pathinfo($msjOrigen->getAdjunto(), PATHINFO_EXTENSION);
                    $nombreUnico2 = $dirDestino2 .  '/' . 
                            pathinfo($msjOrigen->getAdjunto(), PATHINFO_FILENAME) . '_' . 
                            uniqid() . '.' . 
                            pathinfo($msjOrigen->getAdjunto(), PATHINFO_EXTENSION);
                    
                    copy($arOriginal, $nombreUnico);
                    copy($arOriginal, $nombreUnico2);
                    
                    $mensaje = new Mensaje($msjOrigen->getAsunto(),
                    $msjOrigen->getTexto(),
                    (int)$usuDest->getId(),
                     $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $nombreUnico,
                    new DateTime($msjOrigen->getFechaEnvio()));
                    
                    $mensajeEnvRe = new Mensaje($msjOrigen->getAsunto(),
                    $msjOrigen->getTexto(),
                    (int)$usuDest->getId(),
                    $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $nombreUnico2,
                    new DateTime($msjOrigen->getFechaEnvio()));
                    
                    $mensajeDao->creaMensaje($mensaje);
                    $idNewMsj = $mensajeDao->obtenerUltimoIdInsertado();
                    $mensajeDao->creaEnviado($mensajeEnvRe);  //***guarda msj en enviados***
                    $idNewMsjEnv = $mensajeDao->obtenerUltimoIdInsertado();

                    if($msjOrigen->getHilo()!== null){
                    $newMsjBB  = $mensajeDao->recuperaMensajePorId($idNewMsj);
                     $newMsjBBEnv = $mensajeDao->recuperaMensajeEnvPorId($idNewMsjEnv);
                     $newMsjBB->setHilo($msjOrigen->getHilo());
                     $newMsjBBEnv->setHilo($msjOrigen->getHilo());
                    $mensajeDao->modificaHiloMensaje ($newMsjBB, (int)$newMsjBB->getId());
                    $mensajeDao->modificaHiloMensajeEnv ($newMsjBBEnv,(int) $newMsjBBEnv->getId());
                    }
                }
                else{
                    $mensaje = new Mensaje($msjOrigen->getAsunto(),
                    $msjOrigen->getTexto(),
                    (int)$usuDest->getId(),
                     $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $msjOrigen->getAdjunto(),
                    new DateTime($msjOrigen->getFechaEnvio()));
                    
                    $mensajeEnvRe = new Mensaje($msjOrigen->getAsunto(),
                    $msjOrigen->getTexto(),
                    (int)$usuDest->getId(),
                    $destine,
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    "./asset/archivos_enviados/",
                    new DateTime($msjOrigen->getFechaEnvio()));
                    
                    $mensajeDao->creaMensaje($mensaje);
                    $idNewMsj = $mensajeDao->obtenerUltimoIdInsertado();
                    $mensajeDao->creaEnviado($mensajeEnvRe);  //***guarda msj en enviados***
                    $idNewMsjEnv = $mensajeDao->obtenerUltimoIdInsertado();
                    
                    if($msjOrigen->getHilo()!== null){
                     $newMsjBB  = $mensajeDao->recuperaMensajePorId($idNewMsj);
                     $newMsjBBEnv = $mensajeDao->recuperaMensajeEnvPorId($idNewMsjEnv);
                     $newMsjBB->setHilo($msjOrigen->getHilo());
                     $newMsjBBEnv->setHilo($msjOrigen->getHilo());
                    $mensajeDao->modificaHiloMensaje ($newMsjBB, (int)$newMsjBB->getId());
                    $mensajeDao->modificaHiloMensajeEnv ($newMsjBBEnv,(int) $newMsjBBEnv->getId());
                    }
                }
            }
            $msjEnviado = true;
             echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario','msjEnviado'));
             die;
            
        }
    }
    else if(isset($_REQUEST['menEnviados'])){   //pulsado el enlace a mensaje enviados del menú lateral
                                                //de cualquiera de obtenerUltimoIdInsertado( vistas de correo.
        $msjsEnviados = actualizaMsjsEnviados((int)$usuario->getId());
        echo $blade->run("mensajesEnviados", compact('usuario','msjsEnviados'));    
        die; 
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
    else if(isset ($_REQUEST['msjEnvShow'])){ //pulsado en asunto del msj desde v mensajesEnviados.blade
        $idMsj = $_REQUEST['idMsj'];
        $msjSelecc = $mensajeDao->recuperaMensajeEnvPorId((int)$idMsj);
        if($msjSelecc->getHilo()== null){
        echo $blade->run("mensajeEnvSelecc", compact('usuario','msjSelecc'));
        die();
        }else {
          $msjsHilo = getMensajesHilo($msjSelecc); 
          echo $blade->run("mensajeEnvSelecc", compact('usuario','msjSelecc','msjsHilo'));
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
    else if(isset ($_REQUEST['eliminaMensajeEnviado'])){ //desde el btn icono-papelera desde la vista mensajeEnvSelecc.blade
        $idMsjOrigen = $_REQUEST['idEnv'];            //***enlazado al script confEliminaEnv.js (de donde parte la peti)   
        $msjOrigen = $mensajeDao->recuperaMensajeEnvPorId((int)$idMsjOrigen);
        if($mensajeDao->eliminaMsjEnviado($msjOrigen)){
           $msjsEnviados = actualizaMsjsEnviados((int)$usuario->getId()); 
        echo $blade->run("mensajesEnviados", compact('usuario','msjsEnviados'));
        die();
        }
    }
    else if(isset ($_POST['envResMsj'])){  // enviado formResponse.blade
        $msjOrigenRes = unserialize(base64_decode($_POST['msjOrigen']));
        $asuntoRes = $msjOrigenRes->getAsunto();
        $textoRes = trim(filter_input(INPUT_POST, 'txt', FILTER_UNSAFE_RAW));
        $adjuntoRes = trim($_FILES['fResMensaje']['name']);
        if($adjuntoRes !== ""){
            guardaFResMsj($adjuntoRes);
            $mensaje = new Mensaje($asuntoRes,
                    $textoRes,
                    (int)$msjOrigenRes->getIdRemi(),
                     $msjOrigenRes->getNomRemi(),
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $nombreUnico,
                    new DateTime());
                    
            $mensajeEnvRe = new Mensaje($asuntoRes,
                    $textoRes,
                    (int)$msjOrigenRes->getIdRemi(),
                    $msjOrigenRes->getNomRemi(),
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $nombreUnico2,
                    new DateTime());
                    
  //                  $mensajeDao->creaMensaje($mensaje);
  //                  $mensajeDao->creaEnviado($mensajeEnvRe);
           }else{
            $dest="./asset/archivos_mensajes/";
            $destEnv="./asset/archivos_enviados/";
            
            $mensaje = new Mensaje($asuntoRes,
                    $textoRes,
                    (int)$msjOrigenRes->getIdRemi(),
                     $msjOrigenRes->getNomRemi(),
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $dest,
                    new DateTime());
                    
            $mensajeEnvRe = new Mensaje($asuntoRes,
                    $textoRes,
                    (int)$msjOrigenRes->getIdRemi(),
                    $msjOrigenRes->getNomRemi(),
                    (int)$usuario->getId(),
                    $usuario->getNombre(),
                    $destEnv,
                    new DateTime());
                    
  //                  $mensajeDao->creaMensaje($mensaje);
  //                  $mensajeDao->creaEnviado($mensajeEnvRe);
           }
        
        //comprobar si el mensaje original tiene hilo (registrado en bbdd):
        
        if($msjOrigenRes->getHilo()!== null){ //ya tiene hilo
            $mensajeDao->creaMensaje($mensaje);
            $idNewMsj = $mensajeDao->obtenerUltimoIdInsertado();
            $mensajeDao->creaEnviado($mensajeEnvRe);
            $idNewMsjEnv = $mensajeDao->obtenerUltimoIdInsertado();
            $newMsjBB=$mensajeDao->recuperaMensajePorId($idNewMsj);
            $newMsjBBEnv=$mensajeDao->recuperaMensajeEnvPorId($idNewMsjEnv);
            $newMsjBB->setHilo($msjOrigenRes->getHilo().",".$msjOrigenRes->getId()); //concatena lo anterior con el último msj
            $newMsjBBEnv->setHilo($msjOrigenRes->getHilo().",".$msjOrigenRes->getId()); 
            $mensajeDao->modificaHiloMensaje($newMsjBB, (int)$newMsjBB->getId());
            $mensajeDao->modificaHiloMensajeEnv($newMsjBBEnv, (int)$newMsjBBEnv->getId());
            $msjEnviado = true;
 //           $arrContent=[];
            echo $blade->run("mensajesUsuario", compact('usuario','msjsUsuario','msjEnviado'));
            die;
                
        }
        else{                    //se le establece el hilo del msjOrigenRes al nuevo mensaje y al nuevo enviado
            
            $mensajeDao->creaMensaje($mensaje);
            $idNewMsj = $mensajeDao->obtenerUltimoIdInsertado();
            $mensajeDao->creaEnviado($mensajeEnvRe);
            $idNewMsjEnv = $mensajeDao->obtenerUltimoIdInsertado();
            $newMsjBB=$mensajeDao->recuperaMensajePorId($idNewMsj);
            $newMsjBBEnv=$mensajeDao->recuperaMensajeEnvPorId($idNewMsjEnv);
            $newMsjBB->setHilo($msjOrigenRes->getId());
            $newMsjBBEnv->setHilo($msjOrigenRes->getId());
            $mensajeDao->modificaHiloMensaje($newMsjBB, (int)$newMsjBB->getId());
            $mensajeDao->modificaHiloMensajeEnv($newMsjBBEnv, (int)$newMsjBBEnv->getId());
            $msjEnviado = true;
 //           $arrContent=[];
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