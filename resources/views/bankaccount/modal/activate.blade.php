<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Activate</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('bankaccount.activate', encrypt($data->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body p-4">
        <div class="text-center">
            Are you sure to <b>Activate</b> {{ $data->bank_name }}?
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success waves-effect btn-label waves-light" onclick="btnSubmitLoad(this)">
            <i class="mdi mdi-check-circle label-icon"></i>Activate
        </button>
    </div>
</form>