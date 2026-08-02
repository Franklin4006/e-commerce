(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var tableContainer = document.getElementById('categories-table');
    var categoryModal = document.getElementById('category-modal');
    var deleteModal = document.getElementById('delete-modal');
    var form = document.getElementById('category-form');
    var formError = document.getElementById('category-form-error');
    var nameInput = document.getElementById('category-name');
    var imageInput = document.getElementById('category-image');
    var imagePreview = document.getElementById('category-image-preview');
    var statusInput = document.getElementById('category-status');
    var priorityInput = document.getElementById('category-priority');
    var submitBtn = document.getElementById('category-form-submit');
    var modalTitle = document.getElementById('category-modal-title');
    var confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    var deleteCategoryName = document.getElementById('delete-category-name');

    var editingId = null;
    var deletingId = null;

    function categoriesIndexUrl() {
        return window.location.pathname;
    }

    var currentTableUrl = categoriesIndexUrl();

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
            var fieldError = document.getElementById('category-' + field + '-error');
            var input = document.getElementById('category-' + field);
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
        modalTitle.textContent = 'Add Category';
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

    document.querySelector('[data-action="add-category"]').addEventListener('click', function () {
        resetForm();
        openModal(categoryModal);
        nameInput.focus();
    });

    document.querySelectorAll('[data-action="close-modal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeModal(categoryModal);
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
        var editBtn = event.target.closest('[data-action="edit-category"]');
        var deleteBtn = event.target.closest('[data-action="delete-category"]');
        var pageLink = event.target.closest('.pagination a, nav[role="navigation"] a');

        if (editBtn) {
            var id = editBtn.dataset.id;
            fetch(categoriesIndexUrl() + '/' + id, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (category) {
                    resetForm();
                    editingId = category.id;
                    modalTitle.textContent = 'Edit Category';
                    nameInput.value = category.name;
                    statusInput.value = category.status ? '1' : '0';
                    priorityInput.value = category.priority;
                    if (category.image_url) {
                        imagePreview.src = category.image_url;
                        imagePreview.style.display = 'block';
                    }
                    openModal(categoryModal);
                });
            return;
        }

        if (deleteBtn) {
            deletingId = deleteBtn.dataset.id;
            deleteCategoryName.textContent = deleteBtn.dataset.name;
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

        fetch(categoriesIndexUrl() + '/' + deletingId, {
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

        var url = categoriesIndexUrl();
        if (editingId) {
            formData.append('_method', 'PUT');
            url = categoriesIndexUrl() + '/' + editingId;
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

                closeModal(categoryModal);
                showToast(result.body.message, 'success');
                loadTable(currentTableUrl);
            });
    });
})();
