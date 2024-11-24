<?php
require '../conexion/conexion.php';
session_start();
$id_usuario = $_SESSION['id_usuario']; 
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
include 'header.php';

echo '<div class="sticky-top">
        <div class="d-flex justify-content-center my-3">
            <a href="index.php" class="btn btn-primary">Volver a Requerimientos</a>
        </div>
    </div>';
if (!isset($_GET['id_requerimiento'])) {
    echo "ID de requerimiento no especificado.";
    exit();
}

$id_requerimiento = $_GET['id_requerimiento'];

// Obtener información del requerimiento
$query = "SELECT id_oficina, id_tipo_requerimiento, id_tipo_documento, tiempo, comentario, estado, id_oficina_derivada, archivo_pdf FROM historial_requerimientos WHERE id_requerimiento = ? ORDER BY fecha_revision DESC LIMIT 1"; 
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_requerimiento);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_oficina = $row['id_oficina'];
    $id_tipo_requerimiento = $row['id_tipo_requerimiento'];
    $id_tipo_documento = $row['id_tipo_documento'];
    $tiempo = $row['tiempo'];
    $descripcion_original = $row['comentario']; 
    $estado = $row['estado'];
    $id_oficina_derivada = $row['id_oficina_derivada'];
    $archivo_pdf_original = $row['archivo_pdf'];

} else {
    echo "Requerimiento no encontrado.";
    exit();
}

// Obtener nombre de la oficina del usuario
$sql_oficina_usuario = "SELECT nombre_oficina FROM oficinas WHERE id_oficina = ?";
$stmt_oficina = $conn->prepare($sql_oficina_usuario);
$stmt_oficina->bind_param("i", $id_oficina);
$stmt_oficina->execute();
$result_oficina = $stmt_oficina->get_result();
$row_oficina_usuario = $result_oficina->fetch_assoc();
$nombre_oficina_usuario = $row_oficina_usuario['nombre_oficina'];


// Obtener lista de oficinas para el select
$sql_oficinas = "SELECT id_oficina, nombre_oficina FROM oficinas";
$result_oficinas = $conn->query($sql_oficinas);

// Obtener nombre del tipo de requerimiento
$sql_tipo_requerimiento = "SELECT nombre_tipo FROM tipos_requerimiento WHERE id_tipo_requerimiento = ?";
$stmt_tipo_requerimiento = $conn->prepare($sql_tipo_requerimiento);
$stmt_tipo_requerimiento->bind_param("i", $id_tipo_requerimiento);
$stmt_tipo_requerimiento->execute();
$result_tipo_requerimiento = $stmt_tipo_requerimiento->get_result();
$row_tipo_requerimiento = $result_tipo_requerimiento->fetch_assoc();
$nombre_tipo_requerimiento = $row_tipo_requerimiento['nombre_tipo'];

