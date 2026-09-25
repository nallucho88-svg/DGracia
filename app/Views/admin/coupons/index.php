<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Cupones y Fechas Especiales</h2>
    <a href="<?= base_url('admin/cupones/crear') ?>" class="btn btn-dg-primary rounded-pill"><i class="fas fa-plus me-2"></i>Nuevo Cupón</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre / Campaña</th>
                        <th>Descuento</th>
                        <th>Usos</th>
                        <th>Vigencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($coupons)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay cupones registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($coupons as $c): ?>
                        <tr>
                            <td><span class="badge bg-dark fs-6 px-3 py-2"><?= esc($c->code) ?></span></td>
                            <td class="fw-bold"><?= esc($c->name) ?></td>
                            <td class="fw-bold text-success">
                                <?= $c->discount_type === 'percentage' ? (float)$c->value . '%' : '$' . number_format($c->value, 0, ',', '.') ?>
                            </td>
                            <td>
                                <?= $c->used_count ?> / <?= $c->max_uses > 0 ? $c->max_uses : '∞' ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= $c->start_date ? date('d/m/Y', strtotime($c->start_date)) : 'Inmediato' ?> 
                                    ➔ 
                                    <?= $c->end_date ? date('d/m/Y', strtotime($c->end_date)) : 'Indefinido' ?>
                                </small>
                            </td>
                            <td>
                                <?= $c->status ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>' ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/cupones/editar/' . $c->id) ?>" class="btn btn-sm btn-light rounded-circle me-1"><i class="fas fa-edit text-primary"></i></a>
                                <a href="<?= base_url('admin/cupones/eliminar/' . $c->id) ?>" class="btn btn-sm btn-light rounded-circle" onclick="return confirm('¿Seguro de eliminar este cupón?')"><i class="fas fa-trash text-danger"></i></a>
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
