<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    
    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2">
        <h2 style="font-family: var(--font-heading); color: var(--dg-primary);">Catálogo de Productos</h2>
        
        <!-- Categorías Desplegable -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-4" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-filter me-2"></i> Filtrar Categorías
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="categoryDropdown">
                <li><a class="dropdown-item" href="<?= base_url('productos') ?>">Todas</a></li>
                <?php foreach($categories as $cat): ?>
                <li><a class="dropdown-item" href="<?= base_url('productos/categoria/'.$cat->slug) ?>"><?= esc($cat->name) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <?php $productCount = 0; ?>
        <?php foreach($products as $p): ?>
            
            <?php 
                // Insertar banner intermedio cada 6 productos
                if($productCount > 0 && $productCount % 6 == 0): 
            ?>
            <div class="col-12 my-4">
                <div class="card bg-dark text-white border-0 rounded-4 overflow-hidden shadow-sm" style="height: 200px;">
                    <img src="https://images.unsplash.com/photo-1514056052883-d017fddd0426?q=80&w=1200&auto=format&fit=crop" class="card-img h-100" style="object-fit: cover; opacity: 0.6;" alt="Banner promocional">
                    <div class="card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">
                        <h3 class="card-title" style="font-family: var(--font-heading);">El sabor de la felicidad</h3>
                        <p class="card-text">Pide con anticipación para tus eventos especiales.</p>
                        <a href="<?= base_url('contacto') ?>" class="btn btn-dg-accent px-4 rounded-pill">Contáctanos</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tarjeta de Producto -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card card-dg h-100 position-relative">
                    <button type="button" 
                            class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm btn-toggle-favorite <?= (isset($userFavorites) && in_array((int)$p->id, $userFavorites)) ? 'is-favorite text-danger' : 'text-muted' ?>"
                            data-product-id="<?= $p->id ?>"
                            style="z-index: 3; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"
                            title="Guardar en favoritos">
                        <?= (isset($userFavorites) && in_array((int)$p->id, $userFavorites)) ? '♥' : '♡' ?>
                    </button>

                    <?php if(isset($p->discount_info) && $p->discount_info['has_discount']): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1 fw-bold shadow-sm" style="z-index: 2;">
                            <?= $p->discount_info['discount_percentage'] ?>% OFF
                        </span>
                    <?php endif; ?>
                    <a href="<?= base_url('producto/'.$p->slug) ?>">
                        <img src="<?= $p->main_image ?>" class="card-img-top" alt="<?= esc($p->name) ?>" style="height: 220px; object-fit: cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-light text-dark mb-2 align-self-start" style="border: 1px solid #ddd;"><?= esc($p->category_name) ?></span>
                        <h5 class="card-title fw-bold" style="color: var(--dg-text-dark);"><?= esc($p->name) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= esc($p->short_description) ?></p>
                        
                        <?php if(isset($p->discount_info) && $p->discount_info['has_discount']): ?>
                            <div class="mb-3">
                                <span class="text-muted text-decoration-line-through me-2"><?= $p->discount_info['formatted_original'] ?></span>
                                <span class="fw-bold text-danger fs-5"><?= $p->discount_info['formatted_final'] ?></span>
                            </div>
                        <?php else: ?>
                            <h5 class="mb-3" style="color: var(--dg-primary);">$<?= number_format($p->price, 0, ',', '.') ?></h5>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-dg-primary btn-add-cart" data-id="<?= $p->id ?>">
                                <i class="fas fa-cart-plus me-1"></i> Agregar al carrito
                            </button>
                            <a href="<?= base_url('producto/'.$p->slug) ?>" class="btn btn-outline-secondary rounded-pill">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php $productCount++; ?>
        <?php endforeach; ?>
        
        <?php if(empty($products)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-cookie-bite fa-4x text-muted mb-3 opacity-25"></i>
                <h4 class="text-muted">Aún no hay productos en esta categoría.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = '<?= base_url() ?>';
    
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            let productId = this.getAttribute('data-id');
            if (typeof window.addToCart === 'function') {
                window.addToCart(productId, 1, this);
            }
        });
    });
</script>
<?= $this->endSection() ?>
