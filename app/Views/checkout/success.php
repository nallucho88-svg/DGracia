<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 p-5">
                
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                
                <h1 class="display-5 fw-bold mb-3" style="color: var(--dg-primary); font-family: var(--font-heading);">
                    ¡Gracias por tu pedido!
                </h1>
                
                <p class="lead text-muted mb-4">
                    Hola <strong><?= esc($order->customer_name) ?></strong>, hemos recibido tu pedido correctamente.
                </p>

                <div class="bg-light p-4 rounded-4 mb-5 d-inline-block text-start">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-center">Resumen de tu Orden</h5>
                    <p class="mb-1"><strong>Número de Pedido:</strong> <span class="badge bg-secondary fs-6"><?= esc($order->order_number) ?></span></p>
                    <p class="mb-1"><strong>Total a Pagar:</strong> <span style="color: var(--dg-accent); font-weight: bold;">$<?= number_format($order->total, 0, ',', '.') ?></span></p>
                    <p class="mb-1"><strong>Método de Pago:</strong> <?= esc($order->payment_method) ?></p>
                    <p class="mb-0"><strong>Tipo de Entrega:</strong> <?= ucfirst($order->delivery_type) ?></p>
                </div>

                <div class="alert alert-info rounded-4 text-start mb-5">
                    <i class="fas fa-info-circle me-2"></i> 
                    Para procesar tu pedido más rápido, por favor contáctanos por WhatsApp enviándonos el comprobante de pago o para coordinar la entrega.
                </div>

                <?php 
                    // Preparar mensaje de WhatsApp
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
