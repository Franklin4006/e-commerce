(function () {
    var logoInput = document.getElementById('logo');
    var logoPreview = document.getElementById('logo-preview');

    logoInput.addEventListener('change', function () {
        var file = logoInput.files[0];
        if (file) {
            logoPreview.src = URL.createObjectURL(file);
            logoPreview.style.display = 'block';
        }
    });

    var faviconInput = document.getElementById('favicon');
    var faviconPreview = document.getElementById('favicon-preview');

    faviconInput.addEventListener('change', function () {
        var file = faviconInput.files[0];
        if (file) {
            faviconPreview.src = URL.createObjectURL(file);
            faviconPreview.style.display = 'block';
        }
    });
})();
