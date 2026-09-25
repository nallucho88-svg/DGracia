<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    
    <!-- Encabezado de Saludo -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, var(--dg-primary), #b88a5e); color: white;">
        <div class="card-body p-4 p-md-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold mb-2">PANEL DE CLIENTE</span>
                <h1 class="h2 fw-bold mb-1" style="font-family: var(--font-heading);">
                    Hola, <?= esc(session()->get('name') ?? 'Cliente') ?> 👋
                </h1>
                <p class="mb-0 opacity-75">Bienvenido a tu espacio personal en Dulce Gracia</p>
            </div>
            <div>
                <a href="<?= base_url('productos') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                    <i class="fas fa-store me-2" style="color: var(--dg-primary);"></i>Ir a la Tienda
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Menú Lateral del Cliente -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 90px; z-index: 10;">
                <div class="card-body p-3">
                    <div class="nav flex-column nav-pills custom-customer-nav">
                        <a href="<?= base_url('mi-cuenta') ?>" class="nav-link rounded-3 mb-1 <?= ($activeTab ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-home me-2"></i>Inicio
                        </a>
                        <a href="<?= base_url('mi-cuenta/pedidos') ?>" class="nav-link rounded-3 mb-1 <?= ($activeTab ?? '') === 'orders' ? 'active' : '' ?>">
                            <i class="fas fa-shopping-bag me-2"></i>Mis Pedidos
                        </a>
                        <a href="<?= base_url('mi-cuenta/puntos') ?>" class="nav-link rounded-3 mb-1 <?= ($activeTab ?? '') === 'points' ? 'active' : '' ?>">
                            <i class="fas fa-wallet me-2"></i>Mis Puntos
                        </a>
                        <a href="<?= base_url('mi-cuenta/metas') ?>" class="nav-link rounded-3 mb-1 <?= ($activeTab ?? '') === 'goals' ? 'active' : '' ?>">
                            <i class="fas fa-bullseye me-2"></i>Metas & Niveles
                        </a>
                        <a href="<?= base_url('mi-cuenta/favoritos') ?>" class="nav-link rounded-3 mb-1 <?= ($activeTab ?? '') === 'favorites' ? 'active' : '' ?>">
                            <i class="fas fa-heart me-2"></i>Mis Favoritos
                        </a>
                        <a href="<?= base_url('mi-cuenta/perfil') ?>" class="nav-link rounded-3 mb-1 <?= ($activeTab ?? '') === 'profile' ? 'active' : '' ?>">
                            <i class="fas fa-user-cog me-2"></i>Mi Perfil
                        </a>
                        <hr class="my-2 text-muted">
                        <a href="<?= base_url('logout') ?>" class="nav-link rounded-3 text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Específico del Panel -->
        <div class="col-lg-9">
            <?= $this->renderSection('customer_content') ?>
        </div>
    </div>

</div>

<style>
.custom-customer-nav .nav-link {
    color: #495057;
    font-weight: 500;
    padding: 12px 16px;
    transition: all 0.2s ease-in-out;
}
.custom-customer-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: var(--dg-primary);
}
.custom-customer-nav .nav-link.active {
    background-color: var(--dg-primary);
    color: white !important;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3);
}
</style>
<?= $this->endSection() ?>
