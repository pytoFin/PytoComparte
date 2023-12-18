<?php
namespace App\Modelo;

use PDO;

class Usuario
{
    private $id;
    private $nombre;
    private $password;
    private $email;
    private $foto_perfil;

    public static function recuperaUsuarioPorCredencial(PDO $bd, string $nombreUsuario, string $pass): ?Usuario
    {
        $bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $passHashed = hash('sha256', $pass);
        $sql = 'select * from usuarios where usuario=:nombreUsuario and pass=:passHashed';
        $sth = $bd->prepare($sql);
        $sth->execute([":nombreUsuario" => $nombreUsuario, ":passHashed" => $passHashed]);
        $sth->setFetchMode(PDO::FETCH_CLASS, Usuario::class);
        $usuario = ($sth->fetch()) ?: null;
        return $usuario;
    }

    public function __construct(string $usuario = null, string $pass = null, string $email=null, string $foto=null)
    {
        if (!is_null($usuario)) {
            $this->nombre = $usuario;
        }
        if (!is_null($pass)) {
            $this->password = $pass;
        }
        if (!is_null($email)) {
            $this->email = $email;
        }
        if (!is_null($foto)) {
            $this->foto_perfil = $foto;
        }
    }
    public function getNombre() {
        return $this->nombre;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFoto_perfil() {
        return $this->foto_perfil;
    }

    public function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function setPassword($password): void {
        $this->password = $password;
    }

    public function setEmail($email): void {
        $this->email = $email;
    }

    public function setFoto_perfil($foto_perfil): void {
        $this->foto_perfil = $foto_perfil;
    }


    
    
    public function getId() {
        return $this->id;
    }

   


}
