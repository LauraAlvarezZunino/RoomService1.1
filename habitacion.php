<?php

class Habitacion
{
    protected $numero;

    protected $tipo;

    protected $precio;

    public function __construct($numero = null, $tipo = null, $precio = null)
    {
        $this->numero = $numero;
        $this->tipo = $tipo;
        $this->precio = $precio;

    }

    // Getters y Setters
    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function __toString()
    {

        return "Habitación Número: {$this->numero}, Tipo: {$this->tipo}, Precio: {$this->precio}";
    }
}
