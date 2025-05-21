<?php

include_once("../../php/conexionBD.php");

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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido Completado</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesPago.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Futura&amp;family=Roboto&amp;family=Helvetica&amp;family=Garamond&amp;family=Caslon&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&amp;display=swap" rel="stylesheet">
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
        <a href="../pagesAccount/account-lista-deseos.php" class="icon">
          <i class="bi bi-heart"></i>
        </a>

        <a href="cart.php" class="icon">
          <i class="bi bi-cart"></i>
        </a>

        <a href="../pagesAccount/account.php" class="icon">
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

  <div id="containCompleted" class="container">

    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <p>Pedido Completado</p>
      </div>
    </div>

    <div id="msgCompleted">
      <img src="../../img/imgPedidoCompletado.png" alt="imgCompleted" class="imgCompleted">
      <h2>
        <span>¡</span>Tu compra se ha completado <span>exitosamente!</span>
      </h2>

      <p>Su pedido se está procesando. ¡Gracias por su compra!</p>

      <button type="button" class="btnCompleted" onclick="location.href='../../index.php'">SEGUIR COMPRANDO</button>
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
  
  <script src="../../js/navScroll.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>

</body>

</html>