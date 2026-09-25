<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
            <i class="fas fa-box-open me-2"></i>Historial de Pedidos
        </h5>
    </div>
    <div class="card-body p-4">
        <?php if (empty($orders)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-shopping-bag fa-4x mb-3 opacity-25"></i>
                <h5>Aún no tienes pedidos registrados</h5>
                <p class="small">Explora nuestro delicioso catálogo y haz tu primer pedido.</p>
                <a href="<?= base_url('productos') ?>" class="btn btn-dg-primary rounded-pill px-4 py-2 mt-2">
                    <i class="fas fa-store me-2"></i>Ver Productos
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Entrega</th>
                            <th>Método Pago</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($ord->order_number) ?></td>
                            <td class="small"><?= date('d/m/Y h:i A', strtotime($ord->created_at)) ?></td>
                            <td class="small">
                                <span class="badge bg-light text-dark border">
                                    <?= $ord->delivery_type === 'recoger' ? '🏪 Recoger en Tienda' : '🛵 Domicilio' ?>
                                </span>
                            </td>
                            <td class="small text-uppercase"><?= esc($ord->payment_method) ?></td>
                            <td class="fw-bold text-success">$<?= number_format($ord->total, 0, ',', '.') ?></td>
                            <td>
                                <?php 
                                $badgeClass = 'bg-secondary';
                                if ($ord->status === 'pendiente') $badgeClass = 'bg-warning text-dark';
                                elseif ($ord->status === 'en preparacion') $badgeClass = 'bg-info text-dark';
                                elseif ($ord->status === 'enviado') $badgeClass = 'bg-primary';
                                elseif ($ord->status === 'entregado') $badgeClass = 'bg-success';
                                elseif ($ord->status === 'cancelado') $badgeClass = 'bg-danger';
                                ?>
                                <span class="badge <?= $badgeClass ?> rounded-pill px-3"><?= ucfirst($ord->status) ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('mi-cuenta/pedidos/ver/' . $ord->order_number) ?>" class="btn btn-sm btn-dg-primary rounded-pill px-3">
                                    <i class="fas fa-route me-1"></i>Seguimiento
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
