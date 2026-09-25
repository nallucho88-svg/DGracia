<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">Pedidos Recientes</h2>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nº Orden</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Tipo Entrega</th>
                        <th>Total</th>
                        <th>Estado Actual</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay pedidos registrados aún.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold"><?= esc($o->order_number) ?></span>
                            </td>
                            <td>
                                <div><?= date('d M Y', strtotime($o->created_at)) ?></div>
                                <small class="text-muted"><?= date('h:i a', strtotime($o->created_at)) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?= esc($o->customer_name) ?></div>
                                <small class="text-muted"><i class="fab fa-whatsapp text-success me-1"></i><?= esc($o->phone) ?></small>
                            </td>
                            <td>
                                <?php if($o->delivery_type == 'domicilio'): ?>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info"><i class="fas fa-motorcycle me-1"></i> Domicilio</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary"><i class="fas fa-store me-1"></i> Recoger</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold" style="color: var(--dg-primary);">$<?= number_format($o->total, 0, ',', '.') ?></td>
                            <td style="width: 200px;">
                                <select class="form-select form-select-sm status-select fw-bold" data-id="<?= $o->id ?>" 
                                    style="border-color: <?= $o->status == 'pendiente' ? '#ffc107' : ($o->status == 'entregado' ? '#198754' : '#dee2e6') ?>">
                                    <option value="pendiente" <?= $o->status == 'pendiente' ? 'selected' : '' ?>>🟡 Pendiente</option>
                                    <option value="en preparacion" <?= $o->status == 'en preparacion' ? 'selected' : '' ?>>👨‍🍳 En Preparación</option>
                                    <option value="enviado" <?= $o->status == 'enviado' ? 'selected' : '' ?>>🛵 Enviado / Listo</option>
                                    <option value="entregado" <?= $o->status == 'entregado' ? 'selected' : '' ?>>✅ Entregado</option>
                                    <option value="cancelado" <?= $o->status == 'cancelado' ? 'selected' : '' ?>>❌ Cancelado</option>
                                </select>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                        <li>
                                            <a class="dropdown-item btn-view-modal" href="#" data-id="<?= $o->id ?>">
                                                <i class="fas fa-eye text-primary me-2"></i> Ver detalle
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item btn-wa-modal" href="#" data-id="<?= $o->id ?>">
                                                <i class="fab fa-whatsapp text-success me-2"></i> Notificar WhatsApp
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('admin/pedidos/ver/'.$o->id) ?>">
                                                <i class="fas fa-external-link-alt text-secondary me-2"></i> Ver página completa
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

<!-- Modal Detalle de Pedido (Lectura) -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold" id="orderDetailModalLabel" style="color: var(--dg-primary);">
                    <i class="fas fa-file-invoice me-2"></i>Detalle del Pedido <span id="modalOrderNumber" class="text-dark"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                    <p class="text-muted mb-0">Cargando información del pedido...</p>
                </div>

                <div id="modalContent" style="display: none;">
                    <!-- Información General y Cliente -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-muted small text-uppercase mb-2"><i class="fas fa-user me-1"></i> Datos del Cliente</h6>
                                <div id="modalCustomerName" class="fw-bold fs-6"></div>
                                <div id="modalCustomerEmail" class="small text-muted mb-1"></div>
                                <div id="modalCustomerPhone" class="small text-success fw-bold"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-muted small text-uppercase mb-2"><i class="fas fa-truck me-1"></i> Entrega y Pago</h6>
                                <div><span id="modalDeliveryType" class="badge"></span></div>
                                <div id="modalAddress" class="small mt-1 text-muted"></div>
                                <div id="modalNotes" class="small mt-1 text-secondary fst-italic"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Productos Comprados -->
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-shopping-bag me-1 text-primary"></i> Productos del Pedido</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Descuento</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemsTable"></tbody>
                        </table>
                    </div>

                    <!-- Resumen Totales -->
                    <div class="row justify-content-end">
                        <div class="col-md-7">
                            <ul class="list-group list-group-flush rounded-3 border">
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="text-muted">Valor original:</span>
                                    <span id="modalOriginalValue" class="fw-bold"></span>
                                </li>
                                <li id="modalProdDiscRow" class="list-group-item d-flex justify-content-between align-items-center py-2 text-danger" style="display:none;">
                                    <span>Descuentos productos:</span>
                                    <span id="modalProdDiscount" class="fw-bold"></span>
                                </li>
                                <li id="modalCouponRow" class="list-group-item d-flex justify-content-between align-items-center py-2 text-danger" style="display:none;">
                                    <span>Cupón (<span id="modalCouponCode"></span>):</span>
                                    <span id="modalCouponDiscount" class="fw-bold"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="text-muted">Subtotal con descuento:</span>
                                    <span id="modalSubtotal" class="fw-bold"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="text-muted">Valor del Envío:</span>
                                    <span id="modalShippingCost" class="fw-bold"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                                    <span class="fs-6 fw-bold text-dark">TOTAL FINAL</span>
                                    <span id="modalTotal" class="fs-5 fw-bold text-success"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal WhatsApp Confirmation -->
