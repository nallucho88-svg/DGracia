<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    /* ======== HERO SLIDER ======== */
    .hero-slider {
        position: relative;
        height: 80vh;
        min-height: 520px;
        overflow: hidden;
        margin-top: -1rem;
    }

    .hero-slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0;
        transition: opacity 1.2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-slide.active {
        opacity: 1;
        z-index: 1;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(160deg, rgba(62,39,35,0.72) 0%, rgba(30,20,20,0.45) 100%);
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: var(--dg-white);
        padding: 0 1rem;
    }

    .hero-content h1 {
        font-size: clamp(2.4rem, 6vw, 4.5rem);
        color: var(--dg-white);
        margin-bottom: 1rem;
        animation: fadeInDown 1s ease-out;
        text-shadow: 0 2px 16px rgba(0,0,0,.4);
    }

    .hero-content p.hero-sub {
        font-size: clamp(1rem, 2.5vw, 1.5rem);
        margin-bottom: 2rem;
        opacity: .92;
        animation: fadeInUp 1s ease-out .5s both;
    }

    .hero-buttons {
        animation: fadeIn 1s ease-out 1s both;
    }

    /* Dots e indicadores */
    .hero-dots {
        position: absolute;
        bottom: 18px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        display: flex;
        gap: 8px;
    }
    .hero-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,.45);
        cursor: pointer;
        transition: background .3s, transform .3s;
    }
    .hero-dot.active {
        background: var(--dg-primary);
        transform: scale(1.4);
    }

    /* Botones de navegación manual ← y → sobre el banner */
    .hero-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        background: rgba(0, 0, 0, 0.45);
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.6);
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }
    .hero-arrow:hover {
        background: var(--dg-primary);
        border-color: var(--dg-primary);
        color: white;
        transform: translateY(-50%) scale(1.12);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .hero-arrow-left { left: 24px; }
    .hero-arrow-right { right: 24px; }

    /* ======== FEATURED PRODUCTS ======== */
    .section-heading {
        font-family: var(--font-heading);
        color: var(--dg-primary);
        font-size: 2rem;
        font-weight: 700;
        position: relative;
        display: inline-block;
    }
    .section-heading::after {
        content: '';
        display: block;
        height: 3px;
        width: 60px;
        background: var(--dg-accent, #E9EDC9);
        border-radius: 2px;
        margin: 6px auto 0;
    }

    .product-card {
        border: none;
        border-radius: 1.2rem;
        overflow: hidden;
        transition: transform .3s, box-shadow .3s;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,.13) !important;
    }
    .product-card .card-img-top {
        height: 230px;
        object-fit: cover;
        transition: transform .5s;
    }
    .product-card:hover .card-img-top {
        transform: scale(1.04);
    }

    .badge-discount {
        position: absolute;
        top: 12px; left: 12px;
        background: var(--dg-primary);
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: .5px;
    }

    .price-original {
        text-decoration: line-through;
        color: #aaa;
        font-size: .85rem;
    }
    .price-final {
        color: var(--dg-primary);
        font-size: 1.15rem;
        font-weight: 700;
    }

    /* ======== PROMO MODAL ======== */
    .promo-modal-bg {
        border-radius: 15px;
        text-align: center;
        padding: 3rem 2rem;
        background-size: cover;
        background-position: center;
    }
    .promo-modal-text {
        font-family: var(--font-heading);
        font-size: 2rem;
        animation: slidePromo 3s infinite alternate ease-in-out;
    }

    /* ======== ANIMATIONS ======== */
    @keyframes slidePromo {
        0%   { transform: translateY(-5px); }
        100% { transform: translateY(5px); }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ===== HERO SLIDER ===== -->
<?php
$heroBg = setting('hero_bg_path', '');
$heroTitle = setting('hero_title', setting('site_name', 'Dulce Gracia'));
$heroSub   = setting('hero_subtitle', 'Endulzamos tus momentos más especiales con amor y tradición.');
?>
<section class="hero-slider" id="heroSlider">

    <?php if (!empty($banners)): ?>
        <?php foreach ($banners as $i => $banner): ?>
        <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>"
             style="background-image: url('<?= media_url($banner->image_path) ?>');"
             data-slide="<?= $i ?>">
            <div class="hero-overlay"></div>
            <div class="hero-content container">
                <h1><?= esc($heroTitle) ?></h1>
                <p class="hero-sub"><?= esc($heroSub) ?></p>
                <div class="hero-buttons d-flex justify-content-center gap-4 flex-wrap">
                    <a href="<?= base_url('productos') ?>" class="btn btn-dg-primary btn-lg px-5 py-3 fs-5 rounded-pill">
                        <i class="fas fa-store me-2"></i>VER PRODUCTOS
                    </a>
                    <a href="<?= base_url('checkout') ?>" class="btn btn-dg-accent btn-lg px-5 py-3 fs-5 rounded-pill">
                        <i class="fas fa-bolt me-2"></i>HAZ TU PEDIDO
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Dots -->
        <?php if (count($banners) > 1): ?>
        <div class="hero-dots">
            <?php foreach ($banners as $i => $b): ?>
            <div class="hero-dot <?= $i === 0 ? 'active' : '' ?>" data-target="<?= $i ?>"></div>
            <?php endforeach; ?>
        </div>

        <!-- Botones de Navegación Manual (← y →) -->
        <button type="button" class="hero-arrow hero-arrow-left" id="heroPrevBtn" aria-label="Anterior">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="hero-arrow hero-arrow-right" id="heroNextBtn" aria-label="Siguiente">
            <i class="fas fa-chevron-right"></i>
        </button>
        <?php endif; ?>

    <?php else: ?>
        <!-- Fallback hero si no hay banners -->
        <div class="hero-slide active"
             style="background-image: url('<?= media_url(!empty($heroBg) ? $heroBg : 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?q=80&w=2000&auto=format&fit=crop') ?>');">
            <div class="hero-overlay"></div>
            <div class="hero-content container">
                <h1><?= esc($heroTitle) ?></h1>
                <p class="hero-sub"><?= esc($heroSub) ?></p>
                <div class="hero-buttons d-flex justify-content-center gap-4 flex-wrap">
                    <a href="<?= base_url('productos') ?>" class="btn btn-dg-primary btn-lg px-5 py-3 fs-5 rounded-pill">
                        <i class="fas fa-store me-2"></i>VER PRODUCTOS
                    </a>
                    <a href="<?= base_url('checkout') ?>" class="btn btn-dg-accent btn-lg px-5 py-3 fs-5 rounded-pill">
                        <i class="fas fa-bolt me-2"></i>HAZ TU PEDIDO
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- ===== LO MÁS DESTACADO — Productos Reales ===== -->
<section class="container my-5 py-2">
    <div class="text-center mb-5">
        <h2 class="section-heading">Lo Más Destacado</h2>
        <p class="text-muted mt-3">Selección especial de nuestros productos favoritos</p>
    </div>

    <div class="row g-4 justify-content-center">
        <?php if (empty($featuredProducts)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-cookie-bite fa-3x mb-3 d-block opacity-25"></i>
                Pronto tendremos productos disponibles. ¡Visítanos de nuevo!
            </div>
        <?php else: ?>
            <?php foreach ($featuredProducts as $prod): ?>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card product-card shadow-sm h-100">
                    <div class="position-relative overflow-hidden">
                        <img src="<?= media_url($prod->image_path ?? '', 'assets/img/default-product.jpg') ?>"
                             class="card-img-top"
                             alt="<?= esc($prod->name) ?>"
                             loading="lazy">
                        <?php if (!empty($prod->discount_info['has_discount'])): ?>
                        <span class="badge-discount">
                            <?= $prod->discount_info['discount_percentage'] ?>% OFF
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column text-center p-3">
                        <h5 class="card-title fw-bold mb-1" style="color: var(--dg-text-dark);">
                            <?= esc($prod->name) ?>
                        </h5>
                        <?php if (!empty($prod->short_description)): ?>
                        <p class="card-text text-muted small mb-2 flex-grow-1">
                            <?= esc(mb_strimwidth($prod->short_description, 0, 80, '...')) ?>
                        </p>
                        <?php endif; ?>

                        <div class="mb-3">
                            <?php if (!empty($prod->discount_info['has_discount'])): ?>
                            <span class="price-original me-2">
                                $<?= number_format($prod->discount_info['original_price'], 0, ',', '.') ?>
                            </span>
                            <span class="price-final">
                                $<?= number_format($prod->discount_info['final_price'], 0, ',', '.') ?>
                            </span>
                            <?php else: ?>
                            <span class="price-final">
                                $<?= number_format($prod->price, 0, ',', '.') ?>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="<?= base_url('producto/' . $prod->slug) ?>"
                               class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="fas fa-eye me-1"></i>Ver
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-dg-primary rounded-pill px-3 btn-add-cart"
                                    data-product-id="<?= $prod->id ?>"
                                    data-product-name="<?= esc($prod->name) ?>">
                                <i class="fas fa-cart-plus me-1"></i>Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="text-center mt-5">
        <a href="<?= base_url('productos') ?>" class="btn btn-outline-primary rounded-pill px-5 py-2 fs-5">
            <i class="fas fa-th-large me-2"></i>Ver todos los productos
        </a>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ======== HERO AUTO-SLIDER ========
    const slides = document.querySelectorAll('#heroSlider .hero-slide');
    const dots   = document.querySelectorAll('#heroSlider .hero-dot');
    let current  = 0;
    let timer;

    function goTo(idx) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (idx + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function startAuto() {
        timer = setInterval(() => goTo(current + 1), 5000);
    }

    if (slides.length > 1) {
        startAuto();
        dots.forEach(dot => {
            dot.addEventListener('click', function () {
                clearInterval(timer);
                goTo(parseInt(this.dataset.target));
                startAuto();
            });
        });

        const prevBtn = document.getElementById('heroPrevBtn');
        const nextBtn = document.getElementById('heroNextBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                clearInterval(timer);
                goTo(current - 1);
                startAuto();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                clearInterval(timer);
                goTo(current + 1);
                startAuto();
            });
        }
    }

    // ======== MODAL DE PROMOCIÓN ========
    <?php if ($modalPromo): ?>
    if (!sessionStorage.getItem('promo_<?= $modalPromo->id ?>')) {
        setTimeout(() => {
            Swal.fire({
                html: `
                    <div class="promo-modal-bg"
                         style="background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)),
                                url('<?= $modalPromo->image_path ?: base_url('assets/img/default-promo.jpg') ?>') center/cover;">
                        <h2 class="promo-modal-text text-white"><?= esc($modalPromo->title) ?></h2>
                        <p class="mt-4 fs-5 text-white"><?= esc($modalPromo->description) ?></p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-dg-accent btn-lg mt-3 rounded-pill px-4">
                            Ir a la oferta
                        </a>
                    </div>`,
                showConfirmButton: false,
                showCloseButton: true,
                background: 'transparent',
                width: 600,
                customClass: { popup: 'p-0 bg-transparent rounded-4' }
            });
            sessionStorage.setItem('promo_<?= $modalPromo->id ?>', 'true');
        }, 1500);
    }
    <?php endif; ?>

    // ======== PROMOS FLOTANTES (Toasts) ========
    <?php if (!empty($floatingPromos)): ?>
    const floatingPromos = <?= json_encode($floatingPromos) ?>;
    let promoIndex = 0;
    setInterval(() => {
        if (promoIndex >= floatingPromos.length) promoIndex = 0;
        const p = floatingPromos[promoIndex++];
        Toastify({
            text: '✨ ' + p.title + '\n' + p.description,
            duration: 5000, close: true, gravity: 'bottom', position: 'left',
            stopOnFocus: true,
            style: {
                background: 'var(--dg-primary)', color: '#fff',
                borderRadius: '10px', fontFamily: 'var(--font-body)',
                boxShadow: '0 4px 12px rgba(0,0,0,0.15)'
            }
        }).showToast();
    }, 30000);
    <?php endif; ?>

    // ======== AGREGAR AL CARRITO desde home ========
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId;
            if (typeof window.addToCart === 'function') {
                window.addToCart(productId, 1, this);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
