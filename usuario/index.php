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

// Consultar todos los requerimientos del usuario
$sql_principal = "SELECT * FROM historial_requerimientos WHERE id_usuario = ? AND id_oficina = ? ORDER BY fecha_revision DESC";
$stmt_principal = $conn->prepare($sql_principal);
$stmt_principal->bind_param("ii", $id_usuario, $id_oficina);
$stmt_principal->execute();
$result_principal = $stmt_principal->get_result();

if ($result_principal->num_rows > 0) {
    echo "<div style='border: 1px solid #ddd; padding: 20px; margin-top: 20px; border-radius: 10px;'>
            <table border='1' style='border-collapse: collapse; width: 100%;'>
                <tr style='background-color: #f0f0f0;'>
                    <th style='padding: 10px;'>N°</th>
                    <th style='padding: 10px;'>ID Requerimiento</th>
                    <th style='padding: 10px;'>Usuario</th>
                    <th style='padding: 10px;'>Oficina</th>
                    <th style='padding: 10px;'>Fecha Revisión</th>
                    <th style='padding: 10px;'>Comentario</th>
                    <th style='padding: 10px;'>Estado</th>
                    <th style='padding: 10px;'>ID Flujo</th>
                    <th style='padding: 10px;'>Ver Historial</th>
                </tr>";
    while ($row_principal = $result_principal->fetch_assoc()) {
        echo "<tr style='background-color: #fff;'>
                <td style='padding: 10px;'>" . $row_principal["id_historial"] . "</td>
                <td style='padding: 10px;'>" . $row_principal["id_requerimiento"] . "</td>
                <td style='padding: 10px;'>" . $nombre_usuario . "</td>
                <td style='padding: 10px;'>" . htmlspecialchars($nombre_oficina) . "</td>
                <td style='padding: 10px;'>" . $row_principal["fecha_revision"] . "</td>
                <td style='padding: 10px;'>" . $row_principal["comentario"] . "</td>
                <td style='padding: 10px;'>" . $row_principal["estado"] . "</td>
                <td style='padding: 10px;'>" . $row_principal["id_flujo"] . "</td>
                <td style='padding: 10px;'><form action='' method='post'><input type='hidden' name='id_requerimiento' value='" . $row_principal["id_requerimiento"] . "'><input type='submit' name='estado' value='Ver Historial'></form></td>
              </tr>";
    }
    echo "</table></div>";
} else {
    echo "<div style='border: 1px solid #ddd; padding: 20px; margin-top: 20px; border-radius: 10px;'>
            No hay registros en el historial de requerimientos para este usuario y oficina.
          </div>";
}

// Mostrar el historial completo del requerimiento al presionar el estado
if (isset($_POST['estado'])) {
    $id_requerimiento = $_POST['id_requerimiento'];
    $sql_historial = "SELECT * FROM historial_requerimientos WHERE id_requerimiento = ?";
    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->bind_param("i", $id_requerimiento);
    $stmt_historial->execute();
    $result_historial = $stmt_historial->get_result();

    if ($result_historial->num_rows > 0) {
        echo "<div style='border: 1px solid #ddd; padding: 20px; margin-top: 20px; border-radius: 10px;'>
                <table border='1' style='border-collapse: collapse; width: 100%;'>
                    <tr style='background-color: #f0f0f0;'>
                        <th style='padding: 10px;'>N°</th>
                        <th style='padding: 10px;'>ID Requerimiento</th>
                        <th style='padding: 10px;'>Usuario</th>
                        <th style='padding: 10px;'>Oficina</th>
                        <th style='padding: 10px;'>Fecha Revisión</th>
                        <th style='padding: 10px;'>Comentario</th>
                        <th style='padding: 10px;'>Estado</th>
                        <th style='padding: 10px;'>ID Flujo</th>
                    </tr>";
        while($row_historial = $result_historial->fetch_assoc()) {
            echo "<tr style='background-color: #fff;'>
                    <td style='padding: 10px;'>" . $row_historial["id_historial"] . "</td>
                    <td style='padding: 10px;'>" . $row_historial["id_requerimiento"] . "</td>
                    <td style='padding: 10px;'>" . $nombre_usuario . "</td>
                    <td style='padding: 10px;'>" . htmlspecialchars($nombre_oficina) . "</td>
                    <td style='padding: 10px;'>" . $row_historial["fecha_revision"] . "</td>
                    <td style='padding: 10px;'>" . $row_historial["comentario"] . "</td>
                    <td style='padding: 10px;'>" . $row_historial["estado"] . "</td>
                    <td style='padding: 10px;'>" . $row_historial["id_flujo"] . "</td>
                  </tr>";
        }
        echo "</table></div>";
    } else {
        echo "<div style='border: 1px solid #ddd; padding: 20px; margin-top: 20px; border-radius: 10px;'>
                No hay registros en el historial de requerimientos para este requerimiento.
              </div>";
    }
}

$stmt_oficina->close();
$stmt_usuario->close();
$conn->close();

include 'footer.php'
?>
