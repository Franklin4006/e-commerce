<div class="alert alert-error" id="category-form-error" style="display: none;"></div>

<div class="form-group">
    <label for="category-name" class="form-label">Name</label>
    <input id="category-name" type="text" name="name" required class="form-control">
    <span class="field-error" id="category-name-error"></span>
</div>

<div class="form-group">
    <label for="category-priority" class="form-label">Priority</label>
    <input id="category-priority" type="number" name="priority" value="0" min="0" class="form-control">
    <span class="field-error" id="category-priority-error"></span>
    <small style="color: var(--text-muted);">Lower numbers appear first on the website.</small>
</div>

<div class="form-group">
    <label for="category-image" class="form-label">Image</label>
    <input id="category-image" type="file" name="image" accept="image/*" class="form-control">
    <small style="color: var(--text-muted);">PNG, JPG up to 2MB</small>
    <span class="field-error" id="category-image-error"></span>
    <img id="category-image-preview" src="" alt="" class="thumb-lg form-preview" style="display: none;">
</div>

<div class="form-group">
    <label for="category-status" class="form-label">Status</label>
    <select id="category-status" name="status" class="form-control">
        <option value="1" selected>Active</option>
        <option value="0">Inactive</option>
    </select>
</div>
