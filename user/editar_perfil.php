<?php
session_start();
require '../conexion/conexion.php';

include 'header.php';

if (!isset($_SESSION["correo"])) {
    header("Location: ../index.php");
    exit();
}

$correo_usuario = $_SESSION["correo"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $id_oficina = $_POST['id_oficina'];
    $cargo = $_POST['cargo'];
    $contrasena = $_POST['contrasena'];

    if (!empty($contrasena)) {
        $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
    } else {
        // Si no se proporciona una nueva contraseña, no se actualiza
        $sql_select_contrasena = "SELECT contrasena FROM usuarios WHERE correo = ?";
        $stmt_select_contrasena = $conn->prepare($sql_select_contrasena);
        $stmt_select_contrasena->bind_param("s", $correo_usuario);
        $stmt_select_contrasena->execute();
        $result_contrasena = $stmt_select_contrasena->get_result();
        $row_contrasena = $result_contrasena->fetch_assoc();
        $contrasena = $row_contrasena['contrasena'];
        $stmt_select_contrasena->close();
    }


    $sql_update = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, id_oficina = ?, cargo = ?, contrasena = ? WHERE correo = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param('sssssss', $nombre, $apellido, $correo, $id_oficina, $cargo, $contrasena, $correo_usuario);

    if ($stmt->execute()) {
        $_SESSION["correo"] = $correo; // Actualizar correo en la sesión
        echo "<script>alert('Perfil actualizado exitosamente.'); window.location.href = 'perfil.php';</script>";
    } else {
        echo "Error al actualizar el perfil: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT nombre, apellido, correo, cargo, id_oficina FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Editar Perfil</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h2 class="text-center">Editar Perfil</h2>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label text-danger">Nombre:</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellido" class="form-label text-danger">Apellido:</label>
                                    <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="correo" class="form-label text-danger">Correo:</label>
                                    <input type="email" id="correo" name="correo" class="form-control" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="id_oficina" class="form-label text-danger">Oficina:</label>
                                    <select id="id_oficina" name="id_oficina" class="form-select" required>
                                        <?php
                                        $sql_oficinas = "SELECT id_oficina, nombre_oficina FROM oficinas";
                                        $result_oficinas = $conn->query($sql_oficinas);
                                        while ($row_oficina = $result_oficinas->fetch_assoc()) {
                                            $selected = ($usuario['id_oficina'] == $row_oficina['id_oficina']) ? 'selected' : '';
                                            echo "<option value='" . $row_oficina['id_oficina'] . "' " . $selected . ">" . $row_oficina['nombre_oficina'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cargo" class="form-label text-danger">Cargo:</label>
                                    <input type="text" id="cargo" name="cargo" class="form-control" value="<?php echo htmlspecialchars($usuario['cargo']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contrasena" class="form-label text-danger">Contraseña (opcional):</label>
                                    <input type="password" id="contrasena" name="contrasena" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-danger w-100">Actualizar Perfil</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "<p>No se encontró el perfil del usuario.</p>";
}

include 'footer.php';

$stmt->close();
$conn->close();
?>
