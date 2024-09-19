<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGR</title>
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="index.php">
                <img src="logo.png" alt="Logo del sistema">
            </a>
        </div>
        <div class="phrases">
            <p>Bienvenido, <?php echo $_SESSION["nombre"] . " " . $_SESSION["apellido"]; ?>!</p>
            <p>Tu satisfacción es nuestra prioridad</p>
        </div>
    </div>

    <div class="sidebar">
        <h2>Dashboard</h2>
        <a href="index.php">Inicio</a>
        <a href="perfil.php">Perfil</a>
        <a href="requerimiento.php">Nuevo Requerimiento</a>
        <a href="logout.php">Cerrar Secion</a>
        </form>
    </div>