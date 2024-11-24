<?php
session_start();

require 'conexion/conexion.php';

// Limpiar variables de sesión antes de asignar nuevos valores
session_unset();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contrasena_input = $_POST["contrasena"]; 

    // Consulta para verificar el correo
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si el correo existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica la contraseña usando password_verify
        if (password_verify($contrasena_input, $row["contrasena"])) {
            // Credenciales correctas, guardar datos del usuario en la sesión
            $_SESSION["id_usuario"] = $row["id_usuario"];  // Asignar id_usuario a la sesión
            $_SESSION["correo"] = $correo;
            $_SESSION["nombre"] = $row["nombre"];
            $_SESSION["apellido"] = $row["apellido"];
            $_SESSION["id_oficina"] = $row["id_oficina"];
            $_SESSION["cargo"] = $row["cargo"];

            // Redirigir según el cargo del usuario
            if ($row["cargo"] == "admin") {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit();
        } else {
            $error_message = "Credenciales incorrectas. Inténtalo de nuevo.";
        }
    } else {
        $error_message = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Sistema de Gestión de Requerimientos</title>
    <style>
        .background-image {
            background-image: url('https://consultasenlinea.mincetur.gob.pe/fichaInventario//foto.aspx?cod=469927');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            max-width: 700px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .header-container {
            position: absolute;
            top: 80px; 
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 1;
            color: white;
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <div class="header-container">
        <h1>SISTEMA DE GESTION DE REQUERIMIENTOS</h1>
    </div>
    <div class="background-image">
        <div class="card card-custom p-4">
            <div class="row">
                <div class="col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                    <img src="img/escudo.png" alt="Escudo" class="img-fluid mb-3" style="max-width: 120px;">
                    <h5>Municipalidad Distrital de Chucuito</h5>
                    <p class="mb-1">"Ciudad de las Cajas Reales"</p>
                    <small>Gestión 2023-2026</small>
                </div>
                <div class="col-md-6 bg-danger text-white p-4 rounded-end text-center">
                    <h4 class="mb-4">Iniciar Sesión</h4>
                    <img src="http://m.skytrack.hn/img/login_candado.png" alt="Escudo" class="img-fluid mb-3" style="max-width: 120px; margin: 0 auto;">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control rounded-3" id="correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control rounded-3" id="contrasena" name="contrasena" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-light rounded-3">Ingresar</button>
                        </div>
                    </form>
                    <?php
                        if (isset($error_message)) {
                            echo "<div class='alert alert-danger mt-3'>$error_message</div>";
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include 'usuario/footer.php'; ?>
</html>