<div class="modal fade" id="orderWaModal" tabindex="-1" aria-labelledby="orderWaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="orderWaModalLabel">
                    <i class="fab fa-whatsapp me-2"></i>Enviar Confirmación por WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="waPhoneInput" class="form-label fw-bold small text-uppercase">Teléfono Destino (Formato Colombia +57)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-success fw-bold"><i class="fab fa-whatsapp"></i></span>
                        <input type="text" class="form-control fw-bold" id="waPhoneInput" placeholder="Ej: 573108690810">
                    </div>
                    <div class="form-text">Asegúrate de incluir el código 57 antes del número de celular.</div>
                </div>

                <div class="mb-3">
                    <label for="waMessageTextarea" class="form-label fw-bold small text-uppercase">Mensaje de Confirmación</label>
                    <textarea class="form-control small" id="waMessageTextarea" rows="10" style="font-family: monospace; font-size: 0.85rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnSendWa">
                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const baseUrl = '<?= base_url() ?>';
    const detailModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    const waModal     = new bootstrap.Modal(document.getElementById('orderWaModal'));

    // Cambiar estado select
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            let id = this.getAttribute('data-id');
            let status = this.value;
            let originalValue = this.querySelector('option[selected]') ? this.querySelector('option[selected]').value : null;
            
            this.disabled = true;

            let formData = new FormData();
            formData.append('order_id', id);
            formData.append('status', status);

            fetch(baseUrl + 'admin/pedidos/cambiar-estado', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if(data.status === 'success') {
                    if(status === 'pendiente') this.style.borderColor = '#ffc107';
                    else if(status === 'entregado') this.style.borderColor = '#198754';
                    else this.style.borderColor = '#dee2e6';

                    Toastify({
                        text: "Estado del pedido actualizado",
                        duration: 3000,
                        gravity: "bottom", position: "right",
                        style: { background: "#198754" }
                    }).showToast();
                } else {
                    this.value = originalValue;
                    Toastify({
                        text: "Error al actualizar",
                        duration: 3000,
                        gravity: "bottom", position: "right",
                        style: { background: "#dc3545" }
                    }).showToast();
                }
            })
            .catch(err => {
                this.disabled = false;
                this.value = originalValue;
            });
        });
    });

    // Abrir Modal Detalle
    document.querySelectorAll('.btn-view-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const orderId = this.dataset.id;
            
            document.getElementById('modalLoading').style.display = 'block';
            document.getElementById('modalContent').style.display = 'none';
            detailModal.show();

            fetch(baseUrl + 'admin/pedidos/detalle-json/' + orderId)
                .then(r => r.json())
                .then(data => {
                    if(data.status === 'success') {
                        const o = data.order;
                        const items = data.items;

                        document.getElementById('modalOrderNumber').innerText = '#' + o.order_number;
                        document.getElementById('modalCustomerName').innerText = o.customer_name;
                        document.getElementById('modalCustomerEmail').innerText = o.customer_email || 'Sin correo registrado';
                        document.getElementById('modalCustomerPhone').innerText = '📞 ' + o.phone;

                        const delBadge = document.getElementById('modalDeliveryType');
                        if (o.delivery_type === 'domicilio') {
                            delBadge.className = 'badge bg-info text-dark';
                            delBadge.innerText = '🛵 Domicilio';
                        } else {
                            delBadge.className = 'badge bg-secondary';
                            delBadge.innerText = '🏪 Recogida en Tienda';
                        }

                        document.getElementById('modalAddress').innerText = o.address ? ('📍 ' + o.address) : 'Sin dirección';
                        document.getElementById('modalNotes').innerText = o.notes ? ('📝 ' + o.notes) : '';

                        // Render tabla de items
                        let tbody = '';
                        items.forEach(it => {
                            let origFmt = (parseFloat(it.original_price) > parseFloat(it.price)) 
                                ? `<small class="text-muted text-decoration-line-through d-block">$${numberFormat(it.original_price)}</small>` 
                                : '';
                            let discFmt = parseFloat(it.discount_amount) > 0 
                                ? `<span class="text-danger">-$${numberFormat(it.discount_amount * it.quantity)}</span>` 
                                : '<span class="text-muted">$0</span>';

                            tbody += `
                                <tr>
                                    <td><code>${it.sku || 'N/A'}</code></td>
                                    <td class="fw-bold">${it.product_name}</td>
                                    <td class="text-center fw-bold">${it.quantity}</td>
                                    <td class="text-end">${origFmt}<span class="fw-bold">$${numberFormat(it.price)}</span></td>
                                    <td class="text-end">${discFmt}</td>
                                    <td class="text-end fw-bold text-success">$${numberFormat(it.subtotal)}</td>
                                </tr>
                            `;
                        });
                        document.getElementById('modalItemsTable').innerHTML = tbody;

                        // Totales Históricos
                        document.getElementById('modalOriginalValue').innerText = '$' + numberFormat(data.originalSubtotal);
                        
                        const prodDiscRow = document.getElementById('modalProdDiscRow');
                        if (parseFloat(o.discount_amount || 0) > 0) {
                            prodDiscRow.style.display = 'flex';
                            document.getElementById('modalProdDiscount').innerText = '-$' + numberFormat(o.discount_amount);
                        } else {
                            prodDiscRow.style.display = 'none';
                        }

                        const couponRow = document.getElementById('modalCouponRow');
                        if (parseFloat(o.coupon_discount || 0) > 0) {
                            couponRow.style.display = 'flex';
                            document.getElementById('modalCouponCode').innerText = o.coupon_code || 'CUPON';
                            document.getElementById('modalCouponDiscount').innerText = '-$' + numberFormat(o.coupon_discount);
                        } else {
                            couponRow.style.display = 'none';
                        }

                        document.getElementById('modalSubtotal').innerText = '$' + numberFormat(o.subtotal);
                        document.getElementById('modalShippingCost').innerText = '$' + numberFormat(o.delivery_cost || 0);
                        document.getElementById('modalTotal').innerText = '$' + numberFormat(o.total);

                        document.getElementById('modalLoading').style.display = 'none';
                        document.getElementById('modalContent').style.display = 'block';
                    }
                });
        });
    });

    // Abrir Modal WhatsApp
    document.querySelectorAll('.btn-wa-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const orderId = this.dataset.id;

            fetch(baseUrl + 'admin/pedidos/detalle-json/' + orderId)
                .then(r => r.json())
                .then(data => {
                    if(data.status === 'success') {
                        document.getElementById('waPhoneInput').value = data.cleanPhone;
                        document.getElementById('waMessageTextarea').value = data.waMessage;
                        waModal.show();
                    }
                });
        });
    });

    // Enviar WhatsApp
    document.getElementById('btnSendWa').addEventListener('click', function() {
        let phone = document.getElementById('waPhoneInput').value.replace(/[^0-9]/g, '');
        let text  = document.getElementById('waMessageTextarea').value;

        if (!phone) {
            alert('Ingresa un número de teléfono válido.');
            return;
        }

        let url = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
        waModal.hide();
    });

    function numberFormat(num) {
        return new Intl.NumberFormat('es-CO').format(num);
    }
</script>
<?= $this->endSection() ?>
