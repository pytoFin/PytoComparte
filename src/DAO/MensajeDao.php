<?php
namespace App\DAO;


use PDO;
use App\Modelo\Mensaje;

class MensajeDao {

    private $bd;

    function __construct($bd) {
        $this->bd = $bd;
    }

    function creaMensaje(object $mensaje): bool {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "insert into mensajes (asunto,texto,idDesti,nomDesti,idRemi,nomRemi ,adjunto,fechaEnvio) "
                . "values(:asunto,:texto,:idDesti,:nomDesti,:idRemi,:nomRemi,:adjunto,:fechaEnvio)";
        $stm = $this->bd->prepare($sql);
        try {
            $fechaEnvioFormateada = $mensaje->getFechaEnvio()->format('Y-m-d H:i:s');
            $resul = $stm->execute([
               ':asunto'=>$mensaje->getAsunto(),
                ':texto'=>$mensaje->getTexto(),
                ':idDesti'=>$mensaje->getIdDesti(),
                ':nomDesti'=>$mensaje->getNomDesti(),
                ':idRemi'=>$mensaje->getIdRemi(),
                ':nomRemi'=>$mensaje->getNomRemi(),
                ':adjunto'=>$mensaje->getAdjunto(),
                ':fechaEnvio'=>$fechaEnvioFormateada
            ]);
            return true;
        } catch (PDOException $ex) {
            return false;
            
            die('error al insertar mensaje: '.$ex->getMessage());
        }
        
    }

    function modificaMensaje(object $mensaje, int $id) :bool{ //modifica el campo leido
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "update mensajes set leido=:leido where id=:id";
        $sth = $this->bd->prepare($sql);
        try {
            return $result = $sth->execute([
                ":leido" => $mensaje->getLeido(),
                ":id" => $id
                    ]);
        } catch (PDOException $ex) {
            die('error al actualizar el campo leido de mensaje '.$ex->getMessage());
        }
    }
    
    function modificaHiloMensaje(object $mensaje, int $id) :bool{ //modifica el campo hilo
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "update mensajes set hilo=:hilo where id=:id";
        $sth = $this->bd->prepare($sql);
        try {
            return $result = $sth->execute([
                ":hilo" => $mensaje->getHilo(),
                ":id" => $id
                    ]);
        } catch (PDOException $ex) {
            die('error al actualizar elcampo hilo de mensaje '.$ex->getMessage());
        }
    }

    function eliminaMensaje(int $id) {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "delete from mensajes where id=:id";
        $stm = $this->bd->prepare($sql);
        try {
            return $result = $stm->execute([
                ':id'=>$id
            ]);
        } catch (PDOException $ex) {
            die('error al borrar mensaje '.$ex->getMessage());
        }
                
    }
    function eliminaMensajePap(int $id) : bool{
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "delete from papelera where id=:id";
        $stm = $this->bd->prepare($sql);
        try {
            return $result = $stm->execute([
                ':id'=>$id
            ]);
        } catch (PDOException $ex) {
            die('error al borrar mensaje papelera '.$ex->getMessage());
            return false;
        }
                
    }

   

