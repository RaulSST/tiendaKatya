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


mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi Cuenta - Importaciones Katya</title>
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
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Importaciones Katya">
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
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Importaciones Katya">
      <div class="grupo-final">
        <img src="../../img/turespana.jpg" class="logo-turespana" alt="Turespaña">
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
              <a class="nav-link active" href="../../index.php?category_id=0">Inicio</a>
            </li>
            <?php foreach ($categories as $category): ?>
              <li class="nav-item">
                <a class="nav-link" href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a>
              </li>
            <?php endforeach; ?>
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
            <a href="account.php" class="active"><i class="bi bi-person-gear"></i> <span>Administrar cuenta</span></a>
            <a href="account-profile-info.php">Información del perfil</a>
            <a href="account-manage-address.php">Administrar dirección</a>
            <a href="account-change-password.php">Cambiar contraseña</a>
          </div>
          <div>
            <a href="account-order-history.php"><i class="bi bi-bag"></i> <span>Historial de pedidos</span></a>
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
        <div class="profile-section">
          <div class="card">
            <a href="account-profile-info.php"><span class="edit">Editar</span></a>
            <h3>Perfil personal</h3>
            <p id="profileName"></p>
            <p id="profileEmail"></p>
            <p id="profilePhone"></p>
          </div>
          <div class="card">
            <a href="account-manage-address.php"><span class="edit">Editar</span></a>
            <h3>Dirección de envío</h3>
            <p id="shippingAddress"></p>
            <p id="shippingLocation"></p>
          </div>
        </div>
        <h3 style="margin-bottom: 20px;">Pedidos Recientes</h3>
        <div class="orders-section" id="ordersContainer">

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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>
  <script>
    $(document).ready(function() {

      // Actualizar contadores de carrito y lista de deseos al cargar la página
      updateWishlistCounter();
      updateCartCounter();

      // Cargar los pedidos del usuario al cargar la página
      loadUserOrders();

      // Función para cargar la información del usuario
      loadUserInfo();

      function formatDate(dateString) {
        const options = {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        };
        return new Date(dateString).toLocaleDateString('es-PE', options);
      }

      // Función para cargar los pedidos del usuario
      function loadUserOrders() {
        $.ajax({
          url: '../../php/getOrders.php',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.orders) {
              const ordersContainer = $('#ordersContainer');
              ordersContainer.empty();

              const groupedOrders = {};
              response.orders.forEach(function(item) {
                if (!groupedOrders[item.orden_id]) {
                  groupedOrders[item.orden_id] = {
                    orden_id: item.orden_id,
                    fecha: item.fecha,
                    estado: item.estado,
                    total: item.total,
                    productos: []
                  };
                }
                groupedOrders[item.orden_id].productos.push({
                  producto_id: item.producto_id,
                  cantidad: item.producto_cantidad,
                  precio: item.producto_precio,
                  imagen_url: item.imagen_url, // Todavía obtenemos el nombre del archivo de imagen
                  categoria_id: item.categoria_id // Obtenemos el ID de la categoría
                });
              });

              for (const ordenId in groupedOrders) {
                const orden = groupedOrders[ordenId];
                orden.productos.forEach(function(producto) {
                  const orderCard = $('<div class="order-card"></div>');

                  const orderInfoSuperior = $('<div class="order-info-superior"></div>');
                  // Construimos la ruta de la imagen usando la categoría y el nombre del archivo
                  const imageUrl = `../../img/productos/${producto.imagen_url}`;
                  const orderImages = $('<div class="order-images"><img src="' + imageUrl + '" /></div>');
                  const statusRastreo = $('<div class="status-rastreo"></div>');
                  const orderStatus = $('<div class="order-status"><p class="status">Estado</p></div>');
                  let estadoClase = '';
                  let estadoTexto = '';

                  switch (orden.estado) {
                    case 'En proceso':
                      estadoClase = 'proceso';
                      estadoTexto = '<i class="bi bi-hourglass-split me-1"></i> En proceso';
                      break;
                    case 'En camino':
                      estadoClase = 'camino';
                      estadoTexto = '<i class="bi bi-truck me-1"></i> En camino';
                      break;
                    case 'Entregado':
                      estadoClase = 'entregado';
                      estadoTexto = '<i class="bi bi-check-circle me-1"></i> Entregado';
                      break;
                    default:
                      estadoTexto = orden.estado;
                      break;
                  }
                  orderStatus.append(`<p class="status ${estadoClase}">${estadoTexto}</p>`);
                  statusRastreo.append(orderStatus, `<div class="divBtnVerPedido" data-orden-id="${orden.orden_id}" data-producto-id="${producto.producto_id}"><button class="btnPedido">Rastrear Pedido</button></div>`);

                  orderInfoSuperior.append(orderImages, statusRastreo);

                  const orderInfoInferior = $('<div class="order-info-inferior"></div>');
                  orderInfoInferior.append(`<div><p><strong>Número de orden:</strong></p><p>${orden.orden_id}</p></div>`);
                  orderInfoInferior.append(`<div><p><strong>Comprado:</strong></p><p>${formatDate(orden.fecha)}</p></div>`);
                  orderInfoInferior.append(`<div><p><strong>Cantidad:</strong></p><p>x${producto.cantidad}</p></div>`);
                  orderInfoInferior.append(`<div><p><strong>Precio:</strong></p><p>S/${producto.precio}</p></div>`);

                  orderCard.append(orderInfoSuperior, orderInfoInferior);
                  ordersContainer.append(orderCard);
                });
              }
            } else {
              $('#ordersContainer').html('<p>No se encontraron pedidos recientes.</p>');
              console.error('Error al cargar los pedidos:', response.message);
            }
          },
          error: function() {
            $('#ordersContainer').html('<p>Hubo un error al cargar los pedidos.</p>');
            console.error("Error en la petición AJAX para obtener los pedidos.");
          }
        });
      }


      // Función para cargar la información del usuario
      function loadUserInfo() {
        $.ajax({
          url: '../../php/getUserInfo.php',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            if (response.success && response.userData) {
              const userData = response.userData;
              $('#userName').text(userData.nombre);
              $('#profileName').text(userData.nombre + (userData.apellidos ? ' ' + userData.apellidos : ''));
              $('#profileEmail').text(userData.email || 'Dato no introducido');
              $('#profilePhone').text('+51 ' + (userData.telefono || 'Dato no introducido'));

              $('#shippingAddress').text(userData.direccion || 'Dato no introducido');

              const locationParts = [];
              if (userData.departamento) locationParts.push(userData.departamento);
              if (userData.distrito) locationParts.push(userData.distrito);
              if (userData.codigoPostal) locationParts.push(userData.codigoPostal);

              $('#shippingLocation').text(locationParts.length > 0 ? locationParts.join(', ') : 'Dato no introducido');

              const countryParagraph = $('<p>Perú</p>');
              $('#shippingLocation').after(countryParagraph);

            } else {
              console.error('Error al cargar la información del usuario:', response.message);
              $('#userName').text('Usuario');
              $('#profileName').text('Información no disponible');
              $('#profileEmail').text('Información no disponible');
              $('#profilePhone').text('Información no disponible');
              $('#shippingAddress').text('Información no disponible');
              $('#shippingLocation').text('Dato no introducido');
              const countryParagraph = $('<p>Perú</p>');
              $('#shippingLocation').after(countryParagraph);
            }
          },
          error: function() {
            console.error('Error en la petición AJAX para obtener la información del usuario.');
            $('#userName').text('Usuario');
            $('#profileName').text('Error al cargar');
            $('#profileEmail').text('Error al cargar');
            $('#profilePhone').text('Error al cargar');
            $('#shippingAddress').text('Error al cargar');
            $('#shippingLocation').text('Error al cargar');
            const countryParagraph = $('<p>Perú</p>');
            $('#shippingLocation').after(countryParagraph);
          }
        });
      }

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

      $(document).on('click', '.divBtnVerPedido', function() {
        const orderId = $(this).data('orden-id');
        const productId = $(this).data('producto-id');
        if (orderId && productId) {
          window.location.href = `account-rastreo-pedido.php?pedido_id=${orderId}&producto_id=${productId}`;
        } else {
          alert('Error: ID de pedido o producto no encontrado para rastrear.');
        }
      });

    });
  </script>
  <script src="../../js/navScroll.js"></script>
</body>

</html>
