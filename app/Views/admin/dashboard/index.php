<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Dashboard</h2>
        <small class="text-muted">Resumen ejecutivo · <?= date('d \d\e F, Y') ?></small>
    </div>
    <a href="<?= base_url('admin/pedidos') ?>" class="btn btn-dg-primary rounded-pill px-4">
        <i class="fas fa-shopping-bag me-2"></i>Ver todos los pedidos
    </a>
</div>

<!-- ══════════════════════════════════════════════════════════
     FILTRO POR RANGO DE FECHAS (00:00:00 - 23:59:59 America/Bogota)
═══════════════════════════════════════════════════════════ -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="<?= base_url('admin/dashboard') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label for="start_date" class="form-label fw-bold small text-muted">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i>Fecha Inicial (00:00:00)
                </label>
                <input type="date" class="form-control rounded-3" id="start_date" name="start_date" value="<?= esc($startDate ?? '') ?>">
            </div>
            <div class="col-md-4 col-sm-6">
                <label for="end_date" class="form-label fw-bold small text-muted">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i>Fecha Final (23:59:59)
                </label>
                <input type="date" class="form-control rounded-3" id="end_date" name="end_date" value="<?= esc($endDate ?? '') ?>">
            </div>
            <div class="col-md-4 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-dg-primary rounded-pill px-4 flex-grow-1 fw-bold">
                    <i class="fas fa-filter me-2"></i>Aplicar Filtro
                </button>
                <?php if ($isFiltered): ?>
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary rounded-pill px-3" title="Limpiar filtro">
                    <i class="fas fa-undo me-1"></i>Limpiar
                </a>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($isFiltered): ?>
        <div class="alert alert-info border-0 rounded-3 mb-0 mt-3 d-flex align-items-center justify-content-between py-2 px-3 small">
            <div>
                <i class="fas fa-info-circle me-2"></i>
                <strong>Filtro Activo:</strong> Visualizando datos desde 
                <strong><?= !empty($startDate) ? date('d/m/Y', strtotime($startDate)) : 'el inicio' ?></strong> 
                hasta 
                <strong><?= !empty($endDate) ? date('d/m/Y', strtotime($endDate)) : 'la fecha' ?></strong> 
                <span class="text-muted">(America/Bogota: 00:00:00 - 23:59:59)</span>
            </div>
            <a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none fw-bold ms-2 text-info">Restablecer</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     FILA 1 — KPIs Principales
