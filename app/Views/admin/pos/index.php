<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="font-family: var(--font-heading); color: var(--dg-primary);">
            <i class="fas fa-cash-register me-2"></i>Caja / Punto de Venta (POS)
        </h2>
        <small class="text-muted">Registro rápido de ventas para tienda física</small>
    </div>
    <span class="badge bg-success rounded-pill px-3 py-2 fs-6">
        <i class="fas fa-circle me-1"></i> Caja Abierta
    </span>
</div>

<div class="row g-4">
    <!-- PANEL IZQUIERDO: Búsqueda y Catálogo de Productos -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                
                <!-- Buscador de Productos -->
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="posProductSearch" class="form-control border-start-0 rounded-end-pill py-2" placeholder="Buscar producto por nombre...">
                </div>

                <!-- Filtro por Categorías -->
                <div class="d-flex gap-2 overflow-auto pb-2 mb-3" id="posCategoryFilters">
                    <button class="btn btn-sm btn-dg-primary rounded-pill px-3 filter-cat active" data-cat="all">Todas</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-cat" data-cat="<?= $cat->id ?>">
                            <?= esc($cat->name) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Grid de Productos -->
                <div class="row g-3 overflow-auto pe-1" id="posProductGrid" style="max-height: 550px;">
                    <?php foreach ($products as $p): ?>
                    <?php 
                        $disc = $p->discount_info ?? ['has_discount' => false, 'original_price' => $p->price, 'final_price' => $p->price, 'discount_amount' => 0];
                        $hasDisc = !empty($disc['has_discount']);
                        $origPrice = (float)$disc['original_price'];
                        $finalPrice = (float)$disc['final_price'];
                    ?>
                    <div class="col-sm-6 col-md-4 pos-prod-item" data-cat="<?= $p->category_id ?>" data-name="<?= strtolower(esc($p->name)) ?>">
                        <div class="card border-0 shadow-sm rounded-3 h-100 p-2 text-center pos-card cursor-pointer position-relative" 
                             onclick="addToPosCart(<?= $p->id ?>, '<?= esc($p->name) ?>', <?= $finalPrice ?>, <?= $origPrice ?>)">
                            
                            <?php if ($hasDisc): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1 shadow-sm" style="font-size:0.7rem; z-index:2;">
                                    <i class="fas fa-tag me-1"></i>OFERTA <?= $disc['discount_percentage'] ?>% OFF
                                </span>
                            <?php endif; ?>

                            <img src="<?= $p->main_image ?>" class="rounded-3 mb-2" style="height: 100px; object-fit: cover; width: 100%;" alt="<?= esc($p->name) ?>">
                            
                            <h6 class="fw-bold mb-1 text-truncate small" title="<?= esc($p->name) ?>"><?= esc($p->name) ?></h6>
                            
                            <?php if ($hasDisc): ?>
                                <div class="text-muted text-decoration-line-through small">$<?= number_format($origPrice, 0, ',', '.') ?></div>
                                <div class="fw-bold text-danger small">-$<?= number_format($disc['discount_amount'], 0, ',', '.') ?></div>
                                <div class="fw-bold text-success fs-6">$<?= number_format($finalPrice, 0, ',', '.') ?></div>
                            <?php else: ?>
                                <div class="fw-bold text-success small">$<?= number_format($finalPrice, 0, ',', '.') ?></div>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-sm btn-dg-primary rounded-pill mt-2 py-1 w-100 small">
                                <i class="fas fa-plus me-1"></i>Agregar
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- PANEL DERECHO: Ticket de Venta / Carrito POS -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                
                <!-- Búsqueda y Selección de Cliente -->
                <label class="form-label fw-bold small text-muted mb-1"><i class="fas fa-user me-1"></i>Cliente en Caja</label>
                <div class="position-relative mb-2">
                    <input type="text" id="posCustomerInput" class="form-control rounded-pill" placeholder="Buscar cliente por nombre o teléfono..." autocomplete="off">
                    <div id="customerResults" class="dropdown-menu shadow-lg border-0 rounded-4 w-100 p-2 position-absolute" style="max-height: 200px; overflow-y: auto;"></div>
                </div>

                <!-- Tarjeta del Cliente Seleccionado -->
                <div id="selectedCustomerBadge" class="alert alert-light border rounded-3 p-2 mb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block text-dark small" id="custName">Cliente Mostrador (Genérico)</strong>
                        <span class="text-muted small" id="custInfo">Sin puntos vinculados</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" id="btnClearCustomer" style="display: none;" onclick="clearPosCustomer()">
                        <i class="fas fa-times-circle fs-5"></i>
                    </button>
                </div>

            </div>

            <!-- Tabla de Productos en el Ticket -->
            <div class="card-body p-3 overflow-auto flex-grow-1" style="max-height: 320px;">
                <table class="table table-sm table-borderless align-middle mb-0">
                    <thead class="border-bottom">
                        <tr class="text-muted small">
                            <th>Producto</th>
                            <th class="text-center" width="25%">Cant.</th>
                            <th class="text-end" width="25%">Subtotal</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody id="posTicketBody">
                        <tr id="emptyPosCartRow">
                            <td colspan="4" class="text-center text-muted py-4 small">
                                <i class="fas fa-shopping-basket fa-2x mb-2 d-block opacity-25"></i>
                                Selecciona productos a la izquierda para iniciar la venta.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pie del Ticket: Totales y Cobro -->
            <div class="card-footer bg-light border-top p-4 rounded-bottom-4">
                
                <!-- Descuento Cliente Fiel Switch -->
                <div class="d-flex justify-content-between align-items-center mb-2" id="loyaltySwitchWrapper" style="display: none !important;">
                    <span class="small fw-bold text-primary"><i class="fas fa-star me-1 text-warning"></i>Descuento Cliente Fiel (<?= $loyalDiscountPct ?>%)</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="checkLoyalty" onchange="renderPosTotals()">
                    </div>
                </div>

                <!-- Cupón de Descuento POS -->
                <div class="mb-3 border-bottom pb-2">
                    <label class="form-label fw-bold small text-muted mb-1"><i class="fas fa-ticket-alt me-1 text-primary"></i>Cupón de Descuento</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="posCouponInput" class="form-control text-uppercase" placeholder="Código de cupón...">
                        <button class="btn btn-outline-primary" type="button" id="btnApplyPosCoupon" onclick="applyPosCoupon()">Aplicar</button>
                    </div>
                    <div id="posCouponMsg" class="mt-1 small"></div>
                </div>

                <div class="d-flex justify-content-between mb-1 small">
                    <span>Subtotal Productos:</span>
                    <span class="fw-bold" id="posSubtotal">$0</span>
                </div>

                <div class="d-flex justify-content-between mb-1 small text-danger" id="posCouponDiscountRow" style="display: none;">
                    <span>Descuento Cupón (<span id="posCouponCodeBadge"></span>):</span>
                    <span class="fw-bold" id="posCouponDiscount">-$0</span>
                </div>

                <div class="d-flex justify-content-between mb-1 small text-danger" id="posDiscountRow" style="display: none;">
                    <span>Descuento Fiel:</span>
                    <span class="fw-bold" id="posDiscount">-$0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                    <span class="fs-5 fw-bold text-dark">TOTAL:</span>
                    <span class="fs-3 fw-bold text-success" id="posTotal">$0</span>
                </div>

                <!-- Método de Pago -->
                <div class="mt-3 mb-3">
                    <label class="form-label fw-bold small text-muted mb-1">Método de Pago</label>
                    <select id="posPaymentMethod" class="form-select rounded-pill">
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Nequi / Daviplata">📱 Nequi / Daviplata</option>
                        <option value="Tarjeta">💳 Tarjeta Débito/Crédito</option>
                        <option value="Transferencia">🏦 Transferencia Bancaria</option>
                    </select>
                </div>

                <button type="button" class="btn btn-success btn-lg w-100 rounded-pill py-3 fw-bold shadow" id="btnProcessPos" onclick="processPosSale()">
                    <i class="fas fa-check-circle me-2"></i>REGISTRAR VENTA
                </button>

            </div>
        </div>
    </div>
