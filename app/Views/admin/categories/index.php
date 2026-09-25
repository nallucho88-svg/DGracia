<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Categorías</h2>
    <button class="btn btn-dg-primary rounded-pill px-4" onclick="openModal()">
        <i class="fas fa-plus me-2"></i> Nueva Categoría
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">ID</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Estado</th>
                        <th class="text-end" width="15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay categorías registradas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($categories as $cat): ?>
                        <tr id="row-<?= $cat->id ?>">
                            <td><?= $cat->id ?></td>
                            <td class="fw-bold"><?= esc($cat->name) ?></td>
                            <td><span class="text-muted"><?= esc($cat->slug) ?></span></td>
                            <td>
                                <?php if($cat->status == 1): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-light text-primary rounded-circle me-1" onclick="editCategory(<?= $cat->id ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger rounded-circle" onclick="deleteCategory(<?= $cat->id ?>)">
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

<!-- Modal para Crear/Editar Categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTitle">Nueva Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="categoryForm">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="categoryId">
            
            <div class="mb-3">
                <label for="name" class="form-label fw-bold text-muted small">Nombre de la Categoría *</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Ej. Tortas, Postres, etc.">
            </div>

            <div class="mb-4">
                <label for="status" class="form-label fw-bold text-muted small">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="1">Activo (Visible en tienda)</option>
                    <option value="0">Inactivo (Oculto)</option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-dg-primary rounded-pill py-2" id="btnSubmit">Guardar Categoría</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const form = document.getElementById('categoryForm');
    const baseUrl = '<?= base_url('admin/categorias') ?>';

    function openModal() {
        form.reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('modalTitle').innerText = 'Nueva Categoría';
        modal.show();
    }

    function editCategory(id) {
        // Obtener datos
        fetch(baseUrl + '?id=' + id, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('categoryId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('status').value = data.status;
            document.getElementById('modalTitle').innerText = 'Editar Categoría';
            modal.show();
        });
    }

    function deleteCategory(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta categoría se ocultará y los productos podrían quedar sin categoría.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--dg-primary)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('admin/categorias/delete') ?>/${id}`, {
                    method: 'GET', // Usamos GET por simplicidad en la ruta en este ejemplo, idealmente DELETE/POST
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('row-' + id).remove();
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            gravity: "bottom", position: "right",
                            style: { background: "#198754" }
                        }).showToast();
                    }
                });
            }
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        let btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        fetch(baseUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                modal.hide();
                // Recargar para ver los cambios rápidamente (se podría hacer por JS DOM manipulation para mejor UX)
                location.reload(); 
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'Guardar Categoría';
        });
    });
</script>
<?= $this->endSection() ?>
