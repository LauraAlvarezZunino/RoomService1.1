 <?php

include_once 'Controlador/usuarioControlador.php';
include_once 'Controlador/habitacionControlador.php';
include_once 'Modelo/reserva.php';
include_once 'Controlador/reservaControlador.php';
include_once 'Vista/vistaUsuario.php';
include_once 'Vista/vistaAdmin.php';
include_once 'Controlador/menuControlador.php';
include_once 'Controlador/menuUsuarioControlador.php';
include_once 'Controlador/menuAdminControlador.php';

while (true) {
    $clave = 111;
    echo "===Bienvenido===\n";
    echo "1. Administrador\n";
    echo "2. Usuario\n";
    echo "3. Salir\n";

    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            echo 'Ingrese la clave: ';
            $claveAdmin = trim(fgets(STDIN));
            if ($clave == $claveAdmin) {
                menuAdmin();
            } else {
                echo "Clave Erronea.\n";
            }
            break;
        case 2:
            menuUsuario();
            break;
        case 3:
            echo "Saliendo del sistema...\n";
            exit;
        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            break;
    }
}
