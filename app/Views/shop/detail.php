<?= $this->extend('layouts/main') ?>

<?php
$productUrl = base_url('producto/' . $product->slug);
$mainImageUrl = media_url(!empty($images) ? $images[0]->image_path : '', 'assets/img/placeholder.jpg');

$hasDiscount = !empty($product->discount_info) && $product->discount_info['has_discount'];
$originalFmt = '$' . number_format($hasDiscount ? $product->discount_info['original_price'] : $product->price, 0, ',', '.');
$finalFmt    = '$' . number_format($hasDiscount ? $product->discount_info['final_price'] : $product->price, 0, ',', '.');

if ($hasDiscount) {
    $waMessage = "🍰 Mira este producto de Dulce Gracia:\n\n"
        . "*" . $product->name . "*\n\n"
        . "Precio: " . $originalFmt . "\n\n"
        . "🔥 Oferta:\n"
        . "Antes: " . $originalFmt . "\n"
        . "Ahora: " . $finalFmt . "\n\n"
        . "Ver producto:\n"
        . $productUrl;

    $xMessage = "🍰 Mira esta oferta de Dulce Gracia: " . $product->name . " por " . $finalFmt . " (Antes: " . $originalFmt . ") " . $productUrl;
} else {
    $waMessage = "🍰 Mira este producto de Dulce Gracia:\n\n"
        . "*" . $product->name . "*\n\n"
        . "Precio: " . $finalFmt . "\n\n"
        . "Ver producto:\n"
        . $productUrl;

    $xMessage = "🍰 Mira este delicioso postre de Dulce Gracia: " . $product->name . " " . $finalFmt . " " . $productUrl;
}

$waShareUrl = "https://wa.me/?text=" . urlencode($waMessage);
$fbShareUrl = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($productUrl);
$xShareUrl  = "https://x.com/intent/tweet?text=" . urlencode($xMessage);
?>

