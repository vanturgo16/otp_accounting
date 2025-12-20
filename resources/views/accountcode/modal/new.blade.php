<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Add New Account Code</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('accountcode.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body md-body-scroll">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">Account Type</label>
                <select class="form-select select2" name="id_master_account_types" required>
                    <option value="" selected>Select Type</option>
                    @foreach($accTypeActives as $item)
                        <option value="{{ $item->id }}">{{ $item->account_type_code }} - {{ $item->account_type_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">Account Code</label>
                <input class="form-control" name="account_code" type="text" value="" placeholder="Input Account Code.." required>
            </div>
            <div class="col-lg-12 mb-3">
                <label class="form-label required-label">Account Name</label>
                <input class="form-control" name="account_name" type="text" value="" placeholder="Input Account Name.." required>
            </div>
            <div class="col-lg-8 mb-3">
                <label class="form-label required-label">Opening Balance</label>
                <input class="form-control currency-input" name="opening_balance" type="text" value="" placeholder="Input Opening Balance.." required>
            </div>
            <div class="col-lg-4 mb-3">
                <label class="form-label required-label">Type</label>
                <select class="form-select select2" name="type" required>
                    <option value="">Select Type</option>
                    <option value="D">Debit</option>
                    <option value="K">Kredit</option>
                </select>
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