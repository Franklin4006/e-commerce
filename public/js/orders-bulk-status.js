(function () {
    var selectAll = document.getElementById('order-select-all');
    var bulkBar = document.getElementById('bulk-actions-bar');
    var selectedCount = document.getElementById('bulk-selected-count');
    var bulkForm = document.getElementById('bulk-status-form');

    if (!selectAll || !bulkBar || !bulkForm) {
        return;
    }

    var checkboxes = document.querySelectorAll('.order-row-checkbox');

    function updateBulkBar() {
        var checked = document.querySelectorAll('.order-row-checkbox:checked');
        selectedCount.textContent = checked.length;
        bulkBar.style.display = checked.length > 0 ? '' : 'none';
        selectAll.checked = checked.length > 0 && checked.length === checkboxes.length;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
        updateBulkBar();
    });

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', updateBulkBar);
    });

    bulkForm.addEventListener('submit', function (event) {
        var checked = document.querySelectorAll('.order-row-checkbox:checked');
        var status = bulkForm.querySelector('select[name="status"]').value;

        if (checked.length === 0 || !status) {
            event.preventDefault();
            return;
        }

        if (!window.confirm('Update status of ' + checked.length + ' order(s) to "' + status + '"?')) {
            event.preventDefault();
        }
    });
})();
