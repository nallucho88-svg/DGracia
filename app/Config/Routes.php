<?php

use CodeIgniter\Router\RouteCollection;

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Rutas Frontend (Catálogo y Cliente)
$routes->get('productos', 'Shop::index');
$routes->get('productos/categoria/(:segment)', 'Shop::category/$1');
$routes->get('producto/(:segment)', 'Shop::product/$1');

$routes->get('nosotros', 'Home::about');
$routes->get('contacto', 'Home::contact');
$routes->post('contacto/enviar', 'Home::sendMessage');

$routes->get('carrito', 'Cart::index');
$routes->get('carrito/mini', 'Cart::getMiniCart');
$routes->post('carrito/agregar', 'Cart::add');
$routes->post('carrito/actualizar', 'Cart::update');
$routes->post('carrito/eliminar', 'Cart::remove');
$routes->post('carrito/aplicar-cupon', 'Cart::applyCoupon');

$routes->get('checkout', 'Checkout::index');
$routes->post('checkout/procesar', 'Checkout::process');
$routes->get('pedido/confirmacion/(:segment)', 'Checkout::success/$1');

// Rutas Autenticación de Clientes (Públicas)
$routes->get('login', 'CustomerAuth::login');
$routes->post('login', 'CustomerAuth::processLogin');
$routes->get('registro', 'CustomerAuth::register');
$routes->post('registro', 'CustomerAuth::processRegister');
$routes->get('logout', 'CustomerAuth::logout');
$routes->get('olvide-contrasena', 'PasswordReset::forgotForm');
$routes->post('olvide-contrasena', 'PasswordReset::sendResetLink');
$routes->get('restablecer-contrasena/(:segment)', 'PasswordReset::resetForm/$1');
$routes->post('restablecer-contrasena', 'PasswordReset::processReset');
$routes->post('favorito/toggle', 'CustomerAccount::toggleFavorite');
$routes->post('valoracion/guardar', 'CustomerAccount::saveRating');

// Rutas Protegidas del Cliente (Filtro customerauth)
$routes->group('mi-cuenta', ['filter' => 'customerauth'], static function ($routes) {
    $routes->get('/', 'CustomerAccount::index');
    $routes->get('perfil', 'CustomerAccount::profile');
    $routes->post('perfil/actualizar', 'CustomerAccount::updateProfile');
    $routes->post('perfil/cambiar-clave', 'CustomerAccount::updatePassword');
    $routes->get('pedidos', 'CustomerAccount::orders');
    $routes->get('pedidos/ver/(:segment)', 'CustomerAccount::orderDetail/$1');
    $routes->get('puntos', 'CustomerAccount::points');
    $routes->get('metas', 'CustomerAccount::goals');
    $routes->get('favoritos', 'CustomerAccount::favorites');
});

// Rutas del Panel Administrativo (Públicas - Sin filtro)
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login/process', 'Auth::processLogin');
    $routes->get('logout', 'Auth::logout');
    $routes->get('olvide-contrasena', 'Auth::forgotForm');
    $routes->post('olvide-contrasena', 'Auth::sendResetLink');
    $routes->get('restablecer-contrasena/(:segment)', 'Auth::resetForm/$1');
    $routes->post('restablecer-contrasena', 'Auth::processReset');
});

// Rutas del Panel Administrativo Protegidas (Con filtro adminauth)
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'adminauth'], static function ($routes) {
    // Dashboard y CRUDs
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Caja / Punto de Venta (POS)
    $routes->get('pos', 'Pos::index');
    $routes->get('pos/search-customers', 'Pos::searchCustomers');
    $routes->post('pos/process-sale', 'Pos::processSale');
    
    // Productos
    $routes->get('productos', 'Products::index');
    $routes->match(['GET', 'POST'], 'productos/crear', 'Products::create');
    $routes->match(['GET', 'POST'], 'productos/editar/(:num)', 'Products::edit/$1');
    $routes->get('productos/eliminar/(:num)', 'Products::delete/$1');

    // Categorías
    $routes->match(['GET', 'POST'], 'categorias', 'Categories::index');
    $routes->match(['GET', 'POST'], 'categorias/delete/(:num)', 'Categories::delete/$1');
    
    // Ofertas y Descuentos
    $routes->get('descuentos', 'Discounts::index');
    $routes->match(['GET', 'POST'], 'descuentos/crear', 'Discounts::create');
    $routes->match(['GET', 'POST'], 'descuentos/editar/(:num)', 'Discounts::edit/$1');
    $routes->get('descuentos/eliminar/(:num)', 'Discounts::delete/$1');

    // Cupones
    $routes->get('cupones', 'Coupons::index');
    $routes->match(['GET', 'POST'], 'cupones/crear', 'Coupons::create');
    $routes->match(['GET', 'POST'], 'cupones/editar/(:num)', 'Coupons::edit/$1');
    $routes->get('cupones/eliminar/(:num)', 'Coupons::delete/$1');

    // Pedidos
    $routes->get('pedidos', 'Orders::index');
    $routes->get('pedidos/ver/(:num)', 'Orders::view/$1');
    $routes->get('pedidos/detalle-json/(:num)', 'Orders::detailJson/$1');
    $routes->post('pedidos/cambiar-estado', 'Orders::changeStatus');

    // Banners
    $routes->match(['GET', 'POST'], 'banners', 'Banners::index');
    $routes->get('banners/eliminar/(:num)', 'Banners::delete/$1');
    
    // Valoraciones / Reseñas
    $routes->get('valoraciones', 'Ratings::index');
    $routes->get('valoraciones/estado/(:num)/(:segment)', 'Ratings::changeStatus/$1/$2');
    $routes->get('valoraciones/eliminar/(:num)', 'Ratings::delete/$1');

    // Notificaciones
    $routes->get('notificaciones', 'Notifications::index');
    $routes->get('notificaciones/marcar-todas', 'Notifications::markAllRead');
    $routes->get('notificaciones/leer/(:num)', 'Notifications::read/$1');

    // Configuración avanzada (SMTP, Backups, Migraciones)
    $routes->match(['GET', 'POST'], 'configuracion', 'Settings::index');
    $routes->post('configuracion/backup', 'Settings::createBackup');
    $routes->post('configuracion/migrar', 'Settings::runMigrations');
    $routes->post('configuracion/probar-smtp', 'Settings::testSmtp');

    // Dashboard interactivo AJAX
    $routes->get('dashboard/filtro-ajax', 'Dashboard::filterData');

    // Perfil del Administrador
    $routes->get('perfil', 'Profile::index');
    $routes->post('perfil/cambiar-clave', 'Profile::changePassword');
    $routes->post('perfil/actualizar', 'Profile::updateInfo');

    // Usuarios (listado base, puntos y eliminación)
    $routes->get('usuarios', 'Users::index');
    $routes->get('usuarios/fiel/(:num)', 'Users::toggleLoyal/$1');
    $routes->post('usuarios/puntos', 'Users::addPoints');
    $routes->post('usuarios/eliminar', 'Users::deleteCustomer');

    // Mensajes de Contacto / PQRS
    $routes->get('contacto', 'Contact::index');
    $routes->get('contacto/marcar/(:num)', 'Contact::toggleRead/$1');
    $routes->get('contacto/eliminar/(:num)', 'Contact::delete/$1');
});
