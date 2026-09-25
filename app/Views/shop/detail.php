<?= $this->extend('layouts/main') ?>

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
                <img id="mainImage" src="<?= media_url(!empty($images) ? $images[0]->image_path : '', 'assets/img/placeholder.jpg') ?>" class="img-fluid rounded-4" style="object-fit: cover; width: 100%; height: 500px;" alt="<?= esc($product->name) ?>">
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
            <span class="badge bg-secondary mb-2"><?= esc($category->name) ?></span>
            <h1 class="display-5 fw-bold" style="color: var(--dg-primary); font-family: var(--font-heading);"><?= esc($product->name) ?></h1>
            <h2 class="h3 mb-4" style="color: var(--dg-accent);">$<?= number_format($product->price, 0, ',', '.') ?></h2>
            
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
</script>
<?= $this->endSection() ?>
