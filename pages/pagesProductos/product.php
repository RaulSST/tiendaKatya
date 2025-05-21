<?php
include_once("../../php/verificarSesion.php");

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

$productId = $_GET['id'] ?? null;
$product = null;
$comments_data = [];
$error_message = null;

if ($productId === null || !is_numeric($productId)) {
  $error_message = 'ID de producto inválido.';
} else {
  // Obtener detalles del producto
  $sqlProducto = "SELECT p.id, p.nombre, p.precio, p.imagen, p.disponibilidad, c.nombre AS nombre_categoria
                    FROM productos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.id = ?";
  $stmtProducto = mysqli_prepare($conn, $sqlProducto);
  mysqli_stmt_bind_param($stmtProducto, "i", $productId);
  mysqli_stmt_execute($stmtProducto);
  $resultProducto = mysqli_stmt_get_result($stmtProducto);
  $product = mysqli_fetch_assoc($resultProducto);
  mysqli_stmt_close($stmtProducto);

  // Obtener comentarios del producto
  $sqlComentarios = "SELECT com.id AS id, u.nombre AS usuario, com.texto, com.fecha_creacion, com.usuario_id
                                FROM comentarios com
                                LEFT JOIN usuarios u ON com.usuario_id = u.id
                                WHERE com.producto_id = ? AND com.activo = TRUE
                                ORDER BY com.fecha_creacion DESC";
  $stmtComentarios = mysqli_prepare($conn, $sqlComentarios);
  mysqli_stmt_bind_param($stmtComentarios, "i", $productId);
  mysqli_stmt_execute($stmtComentarios);
  $resultComentarios = mysqli_stmt_get_result($stmtComentarios);
  while ($rowComentario = mysqli_fetch_assoc($resultComentarios)) {
    $comments_data[] = $rowComentario;
  }
  mysqli_stmt_close($stmtComentarios);

  if ($product) {
    $product['disponibilidad_texto'] = $product['disponibilidad'] ? 'En stock' : 'Agotado';
  } else {
    $error_message = 'Producto no encontrado.';
  }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $product['nombre'] ?? 'Producto'; ?></title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/product.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script>
    // Pasar datos de comentarios y usuario logueado al JavaScript
    window.initialComments = <?php echo json_encode($comments_data); ?>;
    window.loggedInUserId = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
  </script>
</head>

<body>
  <header id="header">
    <div class="container">
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Spain by Train">

      <form action="buscar.php" method="GET" class="search-form">
        <div class="input-container">
          <input type="text" name="query" placeholder="Buscar" />
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

      <img src="img/logoKatya.png" class="logoTienda" alt="Spain by Train">

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

  <div id="containProduct" class="container">
    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <p>
          <?php if ($product): ?>
            <?php echo htmlspecialchars($product['nombre']); ?>
          <?php else: ?>
            Producto
          <?php endif; ?>
        </p>
      </div>
    </div>

    <?php if ($product): ?>
      <div id="containerProduct">
        <div class="images">
          <img src="../../img/productos/<?php echo htmlspecialchars($product['imagen']); ?>" class="img-main"
            alt="<?php echo htmlspecialchars($product['nombre']); ?>">
        </div>

        <div class="details">
          <div class="title"><?php echo htmlspecialchars($product['nombre']); ?></div>
          <div class="descripcion">
            <p><strong>Disponibilidad:</strong> <span
                class="stock <?php echo $product['disponibilidad'] ? 'en-stock' : 'agotado'; ?>"><?php echo htmlspecialchars($product['disponibilidad_texto']); ?></span>
            </p>
            <p><strong>Categoría:</strong> <span
                class="category"><?php echo htmlspecialchars($product['nombre_categoria']); ?></span></p>
          </div>

          <div class="price-quantity">
            <div class="price">
              <span class="new-price">S/<?php echo number_format($product['precio'], 2); ?></span>
            </div>

            <div class="quantity">
              <strong>Cantidad:</strong><br>
              <div class="cantidad-container">
                <button class="btnCantidad" onclick="adjustQty(-1)">-</button>
                <input type="number" value="1" id="qty" class="cantidad">
                <button class="btnCantidad" onclick="adjustQty(1)">+</button>
              </div>
            </div>
          </div>

          <div class="btns">
            <button class="btn cart" data-id="<?php echo $product['id']; ?>"
              data-nombre="<?php echo htmlspecialchars($product['nombre']); ?>"
              data-precio="<?php echo $product['precio']; ?>"
              data-imagen="<?php echo htmlspecialchars($product['imagen']); ?>">
              <i class="bi bi-cart"></i> AÑADIR A LA CESTA
            </button>
            <button class="btn wishlist"><i class="bi bi-heart"></i> LISTA DE DESEOS</button>
          </div>
        </div>
      </div>

      <div id="containerComments">
        <div class="tab">
          <button class="btnTab">Reseñas</button>

          <select class="selectTab" id="sort-comments" onchange="sortComments()">
            <option value="recientes">Más recientes</option>
            <option value="antiguos">Más antiguos</option>
          </select>
        </div>

        <div class="reviews-section">

          <div id="comments-container">
          </div>

          <div class="comment-form">
            <textarea id="new-comment" rows="2" placeholder="Escribe tu comentario aquí..."></textarea>
            <button onclick="addComment()" class="btnComentario">Escribir comentario</button>
          </div>

        </div>
      </div>

    <?php else: ?>
      <p class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
      </p>
    <?php endif; ?>

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


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../js/reseñas.js"></script>
  <script>
      
      function getParameterByName(name) {
      name = name.replace(/[\[\]]/g, '\\$&');
      var regex = new RegExp('(?:[?&]|^)' + name + '(=([^&#]*)|&|#|$)'),

      results = regex.exec(window.location.href);

      if (!results) return null;

      if (!results[2]) return '';

      return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

function adjustQty(change) {
        const input = document.getElementById("qty");
        let value = parseInt(input.value) || 1;
        value = Math.max(1, value + change);
        input.value = value;
      }

      const input = document.getElementById("qty");
      input.addEventListener("blur", () => {
        if (input.value === "" || isNaN(input.value)) {
          input.value = 1;
        }
      });


    $(document).ready(function() {
        
         // Actualizar contadores de carrito y lista de deseos al cargar la página
    updateWishlistCounter();
    updateCartCounter();
    
   

      
      // Evento botón AÑADIR A CARRO
      $(document).on('click', '.btn.cart', function(event) {
        event.preventDefault();

        // Ahora los datos del producto se obtienen del botón, que es lo correcto.
        let productId = $(this).data('id');
        let productNombre = $(this).data('nombre');
        let productPrecio = $(this).data('precio');
        let productImagen = $(this).data('imagen');
        let cantidad = document.getElementById('qty').value; // Obtener la cantidad

        if (window.loggedInUserId === 'null') { // Usamos la variable global JavaScript
          window.location.href = '../pagesAutentication/login.php';
          alert('Debes iniciar sesión para añadir productos al carrito.');
        } else {
          $.ajax({
            url: '../../php/addCart.php', // El mismo script que tenías
            type: 'POST',
            data: {
              id: productId,
              nombre: productNombre,
              precio: productPrecio,
              imagen: productImagen,
              cantidad: cantidad // Incluir la cantidad en los datos enviados
            },
            success: function(response) {
              let data = typeof response === 'string' ? JSON.parse(response) : response;
              console.log(data);
              if (data.success) {
                alert(data.message);
                updateCartCounter(); // Esta función DEBE estar definida en algún lugar de tu proyecto,
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
          url: '../../php/getCartCount.php', // Ahora apunta al archivo de contador
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

      // Función para actualizar contador de lista de deseos
      function updateWishlistCounter() {
        $.ajax({
          url: '../../php/getWishlistCount.php',
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
    });
  </script>
  <script src="../../js/navScroll.js"></script>
</body>

</html>