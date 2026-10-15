<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Nueva Contraseña Admin') ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --dg-primary: #D4A373;
            --dg-primary-dark: #b88a5e;
            --dg-accent: #E9EDC9;
            --dg-bg: #FAEDCD;
            --dg-text-dark: #333333;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--dg-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
        }

        .login-image {
            background: url('https://images.unsplash.com/photo-1551404973-7bb1e459db62?q=80&w=800&auto=format&fit=crop') center/cover no-repeat;
            min-height: 100%;
        }

        .btn-dg-primary {
            background-color: var(--dg-primary);
            color: white;
            border: none;
        }

        .btn-dg-primary:hover {
            background-color: var(--dg-primary-dark);
            color: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--dg-primary);
            background-color: white;
        }

        .requirement-item {
            transition: all 0.3s ease;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="row login-card mx-auto flex-md-row flex-column-reverse">
        <div class="col-md-6 p-4 p-md-5 d-flex flex-column justify-content-center">
            
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-2" style="width: 60px; height: 60px;">
                    <i class="fas fa-lock-open fa-xl" style="color: var(--dg-primary);"></i>
                </div>
                <h2 style="font-family: var(--font-heading); color: var(--dg-primary);" class="fw-bold mb-1">Nueva Contraseña</h2>
                <p class="text-muted small">Crea una nueva clave de acceso para el panel de administración.</p>
            </div>

            <?php if(session()->has('error')): ?>
                <div class="alert alert-danger rounded-3 p-2 text-center small mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> <?= session('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/restablecer-contrasena') ?>" method="POST" id="adminResetForm">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= esc($token) ?>">

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" id="pwd_new" name="password" required placeholder="••••••••">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-check-double"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" id="pwd_confirm" name="password_confirm" required placeholder="••••••••">
                    </div>
                </div>

                <!-- Indicador visual de requisitos de contraseña -->
                <div class="card bg-light border-0 rounded-3 p-3 mb-4 text-start small">
                    <div class="fw-bold mb-2 text-muted">REQUISITOS DE LA CONTRASEÑA:</div>
                    <div class="requirement-item text-muted" id="req-len"><i class="far fa-circle me-2"></i>Mínimo 6 caracteres</div>
                    <div class="requirement-item text-muted" id="req-letter"><i class="far fa-circle me-2"></i>Al menos una letra</div>
                    <div class="requirement-item text-muted" id="req-number"><i class="far fa-circle me-2"></i>Al menos un número</div>
                    <div class="requirement-item text-muted" id="req-match"><i class="far fa-circle me-2"></i>Las contraseñas coinciden</div>
                </div>

                <button type="submit" class="btn btn-dg-primary w-100 rounded-pill py-2 fw-bold mb-3" id="btnSubmitReset">
                    <i class="fas fa-save me-2"></i>Guardar Nueva Contraseña
                </button>
                
                <div class="text-center border-top pt-3">
                    <a href="<?= base_url('admin/login') ?>" class="text-decoration-none text-muted small">
                        <i class="fas fa-arrow-left me-1"></i> Cancelar y volver al login admin
                    </a>
                </div>
            </form>

        </div>
        <div class="col-md-6 login-image d-none d-md-block"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwdInput = document.getElementById('pwd_new');
    const confirmInput = document.getElementById('pwd_confirm');

    const reqLen = document.getElementById('req-len');
    const reqLetter = document.getElementById('req-letter');
    const reqNumber = document.getElementById('req-number');
    const reqMatch = document.getElementById('req-match');

    function validatePasswordStrength() {
        const val = pwdInput.value;
        const confirmVal = confirmInput.value;

        const hasLen = val.length >= 6;
        const hasLetter = /[a-zA-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasMatch = val.length > 0 && val === confirmVal;

        reqLen.className = hasLen ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqLen.innerHTML = (hasLen ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Mínimo 6 caracteres';

        reqLetter.className = hasLetter ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqLetter.innerHTML = (hasLetter ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Al menos una letra';

        reqNumber.className = hasNumber ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqNumber.innerHTML = (hasNumber ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Al menos un número';

        reqMatch.className = hasMatch ? 'requirement-item text-success fw-bold' : 'requirement-item text-muted';
        reqMatch.innerHTML = (hasMatch ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="far fa-circle me-2"></i>') + 'Las contraseñas coinciden';
    }

    pwdInput.addEventListener('input', validatePasswordStrength);
    confirmInput.addEventListener('input', validatePasswordStrength);
});
</script>
</body>
</html>
