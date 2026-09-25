<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Gestión de Ofertas y Descuentos</h2>
    <a href="<?= base_url('admin/descuentos/crear') ?>" class="btn btn-dg-primary rounded-pill"><i class="fas fa-plus me-2"></i>Nueva Oferta</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Descuento</th>
                        <th>Vigencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($discounts)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay ofertas registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($discounts as $d): ?>
                        <tr>
                            <td class="fw-bold"><?= esc($d->title) ?></td>
                            <td>
                                <?php
                                    $typeBadge = 'bg-secondary';
                                    $typeLabel = 'General';
                                    if ($d->type === 'product') { $typeBadge = 'bg-primary'; $typeLabel = 'Producto'; }
                                    elseif ($d->type === 'category') { $typeBadge = 'bg-info text-dark'; $typeLabel = 'Categoría'; }
                                    elseif ($d->type === 'loyalty') { $typeBadge = 'bg-warning text-dark'; $typeLabel = 'Cliente Fiel'; }
                                ?>
                                <span class="badge <?= $typeBadge ?>"><?= $typeLabel ?></span>
                            </td>
                            <td class="fw-bold text-success">
                                <?= $d->discount_type === 'percentage' ? (float)$d->value . '%' : '$' . number_format($d->value, 0, ',', '.') ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= $d->start_date ? date('d/m/Y', strtotime($d->start_date)) : 'Inicio inmed.' ?> 
                                    ➔ 
                                    <?= $d->end_date ? date('d/m/Y', strtotime($d->end_date)) : 'Sin expiración' ?>
                                </small>
                            </td>
                            <td>
                                <?= $d->status ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>' ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/descuentos/editar/' . $d->id) ?>" class="btn btn-sm btn-light rounded-circle me-1"><i class="fas fa-edit text-primary"></i></a>
                                <a href="<?= base_url('admin/descuentos/eliminar/' . $d->id) ?>" class="btn btn-sm btn-light rounded-circle" onclick="return confirm('¿Seguro de eliminar esta oferta?')"><i class="fas fa-trash text-danger"></i></a>
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
