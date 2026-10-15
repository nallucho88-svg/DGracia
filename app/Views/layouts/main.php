<?php
$siteName = setting('site_name', 'Dulce Gracia');
$siteDesc = setting('site_description', 'Endulzamos tus momentos especiales.');
$siteFavicon = setting('site_favicon', setting('favicon_path', ''));
$siteLogo    = setting('site_logo', setting('store_logo', ''));

// Build $settings array for footer social/contact links
$settingModel = new \App\Models\SettingModel();
$rawSettings = $settingModel->findAll();
$settings = [];
foreach ($rawSettings as $rs) {
    $settings[$rs->key] = $rs->value;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="description" content="<?= esc($siteDesc) ?>">
    <title><?= isset($title) ? esc($title) : esc($siteName) ?></title>
    <?= $this->renderSection('og_meta') ?>
    <?php if(!empty($siteFavicon)): 
        $favUrl = (strpos($siteFavicon, 'http://') === 0 || strpos($siteFavicon, 'https://') === 0) ? $siteFavicon : base_url($siteFavicon);
    ?>
    <link rel="icon" type="image/x-icon" href="<?= esc($favUrl) ?>?v=<?= time() ?>">
    <?php endif; ?>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url() ?>">
                <?php if (!empty($siteLogo)): 
                    $logoUrl = (strpos($siteLogo, 'http://') === 0 || strpos($siteLogo, 'https://') === 0) ? $siteLogo : base_url($siteLogo);
                ?>
                    <img src="<?= esc($logoUrl) ?>" alt="<?= esc($siteName) ?>" class="img-fluid" style="max-height: 48px; object-fit: contain;">
                <?php else: ?>
                    <?= esc($siteName) ?>
                <?php endif; ?>
            </a>
            
            <!-- Botón hamburguesa móvil -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars text-primary" style="color: var(--dg-primary) !important; font-size: 1.5rem;"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('productos') ?>">Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('nosotros') ?>">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('contacto') ?>">Contacto</a></li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Ícono de Carrito con Mini-Carrito Dropdown -->
                    <div class="dropdown position-relative" id="miniCartWrapper">
                        <a href="<?= base_url('carrito') ?>" class="text-decoration-none position-relative d-inline-block p-1" style="color: var(--dg-primary);" id="miniCartTrigger" title="Mi Carrito">
                            <i class="fas fa-shopping-cart fs-4"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-counter">
                                0
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-3 mini-cart-dropdown" aria-labelledby="miniCartTrigger" style="width: 320px; max-width: 90vw;">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <h6 class="fw-bold mb-0" style="color: var(--dg-primary);"><i class="fas fa-shopping-bag me-2"></i>MI CARRITO</h6>
                                <small class="text-muted" id="mini-cart-count-label">0 productos</small>
                            </div>
                            <div id="mini-cart-items" style="max-height: 250px; overflow-y: auto;">
                                <div class="text-center text-muted py-3">Tu carrito está vacío</div>
                            </div>
                            <div class="border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold">TOTAL:</span>
                                    <span class="fw-bold text-success fs-5" id="mini-cart-total">$0</span>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('carrito') ?>" class="btn btn-outline-primary btn-sm rounded-pill"><i class="fas fa-shopping-cart me-1"></i> VER CARRITO</a>
                                    <a href="<?= base_url('checkout') ?>" class="btn btn-dg-primary btn-sm rounded-pill"><i class="fas fa-bolt me-1"></i> COMPRAR</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usuario / Mi Cuenta -->
                    <?php if (session()->get('isLoggedIn') && session()->get('role') === 'customer'): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle px-3 py-2 fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1" style="color: var(--dg-primary);"></i>
                                <?= esc(session('name')) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                <li><a class="dropdown-item rounded-3" href="<?= base_url('mi-cuenta') ?>"><i class="fas fa-home me-2 text-muted"></i> Mi Cuenta</a></li>
                                <li><a class="dropdown-item rounded-3" href="<?= base_url('mi-cuenta/pedidos') ?>"><i class="fas fa-shopping-bag me-2 text-muted"></i> Mis Pedidos</a></li>
                                <li><a class="dropdown-item rounded-3" href="<?= base_url('mi-cuenta/puntos') ?>"><i class="fas fa-wallet me-2 text-warning"></i> Mis Puntos</a></li>
                                <li><a class="dropdown-item rounded-3" href="<?= base_url('mi-cuenta/favoritos') ?>"><i class="fas fa-heart me-2 text-danger"></i> Favoritos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item rounded-3 text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-sm btn-dg-primary rounded-pill px-3 py-2 fw-bold">
                            <i class="fas fa-user me-1"></i> Ingresar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer-dg">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <?php if (!empty($siteLogo)): 
                        $footerLogoUrl = (strpos($siteLogo, 'http://') === 0 || strpos($siteLogo, 'https://') === 0) ? $siteLogo : base_url($siteLogo);
                    ?>
                        <a href="<?= base_url() ?>" class="d-inline-block mb-3">
                            <img src="<?= esc($footerLogoUrl) ?>" alt="<?= esc($siteName) ?>" class="img-fluid" style="max-height: 55px; object-fit: contain;">
                        </a>
                    <?php else: ?>
                        <h3 style="font-family: var(--font-heading); color: var(--dg-white); font-weight: 700;"><?= esc($siteName) ?></h3>
                    <?php endif; ?>
                    <p><?= esc($siteDesc) ?></p>
                    <div class="d-flex gap-3 mt-3">
                        <?php if(!empty($settings['social_facebook'])): ?>
                            <a href="<?= esc($settings['social_facebook']) ?>" target="_blank" class="fs-4"><i class="fab fa-facebook"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($settings['social_instagram'])): ?>
                            <a href="<?= esc($settings['social_instagram']) ?>" target="_blank" class="fs-4"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($settings['contact_whatsapp'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_whatsapp']) ?>" target="_blank" class="fs-4"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="<?= base_url() ?>">Inicio</a></li>
                        <li><a href="<?= base_url('productos') ?>">Productos</a></li>
                        <li><a href="<?= base_url('nosotros') ?>">Nosotros</a></li>
                        <li><a href="<?= base_url('contacto') ?>">Contacto / PQRS</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-12">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><i class="fas fa-map-marker-alt me-2"></i> <?= esc(setting('contact_address', 'Medellín, Colombia')) ?></li>
                        <?php if(!empty($settings['contact_phone'])): ?>
                            <li><i class="fas fa-phone me-2"></i> <?= esc($settings['contact_phone']) ?></li>
                        <?php endif; ?>
                        
                        <?php if(!empty($settings['contact_email'])): ?>
                            <li><i class="fas fa-envelope me-2"></i> <?= esc($settings['contact_email']) ?></li>
                        <?php endif; ?>
                        
                        <li class="mt-2"><strong>Horarios:</strong><br> Lunes a sábado: 8:00 AM - 7:00 PM<br>Domingos: 9:00 AM - 3:00 PM</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 border-light opacity-25">
            <div class="text-center">
                <small>&copy; <?= date('Y') ?> <?= esc($siteName) ?>. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Botón Flotante WhatsApp -->
    <?php if(!empty($settings['contact_whatsapp'])): ?>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_whatsapp']) ?>?text=Hola,%20soy%20cliente%20de%20Dulce%20Gracia.%20%F0%9F%98%8A%0ASe%20me%20antoj%C3%B3%20algo%20y%20quisiera%20realizar%20un%20pedido." 
       class="whatsapp-float" target="_blank" title="¿Se te antojó algo?">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <!-- Toastify -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- DG Notifications & Audio -->
    <script src="<?= base_url('assets/js/dg-notifications.js') ?>"></script>
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
