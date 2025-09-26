@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transsales.local.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Sales Transaction (Local)
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transsales.local.index') }}">Sales (Local)</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.alert')

        <form action="{{ route('transsales.local.store') }}" id="formstore" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Create New</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Ref Number</label><label style="color: darkred">*</label>
                                    <br>
                                    <span class="badge bg-info text-white">Auto Generate</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Invoice Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="date_invoice" value="" required>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Transaction Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="date_transaction" value="" required>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Due Date</label><label style="color: darkred">*</label>
                                    <input type="date" class="form-control" name="due_date" value="" required min="<?= date('Y-m-d'); ?>">
                                </div>
                                <hr>
                            </div>

                            <div class="card">
                                <div class="card-body" style="background-color:ghostwhite">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Delivery Note</label><label style="color: darkred">*</label>
                                            {{-- <select class="form-select js-example-basic-single" style="width: 100%" name="id_delivery_notes" required>
                                                <option value="" selected>-- Select --</option>
                                                @foreach($deliveryNotes as $item)
                                                    <option value="{{ $item->id }}">{{ $item->dn_number." || ". $item->date . " || ". $item->status ." || Ref : ". $item->reference_number ?? '-' }}</option>
                                                @endforeach
                                            </select> --}}
                                            <select class="form-select js-example-basic-single" style="width: 100%" name="id_delivery_notes" required>
                                                <option value="" selected disabled>-- Select --</option>
                                                @foreach($deliveryNotes as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->dn_number }} || {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }} || 
                                                        Status: {{ ucfirst($item->status) }} || Ref: {{ $item->reference_number ?? '-' }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="col-lg-6 mb-3">
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Customer Name</label>
                                            <input class="form-control" id="customer_name" type="text" value="" placeholder="Select Delivery Notes.." style="background-color:#EAECF4" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Sales Name</label>
                                            <input class="form-control" id="sales_name" type="text" value="" placeholder="Select Delivery Notes.." style="background-color:#EAECF4" readonly>
                                        </div>
                                        
                                        <div class="col-12">
                                            <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle text-center">No.</th>
                                                        <th class="align-middle text-center">SO Number</th>
                                                        <th class="align-middle text-center">Product</th>
                                                        <th class="align-middle text-center">Qty (Unit)</th>
                                                        <th class="align-middle text-center">Tax</th>
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
                                                            <label class="form-label" style="text-align: right; display: block;">: Rp. <span id="totalPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3"></div>
                                        <div class="col-lg-2 mb-3">
                                            <label class="form-label" for="tax">Tax (%)</label><label style="color: darkred">*</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="tax" id="tax" value="" placeholder="Tax.." style="background-color:#EAECF4" required readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="background-color:#EAECF4">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label">Tax Amount</label><label style="color: darkred">*</label>
                                            <input type="text" class="form-control rupiah-input" placeholder="Input Amount.." name="tax_sales" value="" style="width: 100%; background-color:#EAECF4" required readonly>
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
            if(totals.debitTotal == totalPriceGlobal){
                var submitButton = this.querySelector('button[name="sb"]');
                submitButton.disabled = true;
                submitButton.innerHTML  = '<i class="mdi mdi-loading mdi-spin label-icon"></i>Please Wait...';
                this.submit();
                return true;
            } 
            else {
                let modalBodyContent = `
                    <div class="col-12">
                        <table class="table dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th class="align-top text-center text-bold">Total Debit</th>
                                    <th class="align-top text-center text-bold">?</th>
                                    <th class="align-top text-center text-bold">Total Sales Price</th>
                                </tr>
                                <tr>
                                    <th class="align-top text-center">${totals.debitTotal.toLocaleString('id-ID')}</th>
                                    <th class="align-top text-center text-danger">!=</th>
                                    <th class="align-top text-center">${totalPriceGlobal}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                `;

                document.getElementById('modalBodyContent').innerHTML = modalBodyContent;
                var myModal = new bootstrap.Modal(document.getElementById('alert'));
                myModal.show();
            }
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
    let totalPriceGlobal = 0;
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
                        return (data ? data : '-') + ' (' + (row.unit ? row.unit : '-') + ')';
                    },
                },
                {
                    data: 'ppn',
                    name: 'ppn',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return (data ? data : '-');
                    },
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (row.price == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        return formatPrice(row.price);
                    },
                },
                {
                    data: 'total_price',
                    name: 'total_price',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (row.total_price == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        return formatPrice(row.total_price);
                    },
                },
            ]
        });

        let initiateppn = parseFloat('{{ $tax }}') || 0;
        $('select[name="id_delivery_notes"]').on('change', function() {
            data.id_delivery_notes = $(this).val();
            dataTable.ajax.reload();

            $('#customer_name').val("");
            $('#sales_name').val("");
            $('#totalPrice').html(0);
            var id_delivery_notes = $(this).val();
            if(id_delivery_notes == ""){
                $('#customer_name').val("");
                $('#sales_name').val("");
                $('#totalPrice').html(0);

                $('input[name="tax"]').val("N/A")
                    .prop('readonly', true).css('background-color', '#EAECF4')
                    .attr('placeholder', 'N/A');
                $('input[name="tax_sales"]').val(0)
                    .prop('readonly', true).css('background-color', '#EAECF4')
                    .attr('placeholder', 'N/A');
            } else {
                let urlTotal = '{{ route("transsales.gettotalprice", ":id") }}'.replace(':id', id_delivery_notes);
                let urlDN    = '{{ route("transsales.getdeliverynote", ":id") }}'.replace(':id', id_delivery_notes);

                if (id_delivery_notes) {
                    $.ajax({
                        url: urlTotal,
                        type: "GET",
                        dataType: "json",
                        success: function(totalPrice) {
                            totalPriceGlobal = parseFloat(totalPrice) || 0;
                            // show total price
                            $('#totalPrice').html(totalPrice ? formatPrice(totalPrice) : 0);

                            // now get delivery note after we know totalPrice
                            $.ajax({
                                url: urlDN,
                                type: "GET",
                                dataType: "json",
                                success: function(data) {
                                    $('#customer_name').val(data.customer_name ?? '-');
                                    $('#sales_name').val(data.salesman_name ?? '-');

                                    if (data.ppn === "Include") {
                                        let initiateAmount = (initiateppn / 100) * (totalPrice ?? 0);
                                        $('input[name="tax"]').val(initiateppn)
                                            .prop('readonly', false).css('background-color', '')
                                            .attr('placeholder', 'Tax..');
                                        $('input[name="tax_sales"]').val(formatPrice(initiateAmount))
                                            .prop('readonly', false).css('background-color', '')
                                            .attr('placeholder', 'Input Amount..');
                                    } else {
                                        $('input[name="tax"]').val("N/A")
                                            .prop('readonly', true).css('background-color', '#EAECF4')
                                            .attr('placeholder', 'N/A');
                                        $('input[name="tax_sales"]').val(0)
                                            .prop('readonly', true).css('background-color', '#EAECF4')
                                            .attr('placeholder', 'N/A');
                                    }
                                }
                            });
                        }
                    });
                }
            }
        });
    });
    
    function formatPrice(value) {
        if (!value) return '0';
        // format with 3 decimals first
        let formatted = Number(value).toLocaleString('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        });
        // remove trailing zeros after comma
        formatted = formatted.replace(/,?0+$/, '');
        return formatted;
    }

    $('input[name="tax"]').on('input', function () {
        let ppn = parseFloat($(this).val()) || 0;
        let newAmount = (ppn / 100) * totalPriceGlobal;
        $('input[name="tax_sales"]').val(formatPrice(newAmount));
    });
</script>

@endsection