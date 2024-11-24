<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION["correo"])) {
    // Limpiar las variables de sesión
    session_unset();
    // Destruir la sesión
    session_destroy();
    // Redirigir a la página de inicio
    header("Location: ../index.php");
    exit();
} else {
    // Si no hay sesión activa, redirigir a la página de inicio
    header("Location: ../index.php");
    exit();
}
?>
