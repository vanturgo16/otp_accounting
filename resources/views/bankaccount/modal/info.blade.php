<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Info Bank Account</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 mb-2">
            <div><span class="fw-bold">Status :</span></div>
            <span class="badge {{ $data->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                {{ $data->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Code :</span></div>
            <span>
                {{ $data->code ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Bank Name :</span></div>
            <span>
                {{ $data->bank_name ?? '-' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Account Name :</span></div>
            <span>
                {{ $data->account_name ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Account Number :</span></div>
            <span>
                {{ $data->account_number ?? '-' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Currency :</span></div>
            <span>
                {{ $data->currency ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Swift Code :</span></div>
            <span>
                {{ $data->swift_code ?? '-' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-2">
            <div><span class="fw-bold">Branch :</span></div>
            <span>
                {{ $data->branch ?? '-' }}
            </span>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Created At :</span></div>
            <span>
                {{ $data->created_at ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Last Updated At :</span></div>
            <span>
                {{ $data->updated_at ?? '-' }}
            </span>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
</div>