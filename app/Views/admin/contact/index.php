<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
            <i class="fas fa-envelope-open-text me-2"></i>Mensajes de Contacto & PQRS
        </h2>
        <p class="text-muted small mb-0">Gestión de peticiones, sugerencias, feliciataciones, quejas y reclamos enviadas desde el sitio web.</p>
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
                        <th class="text-end">Acciones</th>
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
                                <div><i class="fas fa-phone text-muted me-1"></i><?= esc($m->phone) ?></div>
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
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= base_url('admin/contacto/marcar/'.$m->id) ?>" 
                                       class="btn btn-sm <?= $m->status === 'unread' ? 'btn-outline-success' : 'btn-outline-secondary' ?>" 
                                       title="<?= $m->status === 'unread' ? 'Marcar como leído' : 'Marcar como no leído' ?>">
                                        <i class="fas <?= $m->status === 'unread' ? 'fa-check' : 'fa-undo' ?>"></i>
                                    </a>
                                    <a href="<?= base_url('admin/contacto/eliminar/'.$m->id) ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('¿Estás seguro de eliminar este mensaje?');" 
                                       title="Eliminar mensaje">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
<?= $this->endSection() ?>
