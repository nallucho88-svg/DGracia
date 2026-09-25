<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Gestión de Clientes</h2>
        <small class="text-muted">Listado de clientes registrados, consulta de puntos y asignaciones manuales</small>
    </div>
    <span class="badge rounded-pill fs-6 px-3 py-2" style="background:var(--dg-primary);">
        <?= count($users) ?> clientes
    </span>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success rounded-4 mb-4">
        <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger rounded-4 mb-4">
        <i class="fas fa-exclamation-circle me-2"></i><?= session('error') ?>
    </div>
<?php endif; ?>

<!-- Buscador de Clientes -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/usuarios') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-8 col-sm-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" name="search"
                           placeholder="Buscar por nombre, apellidos, correo o teléfono..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4 col-sm-3 d-flex gap-2">
                <button type="submit" class="btn btn-dg-primary rounded-pill px-4 flex-grow-1"><i class="fas fa-search me-1"></i>Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary rounded-pill px-3" title="Limpiar filtro"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th class="text-center">Puntos Saldo</th>
                        <th class="text-center">Cliente Fiel</th>
                        <th>Registrado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5">No se encontraron clientes registrados.</td></tr>
                    <?php else: ?>
                    <?php foreach ($users as $i => $user): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-bold"><?= esc($user->name . ' ' . ($user->lastname ?? '')) ?></div>
                            <small class="text-muted"><?= esc($user->email) ?></small>
                        </td>
                        <td>
                            <?php if (!empty($user->phone)): ?>
                                <div><i class="fas fa-phone me-1 text-muted small"></i><?= esc($user->phone) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($user->whatsapp)): ?>
                                <small class="text-success"><i class="fab fa-whatsapp me-1"></i><?= esc($user->whatsapp) ?></small>
                            <?php endif; ?>
                        </td>
                        <!-- Columna de Puntos (Requerimiento 44) -->
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill fw-bold">
                                <i class="fas fa-coins text-warning me-1"></i><?= number_format((int)($user->points ?? 0), 0, ',', '.') ?> pts
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($user->is_loyal): ?>
                                <span class="badge bg-success rounded-pill"><i class="fas fa-star me-1"></i>Fiel</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Regular</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($user->created_at)) ?></td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <!-- Agregar Puntos Manualmente (Requerimiento 45) -->
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 btn-add-points"
                                        data-id="<?= $user->id ?>"
                                        data-name="<?= esc($user->name . ' ' . ($user->lastname ?? '')) ?>"
                                        data-points="<?= (int)($user->points ?? 0) ?>"
                                        title="Agregar puntos a este cliente">
                                    <i class="fas fa-plus-circle me-1"></i>Puntos
                                </button>

                                <!-- Alternar Cliente Fiel -->
                                <a href="<?= base_url('admin/usuarios/fiel/' . $user->id) ?>"
                                   class="btn btn-sm <?= $user->is_loyal ? 'btn-light text-warning' : 'btn-outline-success' ?> rounded-pill px-2 py-1"
                                   title="<?= $user->is_loyal ? 'Quitar cliente fiel' : 'Marcar como cliente fiel' ?>">
                                    <i class="fas fa-star"></i>
                                </a>

                                <!-- Eliminar / Desactivar Cliente (Requerimiento 47) -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 btn-delete-user"
                                        data-id="<?= $user->id ?>"
                                        data-name="<?= esc($user->name) ?>"
                                        title="Eliminar o desactivar cliente">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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

<!-- Modal Agregar Puntos Manualmente (Requerimiento 45) -->
<div class="modal fade" id="addPointsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" style="color: var(--dg-primary);">
            <i class="fas fa-coins text-warning me-2"></i>Agregar Puntos a Cliente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/usuarios/puntos') ?>" method="POST">
          <?= csrf_field() ?>
          <input type="hidden" name="user_id" id="modal_points_user_id">
          
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">CLIENTE</label>
                  <input type="text" class="form-control bg-light fw-bold" id="modal_points_user_name" readonly>
              </div>

              <div class="mb-3">
                  <label class="form-label text-muted small fw-bold">PUNTOS ACTUALES</label>
                  <input type="text" class="form-control bg-light" id="modal_points_current" readonly>
              </div>

              <div class="mb-3">
                  <label for="modal_points_qty" class="form-label fw-bold">PUNTOS A AGREGAR *</label>
                  <input type="number" class="form-control" name="points" id="modal_points_qty" min="1" placeholder="Ej: 100" required>
              </div>

              <div class="mb-3">
                  <label for="modal_points_reason" class="form-label fw-bold">MOTIVO / OBSERVACIÓN *</label>
                  <input type="text" class="form-control" name="reason" id="modal_points_reason" placeholder="Ej: Premio de fidelización, Bonificación especial" required>
              </div>
          </div>
          <div class="modal-footer border-0 pt-0 px-4 pb-4">
              <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-warning rounded-pill px-4"><i class="fas fa-plus-circle me-1"></i>Agregar Puntos</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Confirmación Eliminar Cliente -->
<form action="<?= base_url('admin/usuarios/eliminar') ?>" method="POST" id="deleteUserForm">
    <?= csrf_field() ?>
    <input type="hidden" name="user_id" id="delete_user_id">
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Abrir Modal de Puntos
    const pointsModal = new bootstrap.Modal(document.getElementById('addPointsModal'));
    document.querySelectorAll('.btn-add-points').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_points_user_id').value = this.dataset.id;
            document.getElementById('modal_points_user_name').value = this.dataset.name;
            document.getElementById('modal_points_current').value = this.dataset.points + ' pts';
            document.getElementById('modal_points_qty').value = '';
            document.getElementById('modal_points_reason').value = '';
            pointsModal.show();
        });
    });

    // Confirmar Eliminación
    document.querySelectorAll('.btn-delete-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.id;
            const userName = this.dataset.name;

            Swal.fire({
                title: '¿Eliminar / Desactivar cliente?',
                text: `¿Estás seguro de que deseas desactivar la cuenta de ${userName}? Su historial de pedidos se conservará intacto.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete_user_id').value = userId;
                    document.getElementById('deleteUserForm').submit();
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>

