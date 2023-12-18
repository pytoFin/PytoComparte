<?php
namespace App\Modelo;



class Material
{
    private $id;
    private $propietario;
    private $descripcion;
    private $foto;


    public function __construct(int $propietario = null, string $descripcion = null, string $foto=null)
    {
        if (!is_null($propietario)) {
            $this->propietario = $propietario;
        }
        if (!is_null($descripcion)) {
            $this->descripcion = $descripcion;
        }
        if (!is_null($foto)) {
            $this->foto = $foto;
        }
        
    }
    public function getPropietario() {
        return $this->propietario;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFoto() {
        return $this->foto;
    }
    public function getId() {
        return $this->id;
    }
    public function setPropietario($propietario): void {
        $this->propietario = $propietario;
    }

    public function setDescripcion($descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function setFoto($foto): void {
        $this->foto = $foto;
    }




    
    

}
