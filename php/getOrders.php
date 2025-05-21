<?php
session_start();
header('Content-Type: application/json');
include_once("conexionBD.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT o.id AS orden_id, o.total, o.estado, o.fecha,
               do.producto_id, do.cantidad AS producto_cantidad, do.precio AS producto_precio,
               p.imagen AS imagen_url,
               p.categoria_id -- Obtenemos el ID de la categoría del producto
        FROM ordenes o
        JOIN detalle_orden do ON o.id = do.orden_id
        JOIN productos p ON do.producto_id = p.id
        WHERE o.usuario_id = ?
        ORDER BY o.fecha DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orders = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    mysqli_free_result($result);
    echo json_encode(['success' => true, 'orders' => $orders]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al obtener los pedidos: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>