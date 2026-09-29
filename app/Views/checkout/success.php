<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5">
                
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                
                <h1 class="display-5 fw-bold mb-3" style="color: var(--dg-primary); font-family: var(--font-heading);">
                    ¡Gracias por tu pedido!
                </h1>
                
                <p class="lead text-muted mb-4">
                    Hola <strong><?= esc($order->customer_name) ?></strong>, tu pedido <strong>#<?= esc($order->order_number) ?></strong> ha sido registrado correctamente.
                </p>

                <!-- ══════════════════════════════════════════════════════════
                     1. SEGUIMIENTO DEL PEDIDO (TIMELINE & DATOS DE CLIENTE)
                ═══════════════════════════════════════════════════════════ -->
                <div class="card border-0 bg-light rounded-4 p-4 text-start mb-4 shadow-sm">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-route me-2 text-primary"></i>Seguimiento de tu pedido
                    </h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Número de Pedido:</strong> <span class="badge bg-secondary fs-6"><?= esc($order->order_number) ?></span></p>
                            <p class="mb-1"><strong>Fecha y Hora:</strong> <?= date('d/m/Y h:i A', strtotime($order->created_at)) ?> <small class="text-muted">(Colombia)</small></p>
                            <p class="mb-1"><strong>Método de Pago:</strong> <?= esc($order->payment_method) ?></p>
                            <p class="mb-0"><strong>Total:</strong> <span class="text-success fw-bold fs-5">$<?= number_format($order->total, 0, ',', '.') ?></span></p>
                        </div>
                        <div class="col-md-6 border-start-md">
                            <p class="mb-1"><strong>Cliente:</strong> <?= esc($order->customer_name) ?></p>
                            <p class="mb-1"><strong>Teléfono:</strong> <?= esc($order->phone) ?></p>
                            <?php if(!empty($order->customer_email)): ?>
                            <p class="mb-1"><strong>Correo:</strong> <?= esc($order->customer_email) ?></p>
                            <?php endif; ?>
                            <p class="mb-0"><strong>Tipo de Entrega:</strong> <?= ucfirst($order->delivery_type) ?> <?= !empty($order->address) ? '(' . esc($order->address) . ')' : '' ?></p>
                        </div>
                    </div>

                    <!-- Timeline de Estado -->
                    <div class="position-relative my-4 px-2">
                        <div class="progress" style="height: 6px;">
                            <?php 
                                $pct = 25;
                                if ($currentStep == 2) $pct = 50;
                                if ($currentStep == 3) $pct = 75;
                                if ($currentStep == 4) $pct = 100;
                                if ($currentStep == 0) $pct = 100;
                            ?>
                            <div class="progress-bar <?= $currentStep == 0 ? 'bg-danger' : 'bg-success' ?>" role="progressbar" style="width: <?= $pct ?>%;"></div>
                        </div>

                        <div class="d-flex justify-content-between position-relative mt-n3" style="top: -18px;">
                            <?php if($order->status === 'cancelado'): ?>
                                <div class="text-center w-100">
                                    <span class="badge bg-danger rounded-circle p-3"><i class="fas fa-times fa-lg"></i></span>
                                    <div class="fw-bold text-danger mt-2">Pedido Cancelado</div>
                                </div>
                            <?php else: ?>
                                <?php foreach($statuses as $stKey => $stVal): ?>
                                    <?php if($stKey === 'cancelado') continue; ?>
                                    <?php $isDone = ($currentStep >= $stVal['step']); ?>
                                    <div class="text-center">
                                        <span class="badge <?= $isDone ? 'bg-success' : 'bg-secondary' ?> rounded-circle p-2 shadow-sm">
                                            <i class="fas <?= $stVal['icon'] ?>"></i>
                                        </span>
                                        <div class="small fw-bold mt-1 <?= $isDone ? 'text-dark' : 'text-muted' ?>" style="font-size: 11px;">
                                            <?= $stVal['label'] ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════════
                     2. DETALLE DE PRODUCTOS COMPRADOS & VALORACIÓN
                ═══════════════════════════════════════════════════════════ -->
                <?php if (!empty($items)): ?>
                <div class="card border-0 bg-light rounded-4 p-4 text-start mb-4 shadow-sm">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-star me-2 text-warning"></i>Calificar Productos Comprados
                    </h5>
                    
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($items as $it): ?>
                        <div class="p-3 bg-white rounded-3 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark"><?= esc($it->product_name) ?></h6>
                                <small class="text-muted">Cantidad: <?= $it->quantity ?> | Subtotal: $<?= number_format($it->subtotal, 0, ',', '.') ?></small>
                            </div>

                            <div class="rating-item-container" data-item-id="<?= $it->id ?>">
                                <?php if (!empty($it->rating)): ?>
                                    <div class="badge bg-success bg-opacity-10 text-success p-2 rounded-3">
                                        <div class="fw-bold mb-1"><i class="fas fa-check-circle me-1"></i>Producto Calificado</div>
                                        <div class="text-warning">
                                            <?php for($s=1; $s<=5; $s++): ?>
                                                <i class="<?= $s <= $it->rating->rating ? 'fas' : 'far' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if(!empty($it->rating->comment)): ?>
                                            <small class="fst-italic text-muted d-block mt-1">"<?= esc($it->rating->comment) ?>"</small>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif (session()->get('is_logged_in')): ?>
                                    <form action="<?= base_url('valoracion/guardar') ?>" method="POST" class="form-rating-ajax d-flex flex-column gap-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="order_item_id" value="<?= $it->id ?>">
                                        <div class="d-flex align-items-center gap-1 rating-stars-picker">
                                            <?php for($s=1; $s<=5; $s++): ?>
                                                <i class="far fa-star text-warning fs-5 cursor-pointer star-pick" data-val="<?= $s ?>"></i>
                                            <?php endfor; ?>
                                            <input type="hidden" name="rating" value="5" class="rating-value-input">
                                        </div>
                                        <input type="text" name="comment" class="form-control form-control-sm rounded-pill" placeholder="Escribe tu comentario opcional...">
                                        <button type="submit" class="btn btn-sm btn-dg-primary rounded-pill fw-bold">Calificar producto</button>
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted"><a href="<?= base_url('login') ?>" class="text-primary font-weight-bold">Inicia sesión</a> para calificar esta compra.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="alert alert-info rounded-4 text-start mb-4">
                    <i class="fas fa-info-circle me-2"></i> 
                    Para procesar tu pedido más rápido, por favor contáctanos por WhatsApp enviándonos el comprobante de pago o para coordinar la entrega.
                </div>

                <?php 
                    $cleanPhone = preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '3108690810'));
                    if (strlen($cleanPhone) === 10 && strpos($cleanPhone, '57') !== 0) {
                        $cleanPhone = '57' . $cleanPhone;
                    }
                    $message = "Hola Dulce Gracia, acabo de realizar un pedido.\n\n";
                    $message .= "*Orden:* " . $order->order_number . "\n";
                    $message .= "*Nombre:* " . $order->customer_name . "\n";
                    $message .= "*Total:* $" . number_format($order->total, 0, ',', '.') . "\n\n";
                    $message .= "Escribo para gestionar el pago/entrega. ¡Gracias!";
                    
                    $waLink = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($message);
                ?>

                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    <a href="<?= $waLink ?>" target="_blank" class="btn btn-success btn-lg rounded-pill px-4">
                        <i class="fab fa-whatsapp me-2"></i> Contactar por WhatsApp
                    </a>
                    <a href="<?= base_url('productos') ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                        Volver al Catálogo
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Interactivad de estrellas para rating
    document.querySelectorAll('.rating-stars-picker').forEach(picker => {
        const stars = picker.querySelectorAll('.star-pick');
        const input = picker.querySelector('.rating-value-input');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-val'));
                input.value = val;
                stars.forEach((s, idx) => {
                    if (idx < val) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
        });
    });

    // Envío AJAX de formulario de valoración
    document.querySelectorAll('.form-rating-ajax').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (typeof DGAlert !== 'undefined') DGAlert.success('¡Gracias!', data.message);
                    location.reload();
                } else {
                    if (typeof DGAlert !== 'undefined') DGAlert.error('Atención', data.message);
                    else alert(data.message);
                    btn.disabled = false;
                    btn.innerHTML = 'Calificar producto';
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = 'Calificar producto';
            });
        });
    });
});
</script>
<?= $this->endSection() ?>

