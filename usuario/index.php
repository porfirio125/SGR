<?php
// Conectar a la base de datos
include('../conexion/conexion.php');
// Obtener el ID del usuario (este valor puede venir de la sesión)
session_start();
$id_usuario = $_SESSION['id_usuario'];  // Suponiendo que tienes el ID del usuario guardado en la sesión
$id_oficina = $_SESSION['id_oficina'];  // Suponiendo que tienes el ID de la oficina del usuario guardado en la sesión
include 'header.php';

// Consultar los requerimientos creados por el usuario
$query = "SELECT id_requerimiento, descripcion, estado, fecha_creacion FROM requerimientos WHERE id_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Mostrar los requerimientos creados por el usuario
echo "<h2>Mis Requerimientos</h2>";
echo "<table border='1'>
      <tr>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Fecha de Creación</th>
        <th>Acciones</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['descripcion'] . "</td>";
    echo "<td>" . $row['estado'] . "</td>";
    echo "<td>" . $row['fecha_creacion'] . "</td>";
    echo "<td><a href='historial.php?id_requerimiento=" . $row['id_requerimiento'] . "'>Ver Historial</a></td>";
    echo "</tr>";
}

echo "</table>";

// Consultar los requerimientos pendientes de revisión según el flujo
$query_pending = "SELECT hr.id_requerimiento, r.descripcion, hr.estado, hr.fecha_revision 
                  FROM historial_requerimientos hr
                  JOIN requerimientos r ON hr.id_requerimiento = r.id_requerimiento
                  JOIN flujo_oficinas fo ON hr.id_flujo = fo.id_flujo
                  WHERE hr.estado = 'Pendiente' 
                  AND fo.id_oficina = ?
                  AND hr.id_flujo = (SELECT MAX(id_flujo) FROM historial_requerimientos WHERE id_requerimiento = hr.id_requerimiento)";
$stmt_pending = $conn->prepare($query_pending);
$stmt_pending->bind_param("i", $id_oficina);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();

// Mostrar los requerimientos pendientes de revisión
echo "<h2>Requerimientos Pendientes de Revisión</h2>";
echo "<table border='1'>
      <tr>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Fecha de Revisión</th>
        <th>Acciones</th>
      </tr>";

while ($row_pending = $result_pending->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row_pending['descripcion'] . "</td>";
    echo "<td>" . $row_pending['estado'] . "</td>";
    echo "<td>" . $row_pending['fecha_revision'] . "</td>";
    echo "<td><a href='editar.php?id_requerimiento=" . $row_pending['id_requerimiento'] . "'>Revisar</a></td>";
    echo "</tr>";
}

echo "</table>";

include 'footer.php';
// Cerrar la conexión
$stmt->close();
$stmt_pending->close();
$conn->close();
?>
