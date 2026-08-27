(function () {
    var nameInput = document.getElementById('name');
    var slugInput = document.getElementById('slug');

    if (!nameInput || !slugInput) {
        return;
    }

    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // The slug only follows the name automatically until the admin edits it
    // by hand — once it diverges from what the name would generate, further
    // name changes stop overwriting the custom slug.
    var slugDirty = slugInput.value !== '' && slugInput.value !== slugify(nameInput.value);

    slugInput.addEventListener('input', function () {
        slugDirty = slugInput.value !== slugify(nameInput.value);
    });

    nameInput.addEventListener('input', function () {
        if (!slugDirty) {
            slugInput.value = slugify(nameInput.value);
        }
    });
})();
