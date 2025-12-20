<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Add New Account Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('accounttype.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body md-body-scroll">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">Account Type Code</label>
                <input type="text" class="form-control" name="account_type_code" placeholder="Input Account Type Code.." required>
            </div>
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">Account Type Name</label>
                <input type="text" class="form-control" name="account_type_name" placeholder="Input Account Type Name.." required>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success waves-effect btn-label waves-light" onclick="btnSubmitLoad(this)">
            <i class="mdi mdi-plus-box label-icon"></i>Add
        </button>
    </div>
</form>