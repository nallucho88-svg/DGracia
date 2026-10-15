<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'Admin | Dulce Gracia') ?></title>
    <?php 
    $siteFavicon = setting('site_favicon', setting('favicon_path', ''));
    if(!empty($siteFavicon)): 
        $favUrl = (strpos($siteFavicon, 'http://') === 0 || strpos($siteFavicon, 'https://') === 0) ? $siteFavicon : base_url($siteFavicon);
    ?>
    <link rel="icon" type="image/x-icon" href="<?= esc($favUrl) ?>?v=<?= time() ?>">
    <?php endif; ?>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <style>
        :root {
            --dg-primary: #D4A373;
            --dg-primary-dark: #b88a5e;
            --dg-accent: #E9EDC9;
            --dg-bg: #f4f6f9; /* Soft background for admin */
            --dg-sidebar: #343a40;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--dg-bg);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            background-color: var(--dg-sidebar);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 20px;
            font-family: var(--font-heading);
            font-size: 1.5rem;
            color: var(--dg-primary);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: block;
            text-decoration: none;
        }

        .sidebar-nav {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-nav li {
            margin-bottom: 5px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #c2c7d0;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar-nav a i {
            width: 30px;
            text-align: center;
            margin-right: 10px;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: white;
            background-color: rgba(255,255,255,0.05);
            border-left: 4px solid var(--dg-primary);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Topbar */
        .topbar {
            background-color: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .btn-dg-primary {
            background-color: var(--dg-primary);
            color: white;
            border: none;
        }
        .btn-dg-primary:hover {
            background-color: var(--dg-primary-dark);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-brand">
            <i class="fas fa-cookie-bite me-2"></i>Admin DG
        </a>
        <ul class="sidebar-nav">
            <li>
                <a href="<?= base_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') || url_is('admin') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/pos') ?>" class="<?= url_is('admin/pos*') ? 'active' : '' ?>" style="color: #ffc107;">
                    <i class="fas fa-cash-register"></i> Ventas / Caja (POS)
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/pedidos') ?>" class="<?= url_is('admin/pedidos*') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-bag"></i> Pedidos
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/productos') ?>" class="<?= url_is('admin/productos*') ? 'active' : '' ?>">
                    <i class="fas fa-box-open"></i> Productos
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/categorias') ?>" class="<?= url_is('admin/categorias*') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Categorías
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/banners') ?>" class="<?= url_is('admin/banners*') ? 'active' : '' ?>">
                    <i class="fas fa-images"></i> Banners
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/descuentos') ?>" class="<?= url_is('admin/descuentos*') ? 'active' : '' ?>">
                    <i class="fas fa-percentage"></i> Ofertas
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/cupones') ?>" class="<?= url_is('admin/cupones*') ? 'active' : '' ?>">
                    <i class="fas fa-ticket-alt"></i> Cupones
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/valoraciones') ?>" class="<?= url_is('admin/valoraciones*') ? 'active' : '' ?>">
                    <i class="fas fa-star"></i> Valoraciones
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/usuarios') ?>" class="<?= url_is('admin/usuarios*') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/contacto') ?>" class="<?= url_is('admin/contacto*') ? 'active' : '' ?>">
                    <i class="fas fa-envelope-open-text"></i> Mensajes / PQRS
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/configuracion') ?>" class="<?= url_is('admin/configuracion*') ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i> Configuración
                </a>
            </li>
            <li class="mt-3" style="border-top:1px solid rgba(255,255,255,.08); padding-top:10px;">
                <a href="<?= base_url('admin/perfil') ?>" class="<?= url_is('admin/perfil*') ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i> Perfil / Clave
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <button class="btn btn-light d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="ms-auto d-flex align-items-center gap-2">
                <!-- Dropdown Notificaciones 🔔 -->
                <div class="dropdown" id="adminNotifDropdown">
                    <button class="btn btn-light rounded-circle position-relative border-0 shadow-sm" type="button" id="notifBellBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Notificaciones">
                        <i class="fas fa-bell text-secondary fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadgeCount">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-2 rounded-4 overflow-hidden" style="width: 340px; max-height: 440px;" aria-labelledby="notifBellBtn">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-bell me-2 text-primary"></i>Notificaciones</h6>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary" id="btnMarkAllRead">Marcar leídas</button>
                        </div>
                        <div id="notifListContainer" class="list-group list-group-flush overflow-y-auto" style="max-height: 350px;">
                            <div class="p-4 text-center text-muted small">Cargando notificaciones...</div>
                        </div>
                    </div>
                </div>

                <!-- Perfil de usuario -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle rounded-pill border-0 px-3" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-2 text-muted"></i> 
                        <span class="fw-bold"><?= esc(session()->get('name') ?? 'Admin') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?= base_url() ?>" target="_blank"><i class="fas fa-external-link-alt me-2 text-muted"></i> Ver Tienda</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/perfil') ?>"><i class="fas fa-user-shield me-2 text-muted"></i> Perfil / Cambiar Clave</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('admin/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4">
            
            <!-- Global Alerts -->
            <?php if(session()->has('success')): ?>
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show rounded-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session()->has('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show rounded-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?= base_url('assets/js/dg-notifications.js') ?>"></script>
    
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
