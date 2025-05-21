<?php
include_once("../../php/verificarSesion.php");
include_once('../../config/routes.php');

$currentPage = $_SERVER['REQUEST_URI'];

if (array_key_exists($currentPage, $rutasProtegidas) && !$isLoggedIn) {
  header("Location: /pages/pagesAutentication/login.php");
  exit();
}

function getAllCategories() {
    global $conn;
    $categories = [];
    $sql = "SELECT id, nombre FROM categorias ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row; // Devuelve ID y nombre
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al obtener categorías en account.php: " . mysqli_error($conn));
    }
    return $categories;
}

$categories = getAllCategories();

$usuarioId = $_SESSION['user_id'];
$pedidoData = null;
$productoARastrear = null; // Variable para almacenar el producto específico a rastrear
$errorMessage = '';

// Obtener el ID del pedido y el ID del producto de la URL
$pedido_id = isset($_GET['pedido_id']) ? (int)$_GET['pedido_id'] : 0;
$producto_id = isset($_GET['producto_id']) ? (int)$_GET['producto_id'] : 0; // <--- NUEVO: Obtener producto_id

// Solo procede si se han proporcionado IDs válidos
if ($pedido_id > 0 && $producto_id > 0) { // <--- Aseguramos que ambos IDs sean válidos
    // Consulta principal para obtener los detalles de la orden y el usuario
    $sql = "SELECT
                o.id AS pedido_id,
                o.total,
                o.estado AS estado_pedido,
                o.fecha AS fecha_pedido,
                o.direccion_envio,
                o.departamento_envio,
                o.distrito_envio,
                o.codigo_postal_envio,
                o.correo_envio,
                o.telefono_envio,
                u.nombre AS usuario_nombre,
                u.apellidos AS usuario_apellido,
                u.email AS usuario_email,
                u.telefono AS usuario_telefono
            FROM
                ordenes o
            JOIN
                usuarios u ON o.usuario_id = u.id
            WHERE
                o.id = ? AND o.usuario_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $pedido_id, $usuarioId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pedidoData = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$pedidoData) {
            $errorMessage = 'Pedido no encontrado o no pertenece a tu cuenta.';
        } else {
            // Consulta para obtener el producto específico a rastrear
            $sqlProductoEspecifico = "SELECT
                                        do.cantidad,
                                        do.precio AS precio_unitario,
                                        p.nombre AS producto_nombre,
                                        p.imagen AS producto_imagen
                                    FROM
                                        detalle_orden do
                                    JOIN
                                        productos p ON do.producto_id = p.id
                                    WHERE
                                        do.orden_id = ? AND do.producto_id = ?"; // <--- FILTRAMOS POR PRODUCTO_ID

            $stmtProductoEspecifico = mysqli_prepare($conn, $sqlProductoEspecifico);
            if ($stmtProductoEspecifico) {
                mysqli_stmt_bind_param($stmtProductoEspecifico, "ii", $pedido_id, $producto_id);
                mysqli_stmt_execute($stmtProductoEspecifico);
                $resultProductoEspecifico = mysqli_stmt_get_result($stmtProductoEspecifico);
                $productoARastrear = mysqli_fetch_assoc($resultProductoEspecifico); // Solo un producto
                mysqli_stmt_close($stmtProductoEspecifico);

                if (!$productoARastrear) {
                    $errorMessage = 'El producto especificado no se encontró en este pedido.';
                }
            } else {
                $errorMessage = 'Error al preparar la consulta del producto específico.';
            }
        }
    } else {
        $errorMessage = 'Error al preparar la consulta de la orden principal.';
    }
} else {
    $errorMessage = 'No se han especificado un ID de pedido o producto válido para rastrear.';
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rastreo de pedido - Importaciones Katya</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="../../css/pagesAccount.css">
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <header id="header">
        <div class="container">
            <img src="../../img/logoKatya.png" class="logoTienda" alt="Logo Importaciones Katya">

            <form action="../pagesProductos/buscar.php" method="GET" class="search-form">
                <div class="input-container">
                    <input type="text" name="query" placeholder="Buscar" />
                    <button type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <div class="icon-container">
                <div class="cart-icon">
                    <a href="account-lista-deseos.php" class="icon">
                        <i class="bi bi-heart"></i><span id="wishlist-count">0</span></span>
                    </a>
                </div>
                <div class="cart-icon">
                    <a href="../pagesPago/cart.php" class="icon">
                        <i class="bi bi-cart"></i><span id="cart-count">0</span>
                    </a>
                </div>
                <a href="account.php" class="icon">
                    <i class="bi bi-person"></i>
                </a>
            </div>
        </div>
    </header>

    <nav id="navContainer">
        <nav id="navDesktop" class="container">
            <ul class="navbar-nav">
                <li class="inicio"><a href="../../index.php?category_id=0">Inicio</a></li>
        <?php foreach ($categories as $category): ?>
          <li><a href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a></li>
        <?php endforeach; ?>
            </ul>
        </nav>
        <nav class="navbar navbar-mobile" id="navMobile">
            <img src="img/logoKatya.png" class="logoTienda" alt="Logo Importaciones Katya">
            <div class="grupo-final">
                <img src="img/turespaña.jpg" class="logo-turespaña" alt="Turespaña">
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
                    aria-controls="offcanvasMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menú</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">DESTINOS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">EXPERIENCIAS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">SPAINRAILPASS</a>
                        </li>
                        <li class="nav-item mt-2">
                            <select id="languageSelect" class="form-select">
                                <option value="ESPAÑOL" selected>ESPAÑOL</option>
                                <option value="INGLÉS">INGLÉS</option>
                                <option value="FRANCÉS">FRANCÉS</option>
                            </select>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </nav>

    <div id="containAccount" class="container">
        <div id="containerBreadcrumbs">
            <div class="breadcrumbs">
                <a href="../../index.php"><i class="bi-house-door"></i></a>
                <i class="bi bi-chevron-right"></i>
                <a href="account.php">Mi cuenta</a>
                <i class="bi bi-chevron-right"></i>
                <a href="account-order-history.php" class="active">Historial de pedidos</a>
                <i class="bi bi-chevron-right"></i>
                <a href="account-rastreo-pedido.php?pedido_id=<?php echo $pedido_id; ?>&producto_id=<?php echo $producto_id; ?>">Rastrear producto</a>
            </div>
        </div>

        <div id="containerAccount">
            <aside id="sidebar">
                <div class="user-info">
                    <i class="bi bi-person"></i>
                    <p class="name" id="userName"></p>
                </div>
                <nav>
                    <div>
                        <a href="account.php"><i class="bi bi-person-gear"></i> <span>Administrar cuenta</span></a>
                        <a href="account-profile-info.php">Información del perfil</a>
                        <a href="account-manage-address.php">Administrar dirección</a>
                        <a href="account-change-password.php">Cambiar contraseña</a>
                    </div>
                    <div>
                        <a href="account-order-history.php" class="active"><i class="bi bi-bag"></i> <span>Historial de pedidos</span></a>
                        <a href="account-my-reviews.php">Mis reseñas</a>
                    </div>
                    <div>
                        <a href="account-lista-deseos.php"><i class="bi bi-heart"></i> <span>Lista de deseos</span></a>
                    </div>
                    <div class="cerrarSesion">
                        <a href="logout.php"><i class="bi bi-power"></i> <span>Cerrar sesión</span></a>
                    </div>
                </nav>
            </aside>

            <main id="main">
                <div class="orders-section">
                    <h2>Rastreo de Producto</h2>

                    <?php if ($pedidoData && $productoARastrear): // Si hay datos del pedido y del producto específico, los mostramos ?>
                    <div class="order-card">
                        <div class="order-info">
                            <div class="order-details">
                                <div>
                                    <p><strong>Vendido por </strong></p>
                                    <p>Importaciones Katya</p>
                                </div>
                                <div>
                                    <p><strong>Número de orden</strong></p>
                                    <p><?php echo htmlspecialchars($pedidoData['pedido_id']); ?></p>
                                </div>
                                <div>
                                    <p><strong>Fecha de pedido</strong></p>
                                    <p><?php echo htmlspecialchars(date('d F Y', strtotime($pedidoData['fecha_pedido']))); ?></p>
                                </div>
                            </div>

                            <div class="timeline" data-step="<?php
                                switch ($pedidoData['estado_pedido']) {
                                    case 'En proceso':
                                        echo '1';
                                        break;
                                    case 'En camino':
                                        echo '2';
                                        break;
                                    case 'Entregado':
                                        echo '3';
                                        break;
                                    default:
                                        echo '1'; // Por defecto, 'En proceso'
                                }
                            ?>">
                                <div class="progress-line"></div>
                                <div class="step">
                                    <div class="circle"></div>
                                    <p>En proceso</p>
                                </div>
                                <div class="step">
                                    <div class="circle"></div>
                                    <p>En camino</p>
                                </div>
                                <div class="step">
                                    <div class="circle"></div>
                                    <p>Entregado</p>
                                </div>
                            </div>

                            <div class="msgEntrega">
                                <div>
                                    <p>Última actualización: <?php echo htmlspecialchars(date('d F Y, H:i', strtotime($pedidoData['fecha_pedido']))); ?></p>
                                </div>
                                <div>
                                    <p>Su pedido está en estado: <strong><?php echo htmlspecialchars($pedidoData['estado_pedido']); ?></strong>.</p>
                                    <?php if ($pedidoData['estado_pedido'] == 'Entregado'): ?>
                                        <p>¡Gracias por comprar en Importaciones Katya!</p>
                                    <?php elseif ($pedidoData['estado_pedido'] == 'En camino'): ?>
                                           <p>Su pedido está en camino a su dirección.</p>
                                    <?php else: ?>
                                           <p>Su pedido está siendo procesado y pronto será enviado.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="productoRastreo">
                                <div class="product">
                                    <div class="divImgProduct">
                                        <img src="../../img/productos/<?php echo htmlspecialchars($productoARastrear['producto_imagen']); ?>" alt="Producto" class="imgProductoRastreo" />
                                    </div>
                                    <div class="product-details">
                                        <h3><?php echo htmlspecialchars($productoARastrear['producto_nombre']); ?></h3>
                                        <p>Precio: S/<?php echo number_format($productoARastrear['precio_unitario'], 2); ?></p>
                                        <p>Cantidad: <?php echo htmlspecialchars($productoARastrear['cantidad']); ?></p>
                                    </div>
                                </div>

                                <div class="perfilPersonal">
                                    <h3>Perfil personal</h3>
                                    <p><?php echo htmlspecialchars($pedidoData['usuario_nombre'] . ' ' . $pedidoData['usuario_apellido']); ?></p>
                                    <p><?php echo htmlspecialchars($pedidoData['usuario_email']); ?></p>
                                    <p><?php echo htmlspecialchars($pedidoData['usuario_telefono']); ?></p>
                                </div>

                                <div class="dirEnvio">
                                    <h3>Dirección de envío</h3>
                                    <p><?php echo htmlspecialchars($pedidoData['direccion_envio']); ?></p>
                                    <p><?php echo htmlspecialchars($pedidoData['distrito_envio'] . ', ' . $pedidoData['departamento_envio'] . ', ' . $pedidoData['codigo_postal_envio']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: // Si no hay datos del pedido o del producto específico, o hubo un error ?>
                        <div class="alert alert-warning" role="alert">
                            <?php echo htmlspecialchars($errorMessage ?: 'No se pudo cargar la información del producto para el rastreo.'); ?>
                        </div>
                        <p>Por favor, regresa a tu <a href="account-order-history.php">historial de pedidos</a> para seleccionar una orden y un producto para rastrear.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <footer id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-md-0">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-12">
                            <div class="footer_logo">
                                <img loading="lazy" src="../../img/logoKatya.png" alt="logo" class="logoTienda">
                            </div>
                            <div class="footet_text">
                                <p>Importaciones Katya es tu tienda de confianza en productos para el hogar, moda y mucho más.<br>
                                    Traemos lo mejor en variedad, calidad y precios directamente para ti.<br>
                                    ¡Gracias por elegirnos!</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3 mb-md-0">
                    <div class="row">
                        <div class="col-6">
                            <div class="footer_menu">
                                <h4 class="footer_title">Mi Cuenta</h4>
                                <a href="account-order-history.php">Pedidos</a>
                                <a href="account-lista-deseos.php">Lista de deseos</a>
                                <a href="account.php">Administrar cuenta</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="footer_menu">
                                <h4 class="footer_title">Informacion</h4>
                                <a href="../pagesFooter/terminos-condiciones.php">Términos y condiciones</a>
                                <a href="../pagesFooter/politica-privacidad.php">Politica de privacidad</a>
                                <a href="../pagesFooter/preguntas-frecuentes.php">Preguntas frecuentes</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer_download">
                        <div class="row">
                            <div class="col-lg-6 col-lg-12">
                                <h4 class="footer_title">Contacto</h4>
                                <div class="footer_contact">
                                    <p>
                                        <span class="icn"><i class="bi bi-geo-alt"></i></span>
                                        Av. Buenos Aires 771, Puente Piedra 15118, Perú
                                    </p>
                                    <p>
                                        <span class="icn"><i class="bi bi-telephone"></i></span>
                                        +51 970 815 826, +51 928 104 181
                                    </p>
                                    <p>
                                        <span class="icn"><i class="bi bi-envelope"></i></span>
                                        Katyaimportaciones2025@gmail.com
                                    </p>
                                    <p>
                                        <a href="https://www.facebook.com/people/Importaciones-Katya/100092336872945/"
                                            style="text-decoration: none; color: black;"><span class="icn"><i
                                                class="bi bi-facebook"></i></span>
                                            Importaciones Katya</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="../../js/navScroll.js"></script>
    <script src="../../js/bootstrap.bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateCartCounter() {
            $.ajax({
                url: '../../php/getCartCount.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#cart-count').text(response.count);
                    } else {
                        $('#cart-count').text('0');
                    }
                },
                error: function() {
                    $('#cart-count').text('0');
                }
            });
        }

        function updateWishlistCounter() {
            $.ajax({
                url: '../../php/getWishlistCount.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#wishlist-count').text(response.count);
                    } else {
                        $('#wishlist-count').text('0');
                    }
                },
                error: function() {
                    $('#wishlist-count').text('0');
                }
            });
        }
        
        function loadUserInfo() {
        $.ajax({
          url: '../../php/getUserInfo.php',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.userData) {
              const userData = response.userData;
              $('#userName').text(userData.nombre);
            } else {
              console.error('Error al cargar la información del usuario:', response.message);
              $('#userName').text('Usuario');
            }
          },
          error: function() {
            console.error('Error en la petición AJAX para obtener la información del usuario.');
            $('#userName').text('Usuario');
          }
        });
      }

        $(document).ready(function() {
            updateCartCounter();
            updateWishlistCounter();
            
            loadUserInfo();
        });
    </script>
</body>
</html>