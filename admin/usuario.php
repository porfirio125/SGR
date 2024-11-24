<?php
require '../conexion/conexion.php';
session_start(); 
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';
include 'header.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $id_oficina = $_POST['id_oficina'];
    $cargo = $_POST['cargo']; //Se mantiene la obtención del cargo desde el formulario.
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Insertar en la tabla 'usuarios'
    $sql_insert = "INSERT INTO usuarios (nombre, apellido, correo, id_oficina, cargo, contrasena) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param('ssssss', $nombre, $apellido, $correo, $id_oficina, $cargo, $contrasena);

    if ($stmt->execute()) {
        echo "Usuario creado exitosamente.";
    } else {
        echo "Error al crear el usuario: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
}

include 'footer.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h2 class="text-center">Nuevo Usuario</h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="nombre" class="form-label text-danger">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label text-danger">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label text-danger">Correo:</label>
                            <input type="email" id="correo" name="correo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_oficina" class="form-label text-danger">Oficina:</label>
                            <select id="id_oficina" name="id_oficina" class="form-select" required>
                                <?php
                                // Obtener las oficinas disponibles
                                $sql_oficinas = "SELECT id_oficina, nombre_oficina FROM oficinas";
                                $result_oficinas = $conn->query($sql_oficinas);
                                while ($row_oficina = $result_oficinas->fetch_assoc()) {
                                    echo "<option value='" . $row_oficina['id_oficina'] . "'>" . $row_oficina['nombre_oficina'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cargo" class="form-label text-danger">Cargo:</label>
                            <select id="cargo" name="cargo" class="form-select" required>
                                <option value="admin">admin</option>
                                <option value="usuario">usuario</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label text-danger">Contraseña:</label>
                            <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Crear Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
