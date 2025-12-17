<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Activate Account Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('accounttype.deactivate', encrypt($data->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body md-body-scroll">
        <div class="text-center">
            Are You Sure to <b>Deactivate</b> This Account Type?
            <br>
            {{ $data->account_type_code }} - {{ $data->account_type_name }}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger waves-effect btn-label waves-light" onclick="btnSubmitLoad(this)">
            <i class="mdi mdi-close-circle label-icon"></i>Deactivate
        </button>
    </div>
</form>