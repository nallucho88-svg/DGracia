<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5 py-3">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-2" style="width: 70px; height: 70px;">
                            <i class="fas fa-user-plus fa-2x" style="color: var(--dg-primary);"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-family: var(--font-heading); color: var(--dg-primary);">Crear Cuenta de Cliente</h3>
                        <p class="text-muted small">Únete a Dulce Gracia y acumula puntos en cada compra</p>
                    </div>

                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0 ps-3 small">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('registro') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="name" class="form-label fw-bold small text-muted">Nombre *</label>
                                <input type="text" class="form-control rounded-3" id="name" name="name" value="<?= old('name') ?>" required placeholder="Ej. Juan">
                            </div>
                            
                            <div class="col-sm-6">
                                <label for="lastname" class="form-label fw-bold small text-muted">Apellidos *</label>
                                <input type="text" class="form-control rounded-3" id="lastname" name="lastname" value="<?= old('lastname') ?>" required placeholder="Ej. Pérez">
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label fw-bold small text-muted">Correo electrónico *</label>
                                <input type="email" class="form-control rounded-3" id="email" name="email" value="<?= old('email') ?>" required placeholder="correo@ejemplo.com">
                            </div>

                            <div class="col-sm-6">
                                <label for="phone" class="form-label fw-bold small text-muted">Teléfono de contacto *</label>
                                <input type="tel" class="form-control rounded-3" id="phone" name="phone" value="<?= old('phone') ?>" required placeholder="Ej. 3001234567">
                            </div>

                            <div class="col-sm-6">
                                <label for="whatsapp" class="form-label fw-bold small text-muted">WhatsApp (Opcional)</label>
                                <input type="tel" class="form-control rounded-3" id="whatsapp" name="whatsapp" value="<?= old('whatsapp') ?>" placeholder="Ej. 3001234567">
                            </div>

                            <div class="col-sm-6">
                                <label for="password" class="form-label fw-bold small text-muted">Contraseña *</label>
                                <input type="password" class="form-control rounded-3" id="password" name="password" required placeholder="Mínimo 8 caracteres">
                            </div>

                            <div class="col-sm-6">
                                <label for="password_confirm" class="form-label fw-bold small text-muted">Confirmar contraseña *</label>
                                <input type="password" class="form-control rounded-3" id="password_confirm" name="password_confirm" required placeholder="Repite tu contraseña">
                            </div>

                            <!-- Indicador visual de requisitos (Requerimiento 35) -->
                            <div class="col-12">
                                <div class="card bg-light border-0 rounded-3 p-3 text-start small">
                                    <div class="fw-bold mb-2 text-muted">REQUISITOS DE SEGURIDAD:</div>
                                    <div class="requirement-item text-muted" id="req-len"><i class="far fa-circle me-2"></i>Mínimo 8 caracteres</div>
                                    <div class="requirement-item text-muted" id="req-letter"><i class="far fa-circle me-2"></i>Al menos una letra</div>
                                    <div class="requirement-item text-muted" id="req-number"><i class="far fa-circle me-2"></i>Al menos un número</div>
                                    <div class="requirement-item text-muted" id="req-special"><i class="far fa-circle me-2"></i>Al menos un carácter especial (!@#$%&*)</div>
                                    <div class="requirement-item text-muted" id="req-match"><i class="far fa-circle me-2"></i>Las contraseñas coinciden</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-dg-primary btn-lg rounded-pill py-3 fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i>Completar Registro
                            </button>
                        </div>
                    </form>

                    <div class="border-top text-center pt-3 mt-4">
                        <p class="text-muted small mb-0">¿Ya tienes una cuenta registrada?</p>
                        <a href="<?= base_url('login') ?>" class="fw-bold text-decoration-none" style="color: var(--dg-primary);">
                            Inicia sesión aquí
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwdInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirm');

    const reqLen = document.getElementById('req-len');
    const reqLetter = document.getElementById('req-letter');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    const reqMatch = document.getElementById('req-match');

    function validatePasswordStrength() {
        const val = pwdInput.value;
        const confirmVal = confirmInput.value;

        const hasLen = val.length >= 8;
        const hasLetter = /[a-zA-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val);
        const hasMatch = val.length > 0 && val === confirmVal;

        reqLen.className = hasLen ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqLen.innerHTML = (hasLen ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Mínimo 8 caracteres';

        reqLetter.className = hasLetter ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqLetter.innerHTML = (hasLetter ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Al menos una letra';

        reqNumber.className = hasNumber ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqNumber.innerHTML = (hasNumber ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Al menos un número';

        reqSpecial.className = hasSpecial ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqSpecial.innerHTML = (hasSpecial ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Al menos un carácter especial (!@#$%&*)';

        reqMatch.className = hasMatch ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqMatch.innerHTML = (hasMatch ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Las contraseñas coinciden';
    }

    if (pwdInput && confirmInput) {
        pwdInput.addEventListener('input', validatePasswordStrength);
        confirmInput.addEventListener('input', validatePasswordStrength);
    }
});
</script>
<?= $this->endSection() ?>
