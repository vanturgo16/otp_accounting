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
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('transpurchase.store') }}" id="formstore" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Create New</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Ref Number</label><label style="color: darkred">*</label>
                                    <br>
                                    <span class="badge bg-info text-white">Auto Generate</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="date_transaction" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <hr>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Good Receipt Note</label><label style="color: darkred">*</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_good_receipt_notes" required>
                                        <option value="" selected>--Select Type--</option>
                                        @foreach($goodReceiptNote as $item)
                                            <option value="{{ $item->id }}">{{ $item->receipt_number." - ". $item->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 mb-2"></div>
                                
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">PO Date</label>
                                    <input class="form-control" id="po_date" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Supplier</label>
                                    <input class="form-control" id="supplier" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input class="form-control" id="reference_number" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <input class="form-control" id="po_status" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Own Remark</label>
                                    <textarea class="form-control" rows="2" type="text" id="own_remarks" placeholder="Select Purchase Orders.." value="" style="background-color:#EAECF4" readonly></textarea>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Supplier Remark</label>
                                    <textarea class="form-control" rows="2" type="text" id="supplier_remarks" placeholder="Select Purchase Orders.." value="" style="background-color:#EAECF4" readonly></textarea>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Sub Total</label>
                                    <input class="form-control" id="sub_total" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Total Discount</label>
                                    <input class="form-control" id="total_discount" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Total Tax</label>
                                    <input class="form-control" id="total_ppn" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Total Amount</label>
                                    <input class="form-control" id="total_amount" type="text" value="" placeholder="Select Purchase Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <br>

                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Delivery Note Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="delivery_note_date" value="" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Delivery Note Number</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="delivery_note_number" value="" placeholder="Input Delivery Note Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Invoice Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="invoice_date" value="" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Invoice Number</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="invoice_number" value="" placeholder="Input Invoice Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Tax Invoice Number</label>
                                    <input type="text" class="form-control" name="tax_invoice_number" value="" placeholder="Input Tax Invoice Number..">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Quantity</label><label style="color: darkred">*</label>
                                    <input type="number" class="form-control" name="quantity" value="" placeholder="Input Quantity.." required>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Description</label><label style="color: darkred">*</label>
                                    <textarea class="form-control" rows="3" type="text" name="description" placeholder="Input Description.." value="" required></textarea>
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
                                                                        <option value="{{ $item->id }}">{{ $item->account_code }} - {{ $item->account_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control rupiah-input addpayment" style="width: 100%" placeholder="Input Amount.." name="addmore[0][nominal]" value="" required>
                                                            </td>
                                                            <td>
                                                                <select class="form-select js-example-basic-single addpayment" style="width: 100%" name="addmore[0][type]" required>
                                                                    <option value="">- Select Type -</option>
                                                                    <option value="D">Debit</option>
                                                                    <option value="K">Kredit</option>
                                                                </select>
                                                            </td>
                                                            <td style="text-align:center"><button type="button" name="add" id="adds" class="btn btn-success"><i class="fas fa-plus"></i></button></td>
                                                        </tr>
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
                                        <i class="mdi mdi-plus-box label-icon"></i>Create
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

<div class="modal fade" id="alert" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"><span class="mdi mdi-alert"></span> Nominal Not Equal !</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="modalBodyContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Validation Form --}}
<script>
    function parseCurrency(value) {
        return parseFloat(value.replace(/\./g, '').replace(',', '.'));
    }

    function calculateTotals() {
        let debitTotal = 0;
        let kreditTotal = 0;

        $("#dynamicTable tbody tr").each(function() {
            let nominal = parseCurrency($(this).find('input[name*="[nominal]"]').val());
            let type = $(this).find('select[name*="[type]"]').val();

            if (type === "D") {
                debitTotal += nominal;
            } else if (type === "K") {
                kreditTotal += nominal;
            }
        });

        return { debitTotal, kreditTotal };
    }

    document.getElementById('formstore').addEventListener('submit', function(event) {
        event.preventDefault();
        let totals = calculateTotals();

        if (totals.debitTotal !== totals.kreditTotal) {
            let modalBodyContent = `
                <div class="col-12">
                    <table class="table dt-responsive w-100">
                        <thead>
                            <tr>
                                <th class="align-top text-bold">Total Debit</th>
                                <th class="text-center">:</th>
                                <th class="align-top">${totals.debitTotal.toLocaleString('id-ID')}</th>
                            </tr>
                            <tr>
                                <th class="align-top text-bold">Total Kredit</th>
                                <th class="text-center">:</th>
                                <th class="align-top">${totals.kreditTotal.toLocaleString('id-ID')}</th>
                            </tr>
                            <tr>
                                <th class="align-top text-bold">Difference</th>
                                <th class="text-center">:</th>
                                <th class="align-top text-danger">${(totals.debitTotal - totals.kreditTotal).toLocaleString('id-ID')}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            `;

            document.getElementById('modalBodyContent').innerHTML = modalBodyContent;

            var myModal = new bootstrap.Modal(document.getElementById('alert'));
            myModal.show();
        } else {
            var submitButton = this.querySelector('button[name="sb"]');
            submitButton.disabled = true;
            submitButton.innerHTML  = '<i class="mdi mdi-loading mdi-spin label-icon"></i>Please Wait...';
            this.submit();
            return true;
        }
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
                        <option value="D">Debit</option>
                        <option value="K">Kredit</option>
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

{{-- Purchase Order Choose --}}
<script>
    $('select[name="id_good_receipt_notes"]').on('change', function() {
        $('#po_date').val("");
        $('#supplier').val("");
        $('#reference_number').val("");
        $('#po_status').val("");
        $('#own_remarks').val("");
        $('#supplier_remarks').val("");
        $('#sub_total').val("");
        $('#total_discount').val("");
        $('#total_ppn').val("");
        $('#total_amount').val("");

        var id_good_receipt_notes = $(this).val();
        if(id_good_receipt_notes == ""){
            $('#po_date').val("");
            $('#supplier').val("");
            $('#reference_number').val("");
            $('#po_status').val("");
            $('#own_remarks').val("");
            $('#supplier_remarks').val("");
            $('#sub_total').val("");
            $('#total_discount').val("");
            $('#total_ppn').val("");
            $('#total_amount').val("");
        } else {
            var url = '{{ route("transpurchase.getgoodReceiptNote", ":id") }}';
            url = url.replace(':id', id_good_receipt_notes);
            if (id_good_receipt_notes) {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#po_date').val(data.date ? data.date : 'Not set');
                        $('#supplier').val(data.supplier ? data.supplier : 'Not set');
                        $('#reference_number').val(data.reference_number ? data.reference_number : 'Not set');
                        $('#po_status').val(data.status ? data.status : 'Not set');
                        $('#own_remarks').val(data.own_remarks ? data.own_remarks : 'Not set');
                        $('#supplier_remarks').val(data.supplier_remarks ? data.supplier_remarks : 'Not set');
                        $('#sub_total').val(data.sub_total ? data.sub_total : 'Not set');
                        $('#total_discount').val(data.total_discount ? data.total_discount : 'Not set');
                        $('#total_ppn').val(data.total_ppn ? data.total_ppn : 'Not set');
                        $('#total_amount').val(data.total_amount ? data.total_amount : 'Not set');
                    }
                });
            }
        }
    });
</script>

@endsection