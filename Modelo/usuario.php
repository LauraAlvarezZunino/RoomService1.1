<?php

class Usuario
{
    private $id;

    private $nombreApellido;

    private $dni;

    private $email;

    private $telefono;
    
    private $clave;

    public function __construct($id, $nombreApellido, $dni, $email, $telefono,$clave)
    {
        $this->id = $id;
        $this->nombreApellido = $nombreApellido;
        $this->dni = $dni;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->clave = $clave;
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

    public function getNombreApellido()
    {
        return $this->nombreApellido;
    }

    public function setNombreApellido($nombreApellido)
    {
        $this->nombreApellido = $nombreApellido;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function setClave($clave)
    {
        $this->clave=$clave;
    }
    
    public function getClave()
    {
        return $this->clave;
    }
    
    public function __toString()
    {
        return 'ID: ' . $this->id . ', Nombre: ' . $this->nombreApellido . ', DNI: ' . $this->dni . ', Email: ' . $this->email . ', Teléfono: ' . $this->telefono;
    }

}    