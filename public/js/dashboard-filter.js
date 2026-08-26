(function () {
    var periodSelect = document.getElementById('dashboard-period-select');
    var fromGroup = document.getElementById('dashboard-from-group');
    var toGroup = document.getElementById('dashboard-to-group');

    if (!periodSelect || !fromGroup || !toGroup) {
        return;
    }

    function toggleCustomInputs() {
        var isCustom = periodSelect.value === 'custom';
        fromGroup.style.display = isCustom ? '' : 'none';
        toGroup.style.display = isCustom ? '' : 'none';
    }

    periodSelect.addEventListener('change', toggleCustomInputs);
})();
