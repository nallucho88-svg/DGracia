<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
        <?= isset($coupon) ? 'Editar Cupón' : 'Nuevo Cupón' ?>
    </h2>
    <a href="<?= base_url('admin/cupones') ?>" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-arrow-left me-2"></i>Volver</a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger rounded-3">
                <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="<?= isset($coupon) ? base_url('admin/cupones/editar/' . $coupon->id) : base_url('admin/cupones/crear') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">Nombre de la Campaña *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $coupon->name ?? '') ?>" placeholder="Ej: Campaña Día de la Madre 2026" required>
                </div>

                <div class="col-md-6">
                    <label for="code" class="form-label fw-bold">Código del Cupón *</label>
                    <input type="text" class="form-control font-monospace fw-bold" id="code" name="code" value="<?= old('code', $coupon->code ?? '') ?>" placeholder="MADRE2026" required style="text-transform: uppercase;">
                </div>

                <div class="col-md-12">
                    <label for="description" class="form-label fw-bold">Descripción (Opcional)</label>
                    <input type="text" class="form-control" id="description" name="description" value="<?= old('description', $coupon->description ?? '') ?>" placeholder="Ej: 20% de descuento en toda la tienda para el día de las madres">
                </div>

                <div class="col-md-4">
                    <label for="discount_type" class="form-label fw-bold">Tipo de Descuento *</label>
                    <select class="form-select" id="discount_type" name="discount_type" required>
                        <option value="percentage" <?= old('discount_type', $coupon->discount_type ?? '') === 'percentage' ? 'selected' : '' ?>>Porcentaje (%)</option>
                        <option value="fixed" <?= old('discount_type', $coupon->discount_type ?? '') === 'fixed' ? 'selected' : '' ?>>Valor Fijo ($)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="value" class="form-label fw-bold">Valor *</label>
                    <input type="number" step="0.01" class="form-control" id="value" name="value" value="<?= old('value', $coupon->value ?? '') ?>" placeholder="Ej: 20 o 15000" required>
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label fw-bold">Estado *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="1" <?= old('status', $coupon->status ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= old('status', $coupon->status ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="form-label fw-bold">Fecha Inicio</label>
                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?= old('start_date', isset($coupon->start_date) ? date('Y-m-d\TH:i', strtotime($coupon->start_date)) : '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="form-label fw-bold">Fecha Fin</label>
                    <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?= old('end_date', isset($coupon->end_date) ? date('Y-m-d\TH:i', strtotime($coupon->end_date)) : '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="max_uses" class="form-label fw-bold">Usos Máximos Totales (0 = Ilimitado)</label>
                    <input type="number" class="form-control" id="max_uses" name="max_uses" value="<?= old('max_uses', $coupon->max_uses ?? 0) ?>">
                </div>

                <div class="col-md-6">
                    <label for="max_uses_per_user" class="form-label fw-bold">Usos Máximos por Cliente</label>
                    <input type="number" class="form-control" id="max_uses_per_user" name="max_uses_per_user" value="<?= old('max_uses_per_user', $coupon->max_uses_per_user ?? 1) ?>">
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="loyal_only" name="loyal_only" value="1" <?= old('loyal_only', $coupon->loyal_only ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="loyal_only">Exclusivo para Cliente Fiel</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="is_stackable" name="is_stackable" value="1" <?= old('is_stackable', $coupon->is_stackable ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold" for="is_stackable">Acumulable con otras ofertas</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-dg-primary rounded-pill px-4"><i class="fas fa-save me-2"></i>Guardar Cupón</button>
                <a href="<?= base_url('admin/cupones') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
