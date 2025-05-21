<?php
include_once("php/verificarSesion.php"); 


function getProductos($categoria_id = null)
{
    global $conn; 

    $sql = "SELECT p.id, p.nombre, p.precio, p.imagen, p.disponibilidad, c.nombre AS categoria
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id";

    if ($categoria_id !== null && $categoria_id !== '' && $categoria_id !== '0') {
        $categoria_id = mysqli_real_escape_string($conn, $categoria_id);
        $sql .= " WHERE c.id = '$categoria_id'";
    }

    $sql .= " ORDER BY p.id DESC";
    $result = mysqli_query($conn, $sql);
    $productos = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['disponibilidadColor'] = $row['disponibilidad'] ? 'green' : 'red';
            $row['disponibilidad_texto'] = $row['disponibilidad'] ? 'En stock' : 'Agotado';
            $productos[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al obtener productos: " . mysqli_error($conn));
    }
    return $productos;
}


function getAllCategories()
{
    global $conn;
    $sql = "SELECT id, nombre FROM categorias ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);
    $categories = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al obtener categorías: " . mysqli_error($conn));
    }
    return $categories;
}

// Lógica para la redirección de rutas protegidas (si es necesario)
$currentPage = $_SERVER['REQUEST_URI'];
if (isset($rutasProtegidas) && array_key_exists($currentPage, $rutasProtegidas) && !$isLoggedIn) {
    header("Location: /pages/pagesAutentication/login.php");
    exit();
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($_GET['category_id'])) {
    $categoryId = $_GET['category_id'];
    $filteredProducts = getProductos($categoryId);

    // Cierra la conexión a la base de datos antes de enviar la respuesta JSON
    mysqli_close($conn);

    // Devuelve los productos en formato JSON y termina el script
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'products' => $filteredProducts]);
    exit(); // ¡Muy importante para que no se renderice el HTML completo!
}



$selected_category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '0'; 

$productos = getProductos($selected_category_id);
$categories = getAllCategories();


mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importaciones Katya</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Futura&family=Roboto&family=Helvetica&family=Garamond&family=Caslon&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="description" content="Importaciones Katya">
</head>

