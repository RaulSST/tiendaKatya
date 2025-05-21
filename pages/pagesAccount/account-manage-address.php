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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrar dirección</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesAccount.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Futura&amp;family=Roboto&amp;family=Helvetica&amp;family=Garamond&amp;family=Caslon&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
</head>

<body>

  <header id="header">
    <div class="container">
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Spain by Train">

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

    <!-- Navbar para computadoras -->
    <nav id="navDesktop" class="container">
      <ul class="navbar-nav">
        <li class="inicio"><a href="../../index.php?category_id=0">Inicio</a></li>
        <?php foreach ($categories as $category): ?>
          <li><a href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <!-- Navbar para móvil -->
    <nav class="navbar navbar-mobile" id="navMobile">

      <img src="img/logoKatya.png" class="logoTienda" alt="Spain by Train">

      <div class="grupo-final">
        <img src="img/turespaña.jpg" class="logo-turespaña" alt="Turespaña">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
          aria-controls="offcanvasMenu">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Offcanvas Menu -->
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
        <a href="account-manage-address.php">Administrar dirección</a>
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
            <a href="account-manage-address.php" class="active">Administrar dirección</a>
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
        <div class="profileInfo">
          <h3>Administrar dirección</h3>

          <form id="profileInfo-Form" action="" method="POST">
            <div class="form-row">
              <div class="form-field">
                <label>Departamento <span style="color:#fd3d57;">*</span></label>
                <select id="departamento" name="departamento">
                  <option value="">Seleccione un departamento</option>
                  <option value="Lima">Lima</option>
                  <option value="Callao">Callao</option>
                </select>
              </div>

              <div class="form-field">
                <label>Distrito <span style="color:#fd3d57;">*</span></label>
                <select id="distrito" name="distrito">
                  <option value="">Seleccione un distrito</option>
                </select>
              </div>

              <div class="form-field">
                <label for="codigoPostal">Código Postal <span style="color:#fd3d57;">*</span></label>
                <input type="text" id="codigoPostal" name="codigoPostal" class="inputAccount" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-field" style="flex: 2;">
                <label for="direccion">Dirección de Domicilio <span style="color:#fd3d57;">*</span></label>
                <input type="text" id="direccion" name="direccion" class="inputAccount" required>
              </div>

              <div class="form-field">
                <label for="pais">Pais <span style="color:#fd3d57;">*</span></label>
                <input type="text" id="pais" name="pais" class="inputAccount" value="Perú" required>
              </div>
            </div>

            <button type="submit" class="btnAccount">GUARDAR CAMBIOS</button>
          </form>

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
  <script src="../../js/selectDepartamDistrito.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
        updateWishlistCounter();
      updateCartCounter();

      loadUserInfo();

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
        
      $('#profileInfo-Form').submit(function (event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        $.ajax({
          url: '../../php/updateAddressInfo.php',
          method: 'POST',
          dataType: 'json',
          data: $(this).serialize(), // Serializar los datos del formulario
          success: function (response) {
            if (response.success) {
              alert(response.message); // Mostrar mensaje de éxito
              window.location.href = 'account.php'; 
            } else {
              alert(response.message); // Mostrar mensaje de error
            }
          },
          error: function () {
            alert('Hubo un error al guardar la dirección.');
          }
        });
      });
      
    });
  </script>
</body>

</html>