<?= $this->section('og_meta') ?>
<meta property="og:type" content="product">
<meta property="og:title" content="<?= esc($product->name) ?> | Dulce Gracia">
<meta property="og:description" content="<?= esc($product->short_description ?: substr(strip_tags($product->description), 0, 160)) ?>">
<meta property="og:image" content="<?= esc($mainImageUrl) ?>">
<meta property="og:url" content="<?= esc($productUrl) ?>">
<meta property="og:site_name" content="<?= esc(setting('site_name', 'Dulce Gracia')) ?>">
<meta property="product:price:amount" content="<?= $hasDiscount ? $product->discount_info['final_price'] : $product->price ?>">
<meta property="product:price:currency" content="COP">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-5">
    
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('productos') ?>" class="text-decoration-none text-muted">Productos</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('productos/categoria/'.$category->slug) ?>" class="text-decoration-none text-muted"><?= esc($category->name) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($product->name) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Galería de Imágenes -->
        <div class="col-md-6">
            <div class="card card-dg border-0 mb-3">
                <img id="mainImage" src="<?= $mainImageUrl ?>" class="img-fluid rounded-4" style="object-fit: cover; width: 100%; height: 500px;" alt="<?= esc($product->name) ?>">
            </div>
            
            <?php if(count($images) > 1): ?>
            <div class="d-flex gap-2 overflow-auto py-2">
                <?php foreach($images as $img): ?>
                <img src="<?= media_url($img->image_path) ?>" class="img-thumbnail rounded-3 cursor-pointer gallery-thumb" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainImage').src=this.src;">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Información del Producto -->
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="badge bg-secondary mb-2"><?= esc($category->name) ?></span>
                    <h1 class="display-5 fw-bold mb-1" style="color: var(--dg-primary); font-family: var(--font-heading);"><?= esc($product->name) ?></h1>
                </div>
                <!-- Botón de compartir con icono ↗ -->
                <button type="button" class="btn btn-outline-secondary rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#shareProductModal" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;" title="Compartir producto">
                    ↗
                </button>
            </div>

            <!-- Promedio de Valoraciones -->
            <?php if (!empty($ratingSummary) && $ratingSummary['total'] > 0): ?>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="text-warning fs-5">
                        <?php 
                            $avg = $ratingSummary['average'];
                            for ($st = 1; $st <= 5; $st++) {
                                echo ($st <= floor($avg)) ? '<i class="fas fa-star"></i>' : (($st - $avg < 1) ? '<i class="fas fa-star-half-alt"></i>' : '<i class="far fa-star"></i>');
                            }
                        ?>
                    </div>
                    <span class="fw-bold fs-5 text-dark"><?= $ratingSummary['average'] ?></span>
                    <span class="text-muted small">(Basado en <?= $ratingSummary['total'] ?> <?= $ratingSummary['total'] == 1 ? 'valoración' : 'valoraciones' ?>)</span>
                </div>
            <?php else: ?>
                <div class="text-muted small mb-3"><i class="far fa-star me-1 text-warning"></i>Sin valoraciones aún</div>
            <?php endif; ?>
            
            <!-- Descuento y Precios -->
            <?php if ($hasDiscount): ?>
                <div class="card bg-light border-danger border-opacity-25 rounded-4 p-3 mb-4 shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-danger rounded-pill px-3 py-2 fs-6 shadow-sm">
                            🔥 <?= $product->discount_info['discount_percentage'] ?>% OFF
                        </span>
                        <?php if (!empty($product->discount_info['discount_title'])): ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">
                                <i class="fas fa-tag me-1"></i><?= esc($product->discount_info['discount_title']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex flex-column gap-1">
                        <div class="text-muted text-decoration-line-through fs-6">
                            Precio anterior: <?= $product->discount_info['formatted_original'] ?>
                        </div>
                        <div class="text-danger fw-bold fs-6">
                            Descuento: -$<?= number_format($product->discount_info['discount_amount'], 0, ',', '.') ?>
                        </div>
                        <div class="h2 fw-bold text-success mb-0 mt-1">
                            Precio actual: <?= $product->discount_info['formatted_final'] ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h2 class="h3 mb-4 fw-bold" style="color: var(--dg-accent);">
                    Precio: $<?= number_format($product->price, 0, ',', '.') ?>
                </h2>
            <?php endif; ?>
            
            <p class="lead mb-4"><?= esc($product->description) ?></p>

            <div class="card bg-light border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Detalles</h5>
                    
                    <?php if(!empty($product->portions)): ?>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users text-muted me-3" style="width: 20px;"></i>
                        <div><strong>Porciones:</strong> <?= esc($product->portions) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($product->ingredients)): ?>
                    <div class="d-flex align-items-start">
                        <i class="fas fa-leaf text-muted me-3 mt-1" style="width: 20px;"></i>
                        <div><strong>Ingredientes:</strong> <?= esc($product->ingredients) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Formulario de Compra -->
            <form id="addToCartForm" class="mt-4">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                
                <div class="row align-items-end g-3">
                    <div class="col-auto">
                        <label for="quantity" class="form-label fw-bold">Cantidad</label>
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary btn-minus" type="button"><i class="fas fa-minus"></i></button>
                            <input type="text" class="form-control text-center" id="quantity" name="quantity" value="1" readonly>
                            <button class="btn btn-outline-secondary btn-plus" type="button"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    
                    <div class="col d-flex gap-2">
                        <button type="button" class="btn btn-dg-primary btn-lg flex-grow-1 btn-add-cart-detail">
                            <i class="fas fa-cart-plus me-2"></i> Agregar al Carrito
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-lg btn-toggle-favorite px-3 <?= (!empty($isFavorite)) ? 'is-favorite' : '' ?>" data-product-id="<?= $product->id ?>" title="Guardar en favoritos">
                            <i class="<?= (!empty($isFavorite)) ? 'fas fa-heart text-danger' : 'far fa-heart' ?>"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Alerta Disponibilidad -->
            <?php if(!$product->availability): ?>
            <div class="alert alert-warning mt-4 rounded-4">
                <i class="fas fa-exclamation-triangle me-2"></i> Este producto no está disponible temporalmente.
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         SECCIÓN DE VALORACIONES Y RESEÑAS DE CLIENTES
    ═══════════════════════════════════════════════════════════ -->
    <div class="row mt-5 pt-4 border-top">
        <div class="col-12 mb-4">
            <h3 class="fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Opiniones de Clientes</h3>
        </div>

        <?php if (!empty($ratingSummary) && $ratingSummary['total'] > 0): ?>
            <div class="col-md-4 mb-4">
                <div class="card border-0 bg-light rounded-4 p-4 text-center h-100 shadow-sm">
                    <h1 class="display-3 fw-bold text-dark mb-0"><?= number_format($ratingSummary['average'], 1) ?></h1>
                    <div class="text-warning fs-4 mb-2">
                        <?php 
                            for ($st = 1; $st <= 5; $st++) {
                                echo ($st <= floor($ratingSummary['average'])) ? '<i class="fas fa-star"></i>' : (($st - $ratingSummary['average'] < 1) ? '<i class="fas fa-star-half-alt"></i>' : '<i class="far fa-star"></i>');
                            }
                        ?>
                    </div>
                    <small class="text-muted fw-bold">Basado en <?= $ratingSummary['total'] ?> valoraciones</small>

                    <!-- Desglose de Distribución -->
                    <div class="mt-4 text-start">
                        <?php for ($st = 5; $st >= 1; $st--): ?>
                            <?php 
                                $cnt = $ratingSummary['distribution'][$st] ?? 0;
                                $pct = $ratingSummary['total'] > 0 ? round(($cnt / $ratingSummary['total']) * 100) : 0;
                            ?>
                            <div class="d-flex align-items-center gap-2 mb-2 small">
                                <span class="fw-bold" style="width: 30px;"><?= $st ?> ★</span>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="text-muted" style="width: 30px; text-align: right;"><?= $cnt ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($reviews as $rev): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                        <?= strtoupper(substr($rev['customer_name'] ?? 'C', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0"><?= esc($rev['customer_name'] ?? 'Cliente') ?></div>
                                        <small class="text-muted" style="font-size: 11px;"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>
                                    </div>
                                </div>
                                <div class="text-warning small">
                                    <?php for ($st = 1; $st <= 5; $st++): ?>
                                        <i class="<?= $st <= $rev['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php if (!empty($rev['comment'])): ?>
                                <p class="mb-0 text-secondary fst-italic">"<?= esc($rev['comment']) ?>"</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12 text-center py-4 bg-light rounded-4">
                <i class="far fa-comments fa-3x text-muted mb-3 opacity-50"></i>
                <p class="text-muted mb-0">Este producto aún no tiene valoraciones. ¡Sé el primero en comprarlo y dejar tu opinión!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Compartir Producto -->
<div class="modal fade" id="shareProductModal" tabindex="-1" aria-labelledby="shareProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="shareProductModalLabel">Compartir producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                    <img src="<?= $mainImageUrl ?>" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;" alt="Producto">
                    <div>
                        <div class="fw-bold text-dark"><?= esc($product->name) ?></div>
                        <div class="text-success fw-bold"><?= $hasDiscount ? $product->discount_info['formatted_final'] : '$' . number_format($product->price, 0, ',', '.') ?></div>
                    </div>
                </div>

                <p class="fw-bold small text-muted text-uppercase mb-3">Compartir en:</p>
                <div class="d-grid gap-2">
                    <a href="<?= $waShareUrl ?>" target="_blank" rel="noopener" class="btn btn-success rounded-pill py-2 fw-bold text-white shadow-sm">
                        <i class="fab fa-whatsapp me-2 fs-5"></i> WhatsApp
                    </a>
                    <a href="<?= $fbShareUrl ?>" target="_blank" rel="noopener" class="btn rounded-pill py-2 fw-bold text-white shadow-sm" style="background-color: #1877f2;">
                        <i class="fab fa-facebook-f me-2 fs-5"></i> Facebook
                    </a>
                    <a href="<?= $xShareUrl ?>" target="_blank" rel="noopener" class="btn btn-dark rounded-pill py-2 fw-bold shadow-sm">
                        <i class="fab fa-x-twitter me-2 fs-5"></i> X
                    </a>
                    <button type="button" class="btn btn-outline-secondary rounded-pill py-2 fw-bold mt-2" id="btnCopyProductLink" data-url="<?= esc($productUrl) ?>">
                        <i class="fas fa-link me-2"></i> Copiar enlace
                    </button>
                </div>
            </div>
        </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = '<?= base_url() ?>';
    
    // Control de cantidad
    document.querySelector('.btn-minus').addEventListener('click', function() {
        let input = document.getElementById('quantity');
        let val = parseInt(input.value);
        if(val > 1) input.value = val - 1;
    });

    document.querySelector('.btn-plus').addEventListener('click', function() {
        let input = document.getElementById('quantity');
        input.value = parseInt(input.value) + 1;
    });

    // Añadir al carrito real
    document.querySelector('.btn-add-cart-detail').addEventListener('click', function() {
        let qty = parseInt(document.getElementById('quantity').value) || 1;
        let productId = '<?= $product->id ?>';
        if (typeof window.addToCart === 'function') {
            window.addToCart(productId, qty, this);
        }
    });

    // Copiar enlace al portapapeles
    const btnCopy = document.getElementById('btnCopyProductLink');
    if (btnCopy) {
        btnCopy.addEventListener('click', function() {
            const url = this.getAttribute('data-url') || window.location.href;
            
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(showCopySuccess).catch(fallbackCopy);
            } else {
                fallbackCopy();
            }

            function fallbackCopy() {
                const tempInput = document.createElement('input');
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                showCopySuccess();
            }

            function showCopySuccess() {
                if (typeof Toastify === 'function') {
                    Toastify({
                        text: "✓ Enlace copiado correctamente",
                        duration: 3000,
                        gravity: "bottom",
                        position: "center",
                        style: { background: "#198754", borderRadius: "30px" }
                    }).showToast();
                } else {
                    alert('✓ Enlace copiado correctamente');
                }
            }
        });
    }
</script>
<?= $this->endSection() ?>
