<?php
require '../conexion/conexion.php';
session_start();

if (!isset($_GET['id_requerimiento'])) {
    echo "ID de requerimiento no especificado.";
    exit();
}

$id_requerimiento = $_GET['id_requerimiento'];

// Actualizar el estado del requerimiento a "Archivado" en la tabla historial_requerimientos
$query_update = "UPDATE historial_requerimientos SET estado = 'Archivado' WHERE id_requerimiento = ?";
$stmt_update = $conn->prepare($query_update);
$stmt_update->bind_param("i", $id_requerimiento);

if ($stmt_update->execute()) {
    echo "<p class='alert alert-success mt-3'>Requerimiento archivado correctamente.</p>";
    header("Location: index.php?filtro=pendientes"); // Redirigir a la página de requerimientos pendientes
    exit();
} else {
    echo "<p class='alert alert-danger mt-3'>Error al archivar el requerimiento: " . $stmt_update->error . "</p>";
}

$stmt_update->close();
$conn->close();
?>