═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Ventas Hoy -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#d4edda,#a8d5b5);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-sun fa-lg me-2 text-success"></i>
                    <small class="fw-bold text-success text-uppercase">Hoy</small>
                </div>
                <h4 class="fw-bold mb-0 text-success">$<?= number_format($salesToday, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- Ventas Semana -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#d1ecf1,#a0d2e8);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-calendar-week fa-lg me-2 text-info"></i>
                    <small class="fw-bold text-info text-uppercase">Semana</small>
                </div>
                <h4 class="fw-bold mb-0 text-info">$<?= number_format($salesWeek, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- Ventas Mes -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#fff3cd,#ffd99a);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-calendar-alt fa-lg me-2 text-warning"></i>
                    <small class="fw-bold text-warning text-uppercase">Mes</small>
                </div>
                <h4 class="fw-bold mb-0 text-warning">$<?= number_format($salesMonth, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- Ventas Año -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#e2d9f3,#c5b3e6);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-chart-line fa-lg me-2 text-purple"></i>
                    <small class="fw-bold text-uppercase" style="color:#6f42c1;">Año</small>
                </div>
                <h4 class="fw-bold mb-0" style="color:#6f42c1;">$<?= number_format($salesYear, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>

    <!-- Pedidos Pendientes -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#f8d7da,#f5b7be);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-clock fa-lg me-2 text-danger"></i>
                    <small class="fw-bold text-danger text-uppercase">Pendientes</small>
                </div>
                <h4 class="fw-bold mb-0 text-danger"><?= $pendingOrders ?></h4>
            </div>
        </div>
    </div>

    <!-- Clientes -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#fde8d8,#f9c9a8);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-users fa-lg me-2 text-warning"></i>
                    <small class="fw-bold text-uppercase" style="color:#d4a373;">Clientes</small>
                </div>
                <h4 class="fw-bold mb-0" style="color:#d4a373;"><?= $totalCustomers ?></h4>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════════
     FILA 2 — Contadores grandes + Pedidos por Estado
═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3" style="background:rgba(212,163,115,.15);">
                    <i class="fas fa-shopping-bag fa-2x" style="color:var(--dg-primary);"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase">Total Pedidos</h6>
                    <h2 class="fw-bold mb-0"><?= $totalOrders ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 bg-success bg-opacity-10">
                    <i class="fas fa-dollar-sign fa-2x text-success"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase">Ventas Totales</h6>
                    <h2 class="fw-bold mb-0 text-success">$<?= number_format($salesTotal, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 bg-info bg-opacity-10">
                    <i class="fas fa-box fa-2x text-info"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase">Productos</h6>
                    <h2 class="fw-bold mb-0"><?= $totalProducts ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Dona: Pedidos por Estado -->
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-3 px-4 pb-0">
                <h6 class="fw-bold mb-0 small text-uppercase text-muted">
                    <i class="fas fa-chart-pie me-1" style="color:var(--dg-primary);"></i> Por Estado
                </h6>
            </div>
            <div class="card-body p-3 d-flex align-items-center justify-content-center">
                <canvas id="statusChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     FILA 3 — Gráficos Chart.js
═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Ventas Mensuales -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-chart-bar me-2" style="color:var(--dg-primary);"></i>Ventas Mensuales
                </h5>
                <div class="d-flex gap-3">
                    <?php if ($bestMonth): ?>
                    <span class="badge rounded-pill" style="background:rgba(40,167,69,.15); color:#198754;">
                        🏆 Mejor: <?= date('M Y', strtotime($bestMonth . '-01')) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($worstMonth): ?>
                    <span class="badge rounded-pill" style="background:rgba(220,53,69,.12); color:#dc3545;">
                        📉 Menor: <?= date('M Y', strtotime($worstMonth . '-01')) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="salesChart" height="90"></canvas>
            </div>
        </div>
    </div>

    <!-- Ganancias Mensuales -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-hand-holding-usd me-2 text-success"></i>Ganancias
                </h5>
                <small class="text-muted">Ingresos - Costo de producto</small>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="profitChart" height="190"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════════
     FILA 3.5 — Comparativo Web vs Caja (POS)
═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-balance-scale me-2 text-primary"></i>Ventas Comparativas por Canal: Tienda Web vs Caja / POS
                    </h5>
                    <small class="text-muted">Desglose diario acumulado en el mes actual</small>
                </div>
                <div class="d-flex gap-3 small fw-bold">
                    <span class="text-primary"><i class="fas fa-globe me-1"></i>Tienda Web</span>
                    <span class="text-warning"><i class="fas fa-cash-register me-1"></i>Caja / POS</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="webVsPosChart" height="90"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     FILA 4 — Top Productos
═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">

    <!-- Más Vendido -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-success border-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase text-muted small mb-3">
                    <i class="fas fa-trophy me-2 text-warning"></i>Producto Más Vendido
                </h6>
                <?php if ($bestSellingProduct): ?>
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= media_url($bestSellingProduct->image_path ?? '', 'assets/img/default-product.jpg') ?>"
                             class="rounded-3" style="width:64px;height:64px;object-fit:cover;" alt="<?= esc($bestSellingProduct->name) ?>">
                        <div>
                            <div class="fw-bold"><?= esc($bestSellingProduct->name) ?></div>
                            <div class="text-muted small"><?= $bestSellingProduct->total_qty ?> unidades</div>
                            <div class="text-success fw-bold">$<?= number_format($bestSellingProduct->total_revenue, 0, ',', '.') ?> en ventas</div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Sin datos aún</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Menos Vendido -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-warning border-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase text-muted small mb-3">
                    <i class="fas fa-arrow-down me-2 text-warning"></i>Producto Menos Vendido
                </h6>
                <?php if ($leastSellingProduct): ?>
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= media_url($leastSellingProduct->image_path ?? '', 'assets/img/default-product.jpg') ?>"
                             class="rounded-3" style="width:64px;height:64px;object-fit:cover;" alt="<?= esc($leastSellingProduct->name) ?>">
                        <div class="flex-grow-1">
                            <div class="fw-bold"><?= esc($leastSellingProduct->name) ?></div>
                            <div class="text-muted small"><?= $leastSellingProduct->total_qty ?> unidades</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="<?= base_url('admin/descuentos/crear?product_id=' . $leastSellingProduct->id) ?>"
                           class="btn btn-sm btn-warning rounded-pill w-100">
                            <i class="fas fa-percentage me-1"></i>Crear Promoción
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Sin datos aún</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Más Rentable -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-info border-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase text-muted small mb-3">
                    <i class="fas fa-gem me-2 text-info"></i>Producto Más Rentable
                </h6>
                <?php if ($mostProfitableProduct): ?>
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= media_url($mostProfitableProduct->image_path ?? '', 'assets/img/default-product.jpg') ?>"
                             class="rounded-3" style="width:64px;height:64px;object-fit:cover;" alt="<?= esc($mostProfitableProduct->name) ?>">
                        <div>
                            <div class="fw-bold"><?= esc($mostProfitableProduct->name) ?></div>
                            <div class="text-muted small"><?= $mostProfitableProduct->total_qty ?> unidades</div>
                            <div class="text-info fw-bold">$<?= number_format($mostProfitableProduct->total_profit, 0, ',', '.') ?> de ganancia</div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Sin datos aún</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════════
     FILA 5 — Últimos Pedidos
═══════════════════════════════════════════════════════════ -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">
            <i class="fas fa-list-alt me-2" style="color:var(--dg-primary);"></i>Últimos Pedidos
        </h5>
        <a href="<?= base_url('admin/pedidos') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Ver todos</a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nº Orden</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">No hay pedidos recientes.</td></tr>
                    <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                    <?php
                        $statusMap = [
                            'pendiente'      => ['bg-warning text-dark', 'Pendiente'],
                            'en preparacion' => ['bg-info',              'En preparación'],
                            'enviado'        => ['bg-primary',           'Enviado'],
                            'entregado'      => ['bg-success',           'Entregado'],
                            'cancelado'      => ['bg-danger',            'Cancelado'],
                        ];
                        [$badgeClass, $statusLabel] = $statusMap[$order->status] ?? ['bg-secondary', ucfirst($order->status)];
                    ?>
                    <tr>
                        <td><span class="badge bg-light text-dark border fw-bold"><?= esc($order->order_number) ?></span></td>
                        <td><?= esc($order->customer_name) ?></td>
                        <td class="text-muted small"><?= date('d M Y, h:i a', strtotime($order->created_at)) ?></td>
                        <td><span class="badge <?= $badgeClass ?> rounded-pill"><?= $statusLabel ?></span></td>
                        <td class="text-end fw-bold text-success">$<?= number_format($order->total, 0, ',', '.') ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('admin/pedidos/ver/' . $order->id) ?>" class="btn btn-sm btn-light rounded-circle" title="Ver detalle">
                                <i class="fas fa-eye text-primary"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels  = <?= json_encode($monthLabels) ?>;
    const sales   = <?= json_encode($salesByMonth) ?>;
    const profits = <?= json_encode($profitByMonth) ?>;

    // ── 1. Gráfico de Ventas Mensuales (Barras) ──────────────────────────
    const bestIdx  = sales.indexOf(Math.max(...sales));
    const worstIdx = sales.indexOf(Math.min(...sales));

    const barColors = sales.map((_, i) => {
        if (i === bestIdx)  return 'rgba(40,167,69,.85)';
        if (i === worstIdx) return 'rgba(220,53,69,.75)';
        return 'rgba(212,163,115,.7)';
    });

    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Ventas ($)',
                data: sales,
                backgroundColor: barColors,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-CO')
                    }
                }
            },
            scales: {
                y: {
                    ticks: { callback: v => '$' + v.toLocaleString('es-CO') },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // ── 2. Gráfico de Ganancias (Línea rellena) ──────────────────────────
    new Chart(document.getElementById('profitChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Ganancia ($)',
                data: profits,
                fill: true,
                backgroundColor: 'rgba(25,135,84,.1)',
                borderColor: 'rgba(25,135,84,.85)',
                borderWidth: 2.5,
                tension: 0.45,
                pointBackgroundColor: 'rgba(25,135,84,.9)',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' $' + ctx.parsed.y.toLocaleString('es-CO')
                    }
                }
            },
            scales: {
                y: {
                    ticks: { callback: v => '$' + v.toLocaleString('es-CO') },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    ticks: { maxRotation: 45, font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });

    // ── 3. Dona — Pedidos por Estado ────────────────────────────────────
    const statusData = <?= json_encode($ordersByStatus) ?>;
    const statusLabels = statusData.map(r => r.status.charAt(0).toUpperCase() + r.status.slice(1));
    const statusCounts = statusData.map(r => parseInt(r.cnt));
    const statusColors = ['#ffc107','#0dcaf0','#0d6efd','#198754','#dc3545','#6c757d'];

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusColors.slice(0, statusLabels.length),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
            }
        }
    });

    // ── 4. Gráfico Comparativo Web vs Caja / POS ─────────────────────────
    const dailyLabels = <?= json_encode($dailyLabels ?? []) ?>;
    const dailyWeb    = <?= json_encode($dailyWebSales ?? []) ?>;
    const dailyPos    = <?= json_encode($dailyPosSales ?? []) ?>;

    new Chart(document.getElementById('webVsPosChart'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [
                {
                    label: 'Tienda Web ($)',
                    data: dailyWeb,
                    borderColor: 'rgba(13, 110, 253, 0.9)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3
                },
                {
                    label: 'Caja / POS ($)',
                    data: dailyPos,
                    borderColor: 'rgba(255, 193, 7, 0.9)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': $' + ctx.parsed.y.toLocaleString('es-CO')
                    }
                }
            },
            scales: {
                y: {
                    ticks: { callback: v => '$' + v.toLocaleString('es-CO') },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    grid: { display: false },
                    title: { display: true, text: 'Día del Mes' }
                }
            }
        }
    });
})();
</script>
<?= $this->endSection() ?>
