<?php

require_once 'Modelo/usuario.php';
class UsuarioControlador
{
    private $usuarios = [];

    private $usuarioJson = 'usuario.json';

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }

    public function crearUsuario($nombreApellido, $dni, $email, $telefono)
    {
        $nuevoId = $this->generarNuevoId();
        $usuario = new Usuario($nuevoId, $nombreApellido, $dni, $email, $telefono);
        $this->usuarios[] = $usuario;
        $this->guardarEnJSON();
    }

    // Generar un nuevo ID basado en el último ID existente
    private function generarNuevoId()
    {
        if (empty($this->usuarios)) {
            return 1; // Si no hay usuarios, el primer ID es 1
        } else {
            $ultimoUsuario = end($this->usuarios); //end busca el ultimo elemento del arreglo

            return $ultimoUsuario->getId() + 1;
        }
    }

    public function obtenerUsuarios()
    {
        return $this->usuarios;
    }

    public function obtenerUsuarioPorId($id)
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getId() == $id) {
                return $usuario;
            }
        }

        return null;
    }

    public function obtenerUsuarioPorDni($dni)
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getDni() == $dni) {
                return $usuario;
            }
        }

        return null;
    }

    public function actualizarUsuario($id, $nuevosDatos)
    {
        foreach ($this->usuarios as &$usuario) {
            if ($usuario->getId() == $id) {
                if (isset($nuevosDatos['nombre'])) {
                    $usuario->setNombreApellido($nuevosDatos['nombre']);
                } else {
                    $usuario->setNombreApellido($usuario->getNombreApellido());
                }

                if (isset($nuevosDatos['email'])) {
                    $usuario->setEmail($nuevosDatos['email']);
                } else {
                    $usuario->setEmail($usuario->getEmail());
                }

                if (isset($nuevosDatos['telefono'])) {
                    $usuario->setTelefono($nuevosDatos['telefono']);
                } else {
                    $usuario->setTelefono($usuario->getTelefono());
                }

                $this->guardarEnJSON();

                return true;
            }
        }

        return false;
    }

 
    public function eliminarUsuario($id)
{
    foreach ($this->usuarios as $indice => $usuario) {
        if ($usuario->getId() == $id) {
            unset($this->usuarios[$indice]);
            $this->usuarios = array_values($this->usuarios); 
            $this->guardarEnJSON(); 

            return true;
        }
    }

    return false; 
}

    private function guardarEnJSON()
    {
        $usuariosArray = array_map([$this, 'usuarioToArray'], $this->usuarios); //aplica una funcion a cada elemento de uno o mas arrays
        $jsonUsuario = json_encode(['usuarios' => $usuariosArray], JSON_PRETTY_PRINT);
        file_put_contents($this->usuarioJson, $jsonUsuario);
    }

    
    private function usuarioToArray($usuario)
    {
        return [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombreApellido(),
            'dni' => $usuario->getDni(),
            'email' => $usuario->getEmail(),
            'telefono' => $usuario->getTelefono(),
        ];
    }
    
    private function cargarDesdeJSON()
    {
        if (file_exists($this->usuarioJson)) {
            $jsonUsuarios = file_get_contents($this->usuarioJson);

            // hacemos un array del json 
            $data = json_decode($jsonUsuarios, true);

            //  json tiene la clave usuarios?
            if (isset($data['usuarios'])) {
                $usuariosArray = $data['usuarios'];

              
                foreach ($usuariosArray as $usuarioData) {
                    $usuario = new Usuario(
                        $usuarioData['id'],
                        $usuarioData['nombre'],
                        $usuarioData['dni'],
                        $usuarioData['email'],
                        $usuarioData['telefono']
                    );
                    $this->usuarios[] = $usuario;
                }
            }
        }
    }
}
