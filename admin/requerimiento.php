<?php
require '../conexion/conexion.php';
session_start(); 

include 'header.php';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
// Obtener la lista de tipos de requerimiento
$sql_tipos = "SELECT id_tipo_requerimiento, nombre_tipo FROM tipos_requerimiento";
$result_tipos = $conn->query($sql_tipos);

// Obtener la lista de tipos de documento
$sql_tipos_documento = "SELECT id_tipo_documento, nombre_tipo_documento FROM tipos_documento";
$result_tipos_documento = $conn->query($sql_tipos_documento);

// Obtener la lista de oficinas
$sql_oficinas = "SELECT id_oficina, nombre_oficina FROM oficinas";
$result_oficinas = $conn->query($sql_oficinas);


// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $id_usuario = $_SESSION['id_usuario']; // Obtenido desde la sesión
    $id_oficina = $_POST['id_oficina'];
    $id_tipo_requerimiento = $_POST['id_tipo_requerimiento'];
    $id_tipo_documento = $_POST['id_tipo_documento']; // Nuevo campo
    $fecha_creacion = $_POST['fecha_creacion'];
    $descripcion = $_POST['descripcion'];
    $estado = 'Pendiente'; // Estado inicial como 'Pendiente'
    $tiempo = $_POST['tiempo']; // Corregido: se eliminó "restante"
    $id_oficina_derivar = $_POST['id_oficina_derivar']; // Nuevo campo para oficina a derivar
    $archivo_pdf = $_FILES['archivo_pdf']['name']; // Nuevo campo para archivo PDF
    $archivo_pdf_tmp = $_FILES['archivo_pdf']['tmp_name']; //obtenemos el nombre temporal del archivo


    // Validar el formato de la fecha y hora para 'tiempo'
    $tiempo_formateado = date('Y-m-d H:i:s', strtotime($tiempo));
    
    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Insertar en la tabla 'requerimientos'
        //Se corrige la consulta para incluir la columna id_oficina_derivar si existe en la tabla requerimientos
        $sql_insert_requerimiento = "INSERT INTO requerimientos (id_usuario, id_oficina, id_tipo_requerimiento, id_tipo_documento, fecha_creacion, descripcion, estado) VALUES (?, ?, ?, ?, ?, ?, ?)"; //Added id_oficina_derivar to the query
        
        /*$stmt_requerimiento = $conn->prepare($sql_insert_requerimiento);
        if (!$stmt_requerimiento) {
            throw new Exception("Error al preparar la consulta de inserción de requerimientos: " . $conn->error);
        }
        $stmt_requerimiento->bind_param('iiisssi', $id_usuario, $id_oficina, $id_tipo_requerimiento, $id_tipo_documento, $fecha_creacion, $descripcion, $estado); //Added $id_oficina_derivar to bind_param
        $stmt_requerimiento->execute();
        if (!$stmt_requerimiento->execute()) {
            throw new Exception("Error al ejecutar la consulta de inserción de requerimientos: " . $stmt_requerimiento->error);
        }*/

        $stmt_requerimiento = $conn->prepare($sql_insert_requerimiento);
        if (!$stmt_requerimiento) {
            throw new Exception("Error al preparar la consulta de inserción de requerimientos: " . $conn->error);
        }
        $stmt_requerimiento->bind_param('iiisssi', $id_usuario, $id_oficina, $id_tipo_requerimiento, $id_tipo_documento, $fecha_creacion, $descripcion, $estado);
        $stmt_requerimiento->execute();

        if (!$stmt_requerimiento) {
        throw new Exception("Error al ejecutar la consulta de inserción de requerimientos: " . $stmt_requerimiento->error);
        }   


        // Obtener el ID del requerimiento insertado
        $id_requerimiento = $conn->insert_id;

        // Insertar en la tabla 'historial_requerimientos'
        $fecha_revision = date('Y-m-d H:i:s'); // Fecha actual
        $comentario = "Requerimiento creado"; // Comentario inicial

        //Se corrige la consulta para incluir la columna id_oficina_derivar si existe en la tabla historial_requerimientos y archivo_pdf
        $sql_insert_historial = "INSERT INTO historial_requerimientos (id_requerimiento, id_usuario, id_oficina, fecha_revision, comentario, estado, id_tipo_requerimiento, tiempo, id_tipo_documento, id_oficina_derivada, archivo_pdf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_historial = $conn->prepare($sql_insert_historial);
        if (!$stmt_historial) {
            throw new Exception("Error al preparar la consulta de inserción de historial: " . $conn->error);
        }

        // Mover el archivo PDF a la carpeta ../usuario/pdf
        $target_dir = "../usuario/pdf/"; //ruta correcta
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($archivo_pdf);
        $ruta_archivo_pdf = $target_file; // Guardar la ruta completa del archivo

        // Verificar si el archivo se subió correctamente y si la carpeta existe
        if (is_dir($target_dir) && move_uploaded_file($archivo_pdf_tmp, $target_file)) {
            $stmt_historial->bind_param('iiisssisiss', $id_requerimiento, $id_usuario, $id_oficina, $fecha_revision, $comentario, $estado, $id_tipo_requerimiento, $tiempo_formateado, $id_tipo_documento, $id_oficina_derivar, $ruta_archivo_pdf); // Use $ruta_archivo_pdf here
            if (!$stmt_historial->execute()) {
                $conn->rollback();
                throw new Exception("Error al ejecutar la consulta de inserción de historial: " . $stmt_historial->error);
            }
            // Confirmar la transacción
            $conn->commit();

            echo "Requerimiento y su historial insertados correctamente.";
        } else {
            // Deshacer la transacción si hay un error al subir el archivo o la carpeta no existe
            $conn->rollback();
            // Agregar manejo de errores más específico, por ejemplo, mostrar el error de move_uploaded_file
            $error_message = "Error al subir el archivo PDF: ";
            if (!is_dir($target_dir)) {
                $error_message .= "La carpeta de destino no existe.";
            } else {
                $error_message .= "Error al mover el archivo: " . error_get_last()['message']; //More descriptive error message
            }
            throw new Exception($error_message);
        }


    } catch (Exception $e) {
        // En caso de error, deshacer los cambios
        $conn->rollback();
        echo "Error al insertar los datos: " . $e->getMessage();
        //Se agrega registro del error en un log.  Reemplazar 'error_log.txt' con la ruta correcta.
        error_log("Error al insertar requerimiento: " . $e->getMessage() . "\n", 3, '../logs/error_log.txt');
    }

    // Cerrar las declaraciones
    $stmt_requerimiento->close();
    $stmt_historial->close();
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h2 class="text-center">Nuevo Requerimiento</h2>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="id_oficina" class="form-label text-danger">Oficina:</label>
                            <select id="id_oficina" name="id_oficina" class="form-select" required>
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
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="id_tipo_requerimiento" class="form-label text-danger">Tipo de Requerimiento:</label>
                            <select id="id_tipo_requerimiento" name="id_tipo_requerimiento" class="form-select" required>
                                <option value="">Seleccione</option>
                                <?php
                                while ($row_tipo = $result_tipos->fetch_assoc()) {
                                    echo "<option value='" . $row_tipo['id_tipo_requerimiento'] . "'>" . $row_tipo['nombre_tipo'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="id_tipo_documento" class="form-label text-danger">Tipo de Documento:</label>
                            <select id="id_tipo_documento" name="id_tipo_documento" class="form-select" required>
                                <option value="">Seleccione</option>
                                <?php
                                while ($row_tipo_documento = $result_tipos_documento->fetch_assoc()) {
                                    echo "<option value='" . $row_tipo_documento['id_tipo_documento'] . "'>" . $row_tipo_documento['nombre_tipo_documento'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="id_oficina_derivar" class="form-label text-danger">Oficina a Derivar:</label>
                            <select id="id_oficina_derivar" name="id_oficina_derivar" class="form-select" required>
                                <option value="">Seleccione</option>
                                <?php
                                while ($row_oficina = $result_oficinas->fetch_assoc()) {
                                    echo "<option value='" . $row_oficina['id_oficina'] . "'>" . $row_oficina['nombre_oficina'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="fecha_creacion" class="form-label text-danger">Fecha de Creación:</label>
                            <input type="datetime-local" id="fecha_creacion" name="fecha_creacion" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="descripcion" class="form-label text-danger">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tiempo" class="form-label text-danger">Tiempo:</label>
                            <input type="datetime-local" id="tiempo" name="tiempo" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="archivo_pdf" class="form-label text-danger">Archivo PDF:</label>
                            <input type="file" name="archivo_pdf" id="archivo_pdf" class="form-control" accept=".pdf">
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Enviar Requerimiento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php';
?>