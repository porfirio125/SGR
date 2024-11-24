<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/escudo.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXhW+ALEwIH" crossorigin="anonymous">
    <title>SGR</title>
    <style>
        .navbar {
            background-color: #dc3545;
        }
        .navbar-brand img {
            width: 80px; /* Tamaño del logo ajustado para dispositivos móviles */
            height: auto;
        }
        .navbar-nav .nav-link {
            color: white !important;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #ffc107 !important; /* Color amarillo al pasar el mouse */
        }
        .phrases {
            color: white;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand" href="index.php">
                    <img src="../img/escudo.png" alt="Logo del sistema">
                </a>
                <div class="phrases ms-lg-3 mt-2 mt-lg-0">
                        <p class="mb-0">Bienvenido, <?php echo $_SESSION["nombre"] . " " . $_SESSION["apellido"]; ?>!</p>
                    </div>
                <!-- Botón de menú para móviles -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Menú de navegación -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="perfil.php">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="requerimiento.php">Nuevo Requerimiento</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="usuario.php">Nuevo Usuario</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
