(function () {
    var container = document.getElementById('cart-items');
    if (!container) {
        return;
    }

    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(message, type) {
        var stack = document.getElementById('toast-stack');
        if (!stack) {
            return;
        }
        var toast = document.createElement('div');
        toast.className = 'toast toast-' + (type || 'success');
        toast.textContent = message;
        stack.appendChild(toast);
        setTimeout(function () {
            toast.classList.add('is-leaving');
            setTimeout(function () {
                toast.remove();
            }, 200);
        }, 3000);
    }

    function updateCartCount(count) {
        var badge = document.getElementById('cart-count');
        if (!badge) {
            return;
        }
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
        badge.classList.remove('is-bumping');
        void badge.offsetWidth;
        badge.classList.add('is-bumping');
    }

    function reload() {
        fetch(window.location.pathname, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
        })
            .then(function (res) {
                return res.text();
            })
            .then(function (html) {
                container.innerHTML = html;
            });
    }

    function colorIdFromDataset(el) {
        return el.dataset.colorId ? parseInt(el.dataset.colorId, 10) : null;
    }

    function updateQuantity(productId, size, quantity, colorId) {
        return fetch('/cart/' + productId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ size: size, quantity: quantity, color_id: colorId }),
        }).then(function (res) {
            return res.json();
        });
    }

    function removeItem(productId, size, colorId) {
        return fetch('/cart/' + productId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ size: size, color_id: colorId }),
        }).then(function (res) {
            return res.json();
        });
    }

    container.addEventListener('click', function (event) {
        var decBtn = event.target.closest('[data-action="cart-qty-decrease"]');
        var incBtn = event.target.closest('[data-action="cart-qty-increase"]');
        var removeBtn = event.target.closest('[data-action="cart-remove"]');

        if (decBtn || incBtn) {
            var input = (decBtn || incBtn).parentElement.querySelector('.qty-input');
            var current = parseInt(input.value, 10) || 1;
            var next = decBtn ? current - 1 : current + 1;
            if (next < 1) {
                next = 1;
            }

            updateQuantity(input.dataset.productId, input.dataset.size, next, colorIdFromDataset(input)).then(function (body) {
                updateCartCount(body.count);
                reload();
            });
            return;
        }

        if (removeBtn) {
            removeItem(removeBtn.dataset.productId, removeBtn.dataset.size, colorIdFromDataset(removeBtn)).then(function (body) {
                updateCartCount(body.count);
                showToast('Item removed from cart.', 'success');
                reload();
            });
        }
    });

    container.addEventListener('change', function (event) {
        var input = event.target.closest('.qty-input');
        if (!input) {
            return;
        }

        var qty = parseInt(input.value, 10);
        if (isNaN(qty) || qty < 1) {
            qty = 1;
        }

        updateQuantity(input.dataset.productId, input.dataset.size, qty, colorIdFromDataset(input)).then(function (body) {
            updateCartCount(body.count);
            reload();
        });
    });
})();
