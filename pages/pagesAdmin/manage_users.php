<?php
session_start();
require '../../php/conexionBD.php'; // Conexión a la base de datos

// Seguridad: Verificar si el usuario es administrador.
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'administrador') {
    header('Location: ../../index.php'); // Redirige a la página principal o a login
    exit();
}

// Lógica para obtener solo los usuarios que son 'cliente'
$usuarios = [];
$sql = "SELECT id, nombre, apellidos, email, telefono, tipo_usuario, direccion, departamento, distrito, codigoPostal 
        FROM usuarios 
        WHERE tipo_usuario = 'cliente' -- ¡CAMBIO AQUÍ! Filtrar por tipo_usuario = 'cliente'
        ORDER BY id ASC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $usuarios[] = $row;
    }
    mysqli_free_result($result);
} else {
    error_log("Error al obtener usuarios para el admin: " . mysqli_error($conn));
    $_SESSION['mensaje_error'] = "Error al cargar los usuarios.";
}

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - Admin</title>
    <link rel="stylesheet" href="../../css/pagesAdmin.css">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <div class="sidebar">
            <h2>Panel de Administración</h2>
            <ul>
                <li><a href="admin.php">Añadir Producto</a></li>
                <li><a href="manage_products.php">Gestionar Productos</a></li>
                <li><a href="manage_users.php" class="active">Gestionar Usuarios</a></li>
                <li><a href="manage_orders.php">Gestionar Órdenes</a></li>
                <li><a href="../pagesAccount/logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="container-admin">
                <h1>Gestionar Usuarios (Clientes)</h1> <?php
                // Mostrar mensajes de sesión (éxito o error)
                if (isset($_SESSION['mensaje'])) {
                    echo "<div class='message-container success'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
                    unset($_SESSION['mensaje']);
                }
                if (isset($_SESSION['mensaje_error'])) {
                    echo "<div class='message-container error'>" . htmlspecialchars($_SESSION['mensaje_error']) . "</div>";
                    unset($_SESSION['mensaje_error']);
                }
                ?>

                <?php if (!empty($usuarios)): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Dirección</th>
                                    <th>Ubicación</th>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['telefono'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['tipo_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['direccion'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php 
                                                $ubicacion = [];
                                                if (!empty($usuario['departamento'])) $ubicacion[] = $usuario['departamento'];
                                                if (!empty($usuario['distrito'])) $ubicacion[] = $usuario['distrito'];
                                                if (!empty($usuario['codigoPostal'])) $ubicacion[] = $usuario['codigoPostal'];
                                                echo htmlspecialchars(implode(', ', $ubicacion) ?: 'N/A');
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data-message">No hay clientes para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
