<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Productos</h2>
    <a href="<?= base_url('admin/productos/crear') ?>" class="btn btn-dg-primary rounded-pill px-4">
        <i class="fas fa-plus me-2"></i> Nuevo Producto
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Disponibilidad</th>
                        <th>Estado</th>
                        <th class="text-end" width="15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($products)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay productos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($products as $p): ?>
                        <tr id="row-<?= $p->id ?>">
                            <td><?= $p->id ?></td>
                            <td class="fw-bold"><?= esc($p->name) ?></td>
                            <td style="color: var(--dg-primary); font-weight: bold;">$<?= number_format($p->price, 0, ',', '.') ?></td>
                            <td>
                                <?php 
                                    $cat = array_filter($categories, fn($c) => $c->id == $p->category_id);
                                    echo !empty($cat) ? esc(reset($cat)->name) : 'N/A';
                                ?>
                            </td>
                            <td>
                                <?php if($p->availability == 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle me-1"></i> Disponible</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-times-circle me-1"></i> Agotado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($p->status == 1): ?>
                                    <span class="badge bg-primary">Visible</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Oculto</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/productos/editar/'.$p->id) ?>" class="btn btn-sm btn-light text-primary rounded-circle me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-light text-danger rounded-circle" onclick="deleteProduct(<?= $p->id ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
<script>
    function deleteProduct(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Este producto se ocultará del catálogo.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--dg-primary)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('admin/productos/eliminar') ?>/${id}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('row-' + id).style.opacity = 0;
                        setTimeout(() => document.getElementById('row-' + id).remove(), 300);
                        
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            gravity: "bottom", position: "right",
                            style: { background: "#dc3545" }
                        }).showToast();
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
