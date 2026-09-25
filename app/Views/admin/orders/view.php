<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center mb-4">
    <a href="<?= base_url('admin/pedidos') ?>" class="btn btn-light rounded-circle me-3">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
        Detalle del Pedido <span class="text-muted">#<?= esc($order->order_number) ?></span>
    </h2>
    <div class="ms-auto">
        <span class="badge bg-secondary fs-6 p-2 rounded-pill">
            Estado: <?= ucfirst($order->status) ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Lista de Productos -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-shopping-basket text-primary me-2"></i>Productos Solicitados</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Precio Unit.</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($item->product_name) ?></td>
                                <td class="text-center text-muted">$<?= number_format($item->price, 0, ',', '.') ?></td>
                                <td class="text-center"><span class="badge bg-light text-dark border px-3 py-2"><?= $item->quantity ?></span></td>
                                <td class="text-end fw-bold">$<?= number_format($item->subtotal, 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top-0">
                            <tr>
                                <td colspan="3" class="text-end text-muted pb-0 pt-3">Subtotal Productos:</td>
                                <td class="text-end fw-bold pb-0 pt-3">$<?= number_format($order->subtotal, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted pb-0 pt-2">Costo de Envío:</td>
                                <td class="text-end fw-bold pb-0 pt-2">$<?= number_format($order->delivery_cost, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fs-5 pb-0 pt-3"><strong>TOTAL PAGADO:</strong></td>
                                <td class="text-end fs-5 pb-0 pt-3" style="color: var(--dg-primary);"><strong>$<?= number_format($order->total, 0, ',', '.') ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php if(!empty($order->notes)): ?>
        <div class="card border-0 shadow-sm rounded-4 border-start border-warning border-5">
            <div class="card-body p-4">
                <h6 class="fw-bold text-warning mb-2"><i class="fas fa-comment-dots me-2"></i>Notas del Cliente:</h6>
                <p class="mb-0 fst-italic text-muted"><?= esc($order->notes) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Panel Lateral Cliente -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 bg-light rounded-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-user-circle text-primary me-2"></i>Información del Cliente</h5>
                
                <p class="mb-2"><strong class="text-muted d-block">Nombre:</strong> <?= esc($order->customer_name) ?></p>
                <p class="mb-2"><strong class="text-muted d-block">Teléfono:</strong> 
                    <a href="tel:<?= esc($order->phone) ?>" class="text-decoration-none"><?= esc($order->phone) ?></a>
                </p>
                <?php if(!empty($order->whatsapp)): ?>
                <p class="mb-3"><strong class="text-muted d-block">WhatsApp:</strong> 
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order->whatsapp) ?>" target="_blank" class="text-success text-decoration-none fw-bold">
                        <i class="fab fa-whatsapp me-1"></i><?= esc($order->whatsapp) ?>
                    </a>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-truck text-primary me-2"></i>Datos de Entrega</h5>
                
                <p class="mb-2"><strong class="text-muted d-block">Tipo:</strong> 
                    <?php if($order->delivery_type == 'domicilio'): ?>
                        <span class="badge bg-info"><i class="fas fa-motorcycle me-1"></i> Domicilio</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><i class="fas fa-store me-1"></i> Recoger en Tienda</span>
                    <?php endif; ?>
                </p>
                
                <?php if($order->delivery_type == 'domicilio'): ?>
                    <p class="mb-2"><strong class="text-muted d-block">Dirección:</strong> <?= esc($order->address) ?></p>
                    <?php if(!empty($order->neighborhood)): ?>
                        <p class="mb-2"><strong class="text-muted d-block">Barrio:</strong> <?= esc($order->neighborhood) ?></p>
                    <?php endif; ?>
                    <?php if($zone): ?>
                        <p class="mb-2"><strong class="text-muted d-block">Zona:</strong> <?= esc($zone->name) ?></p>
                    <?php endif; ?>
                    <?php if(!empty($order->delivery_instructions)): ?>
                        <p class="mb-0"><strong class="text-muted d-block">Instrucciones de llegada:</strong> <?= esc($order->delivery_instructions) ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="mt-4 pt-3 border-top">
                    <p class="mb-0"><strong class="text-muted d-block">Método de Pago Elegido:</strong> 
                        <span class="fw-bold text-dark"><?= esc($order->payment_method) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
