<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="py-5 mt-3">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Contáctanos</h1>
            <p class="lead text-muted">¿Tienes alguna pregunta o quieres hacer un pedido especial? ¡Escríbenos!</p>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5 justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h4 class="fw-bold mb-4" style="color: var(--dg-primary);">Envíanos un mensaje</h4>
                    <form action="<?= base_url('contacto/enviar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Nombre completo *" value="<?= old('name') ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control" name="phone" placeholder="Teléfono *" value="<?= old('phone') ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Correo electrónico (opcional)" value="<?= old('email') ?>">
                        </div>
                        <div class="mb-3">
                            <select class="form-select" name="type" required>
                                <option value="">Tipo de mensaje *</option>
                                <option value="Sugerencia" <?= old('type') == 'Sugerencia' ? 'selected' : '' ?>>Sugerencia</option>
                                <option value="Felicitación" <?= old('type') == 'Felicitación' ? 'selected' : '' ?>>Felicitación</option>
                                <option value="Queja" <?= old('type') == 'Queja' ? 'selected' : '' ?>>Queja</option>
                                <option value="Reclamo" <?= old('type') == 'Reclamo' ? 'selected' : '' ?>>Reclamo</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <textarea class="form-control" name="message" rows="5" placeholder="Escribe tu mensaje aquí..." required><?= old('message') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-dg-primary w-100 rounded-pill py-3">
                            <i class="fas fa-paper-plane me-2"></i> Enviar Mensaje
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light">
                    <h4 class="fw-bold mb-4" style="color: var(--dg-primary);">Información de Contacto</h4>
                    <ul class="list-unstyled d-flex flex-column gap-4">
                        <li>
                            <i class="fas fa-map-marker-alt fa-lg me-3" style="color: var(--dg-primary);"></i>
                            <span><?= esc(setting('contact_address', 'Medellín, Colombia')) ?></span>
                        </li>
                        <li>
                            <i class="fas fa-phone fa-lg me-3" style="color: var(--dg-primary);"></i>
                            <span><?= esc(setting('contact_phone', '3108690810')) ?></span>
                        </li>
                        <li>
                            <i class="fas fa-clock fa-lg me-3" style="color: var(--dg-primary);"></i>
                            <span>Lun – Sáb: 8:00 AM – 7:00 PM<br><small class="text-muted ms-4">Dom: 9:00 AM – 3:00 PM</small></span>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp fa-lg me-3 text-success"></i>
                            <a href="https://wa.me/57<?= preg_replace('/[^0-9]/', '', setting('contact_whatsapp', '3108690810')) ?>" target="_blank" class="text-success fw-bold text-decoration-none">Escríbenos por WhatsApp (<?= esc(setting('contact_whatsapp', '3108690810')) ?>)</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
