@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transsales.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Sales Transaction
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transsales.index') }}">Sales Transaction</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('transsales.update', encrypt($data->id_trans)) }}" id="formupdate" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Edit</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label mb-0">Ref Number</label>
                                    <br><h4><span class="badge bg-info">{{ $data->ref_number }}</span></h4>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="transaction_date" value="{{ $transaction_date }}" required>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">No Delivery Note</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="no_delivery_note" value="{{ $data->no_delivery_note }}" placeholder="Input No Delivery Note.." required>
                                </div>
                                <hr>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Sales Invoices</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_sales_invoices" required>
                                        <option value="" selected>-- Select --</option>
                                        @foreach($sales as $item)
                                            <option value="{{ $item->id }}" @if($data->id_sales_invoices == $item->id) selected="selected" @endif>{{ $item->invoice_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input class="form-control" id="customer_name" type="text" value="{{ $data->customer_name }}" placeholder="Select Sales Invoices.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Customer Address</label>
                                    <input class="form-control" id="customer_address" type="text" value="{{ $data->customer_address }}" placeholder="Select Sales Invoices.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Date</label>
                                    <input class="form-control" id="date" type="text" value="{{ $data->date }}" placeholder="Select Sales Invoices.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Due Date</label>
                                    <input class="form-control" id="due_date" type="text" value="{{ $data->due_date }}" placeholder="Select Sales Invoices.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">PPN</label>
                                    <input class="form-control" id="ppn" type="text" value="{{ $data->ppn }}" placeholder="Select Sales Invoices.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <input class="form-control" id="statusinvoices" type="text" value="{{ $data->status }}" placeholder="Select Sales Invoices.." style="background-color:#EAECF4" readonly>
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
                                    <a href="{{ route('transsales.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
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
    var i = "{{ $index }}";
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

{{-- Sales Invoices Choose --}}
<script>
    $('select[name="id_sales_invoices"]').on('change', function() {
        $('#customer_name').val("");
        $('#customer_address').val("");
        $('#date').val("");
        $('#due_date').val("");
        $('#ppn').val("");
        $('#statusinvoices').val("");

        var id_sales_invoices = $(this).val();
        if(id_sales_invoices == ""){
            $('#customer_name').val("");
            $('#customer_address').val("");
            $('#date').val("");
            $('#due_date').val("");
            $('#ppn').val("");
            $('#statusinvoices').val("");
        } else {
            var url = '{{ route("transsales.getsalesinvoices", ":id") }}';
            url = url.replace(':id', id_sales_invoices);
            if (id_sales_invoices) {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#customer_name').val(data.customer_name);
                        $('#customer_address').val(data.customer_address);
                        $('#date').val(data.date);
                        $('#due_date').val(data.due_date);
                        $('#ppn').val(data.ppn);
                        $('#statusinvoices').val(data.status);
                    }
                });
            }
        }
    });
</script>

@endsection