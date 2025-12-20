<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Update Default PPN</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="formLoad" action="{{ route('ppn.update', encrypt($data->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body" style="max-height: 65vh; overflow:auto">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">PPN Name</label>
                <input type="text" class="form-control" value="{{ $data->tax_name }}" readonly>
            </div>
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">Default Value</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="value" value="{{ $data->value }}" aria-label="Value" placeholder="Input Percentage PPN Value.." required>
                    <div class="input-group-append">
                    <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success waves-effect btn-label waves-light">
            <i class="mdi mdi-update label-icon"></i>Update
        </button>
    </div>
</form>

<script src="{{ asset('assets/js/formLoad.js') }}"></script>