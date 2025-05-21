<?php

/**
 * Este archivo define las rutas protegidas de la aplicación y los permisos requeridos
 * para acceder a ellas.
 */
$rutasProtegidas = [
    '/pages/pagesAccount/account.php' => 'logeado',
    '/pages/pagesAccount/account-profile-info.php' => 'logeado',
    '/pages/pagesAccount/account-manage-address.php' => 'logeado',
    '/pages/pagesAccount/account-lista-deseos.php' => 'logeado',
    '/pages/pagesAccount/account-my-reviews.php' => 'logeado',
    '/pages/pagesAccount/account-order-history.php' => 'logeado',
    '/pages/pagesAccount/account-rastreo-pedido.php' => 'logeado',
    '/pages/pagesAccount/account-change-password.php' => 'logeado',
    '/pages/pagesAutentication/logout.php' => 'logeado',
    '/pages/pagesPago/cart.php' => 'logeado',
    '/pages/pagesPago/checkout.php' => 'logeado',
    '/pages/pagesPago/success.php' => 'logeado',
];

/**
 * Este array define las rutas que solo deberían ser accesibles por usuarios NO logeados.
 */
$rutasSoloNoLogeados = [
    '/pages/pagesAutentication/login.php',
    '/pages/pagesAutentication/registro.php',
    '/pages/pagesAutentication/forgot-password.html',
];

/**
 * Este array define las rutas que son públicas y accesibles por todos los usuarios.
 */
$rutasPublicas = [
    '/', 
    '/index.php',
    '/pages/pagesProductos/product.php',
    '/pages/pagesProductos/productBuscador.html',
    '/pages/pagesFooter/politica-privacidad.html',
    '/pages/pagesFooter/terminos-condiciones.html',
    '/pages/pagesFooter/preguntas-frecuentes.html', 
];