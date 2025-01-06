<?php

include_once 'Controlador/habitacionControlador.php';
class Reserva
{
    private $id;

    private $fechaInicio;

    private $fechaFin;

    private Habitacion $habitacion; 

    private $costo;

    private $usuarioDni;

    public function __construct($id, $fechaInicio, $fechaFin, Habitacion $habitacion, $costo, $usuarioDni)
    {
        $this->id = $id;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->habitacion = $habitacion;
        $this->costo = $costo;
        $this->usuarioDni = $usuarioDni;
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio($fecha_inicio)
    {
        $this->fechaInicio = $fecha_inicio;
    }

    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    public function getCosto()
    {
        return $this->costo;
    }

    public function setCosto($costo)
    {
        $this->costo = $costo;
    }

    public function getHabitacion()
    {
        return $this->habitacion;
    }

    public function setHabitacion(Habitacion $habitacion)
    {
        $this->habitacion = $habitacion;
    }

    public function setUsuarioDni($dni)
    {
        $this->usuarioDni = $dni;
    }

    public function getUsuarioDni()
    {
        return $this->usuarioDni;
    }

    public function reservaToArray($reserva)
    {
        return [
            'id' => $reserva->getId(),
            'Fecha inicio' => $reserva->getFechaInicio(),
            'Fecha fin' => $reserva->getFechaFin(),
            'Estado' => $reserva->getEstado(),
            'Habitacion' => $reserva->getHabitacion(),
            'Costo' => $reserva->getCosto(),
            'Reservado por DNI' => $reserva->getUsuarioDni(),
        ];
    }

    //Habitación: {$this->habitacion->getNumero()} se agrega para mostrar hab en vez de objeto 
    public function __toString()
    {
        return "ID: {$this->id}, Fecha Inicio: {$this->fechaInicio}, Fecha Fin: {$this->fechaFin}, Habitación: {$this->habitacion->getNumero()}, Costo: $ $this->costo, Reservado por dni:{$this->usuarioDni}";
    }
}
