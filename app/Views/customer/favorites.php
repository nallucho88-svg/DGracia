<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
            <i class="fas fa-heart text-danger me-2"></i>Mis Productos Favoritos
        </h5>
        <span class="badge bg-light text-dark border rounded-pill px-3"><?= count($products) ?> guardados</span>
    </div>
    
    <div class="card-body p-4">
        <?php if (empty($products)): ?>
            <div class="text-center text-muted py-5">
                <i class="far fa-heart fa-4x mb-3 opacity-25"></i>
                <h5>Aún no has guardado productos en favoritos</h5>
                <p class="small">Haz clic en el corazón de cualquier producto para guardarlo en tu lista personal.</p>
                <a href="<?= base_url('productos') ?>" class="btn btn-dg-primary rounded-pill px-4 py-2 mt-2">
                    <i class="fas fa-store me-2"></i>Explorar Tienda
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $p): ?>
                <div class="col-md-6 col-lg-4" id="fav-card-<?= $p->id ?>">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative">
                        <!-- Botón Quitar de Favoritos -->
                        <button type="button" 
                                class="btn btn-light btn-sm text-danger rounded-circle position-absolute top-0 end-0 m-2 shadow-sm btn-toggle-fav"
                                data-product-id="<?= $p->id ?>"
                                title="Quitar de favoritos">
                            <i class="fas fa-heart"></i>
                        </button>

                        <a href="<?= base_url('producto/' . $p->slug) ?>">
                            <img src="<?= $p->main_image ?>" class="card-img-top" alt="<?= esc($p->name) ?>" style="height: 180px; object-fit: cover;">
                        </a>

                        <div class="card-body p-3 d-flex flex-column text-center">
                            <h6 class="fw-bold mb-1" style="color: var(--dg-text-dark);"><?= esc($p->name) ?></h6>
                            
                            <div class="my-2">
                                <?php if (!empty($p->discount_info['has_discount'])): ?>
                                    <span class="text-muted text-decoration-line-through me-2 small">$<?= number_format($p->discount_info['original_price'], 0, ',', '.') ?></span>
                                    <span class="fw-bold text-danger">$<?= number_format($p->discount_info['final_price'], 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="fw-bold text-success">$<?= number_format($p->price, 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto d-grid gap-2">
                                <button type="button" 
                                        class="btn btn-dg-primary btn-sm rounded-pill btn-add-cart-fav"
                                        data-product-id="<?= $p->id ?>"
                                        data-product-name="<?= esc($p->name) ?>">
                                    <i class="fas fa-cart-plus me-1"></i>Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Quitar de favoritos
    document.querySelectorAll('.btn-toggle-fav').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const card = document.getElementById('fav-card-' + productId);

            fetch('<?= base_url('favorito/toggle') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'product_id=' + productId + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    if (card) card.remove();
                    DGAlert.info('Favoritos', res.message);
                }
            });
        });
    });

    // Agregar al carrito desde favoritos
    document.querySelectorAll('.btn-add-cart-fav').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId   = this.dataset.productId;
            const productName = this.dataset.productName;

            fetch('<?= base_url('carrito/agregar') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'product_id=' + productId + '&quantity=1&<?= csrf_token() ?>=<?= csrf_hash() ?>'
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    DGAlert.success('🛒 Carrito', productName + ' agregado al carrito.');
                    if (typeof window.refreshMiniCart === 'function') window.refreshMiniCart();
                }
            });
        });
    });
});
</script>

<?= $this->endSection() ?>
