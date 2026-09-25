<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Mi Perfil</h2>
        <small class="text-muted">Gestiona tu información y contraseña de acceso</small>
    </div>
</div>

<div class="row g-4">

    <!-- ── Columna Izquierda: Info del Perfil ── -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:90px;height:90px;background:linear-gradient(135deg,var(--dg-primary),#b88a5e);">
                        <i class="fas fa-user-shield fa-3x text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?= esc($admin->name) ?></h4>
                    <span class="badge rounded-pill mt-1" style="background:var(--dg-primary);">Administrador</span>
                </div>

                <hr>

                <form action="<?= base_url('admin/perfil/actualizar') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="profile-name" class="form-label fw-semibold">
                            <i class="fas fa-user me-1 text-muted"></i>Nombre
                        </label>
                        <input type="text"
                               id="profile-name"
                               name="name"
                               class="form-control rounded-3 <?= session('errors.name') ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('name', $admin->name)) ?>"
                               required minlength="3" maxlength="100">
                        <?php if (session('errors.name')): ?>
                        <div class="invalid-feedback"><?= session('errors.name') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="profile-email" class="form-label fw-semibold">
                            <i class="fas fa-envelope me-1 text-muted"></i>Correo electrónico
                        </label>
                        <input type="email"
                               id="profile-email"
                               name="email"
                               class="form-control rounded-3 <?= session('errors.email') ? 'is-invalid' : '' ?>"
                               value="<?= esc(old('email', $admin->email)) ?>"
                               required>
                        <?php if (session('errors.email')): ?>
                        <div class="invalid-feedback"><?= session('errors.email') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>Miembro desde
                        </label>
                        <p class="mb-0"><?= date('d \d\e F, Y', strtotime($admin->created_at ?? 'now')) ?></p>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dg-primary rounded-pill py-2">
                            <i class="fas fa-save me-2"></i>Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Columna Derecha: Cambiar Contraseña ── -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-lock me-2" style="color:var(--dg-primary);"></i>Cambiar Contraseña
                </h5>
                <small class="text-muted">Usa una contraseña segura de al menos 8 caracteres.</small>
            </div>
            <div class="card-body p-4">

                <?php if (session()->has('success')): ?>
                <div class="alert alert-success border-0 rounded-3 alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                <div class="alert alert-danger border-0 rounded-3 alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (!empty(session('errors'))): ?>
                <div class="alert alert-warning border-0 rounded-3 alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0 ps-3">
                        <?php foreach (session('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/perfil/cambiar-clave') ?>" method="post"
                      id="changePasswordForm" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <!-- Contraseña Actual -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">
                            Contraseña actual <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control rounded-start-3"
                                   placeholder="Tu contraseña actual"
                                   required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Nueva Contraseña -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold">
                            Nueva contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   class="form-control rounded-start-3"
                                   placeholder="Mínimo 8 caracteres"
                                   required minlength="8">
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Indicador de fortaleza -->
                        <div class="progress mt-2" style="height:6px;">
                            <div id="pwStrengthBar" class="progress-bar" role="progressbar" style="width:0%;transition:width .4s;"></div>
                        </div>
                        <small id="pwStrengthLabel" class="text-muted"></small>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">
                            Confirmar nueva contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   id="confirm_password"
                                   name="confirm_password"
                                   class="form-control rounded-start-3"
                                   placeholder="Repite la nueva contraseña"
                                   required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="matchFeedback" class="form-text"></div>
                    </div>

                    <!-- Recomendaciones -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <small class="text-muted fw-semibold d-block mb-2"><i class="fas fa-shield-alt me-1 text-success"></i>Recomendaciones de seguridad:</small>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Al menos 8 caracteres</li>
                            <li>Combina letras mayúsculas, minúsculas, números y símbolos</li>
                            <li>No uses contraseñas utilizadas anteriormente</li>
                        </ul>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger rounded-pill py-2" id="submitPwBtn" disabled>
                            <i class="fas fa-key me-2"></i>Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    // ── Mostrar / ocultar contraseñas ────────────────────────────────────
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // ── Fortaleza de contraseña ───────────────────────────────────────────
    const newPw    = document.getElementById('new_password');
    const bar      = document.getElementById('pwStrengthBar');
    const label    = document.getElementById('pwStrengthLabel');
    const confirmPw= document.getElementById('confirm_password');
    const matchFb  = document.getElementById('matchFeedback');
    const submitBtn= document.getElementById('submitPwBtn');

    function checkStrength(pw) {
        let score = 0;
        if (pw.length >= 8)                          score++;
        if (pw.length >= 12)                         score++;
        if (/[A-Z]/.test(pw))                        score++;
        if (/[0-9]/.test(pw))                        score++;
        if (/[^A-Za-z0-9]/.test(pw))                score++;
        return score;
    }

    function updateUI() {
        const pw = newPw.value;
        const score = checkStrength(pw);
        const pct   = score * 20;
        const colors= ['#dc3545','#fd7e14','#ffc107','#20c997','#198754'];
        const texts = ['Muy débil','Débil','Regular','Buena','Muy segura'];

        bar.style.width = pct + '%';
        bar.style.background = colors[score - 1] || '#e9ecef';
        label.textContent = pw.length ? texts[score - 1] || '' : '';
        label.style.color = colors[score - 1] || '#6c757d';

        checkMatch();
    }

    function checkMatch() {
        const match = confirmPw.value && newPw.value === confirmPw.value;
        if (!confirmPw.value) {
            matchFb.textContent = '';
        } else if (match) {
            matchFb.textContent = '✅ Las contraseñas coinciden';
            matchFb.className = 'form-text text-success';
        } else {
            matchFb.textContent = '❌ Las contraseñas no coinciden';
            matchFb.className = 'form-text text-danger';
        }
        submitBtn.disabled = !match || newPw.value.length < 8;
    }

    newPw.addEventListener('input', updateUI);
    confirmPw.addEventListener('input', checkMatch);
})();
</script>
<?= $this->endSection() ?>
