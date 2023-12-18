<?php
namespace App\Modelo;



class Mensaje
{
    private $id;
    private $asunto;
    private $texto;
    private $idDesti;
    private $nomDesti;
    private $idRemi;
    private $nomRemi;
    private $adjunto;
    private $leido;
    private $fechaEnvio;
    private $hilo;


    public function __construct(string $asunto = null, string $texto = null, int $idDesti=null,
            string $nomDesti=null,int $idRemi=null, string $nomRemi=null, string $adjunto=null, \DateTime $fechaEnvio=null)
    {
        if (!is_null($asunto)) {
            $this->asunto = $asunto;
        }
        if (!is_null($texto)) {
            $this->texto = $texto;
        }
        if (!is_null($idDesti)) {
            $this->idDesti = $idDesti;
        }
        if(!is_null($nomDesti)){
            $this->nomDesti = $nomDesti;
        }
        if (!is_null($idRemi)) {
            $this->idRemi = $idRemi;
        }
        if(!is_null($nomRemi)){
            $this->nomRemi = $nomRemi;
        }
        if (!is_null($adjunto)) {
            $this->adjunto = $adjunto;
        }
        if (!is_null($fechaEnvio)) {
            $this->fechaEnvio = $fechaEnvio;
        }
    }
    public function getId() {
        return $this->id;
    }
    public function getIdDesti() {
        return $this->idDesti;
    }

    public function getNomDesti() {
        return $this->nomDesti;
    }

    public function getIdRemi() {
        return $this->idRemi;
    }

    public function getNomRemi() {
        return $this->nomRemi;
    }

    public function setIdDesti($idDesti): void {
        $this->idDesti = $idDesti;
    }

    public function setNomDesti($nomDesti): void {
        $this->nomDesti = $nomDesti;
    }

    public function setIdRemi($idRemi): void {
        $this->idRemi = $idRemi;
    }

    public function setNomRemi($nomRemi): void {
        $this->nomRemi = $nomRemi;
    }

    public function getAsunto() {
        return $this->asunto;
    }

    public function getTexto() {
        return $this->texto;
    }

    public function getAdjunto() {
        return $this->adjunto;
    }

    public function getLeido() {
        return $this->leido;
    }

    public function getFechaEnvio() {
        return $this->fechaEnvio;
    }

    public function getHilo() {
        return $this->hilo;
    }
    public function setAsunto($asunto): void {
        $this->asunto = $asunto;
    }

    public function setTexto($texto): void {
        $this->texto = $texto;
    }

    public function setAdjunto($adjunto): void {
        $this->adjunto = $adjunto;
    }

    public function setLeido($leido): void {
        $this->leido = $leido;
    }

    public function setFechaEnvio($fechaEnvio): void {
        $this->fechaEnvio = $fechaEnvio;
    }
    
    public function setHilo($hilo): void {
        $this->hilo = $hilo;
    }





    
    

}
