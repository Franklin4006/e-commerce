(function () {
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.content : null;

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

    function addToCart(productId, size, quantity, colorId) {
        return fetch('/cart/' + productId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ size: size, quantity: quantity, color_id: colorId }),
        }).then(function (res) {
            return res.json().then(function (body) {
                return { ok: res.ok, body: body };
            });
        });
    }

    // Sets this single product as a standalone checkout selection (independent
    // of the cart) so checkout only ever includes this item, no matter what
    // else is already sitting in the cart.
    function buyNow(productId, size, quantity, colorId) {
        return fetch('/buy-now/' + productId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ size: size, quantity: quantity, color_id: colorId }),
        }).then(function (res) {
            return res.json().then(function (body) {
                return { ok: res.ok, body: body };
            });
        });
    }

    // Wires up the color selector + size selector + qty stepper + Add to
    // Cart / Buy Now controls inside a single `.product-detail-purchase`
    // container. This runs once for the product detail page's own buybox,
    // and again each time a Quick View modal is opened (a second, independent
    // instance can exist in the DOM at once, e.g. viewing a related
    // product's quick view while still on a PDP), so everything here is
    // scoped to `container` rather than relying on global element IDs.
    function initPurchaseBox(container) {
        if (!container || container.dataset.cartBound) {
            return;
        }
        container.dataset.cartBound = 'true';

        var productId = container.dataset.productId;
        var qtyInput = container.querySelector('.qty-input');
        var colorSelector = container.querySelector('.color-selector');
        var sizeSelector = container.querySelector('.size-selector');
        var stockText = container.querySelector('[data-role="stock-text"]');
        var addBtn = container.querySelector('.add-to-cart-btn');
        var buyBtn = container.querySelector('.buy-now-btn');

        var sizesByColor = {};
        var hasColors = false;
        if (sizeSelector) {
            try {
                sizesByColor = JSON.parse(sizeSelector.dataset.sizesByColor || '{}');
            } catch (e) {
                sizesByColor = {};
            }
            hasColors = sizeSelector.dataset.hasColors === '1';
        }

        var selectedColorId = hasColors ? null : 0;
        var selectedSize = null;
        var sizes = hasColors ? {} : (sizesByColor['0'] || {});

        // The quick-view modal's own image lives alongside `container`
        // (inside the same .quick-view-layout), not inside it — so it can't
        // be found with container.querySelector. The PDP's main image is a
        // page-level #main-image instead. Resolve whichever one actually
        // applies to this container up front. Quick-view has no thumbnail
        // rail (just the one image), so pdpThumbs stays null there.
        var quickViewLayout = container.closest('.quick-view-layout');
        var displayImage = quickViewLayout
            ? quickViewLayout.querySelector('.quick-view-image')
            : document.getElementById('main-image');
        var pdpThumbs = quickViewLayout ? null : document.getElementById('pdp-thumbs');

        // Rebuilds the whole gallery (thumbnail rail + main image) from the
        // selected color's own photos, so picking a color re-renders the
        // section instead of just swapping the single main image.
        function renderColorGallery(urls) {
            if (urls.length === 0) {
                return;
            }

            if (displayImage) {
                displayImage.src = urls[0];
            }

            if (!pdpThumbs) {
                return;
            }

            pdpThumbs.innerHTML = '';

            urls.forEach(function (url, index) {
                var thumb = document.createElement('img');
                thumb.src = url;
                thumb.alt = '';
                thumb.className = 'pdp-thumb' + (index === 0 ? ' active' : '');
                thumb.addEventListener('mouseover', function () {
                    if (displayImage) {
                        displayImage.src = url;
                    }
                });
                thumb.addEventListener('click', function () {
                    if (displayImage) {
                        displayImage.src = url;
                    }
                    pdpThumbs.querySelectorAll('.pdp-thumb').forEach(function (t) {
                        t.classList.remove('active');
                    });
                    thumb.classList.add('active');
                });
                pdpThumbs.appendChild(thumb);
            });
        }

        function applySizeAvailability() {
            if (!sizeSelector) {
                return;
            }
            sizeSelector.querySelectorAll('.size-option').forEach(function (btn) {
                var stock = sizes[btn.dataset.size] || 0;
                btn.disabled = stock <= 0;
                if (btn.disabled && btn.classList.contains('active')) {
                    btn.classList.remove('active');
                }
            });
        }

        function maxStock() {
            return selectedSize ? (sizes[selectedSize] || 0) : 0;
        }

        function clampQty() {
            var max = maxStock();
            var val = parseInt(qtyInput.value, 10);
            if (isNaN(val) || val < 1) {
                val = 1;
            }
            if (max > 0 && val > max) {
                val = max;
            }
            qtyInput.value = val;
        }

        function refreshState() {
            var max = maxStock();
            var needsColor = hasColors && selectedColorId === null;
            var hasSelection = !!selectedSize && !needsColor;
            var inStock = hasSelection && max > 0;

            if (addBtn) {
                addBtn.disabled = !inStock;
            }
            if (buyBtn) {
                buyBtn.disabled = !inStock;
            }

            if (stockText) {
                if (needsColor) {
                    stockText.textContent = 'Select a color to check availability';
                    stockText.className = 'buybox-stock';
                } else if (!selectedSize) {
                    stockText.textContent = 'Select a size to check availability';
                    stockText.className = 'buybox-stock';
                } else if (inStock) {
                    stockText.textContent = 'In stock';
                    stockText.className = 'buybox-stock in-stock';
                } else {
                    stockText.textContent = 'Out of stock in this size';
                    stockText.className = 'buybox-stock out-of-stock';
                }
            }

            if (hasSelection) {
                clampQty();
            }
        }

        if (colorSelector) {
            var colorNameEl = colorSelector.querySelector('[data-role="selected-color-name"]');

            colorSelector.querySelectorAll('.color-option').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    colorSelector.querySelectorAll('.color-option').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    btn.classList.add('active');

                    selectedColorId = parseInt(btn.dataset.colorId, 10);
                    sizes = sizesByColor[String(selectedColorId)] || {};

                    if (colorNameEl) {
                        colorNameEl.textContent = ' : ' + btn.dataset.colorName;
                    }

                    var galleryUrls = [];
                    try {
                        galleryUrls = JSON.parse(btn.dataset.images || '[]');
                    } catch (e) {
                        galleryUrls = [];
                    }
                    renderColorGallery(galleryUrls);

                    selectedSize = null;
                    qtyInput.value = 1;
                    applySizeAvailability();
                    refreshState();
                });
            });
        }

        if (sizeSelector) {
            sizeSelector.querySelectorAll('.size-option').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    if (btn.disabled) {
                        return;
                    }
                    sizeSelector.querySelectorAll('.size-option').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    btn.classList.add('active');
                    selectedSize = btn.dataset.size;
                    qtyInput.value = 1;
                    refreshState();
                });
            });
        }

        if (qtyInput) {
            var decBtn = container.querySelector('[data-action="qty-decrease"]');
            var incBtn = container.querySelector('[data-action="qty-increase"]');

            if (decBtn) {
                decBtn.addEventListener('click', function () {
                    qtyInput.value = (parseInt(qtyInput.value, 10) || 1) - 1;
                    clampQty();
                });
            }

            if (incBtn) {
                incBtn.addEventListener('click', function () {
                    qtyInput.value = (parseInt(qtyInput.value, 10) || 1) + 1;
                    clampQty();
                });
            }

            qtyInput.addEventListener('input', function () {
                qtyInput.value = qtyInput.value.replace(/[^0-9]/g, '');
            });

            qtyInput.addEventListener('blur', clampQty);
        }

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                if (!selectedSize || (hasColors && selectedColorId === null)) {
                    return;
                }
                clampQty();
                addBtn.disabled = true;

                addToCart(productId, selectedSize, parseInt(qtyInput.value, 10), selectedColorId)
                    .then(function (result) {
                        if (result.ok) {
                            updateCartCount(result.body.count);
                            showToast((addBtn.dataset.productName || 'Item') + ' added to cart.', 'success');
                        } else {
                            showToast(result.body.message || 'Something went wrong.', 'error');
                        }
                    })
                    .finally(function () {
                        refreshState();
                    });
            });
        }

        if (buyBtn) {
            buyBtn.addEventListener('click', function () {
                if (!selectedSize || (hasColors && selectedColorId === null)) {
                    return;
                }
                clampQty();
                buyBtn.disabled = true;

                buyNow(productId, selectedSize, parseInt(qtyInput.value, 10), selectedColorId)
                    .then(function (result) {
                        if (result.ok) {
                            window.location.href = result.body.redirect || '/checkout';
                        } else {
                            showToast(result.body.message || 'Something went wrong.', 'error');
                            buyBtn.disabled = false;
                        }
                    });
            });
        }

        applySizeAvailability();

        // Links from the cart / order pages carry ?color=&size= for the
        // combination the shopper actually picked, so the PDP lands on the
        // same one instead of whatever's auto-selected by default. Only
        // applies to the real page (not the quick-view modal, which has no
        // URL of its own to carry these).
        var requestedColorId = null;
        var requestedSize = null;
        if (!quickViewLayout) {
            var urlParams = new URLSearchParams(window.location.search);
            requestedColorId = urlParams.get('color');
            requestedSize = urlParams.get('size');
        }

        // Auto-select the first purchasable color + size, so a shopper lands
        // on a ready-to-buy combination instead of two empty prompts. Prefers
        // a requested color/size (from the URL) or one that's actually in
        // stock, falling back to just the first one (still lets an
        // out-of-stock product show its gallery).
        if (colorSelector) {
            var colorButtons = Array.prototype.slice.call(colorSelector.querySelectorAll('.color-option'));
            var colorToSelect = requestedColorId
                ? colorButtons.find(function (btn) {
                    return btn.dataset.colorId === requestedColorId;
                })
                : null;

            if (!colorToSelect) {
                colorToSelect = colorButtons.find(function (btn) {
                    var colorSizes = sizesByColor[btn.dataset.colorId] || {};
                    return Object.keys(colorSizes).some(function (size) {
                        return colorSizes[size] > 0;
                    });
                }) || colorButtons[0];
            }

            if (colorToSelect) {
                colorToSelect.click();
            }
        }

        if (sizeSelector) {
            var sizeOptions = Array.prototype.slice.call(sizeSelector.querySelectorAll('.size-option'));
            var sizeToSelect = requestedSize
                ? sizeOptions.find(function (btn) {
                    return btn.dataset.size === requestedSize && !btn.disabled;
                })
                : null;

            if (!sizeToSelect) {
                sizeToSelect = sizeOptions.find(function (btn) {
                    return !btn.disabled;
                });
            }

            if (sizeToSelect) {
                sizeToSelect.click();
            }
        }

        refreshState();
    }

    document.querySelectorAll('.product-detail-purchase').forEach(initPurchaseBox);

    // Quick View modal (listing/category/home cards). This script tag is
    // rendered inside the page's own content, which comes *before* the
    // #quick-view-modal markup that lives in the shared layout further down
    // the page — so it doesn't exist yet at the top of this file. Every
    // listener below is delegated on `document` (which always exists) and
    // looks the modal elements up fresh when actually needed, instead of
    // caching them once up front.
    function closeQuickView() {
        var quickViewModal = document.getElementById('quick-view-modal');
        if (!quickViewModal) {
            return;
        }
        quickViewModal.classList.remove('open');
    }

    function openQuickView(slug) {
        var quickViewModal = document.getElementById('quick-view-modal');
        var quickViewDialog = document.getElementById('quick-view-dialog');
        if (!quickViewModal || !quickViewDialog) {
            return;
        }

        fetch('/product/' + slug + '/quick-view', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
        })
            .then(function (res) {
                return res.text();
            })
            .then(function (html) {
                quickViewDialog.innerHTML = html;
                quickViewModal.classList.add('open');

                var purchaseBox = quickViewDialog.querySelector('.product-detail-purchase');
                if (purchaseBox) {
                    initPurchaseBox(purchaseBox);
                }
            });
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-action="quick-view"]');
        if (trigger) {
            event.preventDefault();
            openQuickView(trigger.dataset.productSlug);
            return;
        }

        if (event.target.closest('[data-action="close-quick-view"]')) {
            closeQuickView();
            return;
        }

        if (event.target.id === 'quick-view-modal') {
            closeQuickView();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        var quickViewModal = document.getElementById('quick-view-modal');
        if (quickViewModal && quickViewModal.classList.contains('open')) {
            closeQuickView();
        }
    });
})();
