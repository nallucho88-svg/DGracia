<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-2" style="width: 70px; height: 70px;">
                            <i class="fas fa-user-circle fa-2x" style="color: var(--dg-primary);"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="font-family: var(--font-heading); color: var(--dg-primary);">¡Hola de nuevo!</h3>
                        <p class="text-muted small">Ingresa a tu cuenta de Dulce Gracia</p>
                    </div>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 small text-start mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> <?= session('error') ?>
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show rounded-3 small text-start mb-3" role="alert">
                            <i class="fas fa-check-circle me-1"></i> <?= session('success') ?>
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show rounded-3 small text-start mb-3" role="alert">
                            <i class="fas fa-info-circle me-1"></i> <?= session('info') ?>
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('login') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="form-floating mb-3 text-start">
                            <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="correo@ejemplo.com" value="<?= old('email') ?>" required>
                            <label for="email"><i class="fas fa-envelope me-2 text-muted"></i>Correo electrónico</label>
                        </div>

                        <div class="form-floating mb-2 text-start">
                            <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Contraseña" required>
                            <label for="password"><i class="fas fa-lock me-2 text-muted"></i>Contraseña</label>
                        </div>

                        <div class="text-end mb-4">
                            <a href="<?= base_url('olvide-contrasena') ?>" class="text-muted small text-decoration-none hover-primary">
                                <i class="fas fa-key me-1"></i>¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-dg-primary btn-lg w-100 rounded-pill py-3 fw-bold mb-3 shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </form>

                    <div class="border-top pt-3 mt-3">
                        <p class="text-muted small mb-0">¿Aún no tienes una cuenta?</p>
                        <a href="<?= base_url('registro') ?>" class="fw-bold text-decoration-none" style="color: var(--dg-primary);">
                            Regístrate aquí gratis
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
