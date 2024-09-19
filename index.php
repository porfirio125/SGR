<?php
session_start();

require 'conexion/conexion.php';

// Limpiar variables de sesión antes de asignar nuevos valores
session_unset();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contrasena_input = $_POST["contrasena"]; 

    // Consulta para verificar el correo
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si el correo existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica la contraseña
        if ($contrasena_input == $row["contrasena"]) {
            // Credenciales correctas, guardar datos del usuario en la sesión
            $_SESSION["id_usuario"] = $row["id_usuario"];  // Asignar id_usuario a la sesión
            $_SESSION["correo"] = $correo;
            $_SESSION["nombre"] = $row["nombre"];
            $_SESSION["apellido"] = $row["apellido"];
            $_SESSION["id_oficina"] = $row["id_oficina"];
            $_SESSION["cargo"] = $row["cargo"];

            echo "Inicio de sesión exitoso. Redirigiendo...";
            // Redirigir a la página de usuario
            header("Location: usuario/index.php");
            exit();
        } else {
            $error_message = "Credenciales incorrectas. Inténtalo de nuevo.";
        }
    } else {
        $error_message = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
</head>
<body>
    <h2>Inicio de Sesión</h2>
    <form method="post" action="">
        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br><br>
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br><br>
        <input type="submit" value="Iniciar Sesión">
    </form>
    <?php
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>
</body>
</html>
