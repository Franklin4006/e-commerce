(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var tableContainer = document.getElementById('banners-table');
    var bannerModal = document.getElementById('banner-modal');
    var deleteModal = document.getElementById('delete-modal');
    var form = document.getElementById('banner-form');
    var formError = document.getElementById('banner-form-error');

    var titleInput = document.getElementById('banner-title');
    var subTitleInput = document.getElementById('banner-sub_title');
    var titlePositionInput = document.getElementById('banner-title_position');
    var priorityInput = document.getElementById('banner-priority');
    var buttonTextInput = document.getElementById('banner-button_text');
    var buttonColorInput = document.getElementById('banner-button_color');
    var buttonUrlInput = document.getElementById('banner-button_url');
    var statusInput = document.getElementById('banner-status');

    var imageInput = document.getElementById('banner-image');
    var imagePreview = document.getElementById('banner-image-preview');

    var submitBtn = document.getElementById('banner-form-submit');
    var modalTitle = document.getElementById('banner-modal-title');
    var confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    var deleteBannerName = document.getElementById('delete-banner-name');

    var editingId = null;
    var deletingId = null;

    function bannersIndexUrl() {
        return window.location.pathname;
    }

    var currentTableUrl = bannersIndexUrl();

    function showToast(message, type) {
        var stack = document.getElementById('toast-stack');
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

    function clearFormErrors() {
        formError.style.display = 'none';
        formError.textContent = '';
        document.querySelectorAll('.field-error').forEach(function (el) {
            el.textContent = '';
        });
        document.querySelectorAll('.form-control').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }

    function showFormErrors(errors) {
        Object.keys(errors).forEach(function (field) {
            var fieldError = document.getElementById('banner-' + field + '-error');
            var input = document.getElementById('banner-' + field);
            if (fieldError) {
                fieldError.textContent = errors[field][0];
            }
            if (input) {
                input.classList.add('is-invalid');
            }
        });
    }

    function openModal(modal) {
        modal.classList.add('open');
    }

    function closeModal(modal) {
        modal.classList.remove('open');
    }

    function resetForm() {
        form.reset();
        editingId = null;
        clearFormErrors();
        imagePreview.style.display = 'none';
        imagePreview.src = '';
        imageInput.value = '';
        statusInput.value = '1';
        buttonColorInput.value = '#4f46e5';
        modalTitle.textContent = 'Add Banner';
    }

    function loadTable(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
        })
            .then(function (res) {
                return res.text();
            })
            .then(function (html) {
                tableContainer.innerHTML = html;
                currentTableUrl = url;
            });
    }

    document.querySelector('[data-action="add-banner"]').addEventListener('click', function () {
        resetForm();
        openModal(bannerModal);
        titleInput.focus();
    });

    document.querySelectorAll('[data-action="close-modal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeModal(bannerModal);
        });
    });

    document.querySelectorAll('[data-action="close-delete-modal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeModal(deleteModal);
        });
    });

    imageInput.addEventListener('change', function () {
        var file = imageInput.files[0];
        if (file) {
            imagePreview.src = URL.createObjectURL(file);
            imagePreview.style.display = 'block';
        }
    });

    tableContainer.addEventListener('click', function (event) {
        var editBtn = event.target.closest('[data-action="edit-banner"]');
        var deleteBtn = event.target.closest('[data-action="delete-banner"]');
        var pageLink = event.target.closest('.pagination a, nav[role="navigation"] a');

        if (editBtn) {
            var id = editBtn.dataset.id;
            fetch(bannersIndexUrl() + '/' + id, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (banner) {
                    resetForm();
                    editingId = banner.id;
                    modalTitle.textContent = 'Edit Banner';
                    titleInput.value = banner.title;
                    subTitleInput.value = banner.sub_title || '';
                    titlePositionInput.value = banner.title_position;
                    priorityInput.value = banner.priority;
                    buttonTextInput.value = banner.button_text || '';
                    buttonUrlInput.value = banner.button_url || '';
                    buttonColorInput.value = banner.button_color || '#4f46e5';
                    statusInput.value = banner.status ? '1' : '0';

                    if (banner.image_url) {
                        imagePreview.src = banner.image_url;
                        imagePreview.style.display = 'block';
                    }

                    openModal(bannerModal);
                });
            return;
        }

        if (deleteBtn) {
            deletingId = deleteBtn.dataset.id;
            deleteBannerName.textContent = deleteBtn.dataset.name;
            openModal(deleteModal);
            return;
        }

        if (pageLink) {
            event.preventDefault();
            loadTable(pageLink.href);
        }
    });

    confirmDeleteBtn.addEventListener('click', function () {
        confirmDeleteBtn.disabled = true;

        fetch(bannersIndexUrl() + '/' + deletingId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then(function (res) {
                return res.json().then(function (body) {
                    return { ok: res.ok, body: body };
                });
            })
            .then(function (result) {
                closeModal(deleteModal);
                confirmDeleteBtn.disabled = false;

                if (result.ok) {
                    showToast(result.body.message, 'success');
                    loadTable(currentTableUrl);
                } else {
                    showToast(result.body.message || 'Something went wrong.', 'error');
                }
            });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearFormErrors();

        var formData = new FormData(form);

        var url = bannersIndexUrl();
        if (editingId) {
            formData.append('_method', 'PUT');
            url = bannersIndexUrl() + '/' + editingId;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Saving';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(function (res) {
                return res.json().then(function (body) {
                    return { status: res.status, body: body };
                });
            })
            .then(function (result) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save';

                if (result.status === 422) {
                    if (result.body.errors) {
                        showFormErrors(result.body.errors);
                    }
                    return;
                }

                if (result.status >= 400) {
                    showToast(result.body.message || 'Something went wrong.', 'error');
                    return;
                }

                closeModal(bannerModal);
                showToast(result.body.message, 'success');
                loadTable(currentTableUrl);
            });
    });
})();
