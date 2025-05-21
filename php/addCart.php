<?php
session_start();
header('Content-Type: application/json');
include_once("conexionBD.php"); // Asegúrate de que este archivo contiene la conexión a tu base de datos

// Verificamos si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para añadir productos al carrito.',
        'cart' => [] // Devuelve un carrito vacío para mantener la consistencia
    ]);
    exit;
}

// Verificamos si existen los datos esperados
$productId = $_POST['id'] ?? null;
$productNombre = $_POST['nombre'] ?? null;
$productPrecio = $_POST['precio'] ?? null;
$productImagen = $_POST['imagen'] ?? null;

// --- CAMBIO CLAVE AQUÍ ---
// Intenta obtener la cantidad del POST. Si no existe o no es un número válido, usa 1.
$cantidadRecibida = $_POST['cantidad'] ?? 1; // Obtiene la cantidad del POST o 1 por defecto

// Valida que la cantidad sea un entero positivo.
$cantidad = filter_var($cantidadRecibida, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1)));

if ($cantidad === false) { // Si la cantidad no es un entero válido o es menor que 1
    // Si la cantidad es inválida, se establece en 1 para evitar errores.
    // Esto es un fallback de seguridad, pero idealmente el frontend debería enviar números válidos.
    $cantidad = 1;
}
// --- FIN DEL CAMBIO CLAVE ---


// Validamos que todos los campos estén presentes
if ($productId === null || $productNombre === null || $productPrecio === null || $productImagen === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos del producto incompletos',
        'cart' => [] // Devuelve un carrito vacío
    ]);
    exit;
}

$usuarioId = $_SESSION['user_id'];

// Verificamos si el producto ya está en el carrito del usuario en la base de datos
$sqlCheck = "SELECT cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ?";
$stmtCheck = mysqli_prepare($conn, $sqlCheck);

if ($stmtCheck) {
    mysqli_stmt_bind_param($stmtCheck, "ii", $usuarioId, $productId);
    mysqli_stmt_execute($stmtCheck);
    $resultCheck = mysqli_stmt_get_result($stmtCheck);

    if ($rowCheck = mysqli_fetch_assoc($resultCheck)) {
        // El producto ya existe, actualizamos la cantidad en la base de datos
        // Aquí sumamos la $cantidad que hemos procesado (sea 1 o la enviada desde product.php)
        $nuevaCantidad = $rowCheck['cantidad'] + $cantidad;
        $sqlUpdate = "UPDATE carrito SET cantidad = ? WHERE usuario_id = ? AND producto_id = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);

        if ($stmtUpdate) {
            mysqli_stmt_bind_param($stmtUpdate, "iii", $nuevaCantidad, $usuarioId, $productId);
            if (mysqli_stmt_execute($stmtUpdate)) {
                $message = 'Cantidad del producto actualizada en el carrito.';
            } else {
                $message = 'Error al actualizar la cantidad del producto: ' . mysqli_error($conn);
                $response = [
                    'success' => false,
                    'message' => $message,
                    'cart' => []
                ];
                echo json_encode($response);
                exit;
            }
            mysqli_stmt_close($stmtUpdate);
        } else {
            $message = 'Error al preparar la consulta de actualización de cantidad.';
            $response = [
                'success' => false,
                'message' => $message,
                'cart' => []
            ];
            echo json_encode($response);
            exit;
        }
    } else {
        // El producto no existe en el carrito, lo insertamos en la base de datos con la $cantidad procesada
        $sqlInsert = "INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)";
        $stmtInsert = mysqli_prepare($conn, $sqlInsert);

        if ($stmtInsert) {
            mysqli_stmt_bind_param($stmtInsert, "iii", $usuarioId, $productId, $cantidad); // Usa la $cantidad procesada
            if (mysqli_stmt_execute($stmtInsert)) {
                $message = 'Producto añadido al carrito.';
            } else {
                $message = 'Error al añadir producto al carrito: ' . mysqli_error($conn);
                $response = [
                    'success' => false,
                    'message' => $message,
                    'cart' => []
                ];
                echo json_encode($response);
                exit;
            }
            mysqli_stmt_close($stmtInsert);
        } else {
            $message = 'Error al preparar la consulta de inserción.';
            $response = [
                'success' => false,
                'message' => $message,
                'cart' => []
            ];
            echo json_encode($response);
            exit;
        }
    }
    mysqli_stmt_close($stmtCheck);
} else {
    $message = 'Error al preparar la consulta de verificación de existencia.';
    $response = [
        'success' => false,
        'message' => $message,
        'cart' => []
    ];
    echo json_encode($response);
    exit;
}

// Obtener el carrito actualizado desde la base de datos para devolverlo en la respuesta
$sqlGetCart = "SELECT producto_id, cantidad FROM carrito WHERE usuario_id = ?";
$stmtGetCart = mysqli_prepare($conn, $sqlGetCart);
$cart = [];

if ($stmtGetCart) {
    mysqli_stmt_bind_param($stmtGetCart, "i", $usuarioId);
    mysqli_stmt_execute($stmtGetCart);
    $resultGetCart = mysqli_stmt_get_result($stmtGetCart);
    while ($rowGetCart = mysqli_fetch_assoc($resultGetCart)) {
        $cart[] = [
            'id' => $rowGetCart['producto_id'],
            'cantidad' => $rowGetCart['cantidad']
        ];
    }
    mysqli_stmt_close($stmtGetCart);
}

// Devolvemos la respuesta JSON
echo json_encode([
    'success' => true,
    'message' => $message, // Usamos el mensaje generado arriba
    'cart' => $cart
]);
// Cierra la conexión a la base de datos al final
mysqli_close($conn);
?>