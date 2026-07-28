(function () {
    var template = document.getElementById('color-row-template');
    var rowsContainer = document.getElementById('color-rows');
    var addBtn = document.getElementById('add-color-row');

    if (!template || !rowsContainer || !addBtn) {
        return;
    }

    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.content : null;

    // Existing rows are rendered with real indices (0, 1, 2, ...) matching
    // colors[i][...]; new rows get the next index up so they never collide.
    var nextIndex = rowsContainer.querySelectorAll('[data-color-row]').length;

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

    // New files chosen for a row are only previewed client-side (they don't
    // upload until the whole form is submitted), unlike existing gallery
    // images which are already saved and can be removed immediately via AJAX.
    function bindImagesInput(row) {
        var input = row.querySelector('.color-images-input');
        var preview = row.querySelector('.color-new-images-preview');

        if (!input || !preview || input.dataset.bound) {
            return;
        }
        input.dataset.bound = 'true';

        input.addEventListener('change', function () {
            var files = Array.prototype.slice.call(input.files);
            preview.innerHTML = '';

            files.forEach(function (file) {
                var item = document.createElement('div');
                item.className = 'gallery-item';
                item.innerHTML = '<img src="' + URL.createObjectURL(file) + '" alt="">';
                preview.appendChild(item);
            });
        });
    }

    function bindExistingImages(row) {
        var container = row.querySelector('.color-existing-images');
        if (!container || container.dataset.bound) {
            return;
        }
        container.dataset.bound = 'true';

        container.addEventListener('click', function (event) {
            var removeBtn = event.target.closest('.gallery-item-remove');
            if (!removeBtn) {
                return;
            }

            fetch(removeBtn.dataset.url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (body) {
                    removeBtn.closest('.gallery-item').remove();
                    showToast(body.message, 'success');
                });
        });
    }

    function bindRemoveButton(row) {
        var btn = row.querySelector('[data-action="remove-color-row"]');
        if (!btn || btn.dataset.bound) {
            return;
        }
        btn.dataset.bound = 'true';

        btn.addEventListener('click', function () {
            row.remove();
        });
    }

    function bindColorSelect(row) {
        var select = row.querySelector('.color-row-select');
        var preview = row.querySelector('.color-row-swatch-preview');

        if (!select || !preview) {
            return;
        }

        function syncPreview() {
            var option = select.options[select.selectedIndex];
            var hex = option ? option.dataset.hex : '';
            preview.style.background = hex || '#f3f4f6';
        }

        if (!select.dataset.bound) {
            select.dataset.bound = 'true';
            select.addEventListener('change', syncPreview);
        }

        syncPreview();
    }

    function bindRow(row) {
        bindImagesInput(row);
        bindExistingImages(row);
        bindRemoveButton(row);
        bindColorSelect(row);
    }

    rowsContainer.querySelectorAll('[data-color-row]').forEach(bindRow);

    addBtn.addEventListener('click', function () {
        var html = template.innerHTML.split('__INDEX__').join(String(nextIndex));
        nextIndex += 1;

        var wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        var row = wrapper.firstElementChild;

        rowsContainer.appendChild(row);
        bindRow(row);
    });
})();
