<?php

include('../conexion/conexion.php');
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
session_start();
$id_usuario = $_SESSION['id_usuario']; 

include 'header.php';

echo '<div class="sticky-top">
        <div class="d-flex justify-content-center my-3">
            <a href="index.php" class="btn btn-primary">Volver a Requerimientos</a>
        </div>
    </div>';
// Obtener el ID del requerimiento desde la URL
$id_requerimiento = $_GET['id_requerimiento'];
$id_usuario_sesion = $_SESSION['id_usuario']; // ID del usuario logueado

// Validar que el requerimiento pertenece al usuario logueado
$query_validacion = "SELECT id_usuario FROM requerimientos WHERE id_requerimiento = ?";
$stmt_validacion = $conn->prepare($query_validacion);
if (!$stmt_validacion) {
    die("Error en la consulta: " . $conn->error);
}
$stmt_validacion->bind_param("i", $id_requerimiento);
$stmt_validacion->execute();
$stmt_validacion->bind_result($id_usuario_requerimiento);
$stmt_validacion->fetch();
$stmt_validacion->close();

// Si el requerimiento no pertenece al usuario logueado, denegar acceso
if ($id_usuario_requerimiento != $id_usuario_sesion) {
    echo "No tienes permiso para ver este historial.";
    exit();
}

// Consultar el historial del requerimiento
$query_historial = "SELECT h.id_oficina, h.fecha_revision, h.estado, h.comentario, o.nombre_oficina 
                    FROM historial_requerimientos h 
                    JOIN oficinas o ON h.id_oficina = o.id_oficina
                    WHERE h.id_requerimiento = ?";
$stmt_historial = $conn->prepare($query_historial);
if (!$stmt_historial) {
    die("Error en la consulta: " . $conn->error);
}
$stmt_historial->bind_param("i", $id_requerimiento);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();

// Verificar si hay registros en el historial
if ($result_historial->num_rows > 0) {
    echo "<div class='container mt-5'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-8'>";
    echo "<div class='card border-danger'>";
    echo "<div class='card-header bg-danger text-white'>";
    echo "<h2 class='text-center mb-4'>Historial del Requerimiento</h2>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<table class='table table-striped'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th scope='col'>Oficina</th>";
    echo "<th scope='col'>Fecha de Revisión</th>";
    echo "<th scope='col'>Estado</th>";
    echo "<th scope='col'>Comentario</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result_historial->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['nombre_oficina'] . "</td>";
        echo "<td>" . $row['fecha_revision'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "<td>" . $row['comentario'] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-8'>";
    echo "<div class='card border-danger'>";
    echo "<div class='card-header bg-danger text-white'>";
    echo "<h2 class='text-center mb-4'>Historial del Requerimiento</h2>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<p class='text-center'>No hay historial para este requerimiento.</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

$stmt_historial->close();
$conn->close();
?>