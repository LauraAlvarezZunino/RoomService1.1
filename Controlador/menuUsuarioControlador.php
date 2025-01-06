<?php

function crearReserva($claveGuardada, $habitacionesGestor, $reservasGestor, $usuariosGestor) {
    global $claveGuardada;

    $usuario = $usuariosGestor->obtenerUsuarioPorClave($claveGuardada);
    if (!$usuario) {
        echo "No se encontró un usuario con la clave proporcionada. Por favor, verifica la clave.\n";
        return;
    }

    $dni = $usuario->getDni();
    $tipoHabitacion = solicitarTipoHabitacion();
    $habitacionesDisponibles = $habitacionesGestor->buscarPorTipo($tipoHabitacion);

    if (!empty($habitacionesDisponibles)) {
        mostrarHabitacionesDisponibles($habitacionesDisponibles);
        $habitacionSeleccionada = seleccionarHabitacion($habitacionesDisponibles);

        if ($habitacionSeleccionada) {
            [$fechaInicio, $fechaFin] = solicitarFechasReserva();
            $costo = calcularCostoReserva($fechaInicio, $fechaFin, $habitacionSeleccionada->getPrecio());
            $reservaId = $reservasGestor->generarNuevoId();

            $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, $habitacionSeleccionada, $costo, $dni);

            $reservasGestor->agregarReserva($reserva);
            echo "Reserva creada exitosamente.\n";
        }
    } else {
        echo "No se encontró una habitación disponible de ese tipo.\n";
    }
}

function calcularCostoReserva($fechaInicio, $fechaFin, $precioPorNoche) {
    $fechaInicio = new DateTime($fechaInicio);
    $fechaFin = new DateTime($fechaFin);
    $diferencia = $fechaInicio->diff($fechaFin);

    return $diferencia->days * $precioPorNoche;
}

function solicitarTipoHabitacion() {
    echo 'Ingrese el tipo de habitación para la reserva (simple - doble - familiar): ';
    return trim(fgets(STDIN));
}

function seleccionarHabitacion($habitaciones) {
    echo 'Seleccione una habitación (número): ';
    $eleccionHabitacion = trim(fgets(STDIN));

    foreach ($habitaciones as $habitacion) {
        if ($habitacion->getNumero() == $eleccionHabitacion) {
            return $habitacion;
        }
    }
    echo "No se encontró una habitación con ese número.\n";
    return null;
}

function solicitarFechasReserva() {
    $fechaInicio = '';
    $fechaFin = '';

    while (true) {
        echo 'Ingrese la fecha de inicio (YYYY-MM-DD): ';
        $fechaInicio = trim(fgets(STDIN));
        $fechaActual = date('Y-m-d');

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) && strtotime($fechaInicio) > strtotime($fechaActual)) {
            break;
        } else {
            echo "La fecha de inicio debe tener el formato YYYY-MM-DD y ser posterior a la fecha actual. Por favor, ingrese una fecha válida.\n";
        }
    }

    while (true) {
        echo 'Ingrese la fecha de fin (YYYY-MM-DD): ';
        $fechaFin = trim(fgets(STDIN));

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin) && strtotime($fechaFin) > strtotime($fechaInicio)) {
            break;
        } else {
            echo "La fecha de fin debe tener el formato YYYY-MM-DD y ser posterior a la fecha de inicio. Por favor, ingrese una fecha válida.\n";
        }
    }

    return [$fechaInicio, $fechaFin];
}

// Mostrar datos del usuario
function mostrarDatosUsuario() {
    global $claveGuardada;

    if (!$claveGuardada) {
        echo "No se proporcionó una clave válida.\n";
        return;
    }

    $usuarioControlador = new UsuarioControlador();
    $usuario = $usuarioControlador->obtenerUsuarioPorClave($claveGuardada);

    if ($usuario) {
        echo "-------------------------\n";
        echo 'DNI: ' . $usuario->getDni() . "\n";
        echo 'Nombre: ' . $usuario->getNombreApellido() . "\n";
        echo 'Correo electrónico: ' . $usuario->getEmail() . "\n";
        echo 'Teléfono: ' . $usuario->getTelefono() . "\n";
        echo "-------------------------\n";
    } else {
        echo "No se encontraron datos para el usuario con la clave proporcionada.\n";
    }
}

// Registro de usuario
function registrarse($usuariosGestor) {
    echo "=== Registro de Usuario ===\n";

    while (true) {
        echo 'Ingrese el nombre y apellido del usuario: ';
        $nombreApellido = trim(fgets(STDIN));
        if (preg_match("/^[a-zA-Z\s]+$/", $nombreApellido)) {
            break;
        } else {
            echo "Por favor, ingrese solo letras y espacios para el nombre y apellido.\n";
        }
    }

    while (true) {
        echo 'Ingrese el DNI del usuario sin puntos: ';
        $dni = trim(fgets(STDIN));
        if (preg_match("/^\d{7,8}$/", $dni)) {
            if ($usuariosGestor->obtenerUsuarioPorDni($dni)) {
                echo "El DNI ingresado ya está registrado. Intente nuevamente con otro DNI.\n";
                return;
            } else {
                break;
            }
        } else {
            echo "El DNI debe contener entre 7 y 8 dígitos. Por favor, intente nuevamente.\n";
        }
    }

    while (true) {
        echo 'Ingrese el email del usuario: ';
        $email = trim(fgets(STDIN));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            break;
        } else {
            echo "Por favor, ingrese un email válido.\n";
        }
    }

    while (true) {
        echo 'Ingrese el teléfono del usuario: ';
        $telefono = trim(fgets(STDIN));
        if (preg_match("/^\d+$/", $telefono)) {
            break;
        } else {
            echo "El teléfono debe contener solo números. Por favor, intente nuevamente.\n";
        }
    }

    while (true) {
        echo 'Ingrese la clave del usuario: ';
        $clave = trim(fgets(STDIN));
        if (preg_match("/^[a-zA-Z0-9]+$/", $clave)) {
            break;
        } else {
            echo "La clave debe contener solo letras y números. Por favor, intente nuevamente.\n";
        }
    }

    $usuariosGestor->crearUsuario($nombreApellido, $dni, $email, $telefono, $clave);
    echo "Usuario agregado exitosamente.\n";

    menuUsuario();
}
