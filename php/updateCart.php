<?php
session_start();
header('Content-Type: application/json');
include_once("conexionBD.php"); 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.']);
    exit;
}

$productId = $_POST['id'] ?? null;
$newCantidad = $_POST['cantidad'] ?? null;

if ($productId === null || $newCantidad === null || !is_numeric($newCantidad) || $newCantidad < 1) {
    echo json_encode(['success' => false, 'message' => 'Datos de actualización inválidos.']);
    exit;
}

$usuarioId = $_SESSION['user_id'];

$sqlUpdate = "UPDATE carrito SET cantidad = ? WHERE usuario_id = ? AND producto_id = ?";
$stmtUpdate = mysqli_prepare($conn, $sqlUpdate);

if ($stmtUpdate) {
    mysqli_stmt_bind_param($stmtUpdate, "iii", $newCantidad, $usuarioId, $productId);
    if (mysqli_stmt_execute($stmtUpdate)) {
        // Recalcular subtotal y total del carrito DESDE LA BASE DE DATOS
        $sqlTotal = "SELECT SUM(c.cantidad * p.precio) AS total
                     FROM carrito c
                     JOIN productos p ON c.producto_id = p.id
                     WHERE c.usuario_id = ?";
        $stmtTotal = mysqli_prepare($conn, $sqlTotal);
        mysqli_stmt_bind_param($stmtTotal, "i", $usuarioId);
        mysqli_stmt_execute($stmtTotal);
        $resultTotal = mysqli_stmt_get_result($stmtTotal);
        $rowTotal = mysqli_fetch_assoc($resultTotal);
        $subtotal = $rowTotal['total'] ?? 0.00;
        $total = $rowTotal['total'] ?? 0;
        mysqli_stmt_close($stmtTotal);

        echo json_encode(['success' => true, 
        'message' => 'Cantidad actualizada.', 
        'subtotal' => number_format($subtotal, 2), 
        'total' => $total]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la cantidad en la base de datos: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmtUpdate);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de actualización.']);
}

mysqli_close($conn);
?>