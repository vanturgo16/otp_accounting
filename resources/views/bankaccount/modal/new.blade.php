<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Add New Bank Account</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('bankaccount.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body" style="max-height: 65vh; overflow:auto">
        <div class="row">
            <div class="col-lg-6 mb-3">
                <label class="form-label required-label">Bank Name</label>
                <input type="text" class="form-control" name="bank_name" placeholder="Input Bank Name.." required>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <label class="form-label required-label">Account Name</label>
                <input type="text" class="form-control" name="account_name" placeholder="Input Account Name.." required>
            </div>
            <div class="col-lg-6 mb-3">
                <label class="form-label required-label">Account Number</label>
                <input type="text" class="form-control" name="account_number" placeholder="Input Account Number.." required>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 mb-3">
                <label class="form-label required-label">Currency</label>
                <select class="form-control select2" name="currency" required>
                    <option value="" selected>Choose</option>
                    @foreach($currencies as $item)
                        <option value="{{ $item->currency_code }}">{{ $item->currency_code }} - {{ $item->currency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 mb-3">
                <label class="form-label required-label">Swift Code</label>
                <input type="text" class="form-control" name="swift_code" placeholder="Input Swift Code.." required>
            </div>
            <div class="col-lg-6 mb-3">
                <label class="form-label required-label">Branch</label>
                <input type="text" class="form-control" name="branch" placeholder="Input Branch.." required>
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