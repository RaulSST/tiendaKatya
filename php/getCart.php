<?php
session_start();
header('Content-Type: application/json');
include_once("conexionBD.php"); // Incluye tu archivo de conexión

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para ver el carrito.',
        'cart' => [],
        'totalProductos' => 0,
        'subtotal' => '0.00',
        'total' => '0.00'
    ]);
    exit;
}

$usuarioId = $_SESSION['user_id'];
$getFullDetails = isset($_GET['getFullDetails']); // Comprueba si se solicitan los detalles completos

// Obtiene los productos del carrito del usuario desde la base de datos
$sql = "SELECT p.id, p.nombre, p.precio, p.imagen, c.cantidad
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        WHERE c.usuario_id = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => "Error al preparar la consulta: " . mysqli_error($conn),
        'cart' => [],
        'totalProductos' => 0,
        'subtotal' => '0.00',
        'total' => '0.00'
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $usuarioId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart = [];
$totalProductos = 0;
$subtotal = 0;
$cartDetails = []; // Inicializa el array para los detalles del carrito

while ($row = mysqli_fetch_assoc($result)) {
    $product = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'precio' => $row['precio'],
        'imagen' => $row['imagen'],
        'cantidad' => $row['cantidad']
    ];
    $cart[] = $product;
    $totalProductos += $row['cantidad'];
    $subtotal += $row['precio'] * $row['cantidad'];

    // Si se solicitan los detalles completos, agrega el producto al array $cartDetails
    if ($getFullDetails) {
        $cartDetails[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'precio' => $row['precio'],
            'cantidad' => $row['cantidad']
        ];
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn); //Es importante cerrar la conexión

$total = $subtotal; // El total es igual al subtotal, pero puedes agregar impuestos u otros cargos aquí
// Formateamos los números para que se muestren con dos decimales.
$subtotal_formatted = number_format($subtotal, 2);
$total_formatted = number_format($total, 2);

$response = [
    'success' => true,
    'message' => 'Carrito obtenido correctamente',
    'cart' => $cart,
    'totalProductos' => $totalProductos,
    'subtotal' => $subtotal_formatted,
    'total' => $total_formatted
];

// Agrega los detalles del carrito a la respuesta si se solicitan
if ($getFullDetails) {
    $response['cartDetails'] = $cartDetails;
}

echo json_encode($response);
?>
