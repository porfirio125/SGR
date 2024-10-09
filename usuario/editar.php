<?php

require '../conexion/conexion.php';
session_start();
$id_usuario = $_SESSION['id_usuario']; 

if (!isset($_GET['id_requerimiento'])) {
    echo "ID de requerimiento no especificado.";
    exit();
}

$id_requerimiento = $_GET['id_requerimiento'];

// Obtener información del requerimiento
$query = "SELECT * FROM historial_requerimientos WHERE id_requerimiento = ? ORDER BY fecha_revision DESC LIMIT 1"; // Solo el último historial
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_requerimiento);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $descripcion = $row['comentario']; 
    $estado = $row['estado'];
    $id_flujo = $row['id_flujo'];
} else {
    echo "Requerimiento no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Requerimiento</title>
</head>
<body>
    <h2>Editar Requerimiento</h2>
    <form method="post" action="procesar.php?id_requerimiento=<?php echo $id_requerimiento; ?>">
        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($descripcion); ?></textarea><br><br>

        <label for="estado">Estado:</label><br>
        <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($estado); ?>" readonly><br><br>

        <button type="submit" name="accion" value="rechazar">Rechazar</button>
        <button type="submit" name="accion" value="derivar">Derivar</button>
    </form>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
