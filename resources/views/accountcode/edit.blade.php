@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('accountcode.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Master Account Code
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('accountcode.index') }}">Master Account Code</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('accountcode.update', encrypt($data->id)) }}" id="formedit" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Edit</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" value="{{ $data->is_used === "1" ? 'Running' : 'Initiate' }}" style="background-color:#EAECF4"  readonly>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Account Type</label><label style="color: darkred">*</label>
                                    @if($data->is_used)
                                        <input type="hidden" name="id_master_account_types" value="{{ $data->id_master_account_types }}">
                                        <input type="text" class="form-control" value="{{ $acctypesUsed->account_type_code ." - ". $acctypesUsed->account_type_name }}" style="background-color:#EAECF4"  readonly>
                                    @else
                                        <select class="form-select js-example-basic-single" style="width: 100%" name="id_master_account_types" required>
                                            <option value="">--Select Type--</option>
                                            @foreach($acctypes as $item)
                                                <option value="{{ $item->id }}" @if($data->id_master_account_types == $item->id) selected="selected" @endif>{{ $item->account_type_code }} - {{ $item->account_type_name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Code</label><label style="color: darkred">*</label>
                                        @if($data->is_used)
                                            <input type="text" class="form-control" name="account_code" value="{{ $data->account_code }}" style="background-color:#EAECF4" readonly>
                                        @else
                                            <input class="form-control" name="account_code" type="text" value="{{ $data->account_code }}" placeholder="Input Account Code.." required>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Name</label><label style="color: darkred">*</label>
                                        <input class="form-control" name="account_name" type="text" value="{{ $data->account_name }}" placeholder="Input Account Code Name.." required>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label class="form-label">Opening Balance</label><label style="color: darkred">*</label>
                                        @if($data->is_used)
                                            <input type="text" class="form-control rupiah-input" name="opening_balance" value="{{ number_format($data->opening_balance, 3, ',', '.') }}" style="background-color:#EAECF4"  readonly>
                                        @else
                                            <input class="form-control rupiah-input" name="opening_balance" value="{{ number_format($data->opening_balance, 3, ',', '.') }}" type="text" placeholder="Input Opening Balance.." required>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label">Type</label><label style="color: darkred">*</label>
                                        @if($data->is_used)
                                            <input type="hidden" name="type" value="{{ $data->balance_type }}">
                                            <input type="text" class="form-control" value="{{ $data->balance_type === "K" ? 'Kredit' : 'Debit' }}" style="background-color:#EAECF4"  readonly>
                                        @else
                                            <select class="form-select js-example-basic-single" style="width: 100%" name="type" required>
                                                <option value="">- Select Type -</option>
                                                <option @if($data->balance_type == "D") selected @endif value="D">Debit</option>
                                                <option @if($data->balance_type == "K") selected @endif value="K">Kredit</option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <script>
                                    document.querySelectorAll(".rupiah-input").forEach((input) => {
                                        input.addEventListener("input", formatCurrencyInput);
                                    });
                                </script>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 align-right">
                                    <a href="{{ route('accountcode.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
                                        <i class="mdi mdi-arrow-left-circle label-icon"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-success waves-effect btn-label waves-light" name="sb">
                                        <i class="mdi mdi-update label-icon"></i>Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('formedit').addEventListener('submit', function(event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            return false;
        }
        var submitButton = this.querySelector('button[name="sb"]');
        submitButton.disabled = true;
        submitButton.innerHTML  = '<i class="mdi mdi-reload label-icon"></i>Please Wait...';
        return true;
    });
</script>
@endsection