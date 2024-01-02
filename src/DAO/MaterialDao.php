<?php
namespace App\DAO;


use PDO;
use App\Modelo\Material;

class MaterialDao {

    private $bd;

    function __construct($bd) {
        $this->bd = $bd;
    }

    function creaMaterial(object $material): bool {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "insert into materiales (propietario,descripcion,foto) values(:propietario,:descripcion,:foto)";
        $stm = $this->bd->prepare($sql);
        try {
            $resul = $stm->execute([
               ':propietario'=>$material->getPropietario(),
                ':descripcion'=>$material->getDescripcion(),
                ':foto'=>$material->getFoto()
            ]);
            return true;
        } catch (PDOException $ex) {
            return false;
            
            die('error al insertar material: '.$ex->getMessage());
        }
        
    }

    function modificaMaterial(object $material, int $id) :bool{
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "update materiales set descripcion=:descripcion, foto=:foto where id=:id";
        $sth = $this->bd->prepare($sql);
        try {
            return $result = $sth->execute([
                ":descripcion" => $material->getDescripcion(), 
                ":foto" => $material->getFoto(),
                ":id" => $id
                    ]);
        } catch (PDOException $ex) {
            die('error al actualizar contenido '.$ex->getMessage());
        }
        
    }

    function eliminaMaterial(int $id):bool {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = "delete from materiales where id=:id";
        $stm = $this->bd->prepare($sql);
        try {
            return $result = $stm->execute([
                ':id'=>$id
            ]);
        } catch (PDOException $ex) {
            die('error al borrar contenido '.$ex->getMessage());
        }
                
    }

   

    function recuperaMaterialesPorUsuario(int $propietario):Array {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from materiales where propietario=:propietario';
        $sth = $this->bd->prepare($sql);
        try {
            $sth->execute([":propietario" => $propietario]);
            $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Material::class);
            $materiales = $sth->fetchAll();
            return $materiales;
        } catch (PDOException $ex) {
            die('error al recuperar materiales de usuario: '.$ex->getMessage());
        }
    }
    function recuperaMaterialPorId(int $id): object {
        $this->bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $sql = 'select * from materiales where id=:id';
        $sth = $this->bd->prepare($sql);
        try {
            $sth->execute([":id" => $id]);
            $sth->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Material::class);
            return $material = $sth->fetch();    
        } catch (PDOException $ex) {
            die('error al recuperar material de usuario: '.$ex->getMessage());
        }
        
    }
    function eliminaMaterialesPorUsuario (int $propietario): bool{  //elimina todas las publicaciones de un usuario
        $materialesUsuario = $this->recuperaMaterialesPorUsuario($propietario);
        if(!empty($materialesUsuario)){
            foreach ($materialesUsuario as $materialUsuario){
                $materialUsuario->getFoto()!== "./asset/fotos_material/"? unlink($materialUsuario->getFoto()):"";
                $this->eliminaMaterial((int)$materialUsuario->getId());
            }
        }
        return true;
    }
    

}