    function recuperaMensajesPorUsuario(int $idDesti):Array {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from mensajes where idDesti=:idDesti';
        $sth = $this->bd->prepare($sql);
        try {
            $sth->execute([":idDesti" => $idDesti]);
            $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Mensaje::class);
            $mensajes = $sth->fetchAll();
            return $mensajes;
        } catch (PDOException $ex) {
            die('error al recuperar mensajes de usuario: '.$ex->getMessage());
        }
    }
    function recuperaMensajePorId(int $id): object {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from mensajes where id=:id';
        $sth = $this->bd->prepare($sql);
        try {
            $sth->execute([":id" => $id]);
            $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Mensaje::class);
            return $material = $sth->fetch();    
        } catch (PDOException $ex) {
            die('error al recuperar mensaje por su id: '.$ex->getMessage());
        }
        
    }
    function recuperaMensajePapePorId(int $id): object {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from papelera where id=:id';
        $sth = $this->bd->prepare($sql);
        try {
            $sth->execute([":id" => $id]);
            $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Mensaje::class);
            return $material = $sth->fetch();    
        } catch (PDOException $ex) {
            die('error al recuperar mensaje de papelera por su id: '.$ex->getMessage());
        }
        
    }
    function mueveMsjAPapelera(object $msj): bool {
    $sqlInsert = "INSERT INTO papelera (asunto,texto,idDesti,nomDesti,idRemi,nomRemi ,adjunto,leido,fechaEnvio,hilo)"
            . "values(:asunto,:texto,:idDesti,:nomDesti,:idRemi,:nomRemi,:adjunto,:leido,:fechaEnvio,:hilo)";
    $sqlDelete = "DELETE FROM mensajes WHERE id = :id";

    try {
        $this->bd->beginTransaction();
        $fechaEnvioFormateada = date('Y-m-d H:i:s', strtotime( $msj->getFechaEnvio()));
        
        // Insertar en papelera
        $stmtInsert = $this->bd->prepare($sqlInsert);
        $inserted = $stmtInsert->execute([
            ':asunto'=>$msj->getAsunto(),
                ':texto'=>$msj->getTexto(),
                ':idDesti'=>$msj->getIdDesti(),
                ':nomDesti'=>$msj->getNomDesti(),
                ':idRemi'=>$msj->getIdRemi(),
                ':nomRemi'=>$msj->getNomRemi(),
                ':adjunto'=>$msj->getAdjunto(),
                ':leido'=>$msj->getLeido(),
                ':fechaEnvio'=>$fechaEnvioFormateada,
                ':hilo'=>$msj->getHilo()
        ]);

        // Borrar de mensajes
        $stmtDelete = $this->bd->prepare($sqlDelete);
        $deleted = $stmtDelete->execute([':id' => $msj->getId()]);

        if ($inserted && $deleted) {
            $this->bd->commit();
            return true;
        } else {
            $this->bd->rollBack();
            return false;
        }
    } catch (PDOException $ex) {
        die('Error al mover mensaje a la papelera: ' . $ex->getMessage());
    }
}   
    function mueveMsjAEntrada(object $msj): bool {
    $sqlInsert = "INSERT INTO mensajes (asunto,texto,idDesti,nomDesti,idRemi,nomRemi ,adjunto,leido,fechaEnvio,hilo)"
            . "values(:asunto,:texto,:idDesti,:nomDesti,:idRemi,:nomRemi,:adjunto,:leido,:fechaEnvio,:hilo)";
    $sqlDelete = "DELETE FROM papelera WHERE id = :id";

    try {
        $this->bd->beginTransaction();
        $fechaEnvioFormateada = date('Y-m-d H:i:s', strtotime( $msj->getFechaEnvio()));
        
        // Insertar en papelera
        $stmtInsert = $this->bd->prepare($sqlInsert);
        $inserted = $stmtInsert->execute([
            ':asunto'=>$msj->getAsunto(),
                ':texto'=>$msj->getTexto(),
                ':idDesti'=>$msj->getIdDesti(),
                ':nomDesti'=>$msj->getNomDesti(),
                ':idRemi'=>$msj->getIdRemi(),
                ':nomRemi'=>$msj->getNomRemi(),
                ':adjunto'=>$msj->getAdjunto(),
                ':leido'=>$msj->getLeido(),
                ':fechaEnvio'=>$fechaEnvioFormateada,
                ':hilo'=>$msj->getHilo()
        ]);

        // Borrar de mensajes
        $stmtDelete = $this->bd->prepare($sqlDelete);
        $deleted = $stmtDelete->execute([':id' => $msj->getId()]);

        if ($inserted && $deleted) {
            $this->bd->commit();
            return true;
        } else {
            $this->bd->rollBack();
            return false;
        }
    } catch (PDOException $ex) {
        die('Error al mover mensaje a la bandeja de entrada: ' . $ex->getMessage());
    }
}
    function recuperaMsjsPorUsuarioPape(int $idDesti):Array{
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from papelera where idDesti=:idDesti';
        $sth = $this->bd->prepare($sql);
        try {
            $sth->execute([":idDesti" => $idDesti]);
            $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Mensaje::class);
            $mensajes = $sth->fetchAll();
            return $mensajes;
        } catch (PDOException $ex) {
            die('error al recuperar mensajes de usuario: '.$ex->getMessage());
        }
    } 
    
    function vaciaPapeleraUsuario (int $idDesti): ?bool{
        try {
            $msjsPapeUsu = $this->recuperaMsjsPorUsuarioPape($idDesti);
            foreach ($msjsPapeUsu as $msjPapUsu){
                $idMsjPape = (int)$msjPapUsu->getId();
                $rutaAdjto = $msjPapUsu->getAdjunto();
                $rutaAdjto !== "./asset/archivos_mensajes/" ? unlink($rutaAdjto):"";
                $msjEliminado = $this->eliminaMensajePap($idMsjPape);
            }
            return true;
        } catch (PDOException $ex) {
            die("error al borrar mensajes de papelera: ".$ex->getMessage());
            return false;
        }
    }

    public function obtenerUltimoIdInsertado(): int {
    return (int) $this->bd->lastInsertId();
}

    

}
