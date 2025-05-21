<?php
session_start();
include_once("conexionBD.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.', 'count' => 0]);
    exit;
}

$usuarioId = $_SESSION['user_id'];

$sql = "SELECT SUM(cantidad) AS total FROM carrito WHERE usuario_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => "Error al preparar la consulta: " . mysqli_error($conn), 'count' => 0]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $usuarioId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $count = $row['total'] ?? 0;
    echo json_encode(['success' => true, 'count' => $count]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo obtener el contador del carrito.', 'count' => 0]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
