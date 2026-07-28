(function () {
    var thumbnailInput = document.getElementById('thumbnail');
    var thumbnailPreview = document.getElementById('thumbnail-preview');
    var thumbnailDrop = document.getElementById('thumbnail-drop');
    var thumbnailDropText = document.getElementById('thumbnail-drop-text');

    thumbnailInput.addEventListener('change', function () {
        var file = thumbnailInput.files[0];
        if (file) {
            thumbnailPreview.src = URL.createObjectURL(file);
            thumbnailPreview.style.display = 'block';
            thumbnailDrop.classList.add('has-file');
            thumbnailDropText.textContent = file.name;
        }
    });
})();
