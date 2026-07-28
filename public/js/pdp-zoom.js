(function () {
    var main = document.querySelector('.pdp-main');
    var img = document.getElementById('main-image');

    if (!main || !img) {
        return;
    }

    // Cursor-follow magnify, Amazon-style. Reads #main-image fresh each time
    // rather than caching anything about it, so this keeps working after
    // cart.js swaps the src when a thumbnail or color is picked.
    main.addEventListener('mousemove', function (event) {
        var rect = main.getBoundingClientRect();
        var x = ((event.clientX - rect.left) / rect.width) * 100;
        var y = ((event.clientY - rect.top) / rect.height) * 100;

        img.style.transformOrigin = x + '% ' + y + '%';
        img.style.transform = 'scale(2)';
    });

    main.addEventListener('mouseleave', function () {
        img.style.transform = '';
        img.style.transformOrigin = '';
    });
})();
