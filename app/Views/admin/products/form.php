<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $isEdit = isset($product); ?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= base_url('admin/productos') ?>" class="btn btn-light rounded-circle me-3">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
        <?= $isEdit ? 'Editar Producto' : 'Crear Producto' ?>
    </h2>
</div>

<form action="<?= $isEdit ? base_url('admin/productos/editar/'.$product->id) : base_url('admin/productos/crear') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Información General</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre del Producto *</label>
                        <input type="text" class="form-control" name="name" value="<?= old('name', $product->name ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción Corta</label>
                        <textarea class="form-control" name="short_description" rows="2" maxlength="255"><?= old('short_description', $product->short_description ?? '') ?></textarea>
                        <div class="form-text">Se mostrará en la tarjeta del catálogo (máx 255 chars).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción Detallada *</label>
                        <textarea class="form-control" name="description" rows="5" required><?= old('description', $product->description ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Detalles Adicionales</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Ingredientes</label>
                            <input type="text" class="form-control" name="ingredients" value="<?= old('ingredients', $product->ingredients ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Porciones</label>
                            <input type="text" class="form-control" name="portions" value="<?= old('portions', $product->portions ?? '') ?>" placeholder="Ej. 10 - 12 personas">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Organización & Precio</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Precio ($) *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="price" value="<?= old('price', $product->price ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Categoría *</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Selecciona...</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Estado</label>
                        <select class="form-select" name="status">
                            <option value="1" <?= old('status', $product->status ?? '') == '1' ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= old('status', $product->status ?? '') == '0' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Disponibilidad</label>
                        <select class="form-select" name="availability">
                            <option value="1" <?= old('availability', $product->availability ?? '') == '1' ? 'selected' : '' ?>>Hay Stock</option>
                            <option value="0" <?= old('availability', $product->availability ?? '') == '0' ? 'selected' : '' ?>>Agotado</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold mb-4">Imagen Principal</h5>
                    
                    <div class="mb-3">
                        <!-- Placeholder visual temporal -->
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="height: 150px; border: 2px dashed #ddd;">
                            <i class="fas fa-image fa-3x text-muted opacity-50"></i>
                        </div>
                        
                        <input type="file" class="form-control" name="image" accept="image/*" <?= !$isEdit ? 'required' : '' ?>>
                        <?php if($isEdit): ?>
                            <small class="text-muted d-block mt-2">Sube una nueva si deseas reemplazar la actual.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-dg-primary btn-lg w-100 rounded-pill shadow">
                <i class="fas fa-save me-2"></i> Guardar Producto
            </button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
