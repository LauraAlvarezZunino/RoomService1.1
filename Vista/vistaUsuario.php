<?php

$dniGuardado = null; // variable global
function menuUsuario()
{
    global $dniGuardado;

    // Inicializar los gestores necesarios
    $usuariosGestor = new UsuarioControlador;
    $habitacionesGestor = new HabitacionControlador;
    $reservasGestor = new ReservaControlador($habitacionesGestor);


    echo "=== Menú Usuario ===\n";
    echo "1. Registrarme\n";
    echo "2. Soy Usuario\n";
    echo 'Seleccione una opción: ';

    
    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            registrarse($usuariosGestor);
            break;

        case 2:
            echo 'Ingrese su clave para continuar: ';
            $clave = trim(fgets(STDIN));
            $claveGuardada = $clave;
            $usuario = $usuariosGestor->obtenerUsuarioPorclave($claveGuardada);
            if ($usuario) {
                menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor,$usuariosGestor);
            } else {
                echo "Clave no encontrada. Inténtelo de nuevo.\n";
                menuUsuario();
            }
            break;

        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            menuUsuario();

            break;
    }
}
function menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor, $usuariosGestor)
{
    while (true) {
        echo "\n=== Menú Usuario Registrado ===\n";
        echo "1. Ver Habitaciones\n";
        echo "2. Crear Reserva\n";
        echo "3. Mostrar Reservas\n";
        echo "4. Modificar Reserva\n";
        echo "5. Eliminar Reserva\n";
        echo "6. Ver mis datos\n";
        echo "7. Modificar mis datos\n";
        echo "8. Salir\n";
        echo 'Seleccione una opción: ';

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
case 1:
    verHabitaciones($habitacionesGestor); // Asegúrate de que esta función reciba los parámetros necesarios.
    break;
case 2:
    crearReserva($usuario, $habitacionesGestor, $reservasGestor, $usuariosGestor);
    break;
case 3:
    mostrarReservas($reservasGestor, false, $usuario);
    break;
case 4:
    modificarReserva($reservasGestor, $habitacionesGestor, false, $usuario);
    break;
case 5:
    eliminarReserva($reservasGestor, $usuario);
    break;
case 6:
    mostrarDatosUsuario($usuario);
    break;
case 7:
    modificarUsuario($usuario, false);
    break;
case 8:
    echo "Saliendo del sistema...\n";
    return;
default:
    // Este caso ya no sería necesario por la validación previa.
    echo "Opción no válida. Inténtelo de nuevo.\n";
    break;
}
}
}