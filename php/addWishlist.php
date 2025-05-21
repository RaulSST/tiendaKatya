<?php
session_start();
header('Content-Type: application/json');
include_once("conexionBD.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para añadir productos a la lista de deseos.',
        'wishlist' => []
    ]);
    exit();
}

$productId = $_POST['id'] ?? null;
$productNombre = $_POST['nombre'] ?? null;
$productPrecio = $_POST['precio'] ?? null;
$productImagen = $_POST['imagen'] ?? null;

if ($productId === null || $productNombre === null || $productPrecio === null || $productImagen === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos del producto incompletos',
        'wishlist' => []
    ]);
    exit();
}

$usuarioId = $_SESSION['user_id'];

$sqlCheck = "SELECT * FROM favoritos WHERE usuario_id = ? AND producto_id = ?";
$stmtCheck = mysqli_prepare($conn, $sqlCheck);

if ($stmtCheck) {
    mysqli_stmt_bind_param($stmtCheck, "ii", $usuarioId, $productId);
    mysqli_stmt_execute($stmtCheck);
    $resultCheck = mysqli_stmt_get_result($stmtCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        $message = 'El producto ya está en tu lista de deseos.';
        $response = [
            'success' => false,
            'message' => $message,
            'wishlist' => []
        ];
        echo json_encode($response);
        exit(); // <----- ¡CORRECCIÓN IMPORTANTE!
    } else {
        $sqlInsert = "INSERT INTO favoritos (usuario_id, producto_id) VALUES (?, ?)";
        $stmtInsert = mysqli_prepare($conn, $sqlInsert);

        if ($stmtInsert) {
            mysqli_stmt_bind_param($stmtInsert, "ii", $usuarioId, $productId);

            if (mysqli_stmt_execute($stmtInsert)) {
                $message = 'Producto añadido la lista de deseos.';
            } else {
                $message = 'Error al añadir el producto a la lista de deseos: ' . mysqli_error($conn);
                $response = [
                    'success' => false,
                    'message' => $message,
                    'wishlist' => []
                ];
                echo json_encode($response);
                exit();
            }
            mysqli_stmt_close($stmtInsert);
        } else {
            $message = 'Error al preparar la consulta de inserción.';
            $response = [
                'success' => false,
                'message' => $message,
                'wishlist' => []
            ];
            echo json_encode($response);
            exit();
        }
    }
    mysqli_stmt_close($stmtCheck);
} else {
    $message = 'Error al preparar la consulta de verificación de existencia.';
    $response = [
        'success' => false,
        'message' => $message,
        'wishlist' => []
    ];
    echo json_encode($response);
    exit(); // <----- ¡CORRECCIÓN IMPORTANTE!
}

$sqlGetWishlist = "SELECT * FROM favoritos WHERE usuario_id = ?";
$stmtGetWishlist = mysqli_prepare($conn, $sqlGetWishlist);
$wishlist = [];

if ($stmtGetWishlist) {
    mysqli_stmt_bind_param($stmtGetWishlist, "i", $usuarioId);
    mysqli_stmt_execute($stmtGetWishlist);
    $resultGetWishlist = mysqli_stmt_get_result($stmtGetWishlist);
    while ($rowGetWishlist = mysqli_fetch_assoc($resultGetWishlist)) {
        $wishlist[] = [
            'id' => $rowGetWishlist['producto_id'],
        ];
    }
    mysqli_stmt_close($stmtGetWishlist);
}

echo json_encode([
    'success' => true,
    'message' => $message,
    'wishlist' => $wishlist
]);

mysqli_close($conn);
?>