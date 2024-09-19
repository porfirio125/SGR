<?php
session_start();

require '../conexion/conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["correo"])) {
    header("Location: ../index.php");
    exit();
}
include 'header.php';

// Verificar si se ha enviado el formulario de cierre de sesión
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Consultar el contenido de la tabla historial_requerimientos según el usuario
$id_usuario = $_SESSION["id_usuario"];
$id_oficina = $_SESSION["id_oficina"];

// Obtener el nombre de la oficina
$sql_oficina = "SELECT nombre_oficina FROM oficinas WHERE id_oficina = ?";
$stmt_oficina = $conn->prepare($sql_oficina);
$stmt_oficina->bind_param("i", $id_oficina);
$stmt_oficina->execute();
$result_oficina = $stmt_oficina->get_result();
$nombre_oficina = $result_oficina->fetch_assoc()['nombre_oficina'];

// Obtener el nombre del usuario
$sql_usuario = "SELECT nombre, apellido FROM usuarios WHERE id_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $id_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();
$nombre_usuario = htmlspecialchars($usuario['nombre']);

// obtener Flujo
$sql_flujo = "SELECT flujo FROM tipos_requerimiento WHERE id";


$sql = "SELECT * FROM historial_requerimientos WHERE id_usuario = ? AND id_oficina = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_oficina); // Cambiar el tipo de parámetro a "ii"
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID Historial</th>
                <th>ID Requerimiento</th>
                <th>Usuario</th>
                <th>Oficina</th>
                <th>Fecha Revisión</th>
                <th>Comentario</th>
                <th>Estado</th>
                <th>ID Flujo</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id_historial"] . "</td>
                <td>" . $row["id_requerimiento"] . "</td>
                <td>" . $nombre_usuario . "</td>
                <td>" . htmlspecialchars($nombre_oficina) . "</td>
                <td>" . $row["fecha_revision"] . "</td>
                <td>" . $row["comentario"] . "</td>
                <td>" . $row["estado"] . "</td>
                <td>" . $row["id_flujo"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No hay registros en el historial de requerimientos para este usuario y oficina.";
}

$stmt_oficina->close();
$stmt_usuario->close();
$conn->close();

include 'footer.php'
?>
