<?php
session_start(); // Inicia la sesión para acceder al ID del usuario

// Incluye el archivo de conexión a la base de datos
// Asegúrate de que la ruta sea correcta desde la ubicación de este archivo (public_html/php/)
include_once("conexionBD.php");

// Establece el encabezado de respuesta para JSON
header('Content-Type: application/json');

// 1. Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit;
}

$usuarioId = $_SESSION['user_id'];

// 2. Verifica si se recibió el product_id por POST
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de producto inválido.']);
    exit;
}

$productId = (int)$_POST['product_id']; // Convierte a entero para seguridad

// 3. Prepara la consulta SQL para eliminar el producto de la lista de deseos
$sql = "DELETE FROM favoritos WHERE usuario_id = ? AND producto_id = ?";
$stmt = mysqli_prepare($conn, $sql);

// 4. Verifica si la preparación de la consulta falló
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => "Error al preparar la consulta: " . mysqli_error($conn)]);
    exit;
}

// 5. Vincula los parámetros y ejecuta la consulta
mysqli_stmt_bind_param($stmt, "ii", $usuarioId, $productId); // "ii" indica dos enteros
mysqli_stmt_execute($stmt);

// 6. Verifica si se eliminó alguna fila
if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(['success' => true, 'message' => 'Producto eliminado de la lista de deseos.']);
} else {
    // Esto puede ocurrir si el producto ya no estaba en la lista de deseos del usuario
    echo json_encode(['success' => false, 'message' => 'El producto no se encontró en tu lista de deseos o ya fue eliminado.']);
}

// 7. Cierra la declaración y la conexión a la base de datos
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
