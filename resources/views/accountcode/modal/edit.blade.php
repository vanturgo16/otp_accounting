<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Edit Account Code</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('accountcode.update', encrypt($data->id)) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body md-body-scroll">
        <div class="row">
            <div class="col-12 mb-2">
                <label class="form-label">Status</label>
                <input type="text" class="form-control readonly-input" value="{{ $data->is_used === "1" ? 'Running' : 'Initiate' }}" readonly>
            </div>
            <div class="col-12 mb-2">
                <label class="form-label required-label">Account Type</label>
                @if($data->is_used)
                    <input type="hidden" name="id_master_account_types" value="{{ $data->id_master_account_types }}">
                    <input type="text" class="form-control readonly-input" value="{{ $data->account_type_code ." - ". $data->account_type_name }}" readonly>
                @else
                    <select class="form-select select2" name="id_master_account_types" required>
                        <option value="">Select Type</option>
                        @foreach($accTypeActives as $item)
                            <option value="{{ $item->id }}" @if($data->id_master_account_types == $item->id) selected="selected" @endif>{{ $item->account_type_code }} - {{ $item->account_type_name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label required-label">Account Code</label>
                    @if($data->is_used)
                        <input type="text" class="form-control readonly-input" name="account_code" value="{{ $data->account_code }}" readonly>
                    @else
                        <input class="form-control" name="account_code" type="text" value="{{ $data->account_code }}" placeholder="Input Account Code.." required>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label required-label">Account Name</label>
                    <input class="form-control" name="account_name" type="text" value="{{ $data->account_name }}" placeholder="Input Account Code Name.." required>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-3">
                    <label class="form-label required-label">Opening Balance</label>
                    @if($data->is_used)
                        <input type="text" class="form-control rupiah-input readonly-input" name="opening_balance" value="{{ number_format($data->opening_balance, 2, ',', '.') }}" readonly>
                    @else
                        <input class="form-control currency-input" name="opening_balance" value="{{ number_format($data->opening_balance, 2, ',', '.') }}" type="text" placeholder="Input Opening Balance.." required>
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="mb-3">
                    <label class="form-label required-label">Type</label>
                    @if($data->is_used)
                        <input type="hidden" name="type" value="{{ $data->opening_balance_type }}">
                        <input type="text" class="form-control readonly-input" value="{{ $data->opening_balance_type === "K" ? 'Kredit' : 'Debit' }}" readonly>
                    @else
                        <select class="form-select select2" name="type" required>
                            <option value="">Select Type</option>
                            <option @if($data->opening_balance_type == "D") selected @endif value="D">Debit</option>
                            <option @if($data->opening_balance_type == "K") selected @endif value="K">Kredit</option>
                        </select>
                    @endif
                </div>
            </div>
            
            @if($data->is_used)
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label class="form-label">Balance</label>
                        <input type="text" class="form-control readonly-input" value="{{ number_format($data->balance, 2, ',', '.') }}" readonly>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" class="form-control readonly-input" value="{{ $data->balance_type === "K" ? 'Kredit' : 'Debit' }}" readonly>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info waves-effect btn-label waves-light" onclick="btnSubmitLoad(this)">
            <i class="mdi mdi-update label-icon"></i>Update
        </button>
    </div>
</form>