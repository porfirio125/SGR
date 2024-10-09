<?php

include('../conexion/conexion.php');

session_start();
$id_usuario = $_SESSION['id_usuario']; 

include 'header.php';
include 'footer.php';

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
    echo "<h2>Historial del Requerimiento</h2>";
    while ($row = $result_historial->fetch_assoc()) {
        echo "Oficina: " . $row['nombre_oficina'] . "<br>";
        echo "Fecha de Revisión: " . $row['fecha_revision'] . "<br>";
        echo "Estado: " . $row['estado'] . "<br>";
        echo "Comentario: " . $row['comentario'] . "<br><hr>";
    }
} else {
    echo "No hay historial para este requerimiento.";
}

$stmt_historial->close();
$conn->close();
?>

<a href="requerimientos.php" class="btn-flotante">Volver a Requerimientos</a>

<style>
    .btn-flotante {
        position: fixed;
        width: 200px;
        height: 50px;
        bottom: 40px;
        right: 40px;
        background-color: red;
        color: white;
        text-align: center;
        line-height: 50px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 25px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        text-decoration: none;
        margin-bottom: 100px;
    }

    .btn-flotante:hover {
        background-color: #0056b3;
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.3);
    }
</style>