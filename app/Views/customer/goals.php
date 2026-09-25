<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-1" style="color: var(--dg-primary);">
            <i class="fas fa-bullseye me-2"></i>Mis Metas & Niveles de Fidelidad
        </h5>
        <p class="text-muted small mb-0">Alcanza las metas de puntos y desbloquea cupones y beneficios exclusivos.</p>
    </div>
    
    <div class="card-body p-4">
        
        <div class="alert alert-warning border-0 rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-coins fa-3x me-3 text-warning"></i>
                <div>
                    <h5 class="fw-bold mb-1">Tu Saldo Actual: <?= number_format($points, 0, ',', '.') ?> Puntos</h5>
                    <span class="small text-dark opacity-75">Continúa realizando compras para subir de nivel.</span>
                </div>
            </div>
            <a href="<?= base_url('productos') ?>" class="btn btn-dark rounded-pill px-4">
                <i class="fas fa-shopping-cart me-1"></i>Comprar Ahora
            </a>
        </div>

        <?php if (empty($goals)): ?>
            <!-- Metas Predeterminadas si no hay registradas en BD -->
            <?php 
            $defaultGoals = [
                ['title' => 'Nivel 1 — Dulce Inicio', 'points' => 100, 'reward' => 'Cupón de $5.000 COP'],
                ['title' => 'Nivel 2 — Repostero Fiel', 'points' => 500, 'reward' => 'Cupón de $10.000 COP'],
                ['title' => 'Nivel 3 — Amante del Chocolate', 'points' => 1000, 'reward' => '15% de Descuento Especial'],
                ['title' => 'Nivel 4 — Maestro Pastelero', 'points' => 2500, 'reward' => 'Producto Gratis']
            ];
            ?>
            <div class="row g-4">
                <?php foreach ($defaultGoals as $g): 
                    $req = $g['points'];
                    $pct = min(100, round(($points / $req) * 100));
                    $isAchieved = $points >= $req;
                    $rem = max(0, $req - $points);
                ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 <?= $isAchieved ? 'border-success border border-2 bg-success-subtle' : 'bg-white' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0" style="color: var(--dg-primary);"><?= esc($g['title']) ?></h6>
                            <?php if ($isAchieved): ?>
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i>¡ALCANZADA!</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border"><?= number_format($req, 0, ',', '.') ?> pts</span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progreso: <?= number_format($points, 0, ',', '.') ?> / <?= number_format($req, 0, ',', '.') ?> pts</span>
                                <span class="fw-bold <?= $isAchieved ? 'text-success' : 'text-primary' ?>"><?= $pct ?>%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 12px;">
                                <div class="progress-bar <?= $isAchieved ? 'bg-success' : 'bg-warning' ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>

                        <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                            <small class="text-muted">Premio: <strong><?= esc($g['reward']) ?></strong></small>
                            <?php if (!$isAchieved): ?>
                                <small class="text-danger fw-bold">Faltan <?= number_format($rem, 0, ',', '.') ?> pts</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($goals as $g): 
                    $req = $g->points_required;
                    $pct = min(100, round(($points / $req) * 100));
                    $isAchieved = $points >= $req;
                    $rem = max(0, $req - $points);
                ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 <?= $isAchieved ? 'border-success border border-2 bg-success-subtle' : 'bg-white' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0" style="color: var(--dg-primary);"><?= esc($g->title) ?></h6>
                            <?php if ($isAchieved): ?>
                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i>¡ALCANZADA!</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border"><?= number_format($req, 0, ',', '.') ?> pts</span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progreso: <?= number_format($points, 0, ',', '.') ?> / <?= number_format($req, 0, ',', '.') ?> pts</span>
                                <span class="fw-bold <?= $isAchieved ? 'text-success' : 'text-primary' ?>"><?= $pct ?>%</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 12px;">
                                <div class="progress-bar <?= $isAchieved ? 'bg-success' : 'bg-warning' ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>

                        <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                            <small class="text-muted">Premio: <strong><?= esc($g->reward_value) ?></strong></small>
                            <?php if (!$isAchieved): ?>
                                <small class="text-danger fw-bold">Faltan <?= number_format($rem, 0, ',', '.') ?> pts</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
