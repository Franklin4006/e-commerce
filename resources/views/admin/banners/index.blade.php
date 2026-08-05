<x-layouts.admin title="Banners">
    <div class="page-header">
        <h1 class="page-title">Banners</h1>
        @can('content-write')
            <button type="button" class="btn btn-primary" data-action="add-banner">Add Banner</button>
        @endcan
    </div>

    <div id="banners-table">
        @include('admin.banners._table')
    </div>

    <div class="modal-overlay" id="banner-modal">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h2 class="modal-title" id="banner-modal-title">Add Banner</h2>
                <button type="button" class="modal-close" data-action="close-modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="banner-form" enctype="multipart/form-data">
                <div class="modal-body">
                    @include('admin.banners._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-action="close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="banner-form-submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="delete-modal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Delete Banner</h2>
                <button type="button" class="modal-close" data-action="close-delete-modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <p>Delete <strong id="delete-banner-name"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-action="close-delete-modal">Cancel</button>
                <button type="button" class="btn btn-primary" style="background: var(--color-danger);" id="confirm-delete-btn">Delete</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/banners.js') }}"></script>
</x-layouts.admin>
