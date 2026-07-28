(function () {
    var tabs = document.querySelector('[data-role="pdp-tabs"]');
    if (!tabs) {
        return;
    }

    var panels = document.querySelectorAll('.pdp-tab-panel');

    tabs.querySelectorAll('.pdp-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.querySelectorAll('.pdp-tab').forEach(function (t) {
                t.classList.remove('active');
            });
            tab.classList.add('active');

            panels.forEach(function (panel) {
                var isActive = panel.dataset.panel === tab.dataset.tab;
                panel.style.display = isActive ? '' : 'none';
                panel.classList.toggle('active', isActive);
            });
        });
    });
})();
