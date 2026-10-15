<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Configuración General del Sitio</h2>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success rounded-4 mb-4">
        <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin/configuracion') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <ul class="nav nav-tabs card-header-tabs" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab"><i class="fas fa-store me-2"></i>General & Identidad</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="header-hero-tab" data-bs-toggle="tab" data-bs-target="#header-hero" type="button" role="tab"><i class="fas fa-heading me-2"></i>Header & Body</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="footer-tab" data-bs-toggle="tab" data-bs-target="#footer" type="button" role="tab"><i class="fas fa-shoe-prints me-2"></i>Footer & Contacto</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="discounts-tab" data-bs-toggle="tab" data-bs-target="#discounts" type="button" role="tab"><i class="fas fa-percentage me-2"></i>Reglas de Descuentos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp" type="button" role="tab"><i class="fas fa-paper-plane me-2"></i>Correo SMTP</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button" role="tab"><i class="fas fa-database me-2"></i>Copia de Seguridad</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="migration-tab" data-bs-toggle="tab" data-bs-target="#migration" type="button" role="tab"><i class="fas fa-sync me-2"></i>Migración de Datos</button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="settingsTabContent">
                <!-- 1. General & Identidad -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre de la Tienda *</label>
                            <input type="text" class="form-control" name="settings[site_name]" value="<?= esc($settings['site_name'] ?? 'Dulce Gracia') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Eslogan / Descripción Breve</label>
                            <input type="text" class="form-control" name="settings[site_description]" value="<?= esc($settings['site_description'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6 border-top pt-3 mt-3">
                            <label class="form-label fw-bold">Favicon del Sitio (.ico, .png, .svg)</label>
                            <?php if(!empty($settings['site_favicon'])): ?>
                                <div class="mb-2">
                                    <img src="<?= esc($settings['site_favicon']) ?>" alt="Favicon" style="width: 32px; height: 32px; object-fit: contain;" class="border p-1 rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="favicon" accept=".ico,.png,.svg,.jpg,.jpeg,.webp">
                        </div>

                        <div class="col-md-6 border-top pt-3 mt-3">
                            <label class="form-label fw-bold">Logo de la Tienda</label>
                            <?php if(!empty($settings['site_logo'])): ?>
                                <div class="mb-2">
                                    <img src="<?= esc($settings['site_logo']) ?>" alt="Logo" style="height: 50px; object-fit: contain;" class="border p-1 rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                        </div>

                        <div class="col-md-12 border-top pt-3 mt-3">
                            <label class="form-label fw-bold"><i class="fas fa-volume-up me-2 text-primary"></i>Sonidos de Alertas y Notificaciones</label>
                            <select class="form-select" name="settings[alert_sound_enabled]" id="alertSoundSelect" onchange="if(typeof DGAlert !== 'undefined') DGAlert.setSoundEnabled(this.value === '1')">
                                <option value="1" <?= ($settings['alert_sound_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>[ Activado ] Reproducir tonos suaves en alertas</option>
                                <option value="0" <?= ($settings['alert_sound_enabled'] ?? '1') === '0' ? 'selected' : '' ?>>[ Desactivado ] Modo silencioso (Sin audio)</option>
                            </select>
                            <small class="text-muted">Respetando la privacidad del usuario y las restricciones del navegador sobre reproducción automática.</small>
                        </div>
                    </div>
                </div>

                <!-- 2. Header & Body -->
                <div class="tab-pane fade" id="header-hero" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Texto Anuncio Superior (Header Top Bar)</label>
                            <input type="text" class="form-control" name="settings[header_text]" value="<?= esc($settings['header_text'] ?? '¡Envíos gratis en pedidos mayores a $50.000!') ?>">
                        </div>
                        
                        <div class="col-md-6 border-top pt-3 mt-3">
                            <label class="form-label fw-bold">Título Principal (Hero Banner)</label>
                            <input type="text" class="form-control" name="settings[hero_title]" value="<?= esc($settings['hero_title'] ?? 'Dulce Gracia') ?>">
                        </div>

                        <div class="col-md-6 border-top pt-3 mt-3">
                            <label class="form-label fw-bold">Subtítulo (Hero Banner)</label>
                            <input type="text" class="form-control" name="settings[hero_subtitle]" value="<?= esc($settings['hero_subtitle'] ?? 'Endulzamos tus momentos más especiales con amor y tradición.') ?>">
                        </div>

                        <div class="col-md-12 border-top pt-3 mt-3">
                            <label class="form-label fw-bold">Imagen de Fondo Principal (Hero Background)</label>
                            <?php if(!empty($settings['hero_bg_image'])): ?>
                                <div class="mb-2">
                                    <img src="<?= esc($settings['hero_bg_image']) ?>" alt="Hero BG" style="height: 100px; width: 100%; object-fit: cover;" class="rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="hero_bg_image" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- 3. Footer & Contacto -->
                <div class="tab-pane fade" id="footer" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono de Contacto</label>
                            <input type="text" class="form-control" name="settings[contact_phone]" value="<?= esc($settings['contact_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp de Atención</label>
                            <input type="text" class="form-control" name="settings[contact_whatsapp]" value="<?= esc($settings['contact_whatsapp'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" name="settings[contact_email]" value="<?= esc($settings['contact_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dirección del Local</label>
                            <input type="text" class="form-control" name="settings[contact_address]" value="<?= esc($settings['contact_address'] ?? 'Local, Colombia') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horarios de Atención</label>
                            <textarea class="form-control" rows="2" name="settings[business_hours]"><?= esc($settings['business_hours'] ?? 'Lunes a sábado: 8:00 AM - 7:00 PM | Domingos: 9:00 AM - 3:00 PM') ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Facebook URL</label>
                            <input type="url" class="form-control" name="settings[social_facebook]" value="<?= esc($settings['social_facebook'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Instagram URL</label>
                            <input type="url" class="form-control" name="settings[social_instagram]" value="<?= esc($settings['social_instagram'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- 4. Reglas de Descuentos -->
                <div class="tab-pane fade" id="discounts" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Modo de Acumulación de Descuentos</label>
                            <select class="form-select" name="settings[discount_stackable_mode]">
                                <option value="max_only" <?= ($settings['discount_stackable_mode'] ?? 'max_only') === 'max_only' ? 'selected' : '' ?>>Solo aplicar el descuento mayor (Recomendado)</option>
                                <option value="stack_all" <?= ($settings['discount_stackable_mode'] ?? '') === 'stack_all' ? 'selected' : '' ?>>Permitir acumulación en cascada (Producto/Categoría + Cliente Fiel + Cupón)</option>
                            </select>
                            <small class="text-muted d-block mt-2">Determina cómo reacciona el sistema cuando un producto califica para múltiples promociones a la vez.</small>
                        </div>
                    </div>
                </div>

                <!-- 5. Programa de Fidelización (Sistema de Puntos) -->
                <div class="tab-pane fade" id="points" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado del Sistema de Puntos</label>
                            <select class="form-select" name="settings[points_system_enabled]">
                                <option value="1" <?= ($settings['points_system_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>[ Activo ] Programa de Fidelización Habilitado</option>
                                <option value="0" <?= ($settings['points_system_enabled'] ?? '1') === '0' ? 'selected' : '' ?>>[ Inactivo ] Deshabilitar generación de puntos</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Valor de Compra Requerido</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="settings[point_purchase_amount]" value="<?= esc($settings['point_purchase_amount'] ?? '1000') ?>" min="1" step="1">
                            </div>
                            <small class="text-muted">Monto en COP (Ej. $1.000)</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Puntos Otorgados</label>
                            <input type="number" class="form-control" name="settings[point_reward_qty]" value="<?= esc($settings['point_reward_qty'] ?? '1') ?>" min="1" step="1">
                            <small class="text-muted">Puntos asignados por cada tramo</small>
                        </div>

                        <div class="col-md-6 border-top pt-3">
                            <label class="form-label fw-bold">Valor Monetario de Cada Punto (en COP)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="settings[point_monetary_value]" value="<?= esc($settings['point_monetary_value'] ?? '50') ?>" min="1">
                            </div>
                            <small class="text-muted">Ej: 50 COP por punto -> 100 puntos = $5.000 COP de descuento.</small>
                        </div>

                        <div class="col-md-6 border-top pt-3">
                            <label class="form-label fw-bold">Puntos Mínimos para Redimir</label>
                            <input type="number" class="form-control" name="settings[point_min_redeem]" value="<?= esc($settings['point_min_redeem'] ?? '50') ?>" min="0">
                            <small class="text-muted">Cantidad de puntos requeridos para poder usarlos en el carrito.</small>
                        </div>

                        <div class="col-md-6 border-top pt-3">
                            <label class="form-label fw-bold">Generación de Puntos con Cupones</label>
                            <select class="form-select" name="settings[points_allow_with_coupon]">
                                <option value="1" <?= ($settings['points_allow_with_coupon'] ?? '1') === '1' ? 'selected' : '' ?>>Sí, una compra con cupón sí genera puntos</option>
                                <option value="0" <?= ($settings['points_allow_with_coupon'] ?? '1') === '0' ? 'selected' : '' ?>>No, si aplica cupón no acumula puntos</option>
                            </select>
                        </div>

                        <div class="col-md-6 border-top pt-3">
                            <label class="form-label fw-bold">Cálculo de Puntos</label>
                            <select class="form-select" name="settings[points_calc_before_discount]">
                                <option value="after" <?= ($settings['points_calc_before_discount'] ?? 'after') === 'after' ? 'selected' : '' ?>>Calcular puntos SOBRE EL TOTAL FINAL (Después de descuentos)</option>
                                <option value="before" <?= ($settings['points_calc_before_discount'] ?? '') === 'before' ? 'selected' : '' ?>>Calcular puntos SOBRE EL SUBTOTAL BRUTO (Antes de descuentos)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 6. Correo SMTP -->
                <div class="tab-pane fade" id="smtp" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SMTP Host</label>
                            <input type="text" class="form-control" name="settings[smtp_host]" value="<?= esc($settings['smtp_host'] ?? '') ?>" placeholder="ej: smtp.gmail.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SMTP User</label>
                            <input type="text" class="form-control" name="settings[smtp_user]" value="<?= esc($settings['smtp_user'] ?? '') ?>" placeholder="ej: tu_correo@dominio.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">SMTP Password</label>
                            <input type="password" class="form-control" name="settings[smtp_pass]" value="<?= esc($settings['smtp_pass'] ?? '') ?>" placeholder="••••••••••••">
                            <small class="text-muted">Guardada de forma segura.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">SMTP Port</label>
                            <input type="number" class="form-control" name="settings[smtp_port]" value="<?= esc($settings['smtp_port'] ?? '587') ?>" placeholder="587">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">SMTP Crypto</label>
                            <select class="form-select" name="settings[smtp_crypto]">
                                <option value="tls" <?= ($settings['smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recomendado)</option>
                                <option value="ssl" <?= ($settings['smtp_crypto'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="none" <?= ($settings['smtp_crypto'] ?? '') === 'none' ? 'selected' : '' ?>>Ninguno</option>
                            </select>
                        </div>
                        <div class="col-md-6 border-top pt-3">
                            <label class="form-label fw-bold">From Email (Remitente desde el que salen los correos)</label>
                            <input type="email" class="form-control" name="settings[smtp_from_email]" value="<?= esc($settings['smtp_from_email'] ?? '') ?>" placeholder="notificaciones@dulcegracia.com">
                        </div>
                        <div class="col-md-6 border-top pt-3">
                            <label class="form-label fw-bold">From Name (Nombre del Remitente)</label>
                            <input type="text" class="form-control" name="settings[smtp_from_name]" value="<?= esc($settings['smtp_from_name'] ?? 'Dulce Gracia') ?>" placeholder="Dulce Gracia Repostería">
                        </div>
                        <div class="col-md-12 border-top pt-3">
                            <label class="form-label fw-bold"><i class="fas fa-envelope-open-text me-2 text-primary"></i>Correo de Notificaciones para Administrador (Destinatario de avisos de compras)</label>
                            <input type="email" class="form-control" name="settings[admin_notification_email]" value="<?= esc($settings['admin_notification_email'] ?? '') ?>" placeholder="pedidos@dulcegracia.com">
                            <small class="text-muted">Correo electrónico que recibirá las notificaciones automáticas cada vez que un cliente realice una nueva compra.</small>
                        </div>
                    </div>
                </div>

                <!-- 7. Copia de Seguridad -->
                <div class="tab-pane fade" id="backup" role="tabpanel">
                    <div class="p-3 border rounded-4 bg-light mb-3">
                        <h5 class="fw-bold text-dark mb-2"><i class="fas fa-shield-alt text-success me-2"></i>Respaldos de Base de Datos</h5>
                        <p class="text-muted mb-0 small">Genera y descarga una copia completa en formato <code>.sql</code> de la base de datos actual del sistema. Los archivos se protegen en ubicaciones privadas y conservan fecha y hora de Colombia.</p>
                    </div>
                </div>

                <!-- 8. Migración de Datos -->
                <div class="tab-pane fade" id="migration" role="tabpanel">
                    <div class="p-3 border rounded-4 bg-light mb-3">
                        <h5 class="fw-bold text-dark mb-2"><i class="fas fa-magic text-primary me-2"></i>Verificación y Actualización de Estructura</h5>
                        <p class="text-muted mb-0 small">Ejecuta las migraciones de CodeIgniter 4 de forma segura. Si falta alguna tabla o columna requerida, el sistema la creará automáticamente sin eliminar ni vaciar ningún dato existente.</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-dg-primary rounded-pill px-5"><i class="fas fa-save me-2"></i>Guardar Cambios</button>
            </div>
        </div>
    </div>
</form>

<!-- Formularios secundarios para probar SMTP, Backup y Migraciones -->
<div class="row g-3 mt-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 text-center">
                <i class="fas fa-paper-plane fa-2x text-primary mb-3"></i>
                <h6 class="fw-bold">Probar Conexión SMTP</h6>
                <p class="text-muted small">Envía un correo de prueba con la configuración activa.</p>
                <form action="<?= base_url('admin/configuracion/probar-smtp') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="email" name="test_email" class="form-control form-control-sm mb-3 rounded-pill text-center" placeholder="tu_correo@dominio.com" required>
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold">Enviar Correo Prueba</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 text-center">
                <i class="fas fa-download fa-2x text-success mb-3"></i>
                <h6 class="fw-bold">Crear Copia de Seguridad</h6>
                <p class="text-muted small">Descarga el respaldo completo <code>.sql</code> de la BD.</p>
                <form action="<?= base_url('admin/configuracion/backup') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill w-100 fw-bold mt-4"><i class="fas fa-file-export me-1"></i>Descargar Backup .SQL</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 text-center">
                <i class="fas fa-sync fa-2x text-warning mb-3"></i>
                <h6 class="fw-bold">Actualizar Estructura</h6>
                <p class="text-muted small">Verifica y aplica migraciones de DB pendientes.</p>
                <form action="<?= base_url('admin/configuracion/migrar') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning btn-sm rounded-pill w-100 fw-bold mt-4"><i class="fas fa-check-circle me-1"></i>Verificar y Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
