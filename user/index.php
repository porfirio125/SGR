<?php
// Conectar a la base de datos
include('../conexion/conexion.php');
session_start();
$id_usuario = $_SESSION['id_usuario'];
$id_oficina = $_SESSION['id_oficina'];
include 'header.php';

// Incluir Bootstrap CSS (mejor usar CDN para evitar problemas locales)
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">';

// Determinar filtro (por defecto: "Mis Requerimientos")
$filtro = $_GET['filtro'] ?? 'mis_requerimientos';

// Mostrar los botones de filtro
echo '<div class="sticky-top">
        <div class="d-flex justify-content-center my-3">
            <a href="?filtro=mis_requerimientos" class="btn btn-primary ' . ($filtro == 'mis_requerimientos' ? 'active' : '') . '">Mis Requerimientos</a>
            <a href="?filtro=pendientes" class="btn btn-secondary mx-2 ' . ($filtro == 'pendientes' ? 'active' : '') . '">Requerimientos Pendientes</a>
        </div>
    </div>';

if ($filtro == 'mis_requerimientos') {
    // Consulta para "Mis Requerimientos"
    $query = "SELECT id_requerimiento, descripcion, estado, fecha_creacion, id_tipo_requerimiento, id_tipo_documento FROM requerimientos WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Mostrar tabla de "Mis Requerimientos"
    echo '<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2>Mis Requerimientos</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                    <th>Tipo de Requerimiento</th>
                                    <th>Tipo de Documento</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td class="text-truncate" style="max-width: 200px;">' . htmlspecialchars($row['descripcion']) . '</td>
                <td>' . htmlspecialchars($row['estado']) . '</td>
                <td>' . htmlspecialchars($row['fecha_creacion']) . '</td>
                <td>' . htmlspecialchars($row['id_tipo_requerimiento']) . '</td>
                <td>' . htmlspecialchars($row['id_tipo_documento']) . '</td>
                <td><a href="historial.php?id_requerimiento=' . $row['id_requerimiento'] . '" class="btn btn-info">Ver Historial</a></td>
              </tr>';
    }
    echo '</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

} elseif ($filtro == 'pendientes') {
    // Consulta para "Requerimientos Pendientes"
    $query_pending = "SELECT hr.id_requerimiento, r.descripcion, hr.estado, hr.fecha_revision, hr.tiempo, hr.id_tipo_requerimiento, hr.id_tipo_documento, hr.comentario, hr.id_oficina_derivada
                      FROM historial_requerimientos hr
                      JOIN requerimientos r ON hr.id_requerimiento = r.id_requerimiento
                      WHERE hr.estado = 'Pendiente' AND hr.id_oficina_derivada = ?";
    $stmt_pending = $conn->prepare($query_pending);
    $stmt_pending->bind_param("i", $id_oficina);
    $stmt_pending->execute();
    $result_pending = $stmt_pending->get_result();

    // Mostrar tabla de "Requerimientos Pendientes"
    echo '<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h2>Requerimientos Pendientes de Revisión</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Fecha de Revisión</th>
                                    <th>Tiempo</th>
                                    <th>Tipo de Requerimiento</th>
                                    <th>Tipo de Documento</th>
                                    <th>Comentario</th>
                                    <th>Oficina Derivada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>';
    while ($row_pending = $result_pending->fetch_assoc()) {
        echo '<tr>
                <td class="text-truncate" style="max-width: 200px;">' . htmlspecialchars($row_pending['descripcion']) . '</td>
                <td>' . htmlspecialchars($row_pending['estado']) . '</td>
                <td>' . htmlspecialchars($row_pending['fecha_revision']) . '</td>
                <td>' . htmlspecialchars($row_pending['tiempo']) . '</td>
                <td>' . htmlspecialchars($row_pending['id_tipo_requerimiento']) . '</td>
                <td>' . htmlspecialchars($row_pending['id_tipo_documento']) . '</td>
                <td class="text-wrap" style="max-width: 300px;">' . htmlspecialchars($row_pending['comentario']) . '</td>
                <td>' . htmlspecialchars($row_pending['id_oficina_derivada']) . '</td>
                <td>
                    <a href="editar.php?id_requerimiento=' . $row_pending['id_requerimiento'] . '" class="btn btn-warning">Revisar</a>
                    <a href="archivar.php?id_requerimiento=' . $row_pending['id_requerimiento'] . '" class="btn btn-success">Archivar</a>
                </td>
              </tr>';
    }
    echo '</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
}

include 'footer.php';
$stmt->close();
$stmt_pending->close();
$conn->close();
?>