</div>

<style>
.pos-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.pos-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let posCart = [];
let selectedCustomer = null;
let posAppliedCoupon = null;
const loyalPct = <?= $loyalDiscountPct ?>;

// Agregar producto al Ticket POS
function addToPosCart(id, name, price, normalPrice = null) {
    if (normalPrice === null) normalPrice = price;
    let item = posCart.find(i => i.id === id);
    if (item) {
        item.qty += 1;
    } else {
        posCart.push({ id: id, name: name, price: price, normal_price: normalPrice, qty: 1 });
    }
    renderPosCart();
}

// Renderizar tabla del Ticket POS
function renderPosCart() {
    const tbody = document.getElementById('posTicketBody');
    if (posCart.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyPosCartRow">
                <td colspan="4" class="text-center text-muted py-4 small">
                    <i class="fas fa-shopping-basket fa-2x mb-2 d-block opacity-25"></i>
                    Selecciona productos a la izquierda para iniciar la venta.
                </td>
            </tr>`;
        posAppliedCoupon = null;
        document.getElementById('posCouponInput').value = '';
        document.getElementById('posCouponMsg').innerHTML = '';
        renderPosTotals();
        return;
    }

    let html = '';
    posCart.forEach((item, index) => {
        let itemSub = item.price * item.qty;
        let origFmt = (item.normal_price > item.price) 
            ? `<small class="text-muted text-decoration-line-through d-block">$${(item.normal_price * item.qty).toLocaleString('es-CO')}</small>` 
            : '';
        html += `
            <tr>
                <td><strong class="small">${item.name}</strong></td>
                <td class="text-center">
                    <div class="input-group input-group-sm justify-content-center">
                        <button class="btn btn-outline-secondary px-2 py-0" onclick="changeQty(${index}, -1)">-</button>
                        <span class="input-group-text px-2 py-0 bg-white">${item.qty}</span>
                        <button class="btn btn-outline-secondary px-2 py-0" onclick="changeQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold small">${origFmt}$${itemSub.toLocaleString('es-CO')}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removePosItem(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;
    renderPosTotals();
}

function changeQty(index, delta) {
    posCart[index].qty += delta;
    if (posCart[index].qty <= 0) {
        posCart.splice(index, 1);
    }
    renderPosCart();
}

function removePosItem(index) {
    posCart.splice(index, 1);
    renderPosCart();
}

// Calcular Totales POS
function renderPosTotals() {
    let subtotal = posCart.reduce((sum, i) => sum + (i.price * i.qty), 0);
    
    let couponDiscount = 0;
    if (posAppliedCoupon && posAppliedCoupon.valid) {
        if (posAppliedCoupon.coupon.discount_type === 'percentage') {
            couponDiscount = subtotal * (parseFloat(posAppliedCoupon.coupon.value) / 100);
        } else {
            couponDiscount = Math.min(subtotal, parseFloat(posAppliedCoupon.coupon.value));
        }
    }

    let subtotalAfterCoupon = Math.max(0, subtotal - couponDiscount);
    let loyaltyDiscount = 0;

    const checkLoyalty = document.getElementById('checkLoyalty');
    if (selectedCustomer && (selectedCustomer.is_loyal == 1 || checkLoyalty.checked)) {
        loyaltyDiscount = subtotalAfterCoupon * (loyalPct / 100);
    }

    let total = Math.max(0, subtotalAfterCoupon - loyaltyDiscount);

    document.getElementById('posSubtotal').innerText = '$' + subtotal.toLocaleString('es-CO');
    
    // Cupón UI
    if (couponDiscount > 0 && posAppliedCoupon) {
        document.getElementById('posCouponDiscountRow').style.display = 'flex';
        document.getElementById('posCouponCodeBadge').innerText = posAppliedCoupon.code;
        document.getElementById('posCouponDiscount').innerText = '-$' + couponDiscount.toLocaleString('es-CO');
    } else {
        document.getElementById('posCouponDiscountRow').style.display = 'none';
    }

    // Cliente Fiel UI
    if (loyaltyDiscount > 0) {
        document.getElementById('posDiscountRow').style.display = 'flex';
        document.getElementById('posDiscount').innerText = '-$' + loyaltyDiscount.toLocaleString('es-CO');
    } else {
        document.getElementById('posDiscountRow').style.display = 'none';
    }

    document.getElementById('posTotal').innerText = '$' + total.toLocaleString('es-CO');
}

// Aplicar Cupón en POS
function applyPosCoupon() {
    const input = document.getElementById('posCouponInput');
    const msgBox = document.getElementById('posCouponMsg');
    const btn = document.getElementById('btnApplyPosCoupon');
    const code = input.value.trim();

    if (!code) {
        msgBox.innerHTML = '<span class="text-danger">Ingresa un código.</span>';
        return;
    }

    let subtotal = posCart.reduce((sum, i) => sum + (i.price * i.qty), 0);
    if (subtotal <= 0) {
        msgBox.innerHTML = '<span class="text-danger">El ticket está vacío.</span>';
        return;
    }

    btn.disabled = true;
    btn.innerText = '...';

    const formData = new FormData();
    formData.append('code', code);
    formData.append('subtotal', subtotal);
    formData.append('customer_id', selectedCustomer ? selectedCustomer.id : 0);

    fetch('<?= base_url('admin/pos/validate-coupon') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = 'Aplicar';

        if (data.valid) {
            posAppliedCoupon = data;
            msgBox.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>¡Cupón ${data.code} aplicado!</span>`;
            renderPosTotals();
        } else {
            posAppliedCoupon = null;
            msgBox.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i>${data.message}</span>`;
            renderPosTotals();
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Aplicar';
        msgBox.innerHTML = '<span class="text-danger">Error al validar el cupón.</span>';
    });
}

// Búsqueda AJAX de Cliente en POS
document.getElementById('posCustomerInput').addEventListener('input', function () {
    let q = this.value.trim();
    const dropdown = document.getElementById('customerResults');

    if (q.length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    fetch('<?= base_url('admin/pos/search-customers') ?>?q=' + encodeURIComponent(q), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.length === 0) {
            dropdown.innerHTML = '<div class="dropdown-item disabled text-muted small">No se encontraron clientes</div>';
        } else {
            let html = '';
            data.forEach(c => {
                let loyalBadge = c.is_loyal == 1 ? '<span class="badge bg-warning text-dark ms-1">Fiel</span>' : '';
                html += `
                    <button type="button" class="dropdown-item small" onclick="selectPosCustomer(${c.id}, '${c.name} ${c.lastname || ''}', '${c.phone || ''}', ${c.points || 0}, ${c.is_loyal || 0})">
                        <strong>${c.name} ${c.lastname || ''}</strong> ${loyalBadge}<br>
                        <small class="text-muted">${c.email} | Tel: ${c.phone || 'N/A'} | ${c.points || 0} pts</small>
                    </button>`;
            });
            dropdown.innerHTML = html;
        }
        dropdown.style.display = 'block';
    });
});

function selectPosCustomer(id, name, phone, points, isLoyal) {
    selectedCustomer = { id: id, name: name, phone: phone, points: points, is_loyal: isLoyal };
    document.getElementById('custName').innerText = name + (isLoyal == 1 ? ' ★ (Cliente Fiel)' : '');
    document.getElementById('custInfo').innerText = 'Puntos actuales: ' + points + ' pts | Tel: ' + (phone || 'N/A');
    document.getElementById('btnClearCustomer').style.display = 'block';
    document.getElementById('customerResults').style.display = 'none';
    document.getElementById('posCustomerInput').value = '';
    
    document.getElementById('loyaltySwitchWrapper').style.display = 'flex';
    if (isLoyal == 1) {
        document.getElementById('checkLoyalty').checked = true;
    }

    renderPosTotals();
}

function clearPosCustomer() {
    selectedCustomer = null;
    document.getElementById('custName').innerText = 'Cliente Mostrador (Genérico)';
    document.getElementById('custInfo').innerText = 'Sin puntos vinculados';
    document.getElementById('btnClearCustomer').style.display = 'none';
    document.getElementById('loyaltySwitchWrapper').style.display = 'none';
    document.getElementById('checkLoyalty').checked = false;
    renderPosTotals();
}

// Filtro de categorías en cuadrícula
document.querySelectorAll('.filter-cat').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-cat').forEach(b => {
            b.classList.remove('btn-dg-primary', 'active');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-dg-primary', 'active');

        let cat = this.dataset.cat;
        document.querySelectorAll('.pos-prod-item').forEach(item => {
            if (cat === 'all' || item.dataset.cat === cat) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Buscador de texto en cuadrícula
document.getElementById('posProductSearch').addEventListener('input', function () {
    let val = this.value.toLowerCase();
    document.querySelectorAll('.pos-prod-item').forEach(item => {
        if (item.dataset.name.includes(val)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Procesar Venta en Caja
function processPosSale() {
    if (posCart.length === 0) {
        DGAlert.warning('Caja POS', 'Debes agregar al menos un producto al ticket.');
        return;
    }

    const btn = document.getElementById('btnProcessPos');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESANDO VENTA...';

    const formData = new FormData();
    formData.append('items', JSON.stringify(posCart));
    formData.append('customer_id', selectedCustomer ? selectedCustomer.id : 0);
    formData.append('payment_method', document.getElementById('posPaymentMethod').value);
    formData.append('apply_loyalty_discount', document.getElementById('checkLoyalty').checked ? '1' : '0');
    formData.append('coupon_code', (posAppliedCoupon && posAppliedCoupon.valid) ? posAppliedCoupon.code : '');
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= base_url('admin/pos/process-sale') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            DGAlert.show({
                type: 'purchase',
                title: '✓ Venta Registrada',
                message: `${res.order_number} por ${res.total}. ${res.points_earned > 0 ? '+' + res.points_earned + ' pts generados.' : ''}`,
                duration: 6000
            });

            // Limpiar carrito e interfaz
            posCart = [];
            posAppliedCoupon = null;
            document.getElementById('posCouponInput').value = '';
            document.getElementById('posCouponMsg').innerHTML = '';
            clearPosCustomer();
            renderPosCart();
        } else {
            DGAlert.error('Error en Venta', res.message || 'No se pudo procesar la venta.');
        }
    })
    .catch(() => {
        DGAlert.error('Error', 'Ocurrió un fallo en el servidor.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>REGISTRAR VENTA';
    });
}
</script>
<?= $this->endSection() ?>
