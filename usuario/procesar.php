<?php

require '../conexion/conexion.php';
session_start();
$id_usuario = $_SESSION['id_usuario']; 

if (!isset($_GET['id_requerimiento']) || !isset($_POST['accion'])) {
    echo "Acción no válida.";
    exit();
}

$id_requerimiento = $_GET['id_requerimiento'];
$accion = $_POST['accion'];
$descripcion = $_POST['descripcion'];
$id_usuario = $_SESSION['id_usuario'];
$id_oficina = $_SESSION['id_oficina'];

// Función para obtener la oficina anterior basado en el flujo
function obtener_oficina_anterior($id_flujo, $conn) {
    $query = "SELECT id_oficina FROM flujo_oficinas WHERE id_flujo = ? ORDER BY orden DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_flujo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Retornar la oficina anterior
    if ($row = $result->fetch_assoc()) {
        return $row['id_oficina'];
    }
    return null;
}

// Función para obtener la oficina siguiente basado en el flujo
function obtener_oficina_siguiente($id_flujo, $conn) {
    $query = "SELECT id_oficina FROM flujo_oficinas WHERE id_flujo = ? AND orden > 0 ORDER BY orden ASC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_flujo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Retornar la oficina siguiente
    if ($row = $result->fetch_assoc()) {
        return $row['id_oficina'];
    }
    return null;
}

// Obtener el último flujo para determinar la oficina anterior y siguiente
$query = "SELECT id_flujo FROM historial_requerimientos WHERE id_requerimiento = ? ORDER BY fecha_revision DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_requerimiento);
$stmt->execute();
$result = $stmt->get_result();
$flujo = $result->fetch_assoc();

if (!$flujo) {
    echo "Requerimiento no encontrado.";
    exit();
}

$id_flujo = $flujo['id_flujo'];

// Determinar la acción
if ($accion === 'rechazar') {
    // Lógica para regresar a la oficina anterior
    $id_oficina_anterior = obtener_oficina_anterior($id_flujo, $conn);

    if ($id_oficina_anterior !== null) {
        // Agregar al historial
        $query = "INSERT INTO historial_requerimientos (id_requerimiento, id_usuario, id_oficina, fecha_revision, comentario, estado, id_flujo) VALUES (?, ?, ?, NOW(), ?, 'Rechazado', ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiisi", $id_requerimiento, $id_usuario, $id_oficina_anterior, $descripcion, $id_flujo);
    } else {
        echo "No se puede regresar a una oficina anterior.";
        exit();
    }
    
} elseif ($accion === 'derivar') {
    // Lógica para derivar a la siguiente oficina
    $id_oficina_siguiente = obtener_oficina_siguiente($id_flujo, $conn);

    if ($id_oficina_siguiente !== null) {
        // Agregar al historial
        $query = "INSERT INTO historial_requerimientos (id_requerimiento, id_usuario, id_oficina, fecha_revision, comentario, estado, id_flujo) VALUES (?, ?, ?, NOW(), ?, 'Aprobado', ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiisi", $id_requerimiento, $id_usuario, $id_oficina_siguiente, $descripcion, $id_flujo);
    } else {
        echo "No se puede derivar a una oficina siguiente.";
        exit();
    }
}


if (isset($stmt) && $stmt->execute()) {
    echo "Requerimiento procesado exitosamente.";
} else {
    echo "Error al procesar el requerimiento: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
