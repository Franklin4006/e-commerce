<x-layouts.admin title="Categories">
    <div class="page-header">
        <h1 class="page-title">Categories</h1>
        @can('content-write')
            <button type="button" class="btn btn-primary" data-action="add-category">Add Category</button>
        @endcan
    </div>

    <div id="categories-table">
        @include('admin.categories._table')
    </div>

    <div class="modal-overlay" id="category-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title" id="category-modal-title">Add Category</h2>
                <button type="button" class="modal-close" data-action="close-modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="category-form" enctype="multipart/form-data">
                <div class="modal-body">
                    @include('admin.categories._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-action="close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="category-form-submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="delete-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Delete Category</h2>
                <button type="button" class="modal-close" data-action="close-delete-modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p>Delete <strong id="delete-category-name"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-action="close-delete-modal">Cancel</button>
                <button type="button" class="btn btn-primary" style="background: var(--color-danger);" id="confirm-delete-btn">Delete</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/categories.js') }}"></script>
</x-layouts.admin>
