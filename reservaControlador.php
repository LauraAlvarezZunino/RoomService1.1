<?php

include_once 'Modelo/reserva.php';
include_once 'usuarioControlador.php';
include_once 'habitacionControlador.php';

class ReservaControlador
{
    private $reservas = [];

    private $reservaJson = 'reservas.json';

    private $id = 1; 

    private $habitacionesGestor;

    public function __construct($habitacionesGestor)
    {
        $this->habitacionesGestor = $habitacionesGestor;
        $this->cargarDesdeJSON();
    }

    public function generarNuevoId()
    {
        return $this->id++;
    }

    public function agregarReserva(Reserva $reserva)
    {
        // trae la habitación asociada a la reserva desde el archivo JSON
        $habitacion = $this->habitacionesGestor->buscarHabitacionPorNumero($reserva->getHabitacion()->getNumero());

        foreach ($this->reservas as $existeReserva) {
            if ($existeReserva->getHabitacion()->getNumero() == $habitacion->getNumero() &&
                ! ($reserva->getFechaFin() < $existeReserva->getFechaInicio() ||
                  $reserva->getFechaInicio() > $existeReserva->getFechaFin())) {
                echo "La habitación ya está reservada en las fechas solicitadas.\n";

                return; 
            }
        }

        echo "Reserva creada con éxito.\n";

        $this->reservas[] = $reserva;
        $this->guardarEnJSON();
    }

    public function obtenerReservas()
    {
        return $this->reservas;
    }

    public function modificarReserva($id, $nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion, $nuevoCosto)
    {
        $reserva = $this->buscarReservaPorId($id); 
        if ($reserva) {
            $reserva->setFechaInicio($nuevaFechaInicio);
            $reserva->setFechaFin($nuevaFechaFin);
            $reserva->setHabitacion($nuevaHabitacion);
            $reserva->setCosto($nuevoCosto);
            $this->guardarEnJSON();
        } else {
            echo "Reserva no encontrada.\n";
        }
    }

    public function eliminarReserva($id)
    {
        foreach ($this->reservas as $indice => $reserva) {
            if ($reserva->getId() == $id) {
                unset($this->reservas[$indice]);
                $this->reservas = array_values($this->reservas); // reposicionamos el array para que no quede un lugar vacio
                $this->guardarEnJSON();

                return true;
            }
        }

        return false;
    }

    public function buscarReservaPorId($id)
    {
        foreach ($this->reservas as $reserva) {
            if ($reserva->getId() == $id) {
                return $reserva;
            }
        }

        return null;
    }

    // Guardar reservas en el archivo JSON
    public function guardarEnJSON()
    {
        $reservasArray = [];

        foreach ($this->reservas as $reserva) {
            $reservasArray[] = [
                'id' => $reserva->getId(),
                'fechaInicio' => $reserva->getFechaInicio(),
                'fechaFin' => $reserva->getFechaFin(),
                'habitacion' => $reserva->getHabitacion()->getNumero(), // guardamos solo el número de la habitación
                'costo' => $reserva->getCosto(),
                'usuarioDni' => $reserva->getUsuarioDni(), 
            ];
        }
        
        $datosNuevos = ['reservas' => $reservasArray]; //creo arreglo asociativo para guardar las reservas
        file_put_contents($this->reservaJson, json_encode($datosNuevos, JSON_PRETTY_PRINT)); //lo convierto a json y guardo

    }

    public function cargarDesdeJSON()
    {
        if (file_exists($this->reservaJson)) {
            $json = file_get_contents($this->reservaJson); //lee contenido del archvo
            $data = json_decode($json, true); //convierte el json en array

            if (isset($data['reservas'])) {
                $reservasArray = $data['reservas'];
            } else {
                $reservasArray = []; // Inicializa como un array vacío si no existe
            }

            foreach ($reservasArray as $reservaData) {
                $usuarioDni = isset($reservaData['usuarioDni']) ? $reservaData['usuarioDni'] : null;// si isset true carga usuarioDni, false es null
                $habitacion = $this->habitacionesGestor->buscarHabitacionPorNumero($reservaData['habitacion']);
                if (null === $habitacion) {
                    echo "Advertencia: La habitación número {$reservaData['habitacion']} no fue encontrada. Se omitirá esta reserva.\n";
                    continue;// omite la reserva que no tiene habitacion y continua por la sig
                }

                $reserva = new Reserva(
                    $reservaData['id'],
                    $reservaData['fechaInicio'],
                    $reservaData['fechaFin'],
                    $habitacion,
                    $reservaData['costo'],
                    $usuarioDni
                );
                $this->reservas[] = $reserva;

                // Asegurar que el ID este actualizado
                if ($this->id < $reserva->getId() + 1) {
                    $this->id = $reserva->getId() + 1;
                }
            }
        }
    }
}
