<?= $this->extend('customer/layout') ?>

<?= $this->section('customer_content') ?>

<div class="row g-4">
    <!-- Editar Datos Personales -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
                    <i class="fas fa-user-edit me-2"></i>Datos Personales
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('mi-cuenta/perfil/actualizar') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="name" class="form-label fw-bold small text-muted">Nombre *</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" value="<?= esc(old('name', $user->name ?? '')) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="lastname" class="form-label fw-bold small text-muted">Apellidos *</label>
                            <input type="text" class="form-control rounded-3" id="lastname" name="lastname" value="<?= esc(old('lastname', $user->lastname ?? '')) ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label fw-bold small text-muted">Correo electrónico (No editable)</label>
                            <input type="email" class="form-control rounded-3 bg-light" id="email" value="<?= esc($user->email ?? '') ?>" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label for="phone" class="form-label fw-bold small text-muted">Teléfono *</label>
                            <input type="tel" class="form-control rounded-3" id="phone" name="phone" value="<?= esc(old('phone', $user->phone ?? '')) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="whatsapp" class="form-label fw-bold small text-muted">WhatsApp</label>
                            <input type="tel" class="form-control rounded-3" id="whatsapp" name="whatsapp" value="<?= esc(old('whatsapp', $user->whatsapp ?? '')) ?>">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dg-primary rounded-pill py-2 fw-bold">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambiar Contraseña -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0" style="color: var(--dg-primary);">
                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('mi-cuenta/perfil/cambiar-clave') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold small text-muted">Contraseña Actual *</label>
                        <input type="password" class="form-control rounded-3" id="current_password" name="current_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-bold small text-muted">Nueva Contraseña *</label>
                        <input type="password" class="form-control rounded-3" id="new_password" name="new_password" required placeholder="Mínimo 6 caracteres">
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-bold small text-muted">Confirmar Nueva Contraseña *</label>
                        <input type="password" class="form-control rounded-3" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                            <i class="fas fa-shield-alt me-2"></i>Actualizar Clave
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
