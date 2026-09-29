<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
            <i class="fas fa-envelope-open-text me-2"></i>Mensajes de Contacto & PQRS
        </h2>
        <p class="text-muted small mb-0">Gestión de peticiones, sugerencias, felicitaciones, quejas y reclamos enviadas desde el sitio web.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Remitente</th>
                        <th>Contacto</th>
                        <th>Tipo</th>
                        <th style="width: 35%;">Mensaje</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No se han recibido mensajes de contacto por el momento.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                        <?php
                            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$m->phone);
                            if (strlen($cleanPhone) === 10 && strpos($cleanPhone, '57') !== 0) {
                                $cleanPhone = '57' . $cleanPhone;
                            }
                            $hasPhone = !empty($cleanPhone);

                            $waText = "Hola " . $m->name . " 👋\n\nTe contactamos de parte de Dulce Gracia en relación con tu mensaje:\n\n\"" . mb_strimwidth($m->message, 0, 150, '...') . "\"";
                            $waUrl = $hasPhone ? "https://wa.me/" . $cleanPhone . "?text=" . rawurlencode($waText) : '#';
                        ?>
                        <tr class="<?= $m->status === 'unread' ? 'fw-bold table-warning bg-opacity-10' : '' ?>">
                            <td>
                                <?php if ($m->status === 'unread'): ?>
                                    <span class="badge bg-danger rounded-pill px-3"><i class="fas fa-envelope me-1"></i>Nuevo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3"><i class="fas fa-envelope-open me-1"></i>Leído</span>
                                <?php endif; ?>
                            </td>
                            <td class="small">
                                <div><?= date('d/m/Y', strtotime($m->created_at)) ?></div>
                                <small class="text-muted"><?= date('h:i A', strtotime($m->created_at)) ?></small>
                            </td>
                            <td class="fw-bold"><?= esc($m->name) ?></td>
                            <td class="small">
                                <div><i class="fas fa-phone text-muted me-1"></i><?= esc($m->phone ?: 'Sin teléfono') ?></div>
                                <?php if (!empty($m->email)): ?>
                                    <div><i class="fas fa-at text-muted me-1"></i><?= esc($m->email) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $typeBadge = 'bg-info text-dark';
                                    if ($m->type === 'Queja' || $m->type === 'Reclamo') $typeBadge = 'bg-danger text-white';
                                    elseif ($m->type === 'Felicitación') $typeBadge = 'bg-success text-white';
                                    elseif ($m->type === 'Sugerencia') $typeBadge = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $typeBadge ?> rounded-pill px-3"><?= esc($m->type) ?></span>
                            </td>
                            <td>
                                <div class="text-break" style="max-height: 80px; overflow-y: auto; font-size: 0.9rem;">
                                    <?= nl2br(esc($m->message)) ?>
                                </div>
                            </td>
                            
                            <!-- Menú de Tres Puntos (⋮) -->
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones">
                                        <i class="fas fa-ellipsis-v text-secondary"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-3 py-2 btn-view-message" 
                                                    data-name="<?= esc($m->name) ?>"
                                                    data-phone="<?= esc($m->phone) ?>"
                                                    data-email="<?= esc($m->email) ?>"
                                                    data-type="<?= esc($m->type) ?>"
                                                    data-date="<?= date('d/m/Y h:i A', strtotime($m->created_at)) ?>"
                                                    data-message="<?= esc($m->message) ?>">
                                                <i class="fas fa-eye me-2 text-primary"></i>Ver detalle
                                            </button>
                                        </li>
                                        <li>
                                            <?php if ($hasPhone): ?>
                                                <a class="dropdown-item rounded-3 py-2 text-success" href="<?= $waUrl ?>" target="_blank" rel="noopener">
                                                    <i class="fab fa-whatsapp me-2"></i>Responder por WhatsApp
                                                </a>
                                            <?php else: ?>
                                                <button type="button" class="dropdown-item rounded-3 py-2 text-muted" onclick="alert('Este contacto no tiene un número de teléfono registrado.');">
                                                    <i class="fab fa-whatsapp me-2 text-muted"></i>Responder por WhatsApp
                                                </button>
                                            <?php endif; ?>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 text-info" href="<?= base_url('admin/contacto/marcar/'.$m->id) ?>">
                                                <i class="fas <?= $m->status === 'unread' ? 'fa-check' : 'fa-undo' ?> me-2"></i>
                                                <?= $m->status === 'unread' ? 'Marcar como leído' : 'Marcar como pendiente' ?>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 text-danger" href="<?= base_url('admin/contacto/eliminar/'.$m->id) ?>" 
                                               onclick="return confirm('¿Estás seguro de eliminar este mensaje?');">
                                                <i class="fas fa-trash me-2"></i>Eliminar
                                            </a>
                                        </li>
                                    </ul>
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

<!-- Modal Ver Mensaje Completo -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="viewMessageModalLabel">Detalle del Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <span class="badge bg-primary rounded-pill mb-2" id="modalMsgType">PQRS</span>
                    <h5 class="fw-bold text-dark mb-1" id="modalMsgName">Nombre</h5>
                    <small class="text-muted" id="modalMsgDate">Fecha</small>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="mb-1"><strong>Teléfono:</strong> <span id="modalMsgPhone">---</span></div>
                    <div><strong>Correo:</strong> <span id="modalMsgEmail">---</span></div>
                </div>

                <div class="p-3 border rounded-3 bg-white mb-3">
                    <label class="fw-bold small text-muted text-uppercase d-block mb-1">Mensaje:</label>
                    <p class="mb-0 text-secondary" id="modalMsgBody" style="white-space: pre-wrap;"></p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('viewMessageModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.btn-view-message').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalMsgName').textContent  = this.dataset.name || 'Remitente';
            document.getElementById('modalMsgPhone').textContent = this.dataset.phone || 'No registrado';
            document.getElementById('modalMsgEmail').textContent = this.dataset.email || 'No registrado';
            document.getElementById('modalMsgType').textContent  = this.dataset.type || 'Mensaje';
            document.getElementById('modalMsgDate').textContent  = this.dataset.date || '';
            document.getElementById('modalMsgBody').textContent  = this.dataset.message || '';

            modal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>
