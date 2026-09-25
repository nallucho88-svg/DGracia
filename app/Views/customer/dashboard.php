<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<!-- Tarjetas de Resumen KPI -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Mis Pedidos</span>
                    <h3 class="fw-bold mb-0" style="color: var(--dg-primary);"><?= $ordersCount ?></h3>
                </div>
                <div class="rounded-circle p-3 bg-light" style="color: var(--dg-primary);">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Mis Puntos</span>
                    <h3 class="fw-bold mb-0 text-warning"><?= number_format($userPoints, 0, ',', '.') ?></h3>
                </div>
                <div class="rounded-circle p-3 bg-warning-subtle text-warning">
                    <i class="fas fa-coins fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Favoritos</span>
                    <h3 class="fw-bold mb-0 text-danger"><?= $favoritesCount ?></h3>
                </div>
                <div class="rounded-circle p-3 bg-danger-subtle text-danger">
                    <i class="fas fa-heart fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Meta Actual</span>
                    <h3 class="fw-bold mb-0 text-success"><?= $goalProgress ?>%</h3>
                </div>
                <div class="rounded-circle p-3 bg-success-subtle text-success">
                    <i class="fas fa-bullseye fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjeta Billetera Digital de Puntos -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-white h-100" style="background: linear-gradient(135deg, #2b1810, #3e2723);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold">
                    <i class="fas fa-wallet me-1"></i> BILLETERA DE PUNTOS
                </span>
                <i class="fas fa-cookie-bite fa-2x opacity-50"></i>
            </div>
            
            <div class="my-3 text-center">
                <h1 class="display-4 fw-bold text-warning mb-0"><?= number_format($userPoints, 0, ',', '.') ?></h1>
                <span class="text-uppercase tracking-wider small opacity-75">PUNTOS ACUMULADOS</span>
            </div>

            <div class="border-top border-white border-opacity-25 pt-3 d-flex justify-content-between align-items-center">
                <span class="small opacity-75">Valor monetario equivalente:</span>
                <span class="fs-5 fw-bold text-success">$<?= number_format($userPoints * (float)setting('point_monetary_value', '500'), 0, ',', '.') ?> COP</span>
            </div>
        </div>
    </div>

    <!-- Próxima Meta -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
                    <i class="fas fa-bullseye me-2"></i>PRÓXIMA META
                </h5>
                <?php if ($nextGoal): ?>
                    <span class="badge bg-light text-dark border fw-bold"><?= esc($nextGoal->title) ?></span>
                <?php endif; ?>
            </div>

            <?php if ($nextGoal): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted"><?= number_format($userPoints, 0, ',', '.') ?> / <?= number_format($nextGoal->points_required, 0, ',', '.') ?> pts</span>
                        <span class="small fw-bold text-success"><?= $goalProgress ?>%</span>
                    </div>
                    <div class="progress rounded-pill" style="height: 12px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $goalProgress ?>%"></div>
                    </div>
                </div>

                <p class="small text-muted mb-3">
                    <?php 
                    $remaining = max(0, $nextGoal->points_required - $userPoints);
                    if ($remaining > 0): ?>
                        Te faltan <strong><?= number_format($remaining, 0, ',', '.') ?> puntos</strong> para desbloquear: 
                        <span class="badge bg-success-subtle text-success border border-success"><?= esc($nextGoal->reward_value) ?></span>
                    <?php else: ?>
                        ¡Felicidades! Has alcanzado esta meta. Puedes redimir tu beneficio en tu próxima compra.
                    <?php endif; ?>
                </p>

                <a href="<?= base_url('mi-cuenta/metas') ?>" class="btn btn-outline-primary btn-sm rounded-pill mt-auto">
                    Ver todas mis metas <i class="fas fa-arrow-right ms-1"></i>
                </a>
            <?php else: ?>
                <p class="text-muted">¡Eres un súper cliente de Dulce Gracia! No tienes metas pendientes.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Pedidos Recientes -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
            <i class="fas fa-clock me-2"></i>Últimos Pedidos
        </h5>
        <a href="<?= base_url('mi-cuenta/pedidos') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">Ver todos</a>
    </div>
    <div class="card-body p-4">
        <?php if (empty($recentOrders)): ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-shopping-basket fa-3x mb-2 opacity-25"></i>
                <p class="mb-0">Aún no has realizado pedidos.</p>
                <a href="<?= base_url('productos') ?>" class="btn btn-dg-primary btn-sm rounded-pill mt-2">Ir a la Tienda</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $ord): ?>
                        <tr>
                            <td class="fw-bold">#<?= esc($ord->order_number) ?></td>
                            <td><?= date('d/m/Y h:i A', strtotime($ord->created_at)) ?></td>
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
                                <a href="<?= base_url('mi-cuenta/pedidos/ver/' . $ord->order_number) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-eye me-1"></i>Seguimiento
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
