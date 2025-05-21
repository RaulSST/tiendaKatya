<?php
session_start();
require '../../php/conexionBD.php'; // Conexión a la base de datos

// Seguridad: Verificar si el usuario es administrador.
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'administrador') {
    header('Location: ../../index.php'); // Redirige a la página principal o a login
    exit();
}

// Lógica para obtener todas las órdenes y sus detalles
$ordenes = [];
$sql = "
    SELECT 
        o.id AS orden_id,
        o.usuario_id,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email,
        o.total,
        o.estado,
        o.fecha,
        o.direccion_envio,
        o.departamento_envio,
        o.distrito_envio,
        o.codigo_postal_envio,
        o.correo_envio,
        o.telefono_envio,
        o.metodo_pago,
        do.producto_id,
        p.nombre AS producto_nombre,
        p.imagen AS producto_imagen,
        do.cantidad AS producto_cantidad,
        do.precio AS producto_precio
    FROM ordenes o
    JOIN usuarios u ON o.usuario_id = u.id
    JOIN detalle_orden do ON o.id = do.orden_id
    JOIN productos p ON do.producto_id = p.id
    ORDER BY o.fecha DESC, o.id DESC;
";

$result = mysqli_query($conn, $sql);

if ($result) {
    // Agrupar los resultados por orden_id
    $grouped_orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orden_id = $row['orden_id'];
        if (!isset($grouped_orders[$orden_id])) {
            $grouped_orders[$orden_id] = [
                'id' => $row['orden_id'],
                'usuario_id' => $row['usuario_id'],
                'usuario_nombre' => $row['usuario_nombre'],
                'usuario_email' => $row['usuario_email'],
                'total' => $row['total'],
                'estado' => $row['estado'],
                'fecha' => $row['fecha'],
                'direccion_envio' => $row['direccion_envio'],
                'departamento_envio' => $row['departamento_envio'],
                'distrito_envio' => $row['distrito_envio'],
                'codigo_postal_envio' => $row['codigo_postal_envio'],
                'correo_envio' => $row['correo_envio'],
                'telefono_envio' => $row['telefono_envio'],
                'metodo_pago' => $row['metodo_pago'],
                'productos' => []
            ];
        }
        $grouped_orders[$orden_id]['productos'][] = [
            'producto_id' => $row['producto_id'],
            'nombre' => $row['producto_nombre'],
            'imagen' => $row['producto_imagen'],
            'cantidad' => $row['producto_cantidad'],
            'precio' => $row['producto_precio']
        ];
    }
    mysqli_free_result($result);
    $ordenes = array_values($grouped_orders); // Convertir a array indexado
} else {
    error_log("Error al obtener órdenes para el admin: " . mysqli_error($conn));
    $_SESSION['mensaje_error'] = "Error al cargar las órdenes.";
}

// Cierra la conexión a la base de datos
mysqli_close($conn);

// Función auxiliar para formatear la fecha
function formatDate($dateString) {
    $options = ['year' => 'numeric', 'month' => 'long', 'day' => 'numeric'];
    return (new DateTime($dateString))->format('d/m/Y H:i'); // Formato más común en español
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Órdenes - Admin</title>
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
                <li><a href="manage_users.php">Gestionar Usuarios</a></li>
                <li><a href="manage_orders.php" class="active">Gestionar Órdenes</a></li>
                 <li><a href="../pagesAccount/logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="container-admin">
                <h1>Gestionar Órdenes</h1>

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

                <?php if (!empty($ordenes)): ?>
                    <div class="orders-list">
                        <?php foreach ($ordenes as $orden): ?>
                            <div class="order-card-admin">
                                <div class="order-header">
                                    <h3>Orden #<?php echo htmlspecialchars($orden['id']); ?></h3>
                                    <span class="order-status-badge status-<?php echo strtolower(str_replace(' ', '-', $orden['estado'])); ?>">
                                        <?php echo htmlspecialchars($orden['estado']); ?>
                                    </span>
                                </div>
                                <div class="order-details-grid">
                                    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($orden['usuario_nombre'] . ' (' . $orden['usuario_email'] . ')'); ?></p>
                                    <p><strong>Fecha:</strong> <?php echo formatDate($orden['fecha']); ?></p>
                                    <p><strong>Total:</strong> S/<?php echo htmlspecialchars(number_format($orden['total'], 2)); ?></p>
                                    <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($orden['metodo_pago'] ?? 'N/A'); ?></p>
                                    <p><strong>Dirección de Envío:</strong> <?php echo htmlspecialchars($orden['direccion_envio'] ?? 'N/A'); ?></p>
                                    <p><strong>Ubicación:</strong> <?php 
                                        $ubicacion_envio = [];
                                        if (!empty($orden['departamento_envio'])) $ubicacion_envio[] = $orden['departamento_envio'];
                                        if (!empty($orden['distrito_envio'])) $ubicacion_envio[] = $orden['distrito_envio'];
                                        if (!empty($orden['codigo_postal_envio'])) $ubicacion_envio[] = $orden['codigo_postal_envio'];
                                        echo htmlspecialchars(implode(', ', $ubicacion_envio) ?: 'N/A');
                                    ?></p>
                                    <p><strong>Contacto:</strong> <?php echo htmlspecialchars(($orden['correo_envio'] ?? 'N/A') . ' / ' . ($orden['telefono_envio'] ?? 'N/A')); ?></p>
                                </div>

                                <div class="order-products-section">
                                    <h4>Productos:</h4>
                                    <?php foreach ($orden['productos'] as $producto): ?>
                                        <div class="order-product-item">
                                            <img src="../../img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-thumbnail">
                                            <div class="product-info">
                                                <p><strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></p>
                                                <p>Cantidad: x<?php echo htmlspecialchars($producto['cantidad']); ?></p>
                                                <p>Precio Unitario: S/<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data-message">No hay órdenes para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
