<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Delete Transaction of <b>{{ $detail->ref_number }}</b></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('transsales.local.delete', encrypt($detail->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body md-body-scroll">
        <div class="text-center">
            Are you sure you want to <b class="text-danger">DELETE</b> this transaction?
            <br><br>
            <b>{{ $detail->ref_number }}</b>
        </div>
        <hr>
        <div class="alert alert-warning mb-0" role="alert">
            <ul class="mb-0 ps-3">
                <li>
                    All <b>account codes</b> used in this transaction will be <b>rolled back</b>.
                </li>
                <li>
                    The related <b>Delivery Note</b> status will be returned to <b>Posted</b>.
                </li>
            </ul>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger waves-effect btn-label waves-light" onclick="btnSubmitLoad(this)">
            <i class="mdi mdi-delete label-icon"></i>Delete
        </button>
    </div>
</form>