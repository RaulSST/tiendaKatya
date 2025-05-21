<?php

include_once("../../php/verificarSesion.php"); 

function searchProducts($searchTerm = null, $sortOrder = 'default', $categoryFilter = null)
{
    global $conn; 
    $productos = [];
    $whereClauses = [];

    // Consulta base
    $sql = "SELECT p.id, p.nombre, p.precio, p.imagen, p.disponibilidad, c.nombre AS categoria
            FROM productos p
            INNER JOIN categorias c ON p.categoria_id = c.id";

    if ($categoryFilter && $categoryFilter !== 'Todo') {
        $cleanCategoryFilter = mysqli_real_escape_string($conn, $categoryFilter);
        $whereClauses[] = "c.nombre = '$cleanCategoryFilter'";
    }
 
    elseif ($searchTerm) {
        $cleanSearchTerm = mysqli_real_escape_string($conn, $searchTerm);
        $whereClauses[] = "(p.nombre LIKE '%$cleanSearchTerm%' OR c.nombre LIKE '%$cleanSearchTerm%')";
    }

 
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }


    switch ($sortOrder) {
        case 'asc':
            $sql .= " ORDER BY p.precio ASC";
            break;
        case 'desc':
            $sql .= " ORDER BY p.precio DESC";
            break;
        case 'default':
        default:
            $sql .= " ORDER BY p.id DESC"; 
            break;
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['disponibilidadColor'] = $row['disponibilidad'] ? 'green' : 'red';
            $row['disponibilidad_texto'] = $row['disponibilidad'] ? 'En stock' : 'Agotado';
            $productos[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al buscar productos: " . mysqli_error($conn));
    }
    return $productos;
}

function getAllCategories() {
    global $conn;
    $categories = [];
    $sql = "SELECT nombre FROM categorias ORDER BY nombre ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['nombre'];
        }
        mysqli_free_result($result);
    } else {
        error_log("Error al obtener categorías: " . mysqli_error($conn));
    }
    return $categories;
}


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $searchTerm = isset($_GET['query']) ? $_GET['query'] : null;
    $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'default';
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : null;

    // Realizamos la búsqueda con el término, el orden y la categoría
    $filteredProducts = searchProducts($searchTerm, $sortOrder, $categoryFilter);

    // Cierra la conexión a la base de datos antes de enviar la respuesta JSON
    mysqli_close($conn);

    // Devuelve los productos en formato JSON y termina el script
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'products' => $filteredProducts]);
    exit(); // ¡Muy importante para que no se renderice el HTML completo!
}




$searchTerm = isset($_GET['query']) ? $_GET['query'] : null;
// Obtener el orden de la URL para la carga inicial
$initialSortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'default';

$initialCategoryFilter = isset($_GET['category']) ? $_GET['category'] : null;

// Lógica para determinar el filtro de categoría inicial en la carga completa de la página
if ($initialCategoryFilter === 'Todo') {
    // Si la URL especifica 'Todo', lo mantenemos como 'Todo'
    $initialCategoryFilter = 'Todo';
} else if ($initialCategoryFilter === null && $searchTerm === null) {
    // Si no hay categoría ni término de búsqueda en la URL, por defecto se muestra 'Todo'
    $initialCategoryFilter = 'Todo';
} else if ($initialCategoryFilter === null && $searchTerm !== null) {
    // Si hay un término de búsqueda pero no una categoría en la URL, la categoría inicial es null
    $initialCategoryFilter = null;
}


// Obtener los productos que coinciden con el término de búsqueda, el orden y la categoría inicial
$searchResults = searchProducts($searchTerm, $initialSortOrder, $initialCategoryFilter);

// Obtener todas las categorías para popular el filtro
$allCategories = getAllCategories();

// Cierra la conexión a la base de datos
mysqli_close($conn);