<body>

    <header id="header">
        <div class="container">
            <img src="img/logoKatya.png" class="logoTienda" alt="Logo Importaciones Katya">

            <form action="pages/pagesProductos/buscar.php" method="GET" class="search-form">
                <div class="input-container">
                    <input type="text" name="query" placeholder="Buscar" />
                    <button type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <div class="icon-container">
                <div class="cart-icon">
                    <a href="pages/pagesAccount/account-lista-deseos.php" class="icon">
                        <i class="bi bi-heart"></i><span id="wishlist-count">0</span></span>
                    </a>
                </div>

                <div class="cart-icon">
                    <a href="pages/pagesPago/cart.php" class="icon">
                        <i class="bi bi-cart"></i><span id="cart-count">0</span>
                    </a>
                </div>

                <a href="pages/pagesAccount/account.php" class="icon">
                    <i class="bi bi-person"></i>
                </a>
            </div>
        </div>
    </header>

    <nav id="navContainer">
        <nav id="navDesktop" class="container">
            <ul class="navbar-nav">
                <li><a href="#" class="category-filter" data-category-id="0">Inicio</a></li>
                <?php foreach ($categories as $category): ?>
                    <li><a href="#" class="category-filter" data-category-id="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </nav>

    <div id="containImgPortada">
        <img src="img/imgPortada.png" class="img-principal" alt="Imagen de Portada">
        <div class="texto-encima">
            <h1>Importaciones Katya</h1>
            <p>Confianza, variedad y precios que te encantarán</p>
            <button class="boton"><a href="#productos">EXPLORA NUESTRA TIENDA</a></button>
        </div>
    </div>

    <div class="container mt-5" id="productos">
        <div class="row row-cols-xl-5" id="productos-list">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col producto">
                        <div class="product_item">
                            <div class="img_product">
                                <a href="product-view.php?id=<?php echo htmlspecialchars($producto['id']); ?>">
                                    <img class="imgProducto" src="img/productos/<?php echo htmlspecialchars($producto['imagen']); ?>"
                                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                </a>
                                <div class="opcs_product">
                                    <a href="pages/pagesProductos/product.php?id=<?php echo htmlspecialchars($producto['id']); ?>" class="icon">
                                        <i class="bi bi-search"></i>
                                    </a>
                                    <i class="bi bi-heart wishlistIcon" data-id="<?php echo htmlspecialchars($producto['id']); ?>"
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                        data-precio="<?php echo htmlspecialchars($producto['precio']); ?>"
                                        data-imagen="<?php echo htmlspecialchars($producto['imagen']); ?>"></i>
                                </div>
                            </div>
                            <div class="content_product">
                                <div class="infoPrinc">
                                    <a href="product-view.php?id=<?php echo htmlspecialchars($producto['id']); ?>">
                                        <h5><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                    </a>
                                    <span class="price">S/<?php echo number_format($producto['precio'], 2); ?></span>
                                </div>
                                <div class="infoSecu">
                                    <p><strong>Disponibilidad: <span
                                                style="color:<?php echo htmlspecialchars($producto['disponibilidadColor']); ?>"><?php echo htmlspecialchars($producto['disponibilidad_texto']); ?></span></strong>
                                    </p>
                                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria']); ?></p>
                                </div>
                                <div class="product_adcart">
                                    <button class="btnCart" data-id="<?php echo htmlspecialchars($producto['id']); ?>"
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                        data-precio="<?php echo htmlspecialchars($producto['precio']); ?>"
                                        data-imagen="<?php echo htmlspecialchars($producto['imagen']); ?>">
                                        AÑADIR A CARRO
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center w-100">No se encontraron productos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="loader" style="text-align: center; display: none;">
        <p>Cargando más productos...</p>
    </div>

    <footer id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-md-0">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-12">
                            <div class="footer_logo">
                                <img src="img/logoKatya.png" alt="logo" class="logoTienda">
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
                                <a href="pages/pagesAccount/account-order-history.php">Lista de pedidos</a>
                                <a href="pages/pagesAccount/account-lista-deseos.php">Lista de deseos</a>
                                <a href="pages/pagesAccount/account.php">Administrar cuenta</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="footer_menu">
                                <h4 class="footer_title">Informacion</h4>
                                <a href="pages/pagesFooter/terminos-condiciones.html">Términos y condiciones</a>
                                <a href="pages/pagesFooter/politica-privacidad.html">Politica de privacidad</a>
                                <a href="pages/pagesFooter/preguntas-frecuentes.html">Preguntas frecuentes</a>
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
                                            style="text-decoration: none; color: black;"><i class="bi bi-facebook"
                                                style="color: purple;"></i>
                                            Importaciones Katya
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
    <script src="js/navScroll.js"></script>

    <script>
        $(document).ready(function () {
            // Actualizar contadores de carrito y lista de deseos al cargar la página
            updateWishlistCounter();
            updateCartCounter();

            // Función para establecer la categoría activa al cargar la página
            function initializeActiveCategory() {
                const initialCategoryId = new URLSearchParams(window.location.search).get('category_id');
                // Si no hay category_id en la URL, asumimos que estamos en "Inicio" (ID 0)
                const categoryToActivate = initialCategoryId || '0';
                $(`a.category-filter[data-category-id="${categoryToActivate}"]`).closest('li').addClass('active');
            }

            // Llama a la función de inicialización al cargar la página
            initializeActiveCategory();


            // Evento botón AÑADIR A CARRO
            $(document).on('click', '.btnCart', function (event) {
                event.preventDefault();

                // Asegúrate de que $isLoggedIn esté definido y sea un booleano en PHP.
                // Si es un string 'false', la comparación debe ser estricta.
                if ("<?php echo $isLoggedIn ? 'true' : 'false'; ?>" === 'false') {
                    window.location.href = './pages/pagesAutentication/login.php';
                    alert('Debes iniciar sesión para añadir productos al carrito.');
                } else {
                    let productId = $(this).data('id');
                    let productNombre = $(this).data('nombre');
                    let productPrecio = $(this).data('precio');
                    let productImagen = $(this).data('imagen');

                    $.ajax({
                        url: 'php/addCart.php',
                        type: 'POST',
                        data: {
                            id: productId,
                            nombre: productNombre,
                            precio: productPrecio,
                            imagen: productImagen
                        },
                        success: function (response) {
                            let data = typeof response === 'string' ? JSON.parse(response) : response;

                            console.log(data);

                            if (data.success) {
                                alert(data.message);
                                updateCartCounter();
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function () {
                            alert("Hubo un error al añadir el producto al carrito");
                        }
                    });
                }
            });

            // Función para actualizar contador del carrito
            function updateCartCounter() {
                $.ajax({
                    url: 'php/getCartCount.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (data.success && data.count !== undefined) {
                            $('#cart-count').text(data.count);
                        } else {
                            $('#cart-count').text(0);
                            console.error('Error al obtener el contador del carrito al cargar la página');
                        }
                    },
                    error: function () {
                        console.error("Hubo un error al obtener el contador del carrito al cargar la página.");
                    }
                });
            }

            // Evento botón AÑADIR A LISTA DE DESEOS
            $(document).on('click', '.wishlistIcon', function (event) {
                event.preventDefault();

                if ("<?php echo $isLoggedIn ? 'true' : 'false'; ?>" === 'false') {
                    window.location.href = 'pages/pagesAutentication/login.php';
                    alert('Debes iniciar sesión para añadir productos a la lista de deseos.');
                } else {
                    let productId = $(this).data('id');
                    let productNombre = $(this).data('nombre');
                    let productPrecio = $(this).data('precio');
                    let productImagen = $(this).data('imagen');

                    $.ajax({
                        url: 'php/addWishlist.php',
                        type: 'POST',
                        data: {
                            id: productId,
                            nombre: productNombre,
                            precio: productPrecio,
                            imagen: productImagen
                        },
                        success: function (response) {
                            let data = typeof response === 'string' ? JSON.parse(response) : response;

                            if (data.success) {
                                alert(data.message);
                                updateWishlistCounter();
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                                console.error("Hubo un error al añadir el producto a la lista de deseos");
                                console.error("Estado:", textStatus);
                                console.error("Error:", errorThrown);
                                console.error("Respuesta del servidor:", jqXHR.responseText);
                                alert("Hubo un error al añadir el producto a la lista de deseos");
                            }
                        });
                    }
                });

            // Función para actualizar contador de lista de deseos
            function updateWishlistCounter() {
                $.ajax({
                    url: 'php/getWishlistCount.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        let data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (data.success && data.count !== undefined) {
                            $('#wishlist-count').text(data.count);
                        } else {
                            console.error('Error al obtener el contador de la lista de deseos');
                        }
                    },
                    error: function () {
                        console.error("Hubo un error al obtener el contador de la lista de deseos");
                    }
                });
            }

            // --- Category Filtering y manejo de clase 'active' en el mismo evento ---

            // Event listener para los enlaces de categoría
            $(document).on('click', '.category-filter', function(event) {
                event.preventDefault(); // Evita el comportamiento por defecto del enlace

                const categoryId = $(this).data('category-id'); // Obtiene el data-category-id del enlace clickeado

                // Remueve la clase 'active' de todos los 'a' en el menú
                $('#navDesktop .navbar-nav a').removeClass('active');

                // Añade la clase 'active' al enlace clickeado
                $(this).addClass('active');

                loadProductsByCategory(categoryId); // Carga los productos de la categoría
            });

            // Función para cargar productos por categoría usando AJAX
            function loadProductsByCategory(categoryId) {
                $('#loader').show(); // Muestra el loader
                $('#productos-list').empty(); // Limpia los productos existentes

                $.ajax({
                    url: 'index.php', // ¡Ahora la URL apunta al mismo archivo index.php!
                    method: 'GET',
                    dataType: 'json',
                    data: { category_id: categoryId }, // Envía el ID de la categoría
                    success: function(response) {
                        $('#loader').hide(); // Oculta el loader

                        if (response.success && response.products) {
                            if (response.products.length > 0) {
                                response.products.forEach(function(producto) {
                                    const productHtml = `
                                        <div class="col producto">
                                            <div class="product_item">
                                                <div class="img_product">
                                                    <a href="product-view.php?id=${producto.id}">
                                                        <img class="imgProducto" src="img/productos/${producto.imagen}"
                                                            alt="${producto.nombre}">
                                                    </a>
                                                    <div class="opcs_product">
                                                        <a href="pages/pagesProductos/product.php?id=${producto.id}" class="icon">
                                                            <i class="bi bi-search"></i>
                                                        </a>
                                                        <i class="bi bi-heart wishlistIcon" data-id="${producto.id}"
                                                            data-nombre="${producto.nombre}"
                                                            data-precio="${producto.precio}"
                                                            data-imagen="${producto.imagen}"></i>
                                                    </div>
                                                </div>
                                                <div class="content_product">
                                                    <div class="infoPrinc">
                                                        <a href="product-view.php?id=${producto.id}">
                                                            <h5>${producto.nombre}</h5>
                                                        </a>
                                                        <span class="price">S/${parseFloat(producto.precio).toFixed(2)}</span>
                                                    </div>
                                                    <div class="infoSecu">
                                                        <p><strong>Disponibilidad: <span
                                                                    style="color:${producto.disponibilidadColor}">${producto.disponibilidad_texto}</span></strong>
                                                        </p>
                                                        <p><strong>Categoría:</strong> ${producto.categoria}</p>
                                                    </div>
                                                    <div class="product_adcart">
                                                        <button class="btnCart" data-id="${producto.id}"
                                                            data-nombre="${producto.nombre}"
                                                            data-precio="${producto.precio}"
                                                            data-imagen="${producto.imagen}">
                                                            AÑADIR A CARRO
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    $('#productos-list').append(productHtml);
                                });
                            } else {
                                $('#productos-list').html('<p class="text-center w-100">No se encontraron productos para esta categoría.</p>');
                            }
                        } else {
                            $('#productos-list').html('<p class="text-center w-100">Hubo un error al cargar los productos.</p>');
                            console.error('Error al cargar productos:', response.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#loader').hide();
                        $('#productos-list').html('<p class="text-center w-100">Hubo un error en la petición AJAX para obtener productos.</p>');
                        console.error("Error en la petición AJAX para obtener productos:", textStatus, errorThrown, jqXHR.responseText);
                    }
                });
            }
        });
    </script>

</body>

</html>