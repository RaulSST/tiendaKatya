<?php
session_start();
require '../../php/conexionBD.php'; // Conexión a la base de datos


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'administrador') {
    header('Location: ../../index.php'); // Redirige a la página principal o a login
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Importaciones Katya</title>
    <link rel="stylesheet" href="../../css/pagesAdmin.css">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <div class="sidebar">
            <h2>Panel de Administración</h2>
            <ul>
                <li><a href="admin.php">Añadir Producto</a></li>
                <li><a href="manage_products.php">Gestionar Productos</a></li> 
                <li><a href="manage_users.php">Gestionar Usuarios</a></li>   
                <li><a href="manage_orders.php">Gestionar Órdenes</a></li>   
                <li><a href="../pagesAccount/logout.php">Cerrar Sesión</a></li>    
            </ul>
        </div>

        <div class="main-content">
            <div class="container-admin" style="width: 600px; margin-top: 50px;">
                <h1>Añadir un nuevo producto</h1>

                <?php
                // Verificar y mostrar mensaje de éxito
                if (isset($_SESSION['mensaje'])) {
                    echo "<div class='message-container success'>" . htmlspecialchars($_SESSION['mensaje']) . "</div>";
                    unset($_SESSION['mensaje']); // Limpiar el mensaje
                }
                // Verificar y mostrar mensaje de error
                if (isset($_SESSION['mensaje_error'])) {
                    echo "<div class='message-container error'>" . htmlspecialchars($_SESSION['mensaje_error']) . "</div>";
                    unset($_SESSION['mensaje_error']); // Limpiar el mensaje
                }
                ?>

                <form action="../../php/addProduct.php" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="nombre">Nombre del Producto:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    
                    <div>
                        <label for="precio">Precio:</label>
                        <input type="number" step="0.01" id="precio" name="precio" required>
                    </div>
                    
                    <div>
                        <label for="disponibilidad">Disponibilidad:</label>
                        <select id="disponibilidad" name="disponibilidad" required>
                            <option value="1">Disponible</option>
                            <option value="0">No disponible</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="categoria">Categoría:</label>
                        <select id="categoria" name="categoria" required>
                            <?php
                            // Obtener las categorías de la base de datos con mysqli
                            $query = "SELECT id, nombre FROM categorias ORDER BY id ASC"; // Ordenar alfabéticamente
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($categoria = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($categoria['id']) . "'>" . htmlspecialchars($categoria['nombre']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>No hay categorías disponibles</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label for="imagen">Imagen del Producto (Subir archivo):</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" required>
                    </div>

                    <button type="submit">Añadir Producto</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>