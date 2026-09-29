<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Valoraciones de Productos</h2>
        <small class="text-muted">Administración y moderación de opiniones de clientes</small>
    </div>
</div>

<!-- Filtros de búsqueda -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="<?= base_url('admin/valoraciones') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold small text-muted">Producto</label>
                <select name="product_id" class="form-select rounded-3">
                    <option value="">-- Todos los productos --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $productFilter == $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label fw-bold small text-muted">Estrellas</label>
                <select name="rating" class="form-select rounded-3">
                    <option value="">-- Todas --</option>
                    <?php for($st = 5; $st >= 1; $st--): ?>
                        <option value="<?= $st ?>" <?= $ratingFilter == $st ? 'selected' : '' ?>>
                            <?= $st ?> ★
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label fw-bold small text-muted">Estado</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">Visibles y Ocultas</option>
                    <option value="visible" <?= $statusFilter === 'visible' ? 'selected' : '' ?>>Solo Visibles</option>
                    <option value="oculta" <?= $statusFilter === 'oculta' ? 'selected' : '' ?>>Solo Ocultas</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-bold small text-muted">Buscar</label>
                <input type="text" name="search" class="form-control rounded-3" placeholder="Cliente, comentario o pedido..." value="<?= esc($searchFilter) ?>">
            </div>
            <div class="col-md-2 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-dg-primary rounded-pill px-3 w-100 fw-bold">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?= base_url('admin/valoraciones') ?>" class="btn btn-outline-secondary rounded-pill px-3" title="Limpiar">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Valoraciones -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cliente</th>
                        <th>Pedido</th>
                        <th class="text-center">Calificación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ratings)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No se encontraron valoraciones registradas.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($ratings as $r): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= media_url($r['product_image'] ?? '', 'assets/img/default-product.jpg') ?>" 
                                     class="rounded-3" style="width:48px;height:48px;object-fit:cover;" alt="Producto">
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($r['product_name'] ?? 'Producto no encontrado') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold"><?= esc($r['customer_name'] ?? 'Cliente') ?></div>
                            <small class="text-muted"><?= esc($r['customer_email'] ?? '') ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-bold">#<?= esc($r['order_number'] ?? 'N/A') ?></span>
                        </td>
                        <td class="text-center">
                            <div class="text-warning fs-6">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= $r['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <small class="fw-bold text-muted"><?= $r['rating'] ?> / 5</small>
                        </td>
                        <td style="max-width:250px;">
                            <?php if (!empty($r['comment'])): ?>
                                <span class="fst-italic">"<?= esc($r['comment']) ?>"</span>
                            <?php else: ?>
                                <span class="text-muted small">Sin comentario escrito</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= date('d/m/Y h:i A', strtotime($r['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'visible'): ?>
                                <span class="badge bg-success rounded-pill px-3">Visible</span>
                            <?php elseif ($r['status'] === 'oculta'): ?>
                                <span class="badge bg-secondary rounded-pill px-3">Oculta</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill px-3">Eliminada</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                    <?php if ($r['status'] === 'visible'): ?>
                                        <li>
                                            <a class="dropdown-item text-warning" href="<?= base_url('admin/valoraciones/estado/' . $r['id'] . '/oculta') ?>">
                                                <i class="fas fa-eye-slash me-2"></i>Ocultar
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li>
                                            <a class="dropdown-item text-success" href="<?= base_url('admin/valoraciones/estado/' . $r['id'] . '/visible') ?>">
                                                <i class="fas fa-eye me-2"></i>Mostrar
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= base_url('admin/valoraciones/eliminar/' . $r['id']) ?>" 
                                           onclick="return confirm('¿Estás seguro de eliminar esta valoración?')">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
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
