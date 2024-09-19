<?php
session_start();
require '../conexion/conexion.php';

include 'header.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["correo"])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el correo del usuario desde la sesión
$correo_usuario = $_SESSION["correo"]; 


$sql = "SELECT nombre, apellido, correo, cargo, id_oficina FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Mostrar el perfil si se encuentra el usuario
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    echo "<div class='perfil-container'>";
    echo "<h1>Perfil de Usuario</h1>";
    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($usuario['nombre']) . "</p>";
    echo "<p><strong>Apellido:</strong> " . htmlspecialchars($usuario['apellido']) . "</p>";
    echo "<p><strong>Correo:</strong> " . htmlspecialchars($usuario['correo']) . "</p>";
    echo "<p><strong>Cargo:</strong> " . htmlspecialchars($usuario['cargo']) . "</p>";
    
    // Consulta para obtener el nombre de la oficina
    $id_oficina = $usuario['id_oficina'];
    $sql_oficina = "SELECT nombre_oficina FROM oficinas WHERE id_oficina = ?";
    $stmt_oficina = $conn->prepare($sql_oficina);
    $stmt_oficina->bind_param("i", $id_oficina);
    $stmt_oficina->execute();
    $result_oficina = $stmt_oficina->get_result();

    if ($result_oficina->num_rows > 0) {
        $oficina = $result_oficina->fetch_assoc();
        echo "<p><strong>Oficina:</strong> " . htmlspecialchars($oficina['nombre_oficina']) . "</p>";
    } else {
        echo "<p><strong>Oficina:</strong> No se encontró la oficina.</p>";
    }

    echo "</div>";
} else {
    echo "<p>No se encontró el perfil del usuario.</p>";
}


$stmt->close();
$stmt_oficina->close();
$conn->close();
include 'footer.php'
?>
