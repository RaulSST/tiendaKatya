<?php
session_start();
include_once("conexionBD.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado.', 'wishlist' => []]);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_wishlist = "SELECT p.*
                 FROM favoritos w
                 JOIN productos p ON w.producto_id = p.id
                 WHERE w.usuario_id = ?";
$stmt_wishlist = mysqli_prepare($conn, $sql_wishlist);
mysqli_stmt_bind_param($stmt_wishlist, "i", $user_id);
mysqli_stmt_execute($stmt_wishlist);
$result_wishlist = mysqli_stmt_get_result($stmt_wishlist);
$wishlist_products = [];
while ($row = mysqli_fetch_assoc($result_wishlist)) {
    $wishlist_products[] = $row;
}
mysqli_stmt_close($stmt_wishlist);

echo json_encode(['success' => true, 'wishlist' => $wishlist_products]);
mysqli_close($conn);
?>