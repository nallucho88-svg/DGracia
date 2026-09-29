document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || (window.location.origin + '/DGracia/public/');

    // Hover & Click logic for Mini Cart Dropdown
    const miniCartWrapper = document.getElementById('miniCartWrapper');
    const miniCartTrigger = document.getElementById('miniCartTrigger');

    if (miniCartWrapper && miniCartTrigger) {
        // Desktop Hover
        miniCartWrapper.addEventListener('mouseenter', function() {
            if (window.innerWidth >= 992) {
                const bsDropdown = bootstrap.Dropdown.getOrCreateInstance(miniCartTrigger);
                bsDropdown.show();
                fetchMiniCart();
            }
        });

        miniCartWrapper.addEventListener('mouseleave', function() {
            if (window.innerWidth >= 992) {
                const bsDropdown = bootstrap.Dropdown.getInstance(miniCartTrigger);
                if (bsDropdown) bsDropdown.hide();
            }
        });

        // Click / Tap for touch devices
        miniCartTrigger.addEventListener('click', function(e) {
            fetchMiniCart();
        });
    }

    // Function to fetch mini cart HTML/JSON
    window.fetchMiniCart = function() {
        const miniCartItems = document.getElementById('mini-cart-items');
        const miniCartTotal = document.getElementById('mini-cart-total');
        const miniCartCountLabel = document.getElementById('mini-cart-count-label');
        const cartCounter = document.getElementById('cart-counter');

        fetch(baseUrl + 'carrito/mini')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (cartCounter) cartCounter.innerText = data.cart_count;
                    if (miniCartCountLabel) miniCartCountLabel.innerText = data.cart_count + ' productos';
                    if (miniCartTotal) miniCartTotal.innerText = data.total_formatted;

                    if (!data.cart || data.cart.length === 0) {
                        if (miniCartItems) {
                            miniCartItems.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-shopping-basket fa-2x mb-2 d-block opacity-50"></i>Tu carrito está vacío</div>';
                        }
                    } else {
                        let html = '<div class="list-group list-group-flush">';
                        data.cart.forEach(item => {
                            let subtotalFmt = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(item.subtotal);
                            let normalFmt   = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(item.normal_price || item.price);
                            let offerFmt    = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(item.price);
                            let discFmt     = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format((item.discount_amount || 0) * item.quantity);

                            let offerHtml = item.has_discount ? `
                                <div class="text-success small fw-semibold">Oferta: ${offerFmt}</div>
                                <div class="text-danger small" style="font-size:0.75rem;">Ahorro: -${discFmt}</div>
                            ` : '';

                            html += `
                                <div class="list-group-item px-0 py-2 d-flex align-items-start justify-content-between border-bottom bg-transparent">
                                    <img src="${item.image}" alt="${item.name}" class="rounded-3 me-2 mt-1" style="width: 48px; height: 48px; object-fit: cover;">
                                    <div class="flex-grow-1 me-2" style="font-size: 0.85rem;">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px;" title="${item.name}">${item.name}</div>
                                        <div class="text-muted small">${item.quantity} × ${normalFmt}</div>
                                        ${offerHtml}
                                    </div>
                                    <div class="text-end" style="font-size: 0.85rem;">
                                        <div class="small text-muted">Subtotal</div>
                                        <span class="fw-bold text-primary">${subtotalFmt}</span>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        if (miniCartItems) miniCartItems.innerHTML = html;
                    }
                }
            })
            .catch(err => console.error('Error fetching mini cart:', err));
    };

    // Global Add to Cart function with immediate optimistic feedback & audio chime
    window.addToCart = function(productId, quantity = 1, btnElement = null) {
        let originalContent = '';
        if (btnElement) {
            originalContent = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>...';
        }

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        fetch(baseUrl + 'carrito/agregar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (typeof DGAlert !== 'undefined') {
                    DGAlert.show({
                        type: 'success',
                        title: '🛒 Carrito',
                        message: data.message || 'Producto agregado al carrito',
                        duration: 3000
                    });
                } else if (typeof Toastify === 'function') {
                    Toastify({
                        text: "🛒 " + (data.message || 'Producto agregado al carrito'),
                        duration: 3000,
                        gravity: "bottom",
                        position: "right",
                        style: { background: "var(--dg-primary)", color: "#fff", borderRadius: "10px" }
                    }).showToast();
                }

                updateCartCounter(data.cart_count);
                fetchMiniCart();

                if (btnElement) {
                    btnElement.innerHTML = '<i class="fas fa-check me-1"></i>¡Agregado!';
                    setTimeout(() => {
                        btnElement.innerHTML = originalContent;
                        btnElement.disabled = false;
                    }, 1500);
                }
            } else {
                if (typeof DGAlert !== 'undefined') {
                    DGAlert.error('Atención', data.message || 'No se pudo agregar el producto');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atención',
                        text: data.message || 'No se pudo agregar el producto',
                        confirmButtonColor: 'var(--dg-primary)'
                    });
                }
                if (btnElement) {
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;
                }
            }
        })
        .catch(err => {
            console.error('Error adding to cart:', err);
            if (btnElement) {
                btnElement.innerHTML = originalContent;
                btnElement.disabled = false;
            }
        });
    };

    function updateCartCounter(count) {
        const counter = document.getElementById('cart-counter');
        if (counter) {
            counter.innerText = count;
            counter.style.transform = 'scale(1.4) translate(-50%, -50%)';
            setTimeout(() => {
                counter.style.transform = 'translate(-50%, -50%)';
            }, 200);
        }
    }

    // Suggestions Form submit
    const formSuggestions = document.getElementById('form-suggestions');
    if (formSuggestions) {
        formSuggestions.addEventListener('submit', function(e) {
            e.preventDefault();
            let submitBtn = this.querySelector('button[type="submit"]');
            let originalText = submitBtn.innerText;
            submitBtn.innerText = 'Enviando...';
            submitBtn.disabled = true;

            setTimeout(() => {
                const modalEl = document.getElementById('suggestionsModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
                Swal.fire({
                    title: '¡Mensaje Enviado!',
                    text: 'Gracias por escribirnos. Revisaremos tu mensaje pronto.',
                    icon: 'success',
                    confirmButtonColor: 'var(--dg-primary)'
                });
                formSuggestions.reset();
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }, 1000);
        });
    }

    // Global Favorite Toggle Handler
    document.addEventListener('click', function(e) {
        const favBtn = e.target.closest('.btn-toggle-favorite');
        if (!favBtn) return;
        e.preventDefault();

        const productId = favBtn.dataset.productId;
        if (!productId) return;

        fetch(baseUrl + 'favorito/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'product_id=' + productId
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'unauthorized') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: '¡Inicia sesión!',
                        text: 'Debes iniciar sesión para guardar productos en tus favoritos.',
                        confirmButtonText: 'Iniciar Sesión',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: 'var(--dg-primary)'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = baseUrl + 'login';
                        }
                    });
                } else {
                    alert('Debes iniciar sesión para guardar favoritos.');
                }
            } else if (res.status === 'success') {
                const icon = favBtn.querySelector('i');
                if (res.is_favorite) {
                    if (icon) icon.className = 'fas fa-heart text-danger';
                    else favBtn.textContent = '♥';
                    favBtn.classList.add('is-favorite');
                    favBtn.classList.add('text-danger');
                    favBtn.classList.remove('text-muted');
                } else {
                    if (icon) icon.className = 'far fa-heart text-muted';
                    else favBtn.textContent = '♡';
                    favBtn.classList.remove('is-favorite');
                    favBtn.classList.remove('text-danger');
                    favBtn.classList.add('text-muted');
                }

                if (typeof DGAlert !== 'undefined') {
                    DGAlert.show({
                        type: 'info',
                        title: '❤️ Favoritos',
                        message: res.message,
                        duration: 3000
                    });
                } else if (typeof Toastify === 'function') {
                    Toastify({
                        text: (res.is_favorite ? '❤️ ' : '🤍 ') + res.message,
                        duration: 2500,
                        gravity: "bottom",
                        position: "right"
                    }).showToast();
                }
            }
        })
        .catch(err => console.error('Error toggling favorite:', err));
    });

    // Initial fetch of mini cart
    fetchMiniCart();
});
