(function () {
    var thumbnailInput = document.getElementById('thumbnail');
    var thumbnailPreview = document.getElementById('thumbnail-preview');

    thumbnailInput.addEventListener('change', function () {
        var file = thumbnailInput.files[0];
        if (file) {
            thumbnailPreview.src = URL.createObjectURL(file);
            thumbnailPreview.style.display = 'block';
        }
    });
})();
