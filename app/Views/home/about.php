<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="py-5 mt-3">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary); font-size: 3rem;">Nuestra Historia</h1>
            <p class="lead text-muted">Endulzamos tus momentos más especiales con amor, dedicación y recetas tradicionales.</p>
        </div>
        
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?q=80&w=900&auto=format&fit=crop" 
                     class="img-fluid rounded-4 shadow-lg" alt="Nuestra Pastelería">
            </div>
            <div class="col-lg-6">
                <h2 style="font-family: var(--font-heading); color: var(--dg-primary);">¿Quiénes somos?</h2>
                <p class="text-muted lh-lg">Somos una pastelería artesanal con más de 10 años de experiencia creando postres únicos. Cada producto que sale de nuestras manos está hecho con ingredientes frescos y mucho amor.</p>
                <p class="text-muted lh-lg">Desde nuestras tortas personalizadas para bodas y cumpleaños, hasta nuestros deliciosos postres del día, todo en <strong>Dulce Gracia</strong> está pensado para hacerte sonreír.</p>
                <div class="d-flex gap-4 mt-4 flex-wrap">
                    <div class="text-center bg-white border border-light-subtle p-3 rounded-4 shadow-sm px-4">
                        <h3 class="fw-bold mb-0 text-success"><i class="fas fa-check-circle me-1"></i><?= number_format($deliveredOrdersCount, 0, ',', '.') ?></h3>
                        <small class="text-muted fw-semibold">Pedidos Entregados</small>
                    </div>
                    <div class="text-center bg-white border border-light-subtle p-3 rounded-4 shadow-sm px-4">
                        <h3 class="fw-bold mb-0" style="color: var(--dg-primary);"><i class="fas fa-heart me-1"></i><?= number_format($totalCustomersCount, 0, ',', '.') ?></h3>
                        <small class="text-muted fw-semibold">Clientes Felices</small>
                    </div>
                    <div class="text-center bg-white border border-light-subtle p-3 rounded-4 shadow-sm px-4">
                        <h3 class="fw-bold mb-0 text-warning"><i class="fas fa-cookie-bite me-1"></i><?= number_format($activeProductsCount, 0, ',', '.') ?></h3>
                        <small class="text-muted fw-semibold">Variedades Dulces</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valores -->
        <div class="row g-4 text-center mt-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <i class="fas fa-heart fa-3x mb-3" style="color: var(--dg-primary);"></i>
                    <h5 class="fw-bold">Hecho con Amor</h5>
                    <p class="text-muted small">Cada postre es preparado con cariño y atención al detalle.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <i class="fas fa-leaf fa-3x mb-3" style="color: var(--dg-primary);"></i>
                    <h5 class="fw-bold">Ingredientes Frescos</h5>
                    <p class="text-muted small">Usamos solo los mejores ingredientes, frescos y naturales.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <i class="fas fa-star fa-3x mb-3" style="color: var(--dg-primary);"></i>
                    <h5 class="fw-bold">Calidad Garantizada</h5>
                    <p class="text-muted small">Tu satisfacción es nuestra mayor recompensa.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
