<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <h2 class="mb-4" style="font-family: var(--font-heading); color: var(--dg-primary);">Tu Carrito de Compras</h2>
    
    <?php if(empty($cart)): ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-basket fa-4x text-muted mb-3 opacity-25"></i>
        <h4 class="text-muted">Tu carrito está vacío</h4>
        <p class="text-muted">¡Parece que aún no has agregado ningún postre delicioso!</p>
        <a href="<?= base_url('productos') ?>" class="btn btn-dg-accent px-4 py-2 mt-3 rounded-pill">Ver Catálogo</a>
    </div>
    <?php else: ?>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col" class="text-center">Precio</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-end">Subtotal</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cart as $item): ?>
                                <tr id="cart-row-<?= $item['id'] ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="<?= base_url('producto/' . (!empty($item['slug']) ? $item['slug'] : $item['id'])) ?>" class="d-inline-block text-decoration-none">
                                                <img src="<?= $item['image'] ?>" alt="<?= esc($item['name']) ?>" class="img-thumbnail rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            </a>
                                            <div>
                                                <h6 class="mb-0 fw-bold">
                                                    <a href="<?= base_url('producto/' . (!empty($item['slug']) ? $item['slug'] : $item['id'])) ?>" class="text-decoration-none text-dark hover-primary">
                                                        <?= esc($item['name']) ?>
                                                    </a>
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($item['has_discount'])): ?>
                                            <div class="text-muted text-decoration-line-through small">$<?= number_format($item['normal_price'] ?? $item['price'], 0, ',', '.') ?></div>
                                            <div class="fw-bold text-success">$<?= number_format($item['price'], 0, ',', '.') ?></div>
                                            <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;"><?= $item['discount_percent'] ?>% OFF</span>
                                        <?php else: ?>
                                            <div class="fw-bold text-dark">$<?= number_format($item['price'], 0, ',', '.') ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" style="width: 140px;">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary btn-update-qty" data-id="<?= $item['id'] ?>" data-action="minus" type="button"><i class="fas fa-minus"></i></button>
                                            <input type="text" class="form-control text-center cart-qty-input" id="qty-<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" readonly>
                                            <button class="btn btn-outline-secondary btn-update-qty" data-id="<?= $item['id'] ?>" data-action="plus" type="button"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold" style="color: var(--dg-accent);" id="subtotal-<?= $item['id'] ?>">
                                        $<?= number_format($item['subtotal'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger rounded-circle btn-remove-cart" data-id="<?= $item['id'] ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="form-label text-muted small fw-bold">¿TIENES UN CUPÓN?</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-uppercase" id="coupon-code-input" placeholder="CUPÓN (Ej: MADRE2026)" value="<?= esc($appliedCoupon['code'] ?? '') ?>">
                            <button class="btn btn-outline-primary" type="button" id="btn-apply-coupon">Aplicar</button>
                        </div>
                        <div id="coupon-message" class="mt-2 small">
                            <?php if(!empty($appliedCoupon)): ?>
                                <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Cupón <?= esc($appliedCoupon['code']) ?> aplicado</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Valor real productos:</span>
                        <span class="fw-bold" id="cart-original-subtotal"><?= $summary['formatted']['original_price_subtotal'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 text-danger <?= ($summary['product_discount_total'] > 0) ? '' : 'd-none' ?>" id="cart-product-discount-row">
                        <span>Descuentos productos:</span>
                        <span class="fw-bold" id="cart-product-discount"><?= $summary['formatted']['product_discount_total'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal con descuento:</span>
                        <span class="fw-bold" id="cart-subtotal-after-discount"><?= $summary['formatted']['subtotal_after_product_discount'] ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 text-success <?= ($summary['coupon_discount_total'] > 0) ? '' : 'd-none' ?>" id="coupon-discount-row">
                        <span>Descuento Cupón (<?= esc($appliedCoupon['code'] ?? '') ?>):</span>
                        <span class="fw-bold" id="coupon-discount-amount"><?= $summary['formatted']['coupon_discount_total'] ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Envío:</span>
                        <span class="text-muted small text-end">Se calculará en el checkout</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">TOTAL ESTIMADO:</span>
                        <span class="fw-bold fs-5" style="color: var(--dg-primary);" id="cart-total-final"><?= $summary['formatted']['final_total'] ?></span>
                    </div>
                    
                    <a href="<?= base_url('checkout') ?>" class="btn btn-dg-primary btn-lg w-100 rounded-pill mb-2">
                        Proceder al Pago <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <a href="<?= base_url('productos') ?>" class="btn btn-outline-secondary w-100 rounded-pill">
                        Seguir Comprando
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = '<?= base_url() ?>';
    
    function updateSummaryUI(summary) {
        if (!summary || !summary.formatted) return;
        document.getElementById('cart-original-subtotal').innerText = summary.formatted.original_price_subtotal;
        
        const prodDiscRow = document.getElementById('cart-product-discount-row');
        if (summary.product_discount_total > 0) {
            prodDiscRow.classList.remove('d-none');
            document.getElementById('cart-product-discount').innerText = summary.formatted.product_discount_total;
        } else {
            prodDiscRow.classList.add('d-none');
        }

        document.getElementById('cart-subtotal-after-discount').innerText = summary.formatted.subtotal_after_product_discount;
        
        const couponRow = document.getElementById('coupon-discount-row');
        if (summary.coupon_discount_total > 0) {
            couponRow.classList.remove('d-none');
            document.getElementById('coupon-discount-amount').innerText = summary.formatted.coupon_discount_total;
        } else {
            couponRow.classList.add('d-none');
        }

        document.getElementById('cart-total-final').innerText = summary.formatted.final_total;
    }

    // Función para actualizar cantidades
    document.querySelectorAll('.btn-update-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            const input = document.getElementById('qty-' + id);
            let qty = parseInt(input.value);
            
            if (action === 'minus' && qty > 1) {
                qty--;
            } else if (action === 'plus') {
                qty++;
            } else {
                return;
            }
            
            input.value = qty;
            
            let formData = new FormData();
            formData.append('product_id', id);
            formData.append('quantity', qty);
            
            fetch(baseUrl + 'carrito/actualizar', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('subtotal-' + id).innerText = '$' + new Intl.NumberFormat('es-CO').format(data.item_subtotal);
                    document.getElementById('cart-counter').innerText = data.cart_count;
                    updateSummaryUI(data.summary);
                }
            });
        });
    });

    // Función para eliminar item
    document.querySelectorAll('.btn-remove-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "Se quitará este producto de tu carrito.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--dg-primary)',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('product_id', id);
                    
                    fetch(baseUrl + 'carrito/eliminar', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            let row = document.getElementById('cart-row-' + id);
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = 0;
                            
                            setTimeout(() => {
                                row.remove();
                                document.getElementById('cart-counter').innerText = data.cart_count;
                                updateSummaryUI(data.summary);
                                
                                if(data.cart_count == 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                    });
                }
            });
        });
    });

    // Aplicar Cupón AJAX
    const btnApplyCoupon = document.getElementById('btn-apply-coupon');
    if (btnApplyCoupon) {
        btnApplyCoupon.addEventListener('click', function() {
            const input = document.getElementById('coupon-code-input');
            const msgBox = document.getElementById('coupon-message');
            const code = input.value.trim();

            if (!code) {
                msgBox.innerHTML = '<span class="text-danger">Ingresa un código de cupón.</span>';
                return;
            }

            btnApplyCoupon.innerText = 'Verificando...';
            btnApplyCoupon.disabled = true;

            let formData = new FormData();
            formData.append('code', code);

            fetch(baseUrl + 'carrito/aplicar-cupon', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnApplyCoupon.innerText = 'Aplicar';
                btnApplyCoupon.disabled = false;

                if (data.status === 'success') {
                    msgBox.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>${data.message}</span>`;
                    updateSummaryUI(data.summary);
                } else {
                    msgBox.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i>${data.message}</span>`;
                }
            })
            .catch(err => {
                btnApplyCoupon.innerText = 'Aplicar';
                btnApplyCoupon.disabled = false;
                console.error(err);
            });
        });
    }
</script>
<?= $this->endSection() ?>
