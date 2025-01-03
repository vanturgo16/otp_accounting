@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transsales.export.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Sales Transaction (Export)
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transsales.export.index') }}">Sales (Export)</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('transsales.export.store') }}" id="formstore" method="POST" enctype="multipart/form-data">
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
                                    <label class="form-label">Invoice Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="date_invoice" value="" required>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="date_transaction" value="" required>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Term</label><label style="color: darkred">*</label>
                                    <textarea name="term" id="summernote-editor"></textarea>
                                </div>
                                
                                <div class="col-lg-6 mb-3">
                                    <table style="width: 100%">
                                        <input type="hidden" class="form-control" name="bank_name" value="{{ $bankaccount->bank_name ?? '' }}" required>
                                        <input type="hidden" class="form-control" name="account_name" value="{{ $bankaccount->account_name ?? '' }}" required>
                                        <input type="hidden" class="form-control" name="account_number" value="{{ $bankaccount->account_number ?? '' }}" required>
                                        <input type="hidden" class="form-control" name="currency" value="{{ $bankaccount->currency ?? '' }}" required>
                                        <input type="hidden" class="form-control" name="swift_code" value="{{ $bankaccount->swift_code ?? '' }}" required>
                                        <input type="hidden" class="form-control" name="branch" value="{{ $bankaccount->branch ?? '' }}" required>
                                        <tbody>
                                            <tr>
                                                <td><label class="form-label font-weight-bold">Bank Name</label></td>
                                                <td><label class="form-label">:</label></td>
                                                <td><label class="form-label">{{ $bankaccount->bank_name ?? '' }}</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="form-label font-weight-bold">Account Name</label></td>
                                                <td><label class="form-label">:</label></td>
                                                <td><label class="form-label">{{ $bankaccount->account_name ?? '' }}</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="form-label font-weight-bold">Account Number</label></td>
                                                <td><label class="form-label">:</label></td>
                                                <td><label class="form-label">{{ $bankaccount->account_number ?? '' }} ({{ $bankaccount->currency ?? '' }})</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="form-label font-weight-bold">Swift Code</label></td>
                                                <td><label class="form-label">:</label></td>
                                                <td><label class="form-label">{{ $bankaccount->swift_code ?? '' }}</label></td>
                                            </tr>
                                            <tr>
                                                <td><label class="form-label font-weight-bold">Branch</label></td>
                                                <td><label class="form-label">:</label></td>
                                                <td><label class="form-label">{{ $bankaccount->branch ?? '' }}</label></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-6 mb-3"></div>
                                <hr>
                                
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Tax (%)</label><label style="color: darkred">*</label>
                                    <input type="text" class="form-control" name="tax" value="{{ $tax }}" style="background-color:#EAECF4" required readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Tax</label></label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_tax">
                                        <label class="form-check-label">Need Tax PPN? ({{ $tax }} %)</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Delivery Note</label><label style="color: darkred">*</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_delivery_notes" required>
                                        <option value="" selected>-- Select --</option>
                                        @foreach($deliveryNotes as $item)
                                            <option value="{{ $item->id }}">{{ $item->dn_number." - ". $item->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body" style="background-color:ghostwhite">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Customer Name</label>
                                            <input class="form-control" id="customer_name" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Sales Name</label>
                                            <input class="form-control" id="sales_name" type="text" value="" placeholder="Select Sales Orders.." style="background-color:#EAECF4" readonly>
                                        </div>
                                        
                                        <div class="col-12">
                                            <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle text-center">No.</th>
                                                        <th class="align-middle text-center">SO Number</th>
                                                        <th class="align-middle text-center">Product</th>
                                                        <th class="align-middle text-center">Qty</th>
                                                        <th class="align-middle text-center">Unit</th>
                                                        <th class="align-middle text-center">Price</th>
                                                        <th class="align-middle text-center">Total Price</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>

                                        <div class="col-lg-6 mt-4">
                                        </div>
                                        <div class="col-lg-6 mt-4">
                                            <table style="width: 100%">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-right">
                                                            <label class="form-label font-weight-bold" style="text-align: right; display: block;">Total All Price</label>
                                                        </td>
                                                        <td class="text-right">
                                                            <label class="form-label" style="text-align: right; display: block;">: <span id="currency">IDR</span> <span id="totalPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
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
                                    <a href="{{ route('transsales.local.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
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

{{-- Delivery Notes Choose --}}
<style>
    /* Hide DataTable header and footer */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: none;
    }
</style>

<script>
    $(function() {
        var data = {id_delivery_notes: ''};
        var dataTable = $('#server-side-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('transsales.getsalesorder') !!}',
                type: 'GET',
                data: function(d) {
                    d.id_delivery_notes = data.id_delivery_notes;
                }
            },
            "columns": [
                {
                data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                },
                {
                    data: 'so_number',
                    name: 'so_number',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.so_number == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = '<span class="text-bold">' + row.so_number + '</span>';
                        }
                        return html;
                    },
                },
                {
                    data: 'product',
                    name: 'product',
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        var html;
                        if (row.product == null) {
                            html = '<div class="text-center"><span class="badge bg-secondary">Null</span></div>';
                        } else {
                            html = row.product + '<br><b>(' + row.type_product + ')</b>';
                        }
                        return html;
                    },
                },
                {
                    data: 'qty',
                    name: 'qty',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.qty == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.qty;
                        }
                        return html;
                    },
                },
                {
                    data: 'unit',
                    name: 'unit',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.unit == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.unit;
                        }
                        return html;
                    },
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.price == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.price;
                        }
                        return html;
                    },
                },
                {
                    data: 'total_price',
                    name: 'total_price',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.total_price == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.total_price;
                        }
                        return html;
                    },
                },
            ]
        });

        $('select[name="id_delivery_notes"]').on('change', function() {
            data.id_delivery_notes = $(this).val();
            dataTable.ajax.reload();

            $('#customer_name').val("");
            $('#sales_name').val("");
            $('#currency').html("IDR");
            $('#totalPrice').html(0);
            var id_delivery_notes = $(this).val();
            if(id_delivery_notes == ""){
                $('#customer_name').val("");
                $('#sales_name').val("");
                $('#totalPrice').html(0);
            } else {
                var url = '{{ route("transsales.getdeliverynote", ":id") }}';
                url = url.replace(':id', id_delivery_notes);
                if (id_delivery_notes) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if(data.customer_name == null){
                                $('#customer_name').val('-');
                            } else {
                                $('#customer_name').val(data.customer_name);
                            }

                            if(data.salesman_name == null){
                                $('#sales_name').val('-');
                            } else {
                                $('#sales_name').val(data.salesman_name);
                            }

                            if(data.currency_code == null){
                                $('#currency').html("IDR");
                            } else {
                                $('#currency').html(data.currency_code);
                            }
                        }
                    });
                }
                var url2 = '{{ route("transsales.gettotalprice", ":id") }}';
                url2 = url2.replace(':id', id_delivery_notes);
                if (url2) {
                    $.ajax({
                        url: url2,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if(data == null || data == 0){
                                $('#totalPrice').html(0);
                            } else {
                                $('#totalPrice').html(data);
                            }
                        }
                    });
                }
            }
        });
    });
</script>

@endsection