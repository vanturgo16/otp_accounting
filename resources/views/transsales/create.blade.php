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
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('transsales.store') }}" id="formstore" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Create New</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Ref Number</label><label style="color: darkred">*</label>
                                    <br>
                                    <span class="badge bg-info text-white">Auto Generate</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="transaction_date" value="" required>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">No Delivery Note</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="no_delivery_note" value="" placeholder="Input No Delivery Note.." required>
                                </div>
                                <hr>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Sales Order</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_sales_order" required>
                                        <option value="" selected>-- Select --</option>
                                        @foreach($sales as $item)
                                            <option value="{{ $item->id }}">{{ $item->so_number." - ". $item->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input class="form-control" id="customer_name" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Customer Address</label>
                                    <input class="form-control" id="customer_address" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Date</label>
                                    <input class="form-control" id="date" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Due Date</label>
                                    <input class="form-control" id="due_date" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">PPN</label>
                                    <input class="form-control" id="ppn" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <input class="form-control" id="statusorder" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">SO Type</label>
                                    <input class="form-control" id="so_type" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">SO Category</label>
                                    <input class="form-control" id="so_category" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Product</label>
                                    <input class="form-control" id="product" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Type Product</label>
                                    <input class="form-control" id="type_product" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input class="form-control" id="qty" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Unit</label>
                                    <input class="form-control" id="unit" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Price</label>
                                    <input class="form-control" id="price" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Total Price</label>
                                    <input class="form-control" id="total_price" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
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
                                                                    <option value="Debit">Debit</option>
                                                                    <option value="Kredit">Kredit</option>
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
                                    <a href="{{ route('transsales.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
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

            if (type === "Debit") {
                debitTotal += nominal;
            } else if (type === "Kredit") {
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

{{-- Sales Order Choose --}}
<script>
    $('select[name="id_sales_order"]').on('change', function() {
        $('#customer_name').val("");
        $('#customer_address').val("");
        $('#date').val("");
        $('#due_date').val("");
        $('#ppn').val("");
        $('#statusorder').val("");

        $('#so_type').val("");
        $('#so_category').val("");
        $('#product').val("");
        $('#type_product').val("");
        $('#qty').val("");
        $('#unit').val("");
        $('#price').val("");
        $('#total_price').val("");

        var id_sales_order = $(this).val();
        if(id_sales_order == ""){
            $('#customer_name').val("");
            $('#customer_address').val("");
            $('#date').val("");
            $('#due_date').val("");
            $('#ppn').val("");
            $('#statusorder').val("");

            $('#so_type').val("");
            $('#so_category').val("");
            $('#product').val("");
            $('#type_product').val("");
            $('#qty').val("");
            $('#unit').val("");
            $('#price').val("");
            $('#total_price').val("");
        } else {
            var url = '{{ route("transsales.getsalesorder", ":id") }}';
            url = url.replace(':id', id_sales_order);
            if (id_sales_order) {
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
                        $('#statusorder').val(data.status);

                        $('#so_type').val(data.so_type);
                        $('#so_category').val(data.so_category);
                        $('#product').val(data.product);
                        $('#type_product').val(data.type_product);
                        $('#qty').val(data.qty);
                        $('#unit').val(data.unit);
                        $('#price').val(data.price);
                        $('#total_price').val(data.total_price);
                    }
                });
            }
        }
    });
</script>

@endsection