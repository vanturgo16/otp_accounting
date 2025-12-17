@if (session('success') || session('fail') || session('info') || session('warning') || count($errors)>0)
<div class="position-fixed bottom-0 end-0 p-3 mb-4" style="z-index: 15;">
    @if (session('success'))
        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <span class="badge bg-white text-primary"><i class="mdi mdi-check-all label-icon"></i></span>&nbsp;
                <strong class="me-auto">Success</strong><small>just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="max-height: 80vh; overflow-y: auto;">{{ session('success') }}</div>
        </div>
    @endif
    @if (session('fail'))
        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <span class="badge bg-white text-primary"><i class="mdi mdi-block-helper label-icon"></i></span>&nbsp;
                <strong class="me-auto">Failed</strong><small>just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="max-height: 80vh; overflow-y: auto;">{{ session('fail') }}</div>
        </div>
    @endif
    @if (session('info'))
        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header bg-info text-white">
                <span class="badge bg-white text-primary"><i class="mdi mdi-information label-icon"></i></span>&nbsp;
                <strong class="me-auto">Info</strong><small>just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="max-height: 80vh; overflow-y: auto;">{{ session('info') }}</div>
        </div>
    @endif
    @if (session('warning'))
        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header bg-warning text-white">
                <span class="badge bg-white text-primary"><i class="mdi mdi-alert-circle-outline label-icon"></i></span>&nbsp;
                <strong class="me-auto">Warning</strong><small>just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="max-height: 80vh; overflow-y: auto;">{{ session('warning') }}</div>
        </div>
    @endif
    @if (count($errors)>0)
    @if (session('error'))
        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <span class="badge bg-white text-primary"><i class="mdi mdi-alert-outline label-icon"></i></span>&nbsp;
                <strong class="me-auto">Error</strong><small>just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="max-height: 80vh; overflow-y: auto;">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @endif
</div>
<!-- TOAST JS -->
<script src="{{ asset('assets/js/pages/bootstrap-toasts.init.js') }}"></script>
@endif