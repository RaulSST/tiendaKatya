<?php
session_start();
header('Content-Type: application/json');
include_once("conexionBD.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if ($data === null || !isset($data['total']) || !isset($data['productos']) || !is_array($data['productos']) || empty($data['productos'])) {
    echo json_encode(['success' => false, 'message' => 'Datos de pedido inválidos.']);
    exit;
}

$total = $data['total'];
$productos = $data['productos'];
$shippingAddress = $data['shippingAddress'] ?? null;
$paymentInfo = $data['payment'] ?? null;

// Iniciar transacción para asegurar la integridad de los datos
mysqli_begin_transaction($conn);

try {
    // Insertar en la tabla 'ordenes'
    $sqlOrden = "INSERT INTO ordenes (usuario_id, total, estado, fecha, direccion_envio, departamento_envio, distrito_envio, codigo_postal_envio, correo_envio, telefono_envio, metodo_pago) VALUES (?, ?, 'En proceso', NOW(), ?, ?, ?, ?, ?, ?, ?)";
    $stmtOrden = mysqli_prepare($conn, $sqlOrden);

    $direccion = $shippingAddress['direccion'] ?? '';
    $departamento = $shippingAddress['departamento'] ?? '';
    $distrito = $shippingAddress['distrito'] ?? '';
    $codigoPostal = $shippingAddress['codigoPostal'] ?? '';
    $correo = $shippingAddress['correo'] ?? '';
    $telefono = $shippingAddress['telefono'] ?? '';
    $metodoPago = $paymentInfo['paymentMethod'] ?? 'unknown';
    $infoPago = json_encode($paymentInfo); // Guardar la información de pago como JSON

    // CORRECCIÓN: La cadena de tipo ahora coincide con el número de variables (10)
     mysqli_stmt_bind_param($stmtOrden, "issssssss", $userId, $total, $direccion, $departamento, $distrito, $codigoPostal, $correo, $telefono, $metodoPago);
    mysqli_stmt_execute($stmtOrden);
    $ordenId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmtOrden);

    if (!$ordenId) {
        throw new Exception("Error al crear la orden.");
    }

    // Insertar en la tabla 'detalle_orden'
    $sqlDetalle = "INSERT INTO detalle_orden (orden_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
    $stmtDetalle = mysqli_prepare($conn, $sqlDetalle);

    foreach ($productos as $producto) {
        mysqli_stmt_bind_param($stmtDetalle, "iiid", $ordenId, $producto['producto_id'], $producto['cantidad'], $producto['precio']);
        mysqli_stmt_execute($stmtDetalle);
    }
    mysqli_stmt_close($stmtDetalle);

    // Limpiar el carrito del usuario
    $sqlLimpiarCarrito = "DELETE FROM carrito WHERE usuario_id = ?";
    $stmtLimpiarCarrito = mysqli_prepare($conn, $sqlLimpiarCarrito);
    mysqli_stmt_bind_param($stmtLimpiarCarrito, "i", $userId);
    if (!mysqli_stmt_execute($stmtLimpiarCarrito)) {
        throw new Exception("Error al limpiar el carrito: " . mysqli_stmt_error($stmtLimpiarCarrito));
    }
    mysqli_stmt_close($stmtLimpiarCarrito);

    // Confirmar la transacción
    mysqli_commit($conn);

    echo json_encode(['success' => true, 'order_id' => $ordenId, 'message' => 'Pedido guardado y carrito limpiado exitosamente.']); //mensaje carrito limpiado

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Error al guardar el pedido: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>
