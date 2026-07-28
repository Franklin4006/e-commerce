(function () {
    var toggle = document.getElementById('nav-search-toggle');
    var panel = document.getElementById('nav-search-panel');
    var input = document.getElementById('nav-search-input');

    if (!toggle || !panel) {
        return;
    }

    function close() {
        panel.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    function open() {
        panel.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        if (input) {
            input.focus();
        }
    }

    function isOpen() {
        return panel.classList.contains('is-open');
    }

    toggle.addEventListener('click', function (event) {
        event.stopPropagation();
        isOpen() ? close() : open();
    });

    document.addEventListener('click', function (event) {
        if (isOpen() && !panel.contains(event.target) && !toggle.contains(event.target)) {
            close();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            close();
        }
    });
})();
