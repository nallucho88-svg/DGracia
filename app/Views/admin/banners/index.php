<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Marketing: Banners</h2>
</div>

<div class="row g-4">
    <!-- Formulario para Nuevo Banner -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 90px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Nuevo Banner</h5>
                
                <form action="<?= base_url('admin/banners') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Título (Opcional)</label>
                        <input type="text" class="form-control" name="title" placeholder="Ej. Oferta Navideña">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Enlace del Botón (Opcional)</label>
                        <input type="text" class="form-control" name="link" placeholder="Ej. /productos/categoria/postres">
                        <small class="text-muted">Si lo dejas vacío, no aparecerá el botón "Ver promoción".</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Prioridad (Orden)</label>
                        <input type="number" class="form-control" name="priority" value="0">
                        <small class="text-muted">Menor número aparece primero.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Imagen del Banner *</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                        <small class="text-muted">Recomendado: 800x600px o similar.</small>
                    </div>

                    <button type="submit" class="btn btn-dg-primary w-100 rounded-pill">
                        <i class="fas fa-upload me-2"></i> Subir Banner
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Banners Actuales -->
    <div class="col-md-8">
        <div class="row g-4">
            <?php if(empty($banners)): ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                    <h5>No tienes banners activos.</h5>
                    <p>Agrega uno nuevo desde el panel izquierdo.</p>
                </div>
            <?php else: ?>
                <?php foreach($banners as $b): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <img src="<?= $b->image_path ?>" class="card-img-top" alt="<?= esc($b->title) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="fw-bold"><?= esc($b->title) ?: '<i>Sin título</i>' ?></h6>
                            <p class="small text-muted mb-2">Orden: <?= $b->priority ?></p>
                            <?php if($b->link): ?>
                                <a href="<?= $b->link ?>" target="_blank" class="small text-primary text-decoration-none"><i class="fas fa-link me-1"></i>Ver enlace</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-top-0 pt-0">
                            <a href="<?= base_url('admin/banners/eliminar/'.$b->id) ?>" class="btn btn-sm btn-outline-danger w-100 rounded-pill" onclick="return confirm('¿Seguro que deseas eliminar este banner?');">
                                <i class="fas fa-trash-alt me-1"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
