<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <h2 class="mb-4" style="font-family: var(--font-heading); color: var(--dg-primary);">Finalizar Pedido</h2>
    
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form action="<?= base_url('checkout/procesar') ?>" method="POST" id="checkoutForm">
        <?= csrf_field() ?>
        
        <div class="row g-4">
            <!-- Formulario de Datos -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">1. Datos Personales</h4>
                        
                        <?php
                            $defaultName     = old('customer_name') ?: ($userData ? trim(($userData->name ?? '') . ' ' . ($userData->lastname ?? '')) : '');
                            $defaultPhone    = old('phone') ?: ($userData->phone ?? '');
                            $defaultWhatsapp = old('whatsapp') ?: ($userData->whatsapp ?? '');
                            $defaultEmail    = old('customer_email') ?: ($userData->email ?? '');
                        ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?= esc($defaultName) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_email" class="form-label">Correo electrónico <span class="text-muted small">(Opcional)</span></label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email" value="<?= esc($defaultEmail) ?>" placeholder="ejemplo@correo.com">
                                <small class="text-muted" style="font-size:0.75rem;">Puedes recibir el comprobante de tu compra aquí.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Teléfono *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= esc($defaultPhone) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp" class="form-label">WhatsApp <span class="text-muted small">(Opcional)</span></label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" value="<?= esc($defaultWhatsapp) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">2. Entrega</h4>
                        
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_type" id="delivery_domicilio" value="domicilio" <?= old('delivery_type', 'domicilio') === 'domicilio' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="delivery_domicilio">Envío a Domicilio</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_type" id="delivery_recoger" value="recoger" <?= old('delivery_type') === 'recoger' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="delivery_recoger">Recoger en Tienda</label>
                            </div>
                        </div>

                        <div id="domicilio-fields">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="delivery_zone_id" class="form-label">Zona de Envío *</label>
                                    <select class="form-select" id="delivery_zone_id" name="delivery_zone_id">
                                        <option value="">Selecciona una zona...</option>
                                        <?php foreach($zones as $z): ?>
                                            <option value="<?= $z->id ?>" data-cost="<?= $z->cost ?>" <?= old('delivery_zone_id') == $z->id ? 'selected' : '' ?>><?= esc($z->name) ?> (+ $<?= number_format($z->cost, 0, ',', '.') ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="neighborhood" class="form-label">Barrio</label>
                                    <input type="text" class="form-control" id="neighborhood" name="neighborhood" value="<?= old('neighborhood') ?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="address" class="form-label">Dirección de Entrega *</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?= old('address') ?>">
                                </div>
                                <div class="col-md-12">
                                    <label for="delivery_instructions" class="form-label">Instrucciones de llegada (Opcional)</label>
                                    <input type="text" class="form-control" id="delivery_instructions" name="delivery_instructions" placeholder="Apto, Casa, Conjunto, etc." value="<?= old('delivery_instructions') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4 border-bottom pb-2">3. Pago y Notas</h4>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Método de Pago *</label>
                            <div class="row g-2">
                                <div class="col-sm-4">
                                    <div class="form-check border rounded-3 p-3 text-center">
                                        <input class="form-check-input float-none ms-0 mb-2" type="radio" name="payment_method" id="pay_nequi" value="Nequi/Daviplata" required <?= old('payment_method') === 'Nequi/Daviplata' ? 'checked' : '' ?>>
                                        <label class="form-check-label d-block fw-bold" for="pay_nequi">
                                            Transferencia (Nequi/Daviplata)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-check border rounded-3 p-3 text-center">
                                        <input class="form-check-input float-none ms-0 mb-2" type="radio" name="payment_method" id="pay_cash" value="Efectivo" <?= old('payment_method') === 'Efectivo' ? 'checked' : '' ?>>
                                        <label class="form-check-label d-block fw-bold" for="pay_cash">
                                            Efectivo al recibir
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="form-label">Notas Adicionales sobre tu pedido (Opcional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Mensajes especiales para tarjetas, preferencias, etc."><?= old('notes') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen y Total -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4 bg-light rounded-4">
                        <h5 class="fw-bold mb-4">Tu Pedido</h5>
                        
                        <ul class="list-group list-group-flush mb-4 bg-transparent">
                            <?php foreach($cart as $item): ?>
                            <li class="list-group-item bg-transparent px-0 d-flex justify-content-between align-items-center border-bottom-0 pb-2">
                                <div>
                                    <h6 class="my-0"><?= esc($item['name']) ?></h6>
                                    <small class="text-muted">Cant: <?= $item['quantity'] ?></small>
                                </div>
                                <span class="text-muted">$<?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal / Valor real:</span>
                                <strong id="checkout-original-subtotal"><?= $summary['formatted']['original_price_subtotal'] ?></strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2 text-danger <?= ($summary['product_discount_total'] > 0) ? '' : 'd-none' ?>" id="checkout-product-discount-row">
                                <span>Descuentos productos:</span>
                                <strong id="checkout-product-discount"><?= $summary['formatted']['product_discount_total'] ?></strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal con descuento:</span>
                                <strong id="checkout-subtotal-after-discount"><?= $summary['formatted']['subtotal_after_product_discount'] ?></strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2 text-success <?= ($summary['coupon_discount_total'] > 0) ? '' : 'd-none' ?>" id="checkout-coupon-discount-row">
                                <span>Descuento Cupón (<?= esc($appliedCoupon['code'] ?? '') ?>):</span>
                                <strong id="checkout-coupon-discount"><?= $summary['formatted']['coupon_discount_total'] ?></strong>
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-secondary" id="shipping-row">
                                <span>Valor del Envío:</span>
                                <strong id="shipping-cost">$0</strong>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-4 border-top pt-2">
                                <h5 class="fw-bold">TOTAL A PAGAR:</h5>
                                <h4 class="fw-bold" style="color: var(--dg-primary);" id="final-total" data-subtotal-after-coupon="<?= $summary['subtotal_after_coupon'] ?>"><?= $summary['formatted']['final_total'] ?></h4>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-dg-primary btn-lg w-100 rounded-pill">
                            <i class="fas fa-check-circle me-2"></i> Confirmar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeDomicilio = document.getElementById('delivery_domicilio');
        const typeRecoger = document.getElementById('delivery_recoger');
        const domicilioFields = document.getElementById('domicilio-fields');
        const zoneSelect = document.getElementById('delivery_zone_id');
        const addressInput = document.getElementById('address');
        const shippingCostEl = document.getElementById('shipping-cost');
        const finalTotalEl = document.getElementById('final-total');
        const baseSubtotalAfterCoupon = parseFloat(finalTotalEl.getAttribute('data-subtotal-after-coupon'));

        function toggleDeliveryFields() {
            if (typeDomicilio.checked) {
                domicilioFields.style.display = 'block';
                zoneSelect.required = true;
                addressInput.required = true;
                updateTotal();
            } else {
                domicilioFields.style.display = 'none';
                zoneSelect.required = false;
                addressInput.required = false;
                zoneSelect.value = '';
                updateTotal(0);
            }
        }

        function updateTotal(forceCost = null) {
            let shippingCost = 0;
            
            if (forceCost !== null) {
                shippingCost = forceCost;
            } else if (zoneSelect.value && zoneSelect.options[zoneSelect.selectedIndex]) {
                shippingCost = parseFloat(zoneSelect.options[zoneSelect.selectedIndex].getAttribute('data-cost')) || 0;
            }

            shippingCostEl.innerText = '$' + new Intl.NumberFormat('es-CO').format(shippingCost);
            
            let total = baseSubtotalAfterCoupon + shippingCost;
            finalTotalEl.innerText = '$' + new Intl.NumberFormat('es-CO').format(total);
        }

        typeDomicilio.addEventListener('change', toggleDeliveryFields);
        typeRecoger.addEventListener('change', toggleDeliveryFields);
        zoneSelect.addEventListener('change', () => updateTotal());

        toggleDeliveryFields();
    });
</script>
<?= $this->endSection() ?>
