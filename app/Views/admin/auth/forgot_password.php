<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Recuperar Contraseña Admin') ?></title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
    </style>
</head>
<body>

<div class="container p-3">
    <div class="row login-card mx-auto flex-md-row flex-column-reverse">
        <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
            
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-key fa-xl" style="color: var(--dg-primary);"></i>
                </div>
                <h2 style="font-family: var(--font-heading); color: var(--dg-primary);" class="fw-bold mb-1">Recuperar Acceso</h2>
                <p class="text-muted small mb-0">Panel de Administración</p>
                <p class="text-muted small mt-2">Ingresa tu correo registrado como administrador para enviarte las instrucciones.</p>
            </div>

            <?php if(session()->has('error')): ?>
                <div class="alert alert-danger rounded-3 p-2 text-center small mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i> <?= session('error') ?>
                </div>
            <?php endif; ?>

            <?php if(session()->has('info')): ?>
                <div class="alert alert-info rounded-3 p-3 small mb-3">
                    <i class="fas fa-info-circle me-1"></i> <?= session('info') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/olvide-contrasena') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Correo Electrónico del Administrador</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0" name="email" value="<?= old('email') ?>" required placeholder="admin@dulcegracia.com">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-dg-primary w-100 rounded-pill py-2 fw-bold mb-3">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Instrucciones
                </button>
                
                <div class="text-center border-top pt-3">
                    <a href="<?= base_url('admin/login') ?>" class="text-decoration-none text-muted small">
                        <i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesión admin
                    </a>
                </div>
            </form>

        </div>
        <div class="col-md-6 login-image d-none d-md-block"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
