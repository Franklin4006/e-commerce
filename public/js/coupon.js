(function () {
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.content : null;

    function request(url, method, body) {
        return fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: body ? JSON.stringify(body) : undefined,
        }).then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, status: res.status, body: data };
            });
        });
    }

    document.addEventListener('click', function (event) {
        var applyBtn = event.target.closest('[data-action="apply-coupon"]');
        var removeBtn = event.target.closest('[data-action="remove-coupon"]');

        if (applyBtn) {
            var input = document.getElementById('coupon-code-input');
            var errorEl = document.getElementById('coupon-error');
            var code = input ? input.value.trim() : '';

            if (errorEl) {
                errorEl.style.display = 'none';
            }

            if (!code) {
                return;
            }

            applyBtn.disabled = true;

            request('/coupon', 'POST', { code: code }).then(function (result) {
                if (result.ok) {
                    window.location.reload();
                    return;
                }

                if (errorEl) {
                    errorEl.textContent = result.status === 401
                        ? 'Please login to apply a coupon.'
                        : (result.body.message || 'Unable to apply this coupon.');
                    errorEl.style.display = 'block';
                }

                applyBtn.disabled = false;
            });
        }

        if (removeBtn) {
            removeBtn.disabled = true;

            request('/coupon', 'DELETE').then(function () {
                window.location.reload();
            });
        }
    });
})();
