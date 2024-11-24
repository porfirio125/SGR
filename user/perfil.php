<?php
session_start();
require '../conexion/conexion.php';

include 'header.php';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
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
    echo "<div class='container mt-5'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-8'>";
    echo "<div class='card border-danger shadow p-3 mb-5 bg-body rounded'>"; // Añadido para darle sombra y redondeo
    echo "<div class='card-header bg-danger text-white'>";
    echo "<h2 class='text-center'>Perfil de Usuario</h2>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<div class='perfil-info'>";

    // Se utiliza Bootstrap para un mejor diseño
    echo "<table class='table table-bordered'>";
    echo "<tr><th>Nombre</th><td>" . htmlspecialchars($usuario['nombre']) . "</td></tr>";
    echo "<tr><th>Apellido</th><td>" . htmlspecialchars($usuario['apellido']) . "</td></tr>";
    echo "<tr><th>Correo</th><td>" . htmlspecialchars($usuario['correo']) . "</td></tr>";
    echo "<tr><th>Cargo</th><td>" . htmlspecialchars($usuario['cargo']) . "</td></tr>";

    // Consulta para obtener el nombre de la oficina
    $id_oficina = $usuario['id_oficina'];
    $sql_oficina = "SELECT nombre_oficina FROM oficinas WHERE id_oficina = ?";
    $stmt_oficina = $conn->prepare($sql_oficina);
    $stmt_oficina->bind_param("i", $id_oficina);
    $stmt_oficina->execute();
    $result_oficina = $stmt_oficina->get_result();

    if ($result_oficina->num_rows > 0) {
        $oficina = $result_oficina->fetch_assoc();
        echo "<tr><th>Oficina</th><td>" . htmlspecialchars($oficina['nombre_oficina']) . "</td></tr>";
    } else {
        echo "<tr><th>Oficina</th><td>No se encontró la oficina.</td></tr>";
    }
    echo "</table>";


    echo "</div>";
    echo "<a href='editar_perfil.php?correo=" . urlencode($usuario['correo']) . "' class='btn btn-warning w-100 mt-3'>Editar Usuario</a>"; // Botón para editar usuario
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<p>No se encontró el perfil del usuario.</p>";
}

$stmt->close();
$stmt_oficina->close();
$conn->close();
include 'footer.php';
?>
