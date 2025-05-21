<?php
session_start();
require '../../php/conexionBD.php'; // Conexión a la base de datos

// Seguridad: Verificar si el usuario es administrador.
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'administrador') {
    header('Location: ../../index.php'); // Redirige a la página principal o a login
    exit();
}

// Lógica para obtener todos los productos con su categoría
$productos = [];
$sql = "SELECT p.id, p.nombre, p.precio, p.imagen, p.disponibilidad, c.nombre AS categoria_nombre 
        FROM productos p
        JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.id ASC"; // Ordenar por ID descendente para ver los últimos añadidos

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $productos[] = $row;
    }
    mysqli_free_result($result);
} else {
    // Manejo de error si la consulta falla
    error_log("Error al obtener productos para el admin: " . mysqli_error($conn));
    $_SESSION['mensaje_error'] = "Error al cargar los productos.";
}

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos - Admin</title>
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
                <li><a href="manage_products.php" class="active">Gestionar Productos</a></li>
                <li><a href="manage_users.php">Gestionar Usuarios</a></li>
                <li><a href="manage_orders.php">Gestionar Órdenes</a></li>
                <li><a href="../pagesAccount/logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="container-admin">
                <h1>Gestionar Productos</h1>

                <?php
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

                <?php if (!empty($productos)): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Disponibilidad</th>
                                    <th>Categoría</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($producto['id']); ?></td>
                                        <td>
                                            <?php 
                                            // Construye la ruta de la imagen para el navegador
                                            $image_path = '../../img/productos/' . htmlspecialchars($producto['imagen']);
                                            ?>
                                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-thumbnail">
                                        </td>
                                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                        <td>S/<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></td>
                                        <td style="color: <?php echo ($producto['disponibilidad'] == 1) ? 'var(--color-success)' : 'var(--color-error)'; ?>;">
                                            <?php echo ($producto['disponibilidad'] == 1) ? 'En stock' : 'Agotado'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data-message">No hay productos para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
