<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
        <?= isset($discount) ? 'Editar Oferta' : 'Nueva Oferta' ?>
    </h2>
    <a href="<?= base_url('admin/descuentos') ?>" class="btn btn-outline-secondary rounded-pill"><i class="fas fa-arrow-left me-2"></i>Volver</a>
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

        <form action="<?= isset($discount) ? base_url('admin/descuentos/editar/' . $discount->id) : base_url('admin/descuentos/crear') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="title" class="form-label fw-bold">Título de la Oferta *</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $discount->title ?? '') ?>" placeholder="Ej: Descuento de Aniversario" required>
                </div>

                <div class="col-md-6">
                    <label for="type" class="form-label fw-bold">Tipo de Oferta *</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="general" <?= old('type', $discount->type ?? '') === 'general' ? 'selected' : '' ?>>General (Todos los productos)</option>
                        <option value="category" <?= old('type', $discount->type ?? '') === 'category' ? 'selected' : '' ?>>Por Categoría</option>
                        <option value="product" <?= old('type', $discount->type ?? '') === 'product' ? 'selected' : '' ?>>Por Producto</option>
                        <option value="loyalty" <?= old('type', $discount->type ?? '') === 'loyalty' ? 'selected' : '' ?>>Cliente Fiel</option>
                    </select>
                </div>

                <div class="col-md-6" id="category-selector-group" style="display: none;">
                    <label for="category_id" class="form-label fw-bold">Categoría Aplicable *</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Selecciona una categoría...</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= old('category_id', $discount->category_id ?? '') == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6" id="product-selector-group" style="display: none;">
                    <label for="product_id" class="form-label fw-bold">Producto Aplicable *</label>
                    <select class="form-select" id="product_id" name="product_id">
                        <option value="">Selecciona un producto...</option>
                        <?php foreach($products as $prod): ?>
                            <option value="<?= $prod->id ?>" <?= old('product_id', $discount->product_id ?? '') == $prod->id ? 'selected' : '' ?>><?= esc($prod->name) ?> ($<?= number_format($prod->price, 0, ',', '.') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="discount_type" class="form-label fw-bold">Tipo de Valor *</label>
                    <select class="form-select" id="discount_type" name="discount_type" required>
                        <option value="percentage" <?= old('discount_type', $discount->discount_type ?? '') === 'percentage' ? 'selected' : '' ?>>Porcentaje (%)</option>
                        <option value="fixed" <?= old('discount_type', $discount->discount_type ?? '') === 'fixed' ? 'selected' : '' ?>>Valor Fijo ($)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="value" class="form-label fw-bold">Valor del Descuento *</label>
                    <input type="number" step="0.01" class="form-control" id="value" name="value" value="<?= old('value', $discount->value ?? '') ?>" placeholder="Ej: 15 o 10000" required>
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label fw-bold">Estado *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="1" <?= old('status', $discount->status ?? 1) == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= old('status', $discount->status ?? 1) == 0 ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="form-label fw-bold">Fecha de Inicio (Opcional)</label>
                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?= old('start_date', isset($discount->start_date) ? date('Y-m-d\TH:i', strtotime($discount->start_date)) : '') ?>">
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="form-label fw-bold">Fecha Fin (Opcional)</label>
                    <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?= old('end_date', isset($discount->end_date) ? date('Y-m-d\TH:i', strtotime($discount->end_date)) : '') ?>">
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-dg-primary rounded-pill px-4"><i class="fas fa-save me-2"></i>Guardar Oferta</button>
                <a href="<?= base_url('admin/descuentos') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const catGroup = document.getElementById('category-selector-group');
    const prodGroup = document.getElementById('product-selector-group');

    function toggleFields() {
        const val = typeSelect.value;
        catGroup.style.display = (val === 'category') ? 'block' : 'none';
        prodGroup.style.display = (val === 'product') ? 'block' : 'none';
    }

    typeSelect.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
<?= $this->endSection() ?>