// Obtener lista de tipos de documento para el select
$sql_tipos_documento = "SELECT id_tipo_documento, nombre_tipo_documento FROM tipos_documento";
$result_tipos_documento = $conn->query($sql_tipos_documento);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nuevo Historial de Requerimiento</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h2 class="text-center">Registrar Nuevo Historial de Requerimiento</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción:</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required><?php echo htmlspecialchars($descripcion_original); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado:</label>
                                <select id="estado" name="estado" class="form-select" required>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Rechazado">Rechazado</option>
                                    <option value="Pendiente">Pendiente</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="id_oficina" class="form-label">Oficina:</label>
                                <input type="text" id="id_oficina" name="id_oficina" value="<?php echo $nombre_oficina_usuario; ?>" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="id_tipo_requerimiento" class="form-label">Tipo de requerimiento:</label>
                                <input type="text" id="id_tipo_requerimiento" name="id_tipo_requerimiento" value="<?php echo $nombre_tipo_requerimiento; ?>" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="id_tipo_documento" class="form-label">Tipo de documento:</label>
                                <select id="id_tipo_documento" name="id_tipo_documento" class="form-select" required>
                                    <?php
                                    while ($row_tipo_documento = $result_tipos_documento->fetch_assoc()) {
                                        echo "<option value='" . $row_tipo_documento['id_tipo_documento'] . "'>" . $row_tipo_documento['nombre_tipo_documento'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tiempo" class="form-label">Tiempo:</label>
                                <input type="datetime-local" id="tiempo" name="tiempo" value="<?php echo $tiempo; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="id_oficina_destino" class="form-label">Oficina Destino (Si aplica):</label>
                                <select id="id_oficina_destino" name="id_oficina_destino" class="form-select">
                                    <option value="">Seleccione</option>
                                    <?php
                                    while ($row_oficina = $result_oficinas->fetch_assoc()) {
                                        echo "<option value='" . $row_oficina['id_oficina'] . "'>" . $row_oficina['nombre_oficina'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="archivo_pdf" class="form-label">Archivo PDF:</label>
                                <input type="file" id="archivo_pdf" name="archivo_pdf" accept=".pdf" class="form-control">
                                <?php if (!empty($archivo_pdf_original)): ?>
                                    <a href="<?php echo $archivo_pdf_original; ?>" target="_blank">Descargar archivo actual</a>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="accion" value="guardar" class="btn btn-success">Guardar</button>
                        </form>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'guardar') {
                            $descripcion = $_POST['descripcion'];
                            $estado = $_POST['estado'];
                            $id_oficina_destino = $_POST['id_oficina_destino'];
                            $tiempo = $_POST['tiempo'];
                            $id_tipo_documento = $_POST['id_tipo_documento']; // Corrected line
                            $id_tipo_requerimiento = $id_tipo_requerimiento; 
                            $tiempo_formateado = date('Y-m-d H:i:s', strtotime($tiempo));
                            $archivo_pdf = '';

                            if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] == 0) {
                                $nombre_archivo = basename($_FILES['archivo_pdf']['name']);
                                $tipo_archivo = $_FILES['archivo_pdf']['type'];
                                $tamano_archivo = $_FILES['archivo_pdf']['size'];
                                $ruta_temporal = $_FILES['archivo_pdf']['tmp_name'];

                                if ($tipo_archivo != "application/pdf") {
                                    echo "<p class='alert alert-danger mt-3'>Error: Solo se permiten archivos PDF.</p>";
                                } else if ($tamano_archivo > 5000000) { // 5MB
                                    echo "<p class='alert alert-danger mt-3'>Error: El archivo es demasiado grande (máximo 5MB).</p>";
                                } else {
                                    $carpeta_destino = 'uploads/'; // Crea la carpeta si no existe
                                    if (!is_dir($carpeta_destino)) {
                                        mkdir($carpeta_destino, 0777, true);
                                    }
                                    $ruta_destino = $carpeta_destino . $nombre_archivo;
                                    move_uploaded_file($ruta_temporal, $ruta_destino);
                                    $archivo_pdf = $ruta_destino;
                                }
                            }


                            $query_insert = "INSERT INTO historial_requerimientos (id_requerimiento, id_usuario, id_oficina, fecha_revision, comentario, estado, id_oficina_derivada, id_tipo_requerimiento, id_tipo_documento, tiempo, archivo_pdf) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_insert = $conn->prepare($query_insert);
                            $stmt_insert->bind_param("iiissiiiiss", $id_requerimiento, $id_usuario, $id_oficina, $descripcion, $estado, $id_oficina_destino, $id_tipo_requerimiento, $id_tipo_documento, $tiempo_formateado, $archivo_pdf);

                            if ($stmt_insert->execute()) {
                                echo "<p class='alert alert-success mt-3'>Historial guardado correctamente.</p>";
                            } else {
                                echo "<p class='alert alert-danger mt-3'>Error al guardar el historial: " . $stmt_insert->error . "</p>";
                            }
                            $stmt_insert->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
include 'footer.php';
$stmt->close();
$stmt_oficina->close();
$stmt_tipo_requerimiento->close();
$stmt_tipo_documento->close();
$conn->close();
?>
