<?php

//RESERVAS

function modificarReserva($reservasGestor, $habitacionesGestor, $esAdmin = false, $usuario = null)
{
    global $dniGuardado;
    echo 'Ingrese el ID de la reserva que desea modificar: ';
    $id = trim(fgets(STDIN));
    $reserva = $reservasGestor->buscarReservaPorId($id);

    // Verificar si la reserva existe y si el usuario es el dueño, a menos que sea un administrador
    if (! $reserva || (! $esAdmin && $reserva->getUsuarioDni() !== $dniGuardado)) {
        echo "Reserva no encontrada o no tiene permisos para modificar esta reserva.\n";

        return;
    }

    echo "Modificando Reserva ID: {$reserva->getId()}\n";
    echo 'Fecha Inicio actual: ' . $reserva->getFechaInicio() . "\n";
    echo 'Fecha Fin actual: ' . $reserva->getFechaFin() . "\n";
    echo 'Habitación actual: ' . $reserva->getHabitacion()->getNumero() . "\n";
    echo 'Costo actual: $' . $reserva->getCosto() . "\n";

    echo 'Ingrese la nueva fecha de inicio (YYYY-MM-DD) o deje vacío para mantener la actual: ';
    $nuevaFechaInicio = trim(fgets(STDIN));
    $nuevaFechaInicio = $nuevaFechaInicio ?: $reserva->getFechaInicio();  // ?: operador de fusión de valores nulos  

    echo 'Ingrese la nueva fecha de fin (YYYY-MM-DD) o deje vacío para mantener la actual: ';
    $nuevaFechaFin = trim(fgets(STDIN));
    $nuevaFechaFin = $nuevaFechaFin ?: $reserva->getFechaFin();

       $fechaActual = date('Y-m-d');

    if ($nuevaFechaInicio !== $reserva->getFechaInicio()) {  // Solo validar si se cambió la fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaFechaInicio)) {
            echo "Por favor, ingrese una fecha válida.\n";
            return;
        }
    
        // Comparar con la fecha actual
        if (strtotime($nuevaFechaInicio) < strtotime($fechaActual)) {
            echo "Por favor, ingrese una fecha válida.\n";
            return;
        }
    }
    
    // Validar formato de fecha de fin y que sea posterior a la fecha de inicio
    if ($nuevaFechaFin !== $reserva->getFechaFin()) {  // Solo validar si se cambió la fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaFechaFin)) {
            echo "Por favor, ingrese una fecha válida.\n";
            return;
        }
    
        // Comparar que la fecha de fin sea posterior a la de inicio
        if (strtotime($nuevaFechaFin) < strtotime($nuevaFechaInicio)) {
            echo "Por favor, ingrese una fecha válida.\n";
            return;
        }
    }
    
    echo 'Ingrese el nuevo número de habitación o deje vacío para mantener la actual: ';
    $nuevoNumeroHabitacion = trim(fgets(STDIN));
    if ($nuevoNumeroHabitacion) {
        $nuevaHabitacion = $habitacionesGestor->buscarHabitacionPorNumero($nuevoNumeroHabitacion);
        if (! $nuevaHabitacion) {
            echo "Habitación no encontrada.\n";

            return;
        }
    } else {
        $nuevaHabitacion = $reserva->getHabitacion();
    }

    $nuevoCosto = calcularCostoReserva($nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion->getPrecio());

    // Actualizar la reserva con los nuevos valores
    $reserva->setFechaInicio($nuevaFechaInicio);
    $reserva->setFechaFin($nuevaFechaFin);
    $reserva->setHabitacion($nuevaHabitacion);
    $reserva->setCosto($nuevoCosto);

    $reservasGestor->guardarEnJSON();
    echo 'Reserva actualizada correctamente. Nuevo costo: $' . $nuevoCosto . "\n";
}

