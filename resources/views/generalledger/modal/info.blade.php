<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Info {{ $data->ref_number }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">

    <hr>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Created At :</span></div>
            <span>
                {{ $detailRef['detail']->created_at ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Last Updated At :</span></div>
            <span>
                {{ $detailRef['detail']->updated_at ?? '-' }}
            </span>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
</div>