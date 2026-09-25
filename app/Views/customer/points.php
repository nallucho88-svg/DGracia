<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<!-- Billetera Digital Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-lg rounded-4 p-4 text-white h-100 position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #1e1414, #3e2723); border: 2px solid var(--dg-primary) !important;">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold">
                    <i class="fas fa-wallet me-1"></i> BILLETERA DE PUNTOS
                </span>
                <i class="fas fa-award fa-2x text-warning opacity-75"></i>
            </div>

            <div class="my-3 text-center">
                <span class="text-uppercase tracking-wider small opacity-75 d-block mb-1">Saldo Disponible</span>
                <h1 class="display-3 fw-bold text-warning mb-0" style="letter-spacing: 1px;">
                    <?= number_format($points, 0, ',', '.') ?>
                </h1>
                <span class="fw-bold tracking-widest text-white-50">PUNTOS DULCE GRACIA</span>
            </div>

            <div class="border-top border-white border-opacity-25 pt-3 mt-auto d-flex justify-content-between align-items-center">
                <span class="small opacity-75">Valor monetario equivalente:</span>
                <span class="fs-4 fw-bold text-success">$<?= number_format($monetaryEquivalent, 0, ',', '.') ?> COP</span>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="row g-3 h-100">
            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white text-center d-flex flex-column justify-content-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 mx-auto mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-plus-circle fa-lg"></i>
                    </div>
                    <span class="text-muted small fw-bold">Puntos Acumulados</span>
                    <h3 class="fw-bold text-success mb-0">+<?= number_format($earned, 0, ',', '.') ?></h3>
                </div>
            </div>

            <div class="col-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white text-center d-flex flex-column justify-content-center">
                    <div class="rounded-circle bg-danger-subtle text-danger p-3 mx-auto mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-minus-circle fa-lg"></i>
                    </div>
                    <span class="text-muted small fw-bold">Puntos Redimidos</span>
                    <h3 class="fw-bold text-danger mb-0">-<?= number_format($spent, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reglas de Puntos & Historial -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
            <i class="fas fa-history me-2"></i>Historial de Movimientos
        </h5>
        <a href="<?= base_url('mi-cuenta/metas') ?>" class="btn btn-sm btn-outline-warning text-dark rounded-pill">
            <i class="fas fa-bullseye me-1"></i>Ver mis metas
        </a>
    </div>
    <div class="card-body p-4">
        <?php if (empty($transactions)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-coins fa-4x mb-3 opacity-25"></i>
                <p class="mb-0">Aún no registras movimientos de puntos.</p>
                <small>Por cada $1.000 COP en compras acumulas puntos para redimir descuentos.</small>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto / Descripción</th>
                            <th>Tipo</th>
                            <th class="text-end">Puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td class="small"><?= date('d/m/Y h:i A', strtotime($tx->created_at)) ?></td>
                            <td class="fw-bold"><?= esc($tx->description ?: 'Transacción de puntos') ?></td>
                            <td>
                                <?php if ($tx->type === 'earned'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Ganados</span>
                                <?php elseif ($tx->type === 'spent'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3">Redimidos</span>
                                <?php elseif ($tx->type === 'expired'): ?>
                                    <span class="badge bg-secondary rounded-pill px-3">Vencidos</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info border border-info rounded-pill px-3">Ajuste</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold <?= $tx->points > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $tx->points > 0 ? '+' . number_format($tx->points, 0, ',', '.') : number_format($tx->points, 0, ',', '.') ?> pts
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