$isLoggedIn = isset($_SESSION['user_id']); 

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resultados de búsqueda: <?php echo htmlspecialchars($searchTerm); ?></title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="../../css/productBuscador.css">
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>

    <header id="header">
        <div class="container">
            <img src="../../img/logoKatya.png" class="logoTienda" alt="Importaciones Katya">

            <form action="buscar.php" method="GET" class="search-form">
                <div class="input-container">
                    <input type="text" id="searchInput" name="query" placeholder="Buscar" value="<?php echo htmlspecialchars($searchTerm); ?>" />
                    <button type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <div class="icon-container">
                <div class="cart-icon">
                    <a href="../pagesAccount/account-lista-deseos.php" class="icon">
                        <i class="bi bi-heart"></i><span id="wishlist-count">0</span>
                    </a>
                </div>

                <div class="cart-icon">
                    <a href="../pagesPago/cart.php" class="icon">
                        <i class="bi bi-cart"></i><span id="cart-count">0</span>
                    </a>
                </div>
                <a href="../pagesAccount/account.php" class="icon">
                    <i class="bi bi-person"></i>
                </a>
            </div>
        </div>
    </header>

    <div id="containProductBuscador" class="container">

        <div id="containerBreadcrumbs">
            <div class="breadcrumbs">
                <a href="../../index.php"><i class="bi-house-door"></i></a>
                <i class="bi bi-chevron-right"></i>
                <span id="searchResultsText">Resultados <?php echo ($searchTerm !== null && $initialCategoryFilter === null) ? 'para: "' . htmlspecialchars($searchTerm) . '"' : ''; ?></span>
            </div>
        </div>

        <div id="containerProductBuscador">

            <aside id="sidebarBuscador">
                <div class="sidebar">
                    <div class="filter-section">
                        <h4>Categorías</h4>
                        <div>
                            <label>
                                <input type="checkbox" class="me-2 category-checkbox" value="Todo"
                                    <?php echo ($initialCategoryFilter === 'Todo') ? 'checked' : ''; ?>>
                                Todo
                            </label>
                        </div>
                        <?php foreach ($allCategories as $category): ?>
                            <div>
                                <label>
                                    <input type="checkbox" class="me-2 category-checkbox" value="<?php echo htmlspecialchars($category); ?>"
                                        <?php echo ($initialCategoryFilter === $category) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($category); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="divBtnFiltro">
                        <button class="btnFiltro">Aplicar Cambios</button>
                    </div>
                </div>
            </aside>

            <main id="mainBuscador">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="filterPrecio">
                        <p>Ordenar precio:</p>
                        <select id="sortOptions" class="custom-select">
                            <option value="default" <?php echo ($initialSortOrder == 'default') ? 'selected' : ''; ?>>Predeterminado</option>
                            <option value="asc" <?php echo ($initialSortOrder == 'asc') ? 'selected' : ''; ?>>De menor a mayor</option>
                            <option value="desc" <?php echo ($initialSortOrder == 'desc') ? 'selected' : ''; ?>>De mayor a menor</option>
                        </select>
                    </div>

                    <div class="view-buttons">
                        <button id="gridViewBtn" class="btnGrid activeBtn"><i class="bi bi-grid"></i></button>
                        <button id="listViewBtn" class="btnList defaultBtn"><i class="bi bi-list"></i></i></button>
                    </div>
                </div>

                <div class="row g-3 product-grid" id="productContainer">
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
                <a href="../pagesAccount/account-order-history.php">Lista de pedidos</a>
                <a href="../pagesAccount/account-lista-deseos.php">Lista de deseos</a>
                <a href="../pagesAccount/account.php">Administrar cuenta</a>
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

    <script>
    // Datos iniciales de los productos pasados desde PHP
    const initialProductsData = <?php echo json_encode($searchResults); ?>;
    
    // Categoría actual seleccionada. Se inicializa con el valor PHP y se actualiza en JavaScript.
    // Puede ser 'Todo', el nombre de una categoría específica, o null (cuando solo el buscador está activo).
    let currentCategoryFilter = <?php echo json_encode($initialCategoryFilter); ?>;

    // Función auxiliar para determinar el término de búsqueda efectivo a enviar a PHP
    // Si hay una categoría específica seleccionada (o 'Todo'), el término de búsqueda se ignora (se envía vacío).
    // Si no hay categoría seleccionada (currentCategoryFilter es null), se usa el valor actual del input de búsqueda.
    function getEffectiveSearchTerm() {
        if (currentCategoryFilter !== null) { // Si hay una categoría seleccionada (incluido 'Todo')
            return ''; // Ignorar el término de búsqueda del input
        }
        return $('#searchInput').val(); // Usar el valor del input si no hay categoría seleccionada
    }

    // Función para actualizar el estado visual de los checkboxes de categoría y el texto de los breadcrumbs
    function updateCategoryCheckboxesAndBreadcrumbs() {
        $('.category-checkbox').prop('checked', false); // Desmarcar todos primero

        if (currentCategoryFilter === 'Todo') {
            $('input[value="Todo"]').prop('checked', true);
            $('#searchInput').val(''); // Vaciar el campo de búsqueda cuando 'Todo' está activo
            $('#searchResultsText').text('Resultados'); // "Resultados" para "Todo"
        } else if (currentCategoryFilter !== null) { // Categoría específica seleccionada
            $(`input[value="${currentCategoryFilter}"]`).prop('checked', true);
            $('#searchInput').val(''); // Vaciar el campo de búsqueda cuando categoría específica está activa
            $('#searchResultsText').text('Resultados'); // "Resultados" para categoría específica
        } else { // currentCategoryFilter es null (solo búsqueda por texto activa)
            // No vaciar el campo de búsqueda aquí, su valor ya es el que el usuario escribió
            const searchTerm = $('#searchInput').val();
            if (searchTerm) {
                $('#searchResultsText').text(`Resultados para: "${searchTerm}"`);
            } else {
                $('#searchResultsText').text('Resultados'); // Si no hay búsqueda ni categoría, solo "Resultados"
            }
        }
    }


    $(document).ready(function() {
        // Actualizar contadores de carrito y lista de deseos al cargar la página
        updateWishlistCounter();
        updateCartCounter();

        // Renderizar los productos iniciales al cargar la página
        renderProducts(initialProductsData);
        // Actualizar el estado visual de los checkboxes y los breadcrumbs al cargar la página
        updateCategoryCheckboxesAndBreadcrumbs();

        // --- Evento para el select de ordenamiento ---
        $('#sortOptions').on('change', function() {
            const selectedSortOrder = $(this).val();
            // Usar el término de búsqueda efectivo y la categoría actual
            loadProducts(getEffectiveSearchTerm(), selectedSortOrder, currentCategoryFilter);
        });

        // --- Evento para los checkboxes de categoría (solo actualiza el estado, no carga productos) ---
        $(document).on('change', '.category-checkbox', function() {
            const clickedCategory = $(this).val();
            
            // Lógica para asegurar que solo una categoría esté seleccionada
            if (clickedCategory === 'Todo') {
                $('.category-checkbox').not(this).prop('checked', false);
                currentCategoryFilter = 'Todo';
            } else {
                // Si se hace clic en una categoría específica y se marca
                if ($(this).is(':checked')) {
                    $('.category-checkbox').not(this).prop('checked', false); // Desmarcar las otras
                    $('input[value="Todo"]').prop('checked', false); // Desmarcar "Todo"
                    currentCategoryFilter = clickedCategory;
                } else {
                    // Si se desmarca una categoría específica, y no hay otra específica marcada,
                    // el filtro vuelve a ser 'Todo' por defecto
                    const anySpecificCategoryChecked = $('.category-checkbox[value!="Todo"]:checked').length > 0;
                    if (!anySpecificCategoryChecked) {
                        currentCategoryFilter = 'Todo'; 
                    } else {
                        // Si se desmarca una específica pero otra específica sigue marcada,
                        // la lógica de "solo una marcada" debería prevenir esto si el usuario solo puede marcar una.
                        // Si se permite desmarcar y dejar sin ninguna, entonces currentCategoryFilter
                        // debería ser null en este caso. Vamos a forzar a 'Todo' si no hay otra marcada.
                        currentCategoryFilter = 'Todo'; // Forzar a 'Todo' si se desmarca la única específica
                    }
                }
            }
            
            // Actualizar el estado visual de los checkboxes y los breadcrumbs
            updateCategoryCheckboxesAndBreadcrumbs();

            // NOTA: loadProducts() NO se llama aquí. Se llamará al hacer clic en "Aplicar Cambios".
        });

        // --- Evento para el botón "Aplicar Cambios" ---
        $('.btnFiltro').on('click', function() {
            // Carga los productos con los filtros actuales (término de búsqueda efectivo y categoría actual)
            loadProducts(getEffectiveSearchTerm(), $('#sortOptions').val(), currentCategoryFilter);
        });


        // --- Evento para el formulario de búsqueda (cuando se presiona Enter o el botón de búsqueda) ---
        $('.search-form').on('submit', function(event) {
            event.preventDefault(); // Evita el envío normal del formulario
            const searchTermFromInput = $('#searchInput').val();
            
            // Al buscar, se desmarcan todas las categorías.
            // La categoría activa pasa a ser null para que PHP aplique solo el término de búsqueda.
            currentCategoryFilter = null; // Establece a null para indicar que no hay filtro de categoría activo
            updateCategoryCheckboxesAndBreadcrumbs(); // Actualiza visualmente los checkboxes (ninguno marcado) y el texto

            // Carga los productos usando el término de búsqueda del input (ya que currentCategoryFilter es null)
            loadProducts(searchTermFromInput, $('#sortOptions').val(), currentCategoryFilter);
        });


        // Evento para el botón AÑADIR A CARRO
        $(document).on('click', '.btnCart', function(event) {
            event.preventDefault();
            if ("<?php echo $isLoggedIn ? 'true' : 'false'; ?>" === 'false') {
                window.location.href = '../pagesAutentication/login.php'; // Ruta relativa
                alert('Debes iniciar sesión para añadir productos al carrito.');
            } else {
                let productId = $(this).data('id');
                let productNombre = $(this).data('nombre');
                let productPrecio = $(this).data('precio');
                let productImagen = $(this).data('imagen');

                $.ajax({
                    url: '../../php/addCart.php', // Ruta relativa desde pages/pagesProductos/
                    type: 'POST',
                    data: {
                        id: productId,
                        nombre: productNombre,
                        precio: productPrecio,
                        imagen: productImagen
                    },
                    success: function(response) {
                        let data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            alert(data.message);
                            updateCartCounter();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function() {
                        alert("Hubo un error al añadir el producto al carrito");
                    }
                });
            }
        });

        // Función para actualizar contador del carrito
        function updateCartCounter() {
            $.ajax({
                url: '../../php/getCartCount.php', // Ruta relativa desde pages/pagesProductos/
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    let data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success && data.count !== undefined) {
                        $('#cart-count').text(data.count);
                    } else {
                        $('#cart-count').text(0);
                        console.error('Error al obtener el contador del carrito al cargar la página');
                    }
                },
                error: function() {
                    console.error("Hubo un error al obtener el contador del carrito al cargar la página.");
                }
            });
        }

        // Evento botón AÑADIR A LISTA DE DESEOS
        $(document).on('click', '.wishlistIcon, .btnWishlist', function(event) {
            event.preventDefault();

            if ("<?php echo $isLoggedIn ? 'true' : 'false'; ?>" === 'false') {
                window.location.href = '../pagesAutentication/login.php'; // Ruta relativa
                alert('Debes iniciar sesión para añadir productos a la lista de deseos.');
            } else {
                let productId = $(this).data('id');
                let productNombre = $(this).data('nombre');
                let productPrecio = $(this).data('precio');
                let productImagen = $(this).data('imagen');

                $.ajax({
                    url: '../../php/addWishlist.php', // Ruta relativa desde pages/pagesProductos/
                    type: 'POST',
                    data: {
                        id: productId,
                        nombre: productNombre,
                        precio: productPrecio,
                        imagen: productImagen
                    },
                    success: function(response) {
                        let data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (data.success) {
                            alert(data.message);
                            updateWishlistCounter();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
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
                url: '../../php/getWishlistCount.php', // Ruta relativa desde pages/pagesProductos/
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    let data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success && data.count !== undefined) {
                        $('#wishlist-count').text(data.count);
                    } else {
                        console.error('Error al obtener el contador de la lista de deseos');
                    }
                },
                error: function() {
                    console.error("Hubo un error al obtener el contador de la lista de deseos");
                }
            });
        }

        // --- Función para cargar productos con filtros (término de búsqueda, ordenamiento y categoría) ---
        function loadProducts(searchTerm, sortOrder = 'default', categoryFilter = 'Todo') {
            $('#loader').show(); // Muestra el loader (asegúrate de tener un elemento con id="loader" en tu HTML)
            $('#productContainer').empty(); // Limpia los productos existentes

            const dataToSend = {
                query: searchTerm,
                sort_order: sortOrder,
                category: categoryFilter // Envía el parámetro de categoría
            };

            $.ajax({
                url: 'buscar.php', // Apunta a este mismo archivo para procesar la búsqueda y filtros
                method: 'GET',
                dataType: 'json',
                data: dataToSend,
                success: function(response) {
                    $('#loader').hide(); // Oculta el loader

                    if (response.success && response.products) {
                        renderProducts(response.products); // Llama a renderProducts con los datos recibidos
                    } else {
                        $('#productContainer').html('<p class="text-center w-100">No se encontraron productos que coincidan con tu búsqueda y filtros.</p>');
                        console.error('Error al cargar productos:', response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loader').hide();
                    $('#productContainer').html('<p class="text-center w-100">Hubo un error en la petición AJAX para obtener productos.</p>');
                    console.error("Error en la petición AJAX para obtener productos:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        }

        // Función para renderizar productos (ajustada para usar los datos PHP)
        function renderProducts(productsToRender) {
            const productContainer = document.getElementById('productContainer');

            const isListView = document.body.classList.contains('list-view');

            if (!productsToRender || productsToRender.length === 0) {
                productContainer.innerHTML = '<p class="text-center w-100">No se encontraron productos que coincidan con tu búsqueda y filtros.</p>';
                return;
            }

            productContainer.innerHTML = productsToRender.map(p => {
                const columnClass = isListView ? 'col-12 col-md-6' : 'col-6 col-md-4'; 
                const cardClass = isListView ? 'list-view-card' : 'grid-view-card';

                const productName = p.nombre;
                const productPrice = parseFloat(p.precio).toFixed(2);
                const productImage = `../../img/productos/${p.imagen}`;
                const productAvailabilityText = p.disponibilidad_texto;
                const productAvailabilityColor = p.disponibilidadColor;
                const productCategory = p.categoria;
                const productId = p.id;

                const extraInfo = isListView ? `
                    <div class="infoSecu">
                        <p><strong>Disponibilidad: <span style="color:${productAvailabilityColor}">${productAvailabilityText}</span></strong></p>
                        <p><strong>Categoría:</strong> ${productCategory}</p>
                    </div>
                ` : '';

                return `
                    <div class="${columnClass}">
                        <div class="${cardClass}">
                            <div class="product-image">
                                <a href="product.php?id=${productId}">
                                    <img src="${productImage}" alt="${productName}" />
                                </a>
                                <div class="opcs_product">
                                    <a href="product.php?id=${productId}" class="icon" tabindex="0">
                                        <i class="bi bi-search"></i>
                                    </a>
                                    <i class="bi bi-heart wishlistIcon" data-id="${productId}"
                                        data-nombre="${productName}"
                                        data-precio="${productPrice}"
                                        data-imagen="${p.imagen}"></i>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="infoPrinc">
                                    <a href="product.php?id=${productId}">
                                        <h5>${productName}</h5>
                                    </a>
                                    <span class="price">S/${productPrice}</span>
                                </div>
                                ${extraInfo}
                                <div class="btns">
                                    <button class="btnCart" data-id="${productId}"
                                        data-nombre="${productName}"
                                        data-precio="${productPrice}"
                                        data-imagen="${p.imagen}">
                                        Añadir al carro
                                    </button>
                                    ${isListView ? `<button class="btnWishlist" data-id="${productId}" data-nombre="${productName}" data-precio="${productPrice}" data-imagen="${p.imagen}">Lista de deseos</button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

      
        document.getElementById('gridViewBtn').addEventListener('click', () => {
            document.body.classList.remove('list-view');
            // Al cambiar la vista, re-renderiza con los datos actuales
            // Mantiene el término de búsqueda efectivo y la categoría actual
            loadProducts(getEffectiveSearchTerm(), $('#sortOptions').val(), currentCategoryFilter);
            updateCategoryCheckboxesAndBreadcrumbs(); // Asegurar que el UI refleje el estado
            document.getElementById('gridViewBtn').classList.add('activeBtn');
            document.getElementById('gridViewBtn').classList.remove('defaultBtn');
            document.getElementById('listViewBtn').classList.remove('activeBtn');
            document.getElementById('listViewBtn').classList.add('defaultBtn');
        });

        document.getElementById('listViewBtn').addEventListener('click', () => {
            document.body.classList.add('list-view');
            // Al cambiar la vista, re-renderiza con los datos actuales
            // Mantiene el término de búsqueda efectivo y la categoría actual
            loadProducts(getEffectiveSearchTerm(), $('#sortOptions').val(), currentCategoryFilter);
            updateCategoryCheckboxesAndBreadcrumbs(); // Asegurar que el UI refleje el estado
            document.getElementById('listViewBtn').classList.add('activeBtn');
            document.getElementById('listViewBtn').classList.remove('defaultBtn');
            document.getElementById('gridViewBtn').classList.remove('activeBtn');
            document.getElementById('gridViewBtn').classList.add('defaultBtn');
        });

    }); 
    </script>
    <script src="../../js/bootstrap.bundle.js"></script>
</body>

</html>
