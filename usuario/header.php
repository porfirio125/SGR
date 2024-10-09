<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGR</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f0f0f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: red;
            padding: 20px;
            border-radius: 20px;
        }
        .logo {
            margin-left: 20px;
        }
        .logo img {
            width: 100px; /* Tamaño del logo */
            height: auto;
        }
        .phrases {
            margin-right: 20px;
        }
        .sidebar {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin: 0 20px;
        }
        .sidebar a {
            margin-right: 10px;
            display: block;
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }
        .sidebar a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="index.php">
                <img src="../img/escudo.png" alt="Logo del sistema">
            </a>
        </div>
        <div class="sidebar">
        <a href="index.php">Inicio</a>
        <a href="perfil.php">Perfil</a>
        <a href="requerimiento.php">Nuevo Requerimiento</a>
        <a href="usuario.php">Nuevo Usuario</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
        <div class="phrases">
            <p>Bienvenido, <?php echo $_SESSION["nombre"] . " " . $_SESSION["apellido"]; ?>!</p>
            <p>Tu satisfacción es nuestra prioridad</p>
        </div>
    </div>
</body>