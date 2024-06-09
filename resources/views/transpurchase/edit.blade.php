@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transpurchase.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Purchase Transaction
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transpurchase.index') }}">Purchase Transaction</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('transpurchase.update', encrypt($data->id_trans)) }}" id="formupdate" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Edit</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Ref Number</label>
                                    <br><h4><span class="badge bg-info">{{ $data->ref_number }}</span></h4>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="transaction_date" value="{{ $transaction_date }}" required>
                                </div>
                                <hr>
                                {{-- <div class="col-lg-6 mb-3">
                                    <label class="form-label">Purchase Invoices</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="purchase_source" required>
                                        <option value="" selected>-- Select --</option>
                                    </select>
                                </div> --}}

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Delivery Note Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="delivery_note_date" value="{{ $data->delivery_note_date }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Delivery Note Number</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="delivery_note_number" value="{{ $data->delivery_note_number }}" placeholder="Input Delivery Note Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Invoice Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="invoice_date" value="{{ $data->invoice_date }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Invoice Number</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="invoice_number" value="{{ $data->invoice_number }}" placeholder="Input Invoice Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Tax Invoice Number</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="tax_invoice_number" value="{{ $data->tax_invoice_number }}" placeholder="Input Tax Invoice Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Quantity</label><label style="color: darkred">*</label>
                                    <input type="number" class="form-control" name="quantity" value="{{ $data->quantity }}" placeholder="Input Quantity.." required>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Description</label><label style="color: darkred">*</label>
                                    <textarea class="form-control" rows="3" type="text" name="description" placeholder="Input Description.." value="" required>{{ $data->description }}</textarea>
                                </div>

                                <div class="col-lg-12 mt-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <h6 class="mb-0">Transaction</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-repsonsive">
                                                <table class="table table-bordered" id="dynamicTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Account Code</th>
                                                            <th>Nominal</th>
                                                            <th>Debit / Kredit</th>
                                                            <th style="text-align:center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[0][account_code]" required>
                                                                    <option value="">- Select Account Code -</option>
                                                                    @foreach( $accountcodes as $item )
                                                                        <option value="{{ $item->id }}" @if($general_ledger->id_account_code == $item->id) selected="selected" @endif>{{ $item->account_code }} - {{ $item->account_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control rupiah-input addpayment" style="width: 100%" placeholder="Input Amount.." name="addmore[0][nominal]" value="{{ number_format($general_ledger->amount, 3, ',', '.') }}" required>
                                                            </td>
                                                            <td>
                                                                <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[0][type]" required>
                                                                    <option value="">- Select Type -</option>
                                                                    <option value="Debit" @if($general_ledger->transaction == "D") selected="selected" @endif>Debit</option>
                                                                    <option value="Kredit" @if($general_ledger->transaction == "K") selected="selected" @endif>Kredit</option>
                                                                </select>
                                                            </td>
                                                            <td style="text-align:center"><button type="button" name="add" id="adds" class="btn btn-success"><i class="fas fa-plus"></i></button></td>
                                                        </tr>

                                                        <?php $index = 0; ?>
                                                        @if($general_ledgers != [])
                                                            @foreach($general_ledgers as $gl)
                                                            <?php $index++; ?>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[{{ $index }}][account_code]" required>
                                                                        <option value="">- Select Account Code -</option>
                                                                        @foreach( $accountcodes as $item )
                                                                            <option value="{{ $item->id }}" @if($gl->id_account_code == $item->id) selected="selected" @endif>{{ $item->account_code }} - {{ $item->account_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control rupiah-input addpayment" style="width: 100%" placeholder="Input Amount.." name="addmore[{{ $index }}][nominal]" value="{{ number_format($gl->amount, 3, ',', '.') }}" required>
                                                                </td>
                                                                <td>
                                                                    <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[{{ $index }}][type]" required>
                                                                        <option value="">- Select Type -</option>
                                                                        <option value="Debit" @if($gl->transaction == "D") selected="selected" @endif>Debit</option>
                                                                        <option value="Kredit" @if($gl->transaction == "K") selected="selected" @endif>Kredit</option>
                                                                    </select>
                                                                </td>
                                                                <td style="text-align:center">
                                                                    <button type="button" class="btn btn-danger remove-tr"><i class="fas fa-minus"></i></button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @endif

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 align-right">
                                    <a href="{{ route('transpurchase.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
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

{{-- Validation Form --}}
<script>
    document.getElementById('formupdate').addEventListener('submit', function(event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            return false;
        }
        var submitButton = this.querySelector('button[name="sb"]');
        submitButton.disabled = true;
        submitButton.innerHTML  = '<i class="mdi mdi-loading mdi-spin label-icon"></i>Please Wait...';
        return true;
    });
</script>

{{-- Dynamic Table --}}                                                    
<script>
    var i = 0;
    $("#adds").click(function() {
        $(".js-example-basic-single").select2();
        ++i;
        $("#dynamicTable").append(
            `<tr>
                <td>
                    <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[`+i+`][account_code]" required>
                        <option value="">- Select Account Code -</option>
                        @foreach( $accountcodes as $item )
                            <option value="{{ $item->id }}">{{ $item->account_code }} - {{ $item->account_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control rupiah-input addpayment" style="width: 100%" placeholder="Input Amount.." name="addmore[`+i+`][nominal]" value="" required>
                </td>
                <td>
                    <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[`+i+`][type]" required>
                        <option value="">- Select Type -</option>
                        <option value="Debit">Debit</option>
                        <option value="Kredit">Kredit</option>
                    </select>
                </td>
                <td style="text-align:center">
                    <button type="button" class="btn btn-danger remove-tr"><i class="fas fa-minus"></i></button>
                </td>
            </tr>`);

        $(".js-example-basic-single").select2();

        document.querySelectorAll(".rupiah-input").forEach((input) => {
            input.addEventListener("input", formatCurrencyInput);
        });
    });
    $(document).on('click', '.remove-tr', function() {
        $(this).parents('tr').remove();
    });
</script>

@endsection