function mostrarReservas($reservasGestor, $esAdmin = false, $usuario = null)
{
    global $dniGuardado;
    $reservas = $reservasGestor->obtenerReservas();
    $tieneReservas = false;

    foreach ($reservas as $reserva) {
        // Si no es administrador, mostramos solo las reservas del usuario actual
        if ($esAdmin || ($usuario && $reserva->getUsuarioDni() === $dniGuardado)) {
            echo "-------------------------\n";
            echo 'ID: ' . $reserva->getId() . "\n";
            echo 'Fecha Inicio: ' . $reserva->getFechaInicio() . "\n";
            echo 'Fecha Fin: ' . $reserva->getFechaFin() . "\n";
            echo 'Habitación: ' . $reserva->getHabitacion()->getNumero() . ' (' . $reserva->getHabitacion()->getTipo() . ")\n";
            echo 'Costo Total: $' . $reserva->getCosto() . "\n";
            echo 'Usuario DNI: ' . $reserva->getUsuarioDni() . "\n";
            echo "-------------------------\n";
            $tieneReservas = true;
        }
    }

    if (! $tieneReservas) {
        echo $esAdmin ? "No hay reservas registradas.\n" : "No tienes reservas registradas.\n";   //condición ? valorSiVerdadero : valorSiFalso;
    }
}

function eliminarReserva($reservasGestor, $usuario = null, $esAdmin = false)
{
    echo 'Ingrese el ID de la reserva que desea eliminar: ';
    $idEliminar = trim(fgets(STDIN));
    $reserva = $reservasGestor->buscarReservaPorId($idEliminar);

    // Si no es administrador, verificamos que la reserva pertenezca al usuario
    if (! $reserva || (! $esAdmin && $reserva->getUsuarioDni() !== $usuario->getDni())) {
        echo "Reserva no encontrada o no pertenece a este usuario.\n";

        return;
    }

    $reservasGestor->eliminarReserva($idEliminar);
    echo "Reserva eliminada con éxito.\n";
}

//USUARIOS

function modificarUsuario($usuario, $esAdministrador = false)
{
    global $dniGuardado;
    $usuariosGestor = new UsuarioControlador;
    $usuario = $usuariosGestor->obtenerUsuarioPorDni($dniGuardado);

    if ($esAdministrador) {
        echo 'Ingrese el ID del usuario que quiere modificar: ';
        $id = trim(fgets(STDIN));
    } else {
        if (! $usuario) {
            echo "Usuario no encontrado o no autorizado.\n";

            return false;
        }
        $id = $usuario->getId();
    }

    $usuario = $usuariosGestor->obtenerUsuarioPorId($id);

    if (! $usuario) {
        echo "Usuario no encontrado.\n";

        return false;
    }

    echo "Modificando al usuario con ID: {$usuario->getId()}\n";
    echo 'Nombre actual: ' . $usuario->getNombreApellido() . "\n";
    echo 'DNI actual: ' . $usuario->getDni() . "\n";
    echo 'Email actual: ' . $usuario->getEmail() . "\n";
    echo 'Teléfono actual: ' . $usuario->getTelefono() . "\n";

    echo 'Introduce el nuevo nombre (deja vacío para mantener el actual): ';
    $nombreApellido = trim(fgets(STDIN));

    echo 'Introduce el nuevo email (deja vacío para mantener el actual): ';
    $email = trim(fgets(STDIN));

    echo 'Introduce el nuevo teléfono (deja vacío para mantener el actual): ';
    $telefono = trim(fgets(STDIN));

    $nuevosDatos = [
        'nombre' => $nombreApellido ?: null,
        'email' => $email ?: null,
        'telefono' => $telefono ?: null,
    ];

    if ($usuariosGestor->actualizarUsuario($id, $nuevosDatos)) {
        echo "Usuario actualizado correctamente.\n";
    } else {
        echo "No se pudo actualizar el usuario.\n";
    }
}

//HABITACIONES

function verHabitaciones()
{
    $habitacionesGestor = new HabitacionControlador;
    $habitacionesGestor->cargarDesdeJSON();
    $habitaciones = $habitacionesGestor->obtenerHabitaciones();
    foreach ($habitaciones as $habitacion) {
        echo $habitacion . "\n";
    }
}
function mostrarHabitacionesDisponibles($habitaciones)
{
    echo "Habitaciones disponibles:\n";
    foreach ($habitaciones as $index => $habitacion) {
        echo $index . '. Número: ' . $habitacion->getNumero() . ' - Precio por noche: ' . $habitacion->getPrecio() . "\n";
    }
}
