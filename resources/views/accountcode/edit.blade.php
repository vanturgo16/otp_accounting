@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('accountcode.index') }}" class="btn btn-secondary waves-effect btn-label waves-light">
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Edit</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Account Type</label><label style="color: darkred">*</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_master_account_types" required>
                                        <option value="">--Select Type--</option>
                                        @foreach($acctypes as $item)
                                            <option value="{{ $item->id }}" @if($data->id_master_account_types == $item->id) selected="selected" @endif>{{ $item->account_type_code }} - {{ $item->account_type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Code</label><label style="color: darkred">*</label>
                                        <input class="form-control" name="account_code" type="text" value="{{ $data->account_code }}" placeholder="Input Account Code Code.." required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label">Account Name</label><label style="color: darkred">*</label>
                                        <input class="form-control" name="account_name" type="text" value="{{ $data->account_name }}" placeholder="Input Account Code Name.." required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label">Opening Balance</label><label style="color: darkred">*</label>
                                        <input class="form-control rupiah-input" name="opening_balance" value="{{ $data->opening_balance }}" type="text" placeholder="Input Opening Balance.." required>
                                    </div>
                                </div>
                                <script>
                                    // Format opening balance value inside input field
                                    var openingBalanceInput = document.querySelector('[name="opening_balance"]');
                                    if (openingBalanceInput) {
                                        openingBalanceInput.value = formatOpeningBalance(openingBalanceInput.value);
                                    }
                                
                                    // Function to format opening balance
                                    function formatOpeningBalance(value) {
                                        if (!value) return value; // If value is empty, return it as it is
                                
                                        // Convert value to floating-point number and format it
                                        return parseFloat(value).toLocaleString('en-US', {
                                            minimumFractionDigits: 3,
                                            maximumFractionDigits: 3
                                        });
                                    }
                                </script>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 align-right">
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