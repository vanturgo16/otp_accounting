<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Info Account Code</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Status :</span></div>
            <span class="badge {{ $data->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                {{ $data->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Used :</span></div>
            <span class="badge {{ $data->is_used ? 'bg-success' : 'bg-secondary' }} text-white">
                {{ $data->is_used ? 'Running' : 'Initiate' }}
            </span>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Account Type Code :</span></div>
            <span>
                {{ $data->account_type_code ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Account Type Name :</span></div>
            <span>
                {{ $data->account_type_name ?? '-' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Account Code :</span></div>
            <span>
                {{ $data->account_code ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Account Name :</span></div>
            <span>
                {{ $data->account_name ?? '-' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Opening Balance :</span></div>
            @if($data->opening_balance_type == 'D')
                <span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>
            @else 
                <span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>
            @endif
            <span>
                {{ number_format($data->opening_balance, 2, ',', '.') }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Balance :</span></div>
            @if($data->balance_type == 'D')
                <span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>
            @else 
                <span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>
            @endif
            <span>
                {{ number_format($data->balance, 2, ',', '.') }}
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