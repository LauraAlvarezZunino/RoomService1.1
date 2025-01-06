<?php

require_once 'Modelo/habitacion.php';

class HabitacionControlador
{
    private $habitaciones = [];

    private $archivoJson = 'habitacion.json';

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }
    // CRUD

    public function agregarHabitacion($habitacion)
    {
        $this->habitaciones[] = $habitacion;
        $this->guardarEnJSON();

    }

    public function obtenerHabitaciones()
    {
        return $this->habitaciones;
    }

    public function buscarHabitacionPorNumero($numero)
    {
        foreach ($this->habitaciones as $habitacion) {
            if ($habitacion->getNumero() == $numero) {
                return $habitacion;
            }
        }

        return null; // si no se encuentra la habitación
    }

    public function buscarPorTipo($tipo)
    {
        $resultados = [];
        $tipo = strtolower($tipo); // starlower pasa a minuscula

        foreach ($this->habitaciones as $habitacion) {
            if (strtolower($habitacion->getTipo()) == $tipo) { 
                $resultados[] = $habitacion;
            }
        }

        return $resultados;
    }

    public function actualizarHabitacion($numero, $nuevosDatos)
    {
        foreach ($this->habitaciones as &$habitacion) {
            if ($habitacion->getNumero() == $numero) {
                if (isset($nuevosDatos['tipo'])) { //isset chequea que no es nulo
                    $habitacion->setTipo($nuevosDatos['tipo']);
                } else {
                    $habitacion->setTipo($habitacion->getTipo());
                }

                if (isset($nuevosDatos['precio'])) {
                    $habitacion->setPrecio($nuevosDatos['precio']);
                } else {
                    $habitacion->setPrecio($habitacion->getPrecio());
                }

                $this->guardarEnJSON();

                return true;
            }
        }

        return false;
    }

    public function eliminarHabitacion($numero)
{
    foreach ($this->habitaciones as $indice => $habitacion) {
        if ($habitacion->getNumero() == $numero) {
            unset($this->habitaciones[$indice]);
            $this->habitaciones = array_values($this->habitaciones); // Reacomoda los indices del array  
            $this->guardarEnJSON(); 

            return true;
        }
    }

    return false; 
}

    // Json

    public function guardarEnJSON()
    {
        $habitacionesArray = [];

        foreach ($this->habitaciones as $habitacion) {
            $habitacionesArray[] = $this->habitacionToArray($habitacion);
        }

        $jsonHabitacion = json_encode(['habitacion' => $habitacionesArray], JSON_PRETTY_PRINT);
        file_put_contents($this->archivoJson, $jsonHabitacion);
    }

    public function habitacionToArray($habitacion)
    {
        return [
            'numero' => $habitacion->getNumero(),
            'tipo' => $habitacion->getTipo(),
            'precio' => $habitacion->getPrecio(),

        ];
    }

    public function cargarDesdeJSON()
    {
        if (file_exists($this->archivoJson)) { //existe?
            $jsonHabitacion = file_get_contents($this->archivoJson); //lo lee y lo guarda
            $habitacionesArray = json_decode($jsonHabitacion, true)['habitacion'];
            $this->habitaciones = []; // Asegura que se vacie el array antes de cargar los datos

            foreach ($habitacionesArray as $habitacionData) {
                $habitacion = new Habitacion;
                $habitacion->setNumero($habitacionData['numero']);
                $habitacion->setTipo($habitacionData['tipo']);
                $habitacion->setPrecio($habitacionData['precio']);
                $this->habitaciones[] = $habitacion;
            }
        }
    }
}
