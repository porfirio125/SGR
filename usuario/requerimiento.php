<?php
require '../conexion/conexion.php';
session_start(); 

include 'header.php';

// Obtener la lista de tipos de requerimiento
$sql_tipos = "SELECT id_tipo_requerimiento, nombre_tipo FROM tipos_requerimiento";
$result_tipos = $conn->query($sql_tipos);

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $id_usuario = $_SESSION['id_usuario']; // Obtenido desde la sesión
    $id_oficina = $_POST['id_oficina'];
    $id_tipo_requerimiento = $_POST['id_tipo_requerimiento'];
    $fecha_creacion = $_POST['fecha_creacion'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Obtener automáticamente el flujo basado en el tipo de requerimiento y la oficina
        $sql_flujo = "SELECT id_flujo FROM flujo_oficinas 
                      WHERE id_tipo_requerimiento = ? AND id_oficina = ? 
                      ORDER BY orden ASC LIMIT 1";
        $stmt_flujo = $conn->prepare($sql_flujo);
        $stmt_flujo->bind_param('ii', $id_tipo_requerimiento, $id_oficina);
        $stmt_flujo->execute();
        $result_flujo = $stmt_flujo->get_result();
        $row_flujo = $result_flujo->fetch_assoc();
        $id_flujo = $row_flujo['id_flujo'];

        // Insertar en la tabla 'requerimientos'
        $sql_insert_requerimiento = "INSERT INTO requerimientos 
            (id_usuario, id_oficina, id_tipo_requerimiento, fecha_creacion, descripcion, estado, id_flujo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_requerimiento = $conn->prepare($sql_insert_requerimiento);
        $stmt_requerimiento->bind_param('iiisssi', $id_usuario, $id_oficina, $id_tipo_requerimiento, $fecha_creacion, $descripcion, $estado, $id_flujo);
        $stmt_requerimiento->execute();

        // Obtener el ID del requerimiento insertado
        $id_requerimiento = $conn->insert_id;

        // Insertar en la tabla 'historial_requerimientos'
        $fecha_revision = date('Y-m-d H:i:s'); // Fecha actual
        $comentario = "Requerimiento creado"; // Comentario inicial

        $sql_insert_historial = "INSERT INTO historial_requerimientos 
            (id_requerimiento, id_usuario, id_oficina, fecha_revision, comentario, estado, id_flujo, id_tipo_requermiento) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_historial = $conn->prepare($sql_insert_historial);
        $stmt_historial->bind_param('iiisssii', $id_requerimiento, $id_usuario, $id_oficina, $fecha_revision, $comentario, $estado, $id_flujo, $id_tipo_requerimiento);
        $stmt_historial->execute();

        // Confirmar la transacción
        $conn->commit();

        echo "Requerimiento y su historial insertados correctamente.";

    } catch (Exception $e) {
        // En caso de error, deshacer los cambios
        $conn->rollback();
        echo "Error al insertar los datos: " . $e->getMessage();
    }

    // Cerrar las declaraciones
    $stmt_flujo->close();
    $stmt_requerimiento->close();
    $stmt_historial->close();
}

include 'footer.php';
?>

<!-- Formulario HTML -->
<form method="post">
    <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" required>

    <label for="id_oficina">Oficina:</label>
    <select id="id_oficina" name="id_oficina" required>
        <?php
        // Obtener la oficina del usuario desde la sesión
        $sql_oficina = "SELECT id_oficina, nombre_oficina FROM oficinas WHERE id_oficina = ?";
        $stmt_oficina = $conn->prepare($sql_oficina);
        $stmt_oficina->bind_param('i', $_SESSION['id_oficina']);
        $stmt_oficina->execute();
        $result_oficina = $stmt_oficina->get_result();
        while ($row_oficina = $result_oficina->fetch_assoc()) {
            echo "<option value='" . $row_oficina['id_oficina'] . "'>" . $row_oficina['nombre_oficina'] . "</option>";
        }
        $stmt_oficina->close();
        ?>
    </select><br>

    <label for="id_tipo_requerimiento">Tipo de Requerimiento:</label>
    <select id="id_tipo_requerimiento" name="id_tipo_requerimiento" required>
        <option value="">Seleccione</option>
        <?php
        while ($row_tipo = $result_tipos->fetch_assoc()) {
            echo "<option value='" . $row_tipo['id_tipo_requerimiento'] . "'>" . $row_tipo['nombre_tipo'] . "</option>";
        }
        ?>
    </select><br>

    <label for="fecha_creacion">Fecha de Creación:</label>
    <input type="datetime-local" id="fecha_creacion" name="fecha_creacion" required><br>

    <label for="descripcion">Descripción:</label>
    <textarea id="descripcion" name="descripcion" required></textarea><br>

    <label for="estado">Estado:</label>
    <input type="text" id="estado" name="estado" required><br>

    <input type="submit" value="Enviar Requerimiento">
</form>
