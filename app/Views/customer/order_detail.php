<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--dg-primary);">
                Seguimiento de Pedido #<?= esc($order->order_number) ?>
            </h5>
            <small class="text-muted">Realizado el <?= date('d/m/Y h:i A', strtotime($order->created_at)) ?></small>
        </div>
        <a href="<?= base_url('mi-cuenta/pedidos') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i>Volver a mis pedidos
        </a>
    </div>

    <div class="card-body p-4">

        <!-- LÍNEA DE TIEMPO VISUAL DE SEGUIMIENTO -->
        <?php if ($order->status === 'cancelado'): ?>
            <div class="alert alert-danger rounded-4 p-4 text-center">
                <i class="fas fa-times-circle fa-3x mb-2"></i>
                <h5 class="fw-bold">Pedido Cancelado</h5>
                <p class="mb-0 small">Este pedido ha sido cancelado. Si tienes dudas, contáctanos a través de WhatsApp.</p>
            </div>
        <?php else: ?>
            <div class="py-3 px-2 mb-4">
                <div class="timeline-wrapper d-flex justify-content-between align-items-center position-relative">
                    <div class="timeline-line position-absolute top-50 start-0 end-0 translate-middle-y bg-light" style="height: 6px; z-index: 1;"></div>
                    <div class="timeline-line-active position-absolute top-50 start-0 translate-middle-y bg-success" 
                         style="height: 6px; z-index: 1; width: <?= (($currentStep - 1) / 3) * 100 ?>%; transition: width 0.8s ease;"></div>

                    <!-- Paso 1: Pedido Recibido -->
                    <div class="timeline-step text-center position-relative" style="z-index: 2;">
                        <div class="step-icon rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep >= 1 ? 'bg-success text-white' : 'bg-white text-muted border' ?>" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="fas <?= $currentStep > 1 ? 'fa-check' : 'fa-clipboard-check' ?>"></i>
                        </div>
                        <div class="mt-2">
                            <strong class="d-block small <?= $currentStep >= 1 ? 'text-success' : 'text-muted' ?>">Pedido Recibido</strong>
                            <small class="text-muted d-none d-sm-block">Registrado</small>
                        </div>
                    </div>

                    <!-- Paso 2: En Preparación -->
                    <div class="timeline-step text-center position-relative" style="z-index: 2;">
                        <div class="step-icon rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep >= 2 ? 'bg-success text-white' : 'bg-white text-muted border' ?>" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="fas <?= $currentStep > 2 ? 'fa-check' : 'fa-blender' ?>"></i>
                        </div>
                        <div class="mt-2">
                            <strong class="d-block small <?= $currentStep >= 2 ? 'text-success' : 'text-muted' ?>">En Preparación</strong>
                            <small class="text-muted d-none d-sm-block">En cocina</small>
                        </div>
                    </div>

                    <!-- Paso 3: En Camino -->
                    <div class="timeline-step text-center position-relative" style="z-index: 2;">
                        <div class="step-icon rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep >= 3 ? 'bg-success text-white' : 'bg-white text-muted border' ?>" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="fas <?= $currentStep > 3 ? 'fa-check' : 'fa-motorcycle' ?>"></i>
                        </div>
                        <div class="mt-2">
                            <strong class="d-block small <?= $currentStep >= 3 ? 'text-success' : 'text-muted' ?>">En Camino / Listo</strong>
                            <small class="text-muted d-none d-sm-block">Ruta o recojo</small>
                        </div>
                    </div>

                    <!-- Paso 4: Entregado -->
                    <div class="timeline-step text-center position-relative" style="z-index: 2;">
                        <div class="step-icon rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm <?= $currentStep >= 4 ? 'bg-success text-white' : 'bg-white text-muted border' ?>" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="mt-2">
                            <strong class="d-block small <?= $currentStep >= 4 ? 'text-success' : 'text-muted' ?>">Entregado</strong>
                            <small class="text-muted d-none d-sm-block">¡A disfrutar!</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- RESUMEN DEL PEDIDO -->
        <div class="row g-4 border-top pt-4">
            <div class="col-md-7">
                <h6 class="fw-bold mb-3" style="color: var(--dg-primary);"><i class="fas fa-cookie me-2"></i>Productos Comprados</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($it->product_name) ?></td>
                                <td class="text-center"><?= $it->quantity ?></td>
                                <td class="text-end">$<?= number_format($it->price, 0, ',', '.') ?></td>
                                <td class="text-end fw-bold">$<?= number_format($it->subtotal, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-5">
                <div class="bg-light p-4 rounded-4">
                    <h6 class="fw-bold mb-3" style="color: var(--dg-primary);"><i class="fas fa-file-invoice me-2"></i>Detalles de Entrega & Pago</h6>
                    
                    <ul class="list-unstyled mb-3 small">
                        <li class="mb-2"><strong>Cliente:</strong> <?= esc($order->customer_name) ?></li>
                        <li class="mb-2"><strong>Teléfono:</strong> <?= esc($order->phone) ?></li>
                        <li class="mb-2"><strong>Tipo de entrega:</strong> <?= $order->delivery_type === 'recoger' ? '🏪 Recoger en Tienda' : '🛵 Domicilio' ?></li>
                        <?php if ($order->delivery_type === 'domicilio'): ?>
                            <li class="mb-2"><strong>Dirección:</strong> <?= esc($order->address) ?> <?= esc($order->neighborhood ? '('.$order->neighborhood.')' : '') ?></li>
                        <?php endif; ?>
                        <li class="mb-2"><strong>Método de pago:</strong> <?= esc($order->payment_method) ?></li>
                    </ul>

                    <div class="border-top pt-2">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span>Subtotal productos:</span>
                            <span>$<?= number_format($order->subtotal, 0, ',', '.') ?></span>
                        </div>
                        <?php if ($order->delivery_cost > 0): ?>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span>Costo de envío:</span>
                            <span>$<?= number_format($order->delivery_cost, 0, ',', '.') ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between fw-bold fs-5 text-success border-top pt-2">
                            <span>TOTAL:</span>
                            <span>$<?= number_format($order->total, 0, ',', '.') ?> COP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
