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
})();
