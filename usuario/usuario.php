<?php
require '../conexion/conexion.php';
session_start(); 

include 'header.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $id_oficina = $_POST['id_oficina'];
    $cargo = $_POST['cargo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Insertar en la tabla 'usuarios'
    $sql_insert = "INSERT INTO usuarios (nombre, apellido, correo, id_oficina, cargo, contrasena) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param('ssssss', $nombre, $apellido, $correo, $id_oficina, $cargo, $contrasena);

    if ($stmt->execute()) {
        echo "Usuario creado exitosamente.";
    } else {
        echo "Error al crear el usuario: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}

include 'footer.php';
?>

<!-- Formulario HTML -->
<form method="post">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br>

    <label for="apellido">Apellido:</label>
    <input type="text" id="apellido" name="apellido" required><br>

    <label for="correo">Correo:</label>
    <input type="email" id="correo" name="correo" required><br>

    <label for="id_oficina">Oficina:</label>
    <select id="id_oficina" name="id_oficina" required>
        <?php
        // Obtener las oficinas disponibles
        $sql_oficinas = "SELECT id_oficina, nombre_oficina FROM oficinas";
        $result_oficinas = $conn->query($sql_oficinas);
        while ($row_oficina = $result_oficinas->fetch_assoc()) {
            echo "<option value='" . $row_oficina['id_oficina'] . "'>" . $row_oficina['nombre_oficina'] . "</option>";
        }
        ?>
    </select><br>

    <label for="cargo">Cargo:</label>
    <input type="text" id="cargo" name="cargo" required><br>

    <label for="contrasena">Contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required><br>

    <input type="submit" value="Crear Usuario">
</form>
