(function () {
    var checkbox = document.getElementById('billing_same_as_shipping');
    var billingList = document.getElementById('billing-address-list');

    if (checkbox && billingList) {
        var sync = function () {
            billingList.style.display = checkbox.checked ? 'none' : 'block';
        };

        checkbox.addEventListener('change', sync);
        sync();
    }

    document.querySelectorAll('input[name="shipping_address_id"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (!radio.checked) {
                return;
            }

            var url = new URL(window.location.href);
            url.searchParams.set('shipping_address_id', radio.value);
            window.location.href = url.toString();
        });
    });
})();
