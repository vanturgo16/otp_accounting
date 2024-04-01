@if (session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('fail'))
    <div id="fail-alert" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="mdi mdi-block-helper label-icon"></i><strong>Failed</strong> - {{ session('fail') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('warning'))
    <div id="warning-alert" class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="mdi mdi-alert-outline label-icon"></i><strong>Warning</strong> - {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('info'))
    <div id="info-alert" class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="mdi mdi-alert-circle-outline label-icon"></i><strong>Info</strong> - {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
    $(document).ready(function(){
        setTimeout(function() {
            $("#success-alert, #fail-alert, #warning-alert, #info-alert").fadeOut('fast');
        }, 4000);
    });
</script>

<div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show d-none" role="alert"
    id="alertSuccess">
    <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - <span
        class="alertMessage">{{ session('success') }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show d-none" role="alert"
    id="alertFail">
    <i class="mdi mdi-block-helper label-icon"></i><strong>Failed</strong> - <span
        class="alertMessage">{{ session('fail') }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show d-none" role="alert"
    id="alertWarning">
    <i class="mdi mdi-alert-outline label-icon"></i><strong>Failed</strong> - <span
        class="alertMessage">{{ session('warning') }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
    $(document).ready(function(){
        setTimeout(function() {
            $("#success-alert, #fail-alert, #warning-alert, #info-alert").fadeOut('fast');
        }, 4000);
    });
</script>