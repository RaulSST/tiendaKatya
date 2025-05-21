<?php
// Incluye los archivos necesarios
include_once("../../php/verificarSesion.php"); // Para manejar la sesión y la variable $isLoggedIn
include_once('../../config/routes.php');      // Para las rutas protegidas

// Obtiene la URL actual para la protección de la página
$currentPage = $_SERVER['REQUEST_URI'];

// Redirige al usuario a la página de login si la página actual está protegida y el usuario no está logueado
if (array_key_exists($currentPage, $rutasProtegidas ?? []) && !$isLoggedIn) {
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

// Obtiene el ID del usuario logueado desde la sesión
// Si no hay user_id en la sesión (lo cual no debería pasar si la protección funciona), redirige.
$usuarioId = null;
if (isset($_SESSION['user_id'])) {
  $usuarioId = $_SESSION['user_id'];
} else {
  header("Location: /pages/pagesAutentication/login.php");
  exit();
}

// Consulta SQL para obtener los productos de la lista de deseos del usuario
$sql = "
    SELECT
        p.id AS producto_id,
        p.nombre AS nombre_producto,
        p.precio,
        p.imagen,
        p.disponibilidad -- Para saber si está en stock o agotado
    FROM
        favoritos f
    INNER JOIN
        productos p ON f.producto_id = p.id
    WHERE
        f.usuario_id = ?
    ORDER BY
        p.nombre ASC
";

// Prepara la consulta SQL
$stmt = mysqli_prepare($conn, $sql);

// Verifica si la preparación de la consulta falló
if (!$stmt) {
  echo "Error al preparar la consulta: " . mysqli_error($conn);
  exit;
}

// Vincula el parámetro (ID de usuario) a la consulta
mysqli_stmt_bind_param($stmt, "i", $usuarioId);

// Ejecuta la consulta
mysqli_stmt_execute($stmt);

// Obtiene el resultado de la consulta
$result = mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lista de Deseos - Importaciones Katya</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesAccount.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Futura&family=Roboto&family=Helvetica&family=Garamond&family=Caslon&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            <i class="bi bi-heart"></i><span id="wishlist-count">0</span>
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
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Logo Importaciones Katya">

      <div class="grupo-final">
        <img src="../../img/turespaña.jpg" class="logo-turespaña" alt="Turespaña">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
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
        <a class="active" href="account.php">Mi cuenta</a>
        <i class="bi bi-chevron-right"></i>
        <a href="account-lista-deseos.php">Lista de deseos</a>
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
            <a href="account-order-history.php"><i class="bi bi-bag"></i> <span>Historial de pedidos</span></a>
            <a href="account-my-reviews.php">Mis reseñas</a>
          </div>
          <div>
            <a href="account-lista-deseos.php" class="active"><i class="bi bi-heart"></i> <span>Lista de deseos</span></a>
          </div>
          <div class="cerrarSesion">
            <a href="logout.php"><i class="bi bi-power"></i> <span>Cerrar sesión</span></a>
          </div>
        </nav>
      </aside>

      <main id="main">
        <div class="orders-section">

          <h2>Lista de deseos</h2>

          <?php
          // Verifica si hay resultados de la consulta
          if (isset($result) && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              // Determina el estado de disponibilidad y el color del texto
              $stockStatusColor = ($row['disponibilidad'] == 1) ? '#448c47' : 'rgb(238, 5, 5)';
              $stockStatusText = ($row['disponibilidad'] == 1) ? 'En stock' : 'Agotado';

              // Determina el texto y la clase del botón "Añadir a la cesta"
              $buttonText = ($row['disponibilidad'] == 1) ? 'Añadir a la cesta' : 'Producto no disponible';
              $buttonClass = ($row['disponibilidad'] == 1) ? 'btnPedido addToCartBtn' : 'btnPedido disabled';
              $addToCartBtnAttributes = ($row['disponibilidad'] == 1) ? 'data-product-id="' . htmlspecialchars($row['producto_id']) . '"' : '';
          ?>
              <div class="order-card" id="cardListDeseo">
                <div class="order-info">
                  <div class="order-images">
                    <img src="../../img/productos/<?php echo htmlspecialchars($row['imagen']); ?>"
                      alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>" />
                  </div>

                  <div class="order-details">
                    <div>
                      <p><strong><?php echo htmlspecialchars($row['nombre_producto']); ?></strong></p>
                      <p>S/<?php echo htmlspecialchars(number_format($row['precio'], 2)); ?></p>
                    </div>

                    <div>
                      <p><strong>Disponibilidad</strong></p>
                      <p style="color: <?php echo $stockStatusColor; ?>;"><strong><?php echo htmlspecialchars($stockStatusText); ?></strong></p>
                    </div>

                    <div class="añadirCesta">
                      <button class="<?php echo $buttonClass; ?>" <?php echo $addToCartBtnAttributes; ?>>
                        <?php echo htmlspecialchars($buttonText); ?>
                      </button>

                      <button class="delete-comment delete-from-wishlist" data-product-id="<?php echo htmlspecialchars($row['producto_id']); ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
          <?php
            }
          } else {
            // Mensaje si la lista de deseos está vacía
            echo '<p>Tu lista de deseos está vacía.</p>';
          }
          ?>

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
                    <a href="https://www.facebook.com/people/Importaciones-Katya/100092336872945/" style="text-decoration: none; color: black;"><span class="icn"><i class="bi bi-facebook"></i></span>
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
  <script src="../../js/bootstrap.bundle.js"></script>
  <script>
    $(document).ready(function() {
      // Actualizar contadores de carrito y lista de deseos al cargar la página
      updateWishlistCounter();
      updateCartCounter();

      loadUserInfo();

      // Función para actualizar contador del carrito
      function updateCartCounter() {
        $.ajax({
          url: '../../php/getCartCount.php',
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
              // Si success es false o count no está definido, resetea a 0
              $('#wishlist-count').text(0);
              console.error('Error al obtener el contador de la lista de deseos');
            }
          },
          error: function() {
            console.error("Hubo un error al obtener el contador de la lista de deseos");
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

      // Lógica para añadir producto al carrito desde la lista de deseos
      // Lógica para añadir producto al carrito desde la lista de deseos
      $(document).on('click', '.addToCartBtn', function(event) {
        event.preventDefault();

        const $button = $(this); // El botón "Añadir a la cesta" que fue clickeado
        const $card = $button.closest('.order-card'); // La tarjeta de producto más cercana

        // Obtener los datos del producto de los elementos dentro de la tarjeta
        // Asegúrate de que los selectores CSS (.order-details p:nth-child(1) strong, etc.)
        // apunten correctamente a tus elementos HTML.
        const productId = $button.data('product-id');
        const productNombre = $card.find('.order-details p:nth-child(1) strong').text().trim();
        // Elimina "S/" y convierte el precio a un formato numérico
        const productPrecio = parseFloat($card.find('.order-details p:nth-child(2)').text().replace('S/', '').replace(',', '.').trim());
        // Obtiene la parte final de la URL de la imagen para el nombre del archivo
        const productImagenFull = $card.find('.order-images img').attr('src');
        const productImagen = productImagenFull ? productImagenFull.split('/').pop() : '';

        // Asumimos una cantidad de 1 al añadir desde la lista de deseos
        const cantidad = 1;

        // Validación básica de los datos
        if (!productId || !productNombre || isNaN(productPrecio) || productPrecio <= 0 || !productImagen) {
          alert('Error: No se pudieron obtener todos los datos del producto de la tarjeta.');
          console.error('Datos faltantes o inválidos:', {
            productId,
            productNombre,
            productPrecio,
            productImagen,
            cantidad
          });
          return;
        }

        $.ajax({
          url: '../../php/addCart.php',
          type: 'POST',
          data: {
            id: productId, // Ahora enviamos 'id' en lugar de 'product_id' para que coincida con el PHP
            nombre: productNombre,
            precio: productPrecio,
            imagen: productImagen,
            cantidad: cantidad
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              alert(response.message);
              updateCartCounter(); // Actualiza el contador del carrito
            } else {
              alert(response.message || 'Error al añadir el producto al carrito.');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error AJAX:", textStatus, errorThrown, jqXHR.responseText);
            alert('Error de comunicación con el servidor al añadir al carrito.');
          }
        });
      });



      // Lógica para eliminar producto de la lista de deseos
      $(document).on('click', '.delete-from-wishlist', function(event) {
        event.preventDefault();
        const productId = $(this).data('product-id');
        const $cardToRemove = $(this).closest('.order-card'); // Elemento a eliminar del DOM

        if (!productId) {
          alert('Error: ID de producto no encontrado para eliminar.');
          return;
        }


        // La eliminación ahora será directa al hacer clic.

        $.ajax({
          url: '../../php/removeWishlist.php',
          type: 'POST',
          data: {
            product_id: productId
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              alert(response.message);
              $cardToRemove.remove(); // Elimina la tarjeta del DOM
              updateWishlistCounter(); // Actualiza el contador de la lista de deseos
            } else {
              alert(response.message || 'Error al eliminar el producto de la lista de deseos.');
            }
          },
          error: function() {
            alert('Error de comunicación con el servidor al eliminar de la lista de deseos.');
          }
        });
      });

    });
  </script>
</body>

</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>