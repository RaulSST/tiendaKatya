<?php
// Incluye tu archivo de conexión a la base de datos.
// Asegúrate de que 'conexionBD.php' contiene el código para conectar a tu base de datos
// y que la variable de conexión ($conn) está disponible globalmente o se devuelve.
include_once("conexionBD.php");

// Establece la cabecera para que el navegador sepa que la respuesta es JSON.
header('Content-Type: application/json');

/**
 * Función para obtener productos de la base de datos, opcionalmente filtrados por categoría.
 *
 * @param int|null $categoria_id El ID de la categoría para filtrar, o null para obtener todos los productos.
 * @return array Un array de productos con su información.
 */
function getProductos($categoria_id = null)
{
    global $conn; // Accede a la variable de conexión a la base de datos.

    // Consulta SQL base para obtener productos y su categoría.
    $sql = "SELECT p.id, p.nombre, p.precio, p.imagen, p.disponibilidad, c.nombre AS categoria
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id";

    // Si se proporciona un ID de categoría, añade la cláusula WHERE para filtrar.
    if ($categoria_id !== null && $categoria_id !== '') {
        // Escapa la entrada para prevenir inyecciones SQL.
        $categoria_id = mysqli_real_escape_string($conn, $categoria_id);
        $sql .= " WHERE c.id = '$categoria_id'";
    }

    // Ordena los productos por ID de forma descendente.
    $sql .= " ORDER BY p.id DESC";

    // Ejecuta la consulta.
    $result = mysqli_query($conn, $sql);

    // Array para almacenar los productos.
    $productos = [];

    // Itera sobre los resultados y los añade al array de productos.
    while ($row = mysqli_fetch_assoc($result)) {
        // Añade información de color y texto de disponibilidad para el frontend.
        $row['disponibilidadColor'] = $row['disponibilidad'] ? 'green' : 'red';
        $row['disponibilidad_texto'] = $row['disponibilidad'] ? 'En stock' : 'Agotado';
        $productos[] = $row;
    }
    return $productos;
}

// Obtiene el category_id de la solicitud GET (si existe).
// Si no se envía category_id, será null y la función getProductos devolverá todos los productos.
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

// Obtiene los productos filtrados (o todos, si no hay filtro).
$filteredProducts = getProductos($category_id);

// Prepara la respuesta JSON.
if ($filteredProducts !== false) { // Verifica si la consulta fue exitosa
    echo json_encode(['success' => true, 'products' => $filteredProducts]);
} else {
    // Si hubo un error en la consulta (aunque la función `getProductos` no devuelve false directamente en este caso,
    // es una buena práctica tener un manejo de errores más robusto si la consulta de la BD fallara).
    echo json_encode(['success' => false, 'message' => 'Error al obtener productos de la base de datos.']);
}

// Cierra la conexión a la base de datos al finalizar.
mysqli_close($conn);
?>