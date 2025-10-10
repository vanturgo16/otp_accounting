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
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Ref Number</label><label style="color: darkred">*</label>
                                    <br><span class="badge bg-info text-white">Auto Generate</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Invoice Date</label><label style="color: darkred">*</label>
                                    <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Tanggal yang akan ditampilkan pada lembar invoice"></i>
                                    <input type="date" class="form-control" name="date_invoice" value="{{ date('Y-m-d') }}" required max="<?= date('Y-m-d'); ?>">
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
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Daftar Delivery Note (DN) berstatus Posted yang belum dibuatkan invoice"></i>
                                            <select class="form-select js-example-basic-single" style="width: 100%" name="id_delivery_notes" required>
                                                <option value="" selected disabled>-- Select --</option>
                                                @foreach($deliveryNotes as $item)
                                                    <option value="{{ $item->id }}"
                                                        data-dn-number="{{ $item->dn_number }}"
                                                        data-dn-date="{{ $item->date }}"
                                                        data-po-number="{{ $item->po_number }}"
                                                        data-ko-number="{{ $item->ko_number }}">
                                                        {{ $item->dn_number }} || 
                                                        {{ $item->date }} || 
                                                        {{ ucfirst($item->status) }} || 
                                                        KO/PO: {{ $item->ko_number ?? $item->po_number ?? '-' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="dn_number" value="">
                                            <input type="hidden" name="dn_date" value="">
                                            <input type="hidden" name="po_number" value="">
                                            <input type="hidden" name="ko_number" value="">
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Customer Name</label>
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Otomatis terisi dari DN yang dipilih"></i>
                                            <input type="hidden" name="id_master_customers" id="id_master_customers" value="">
                                            <input class="form-control" id="customer_name" type="text" value="" placeholder="Select Delivery Notes.." style="background-color:#EAECF4" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Sales Name</label>
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Otomatis terisi dari DN yang dipilih"></i>
                                            <input class="form-control" id="sales_name" type="text" value="" placeholder="Select Delivery Notes.." style="background-color:#EAECF4" readonly>
                                        </div>
                                        
                                        <div class="col-12">
                                            <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th rowspan="2" class="align-middle text-center">No.</th>
                                                        <th rowspan="2" class="align-middle text-center">SO Number</th>
                                                        <th rowspan="2" class="align-middle text-center">Product</th>
                                                        <th rowspan="2" class="align-middle text-center">Qty (Unit)</th>
                                                        <th rowspan="2" class="align-middle text-center">Tax Type</th>
                                                        <th colspan="3" class="align-middle text-center">Price</th>
                                                        <th colspan="2" class="align-middle text-center">Total Price</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="align-middle text-center">Before Tax</th>
                                                        <th class="align-middle text-center">Tax Value</th>
                                                        <th class="align-middle text-center">After Tax</th>
                                                        <th class="align-middle text-center">Before Tax</th>
                                                        <th class="align-middle text-center">After Tax</th>
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
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">Set PPN Rate <br><small>(default {{ $initPPN }}%)</small> :</label>
                                                        </td>
                                                        <td class="text-end" style="width: 50%;">
                                                            <div class="input-group" style="width: 150px; margin-left: auto;">
                                                                <button class="btn btn-outline-secondary" type="button" id="buttonMinusPPNRate" disabled>-</button>
                                                                <input type="text" name="ppn_rate" class="form-control text-center" value="{{ $initPPN }}" id="ppn_rate" style="background-color:#EAECF4" required readonly>
                                                                <button class="btn btn-outline-secondary" type="button" id="buttonPlusPPNRate" disabled>+</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br></td><td><br></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">Total Nilai Jual :</label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> <span class="text-muted">Rp. </span><span id="njPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">
                                                                DPP Lain-lain 
                                                                <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Total Nilai Jual X (11/12)"></i>
                                                                :
                                                            </label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> <span class="text-muted">Rp. </span><span id="dppPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">
                                                                PPN (<span id="labelPPNRate">{{ $initPPN }}</span><span>%)</span>
                                                                <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Total Nilai Jual X PPN Rate"></i>
                                                                :
                                                            </label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> <span class="text-muted">Rp. </span><span id="ppnPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">Total Nilai Jual + PPN :</label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> Rp. <span id="totalPrice">0</span></label>
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
                                            <h6 class="mb-0">
                                                Transaction
                                                <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Aturan Transaksi: Total Debit harus sama dengan Total Kredit, serta sama dengan Total Nilai Jual + PPN"></i>
                                            </h6>
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
    function normalizeNumber(value) {
        if (typeof value === "string") {
            return parseFloat(
                value.replace(/\./g, "").replace(",", ".")
            );
        }
        return Number(value);
    }

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
            let numDebit = normalizeNumber(totals.debitTotal);
            let numPrice = normalizeNumber(totalPriceGlobal);
            if(numDebit == numPrice){
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
        var data = {
            idDN    : '',
            ppnRate : '{{ $initPPN }}',
        };
        var dataTable = $('#server-side-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('transsales.getSOPriceFromDN') !!}',
                type: 'GET',
                data: function(d) {
                    d.idDN      = data.idDN;
                    d.ppnRate   = data.ppnRate;
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
                    render: function(data, type, row) {
                        return row.so_number 
                            ? `<span class="text-bold">${row.so_number}</span>` 
                            : `<span class="badge bg-secondary">Null</span>`;
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
                    data: 'ppn_type',
                    name: 'ppn_type',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return (data ? data : '-');
                    },
                },
                {
                    data: 'price_before_ppn',
                    name: 'price_before_ppn',
                    orderable: true,
                    searchable: true,
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (data == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        var formattedAmount = numberFormat(data, 3, ',', '.'); 
                        var parts = formattedAmount.split(',');
                        if (parts.length > 1) {
                            return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span>';
                        }
                        return '<span class="text-bold">' + parts[0] + '</span>';
                    },
                },
                {
                    data: 'ppn_value',
                    name: 'ppn_value',
                    orderable: true,
                    searchable: true,
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (data == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        var formattedAmount = numberFormat(data, 3, ',', '.'); 
                        var parts = formattedAmount.split(',');
                        if (parts.length > 1) {
                            return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span><br>(' + row.ppn_rate + '%)';
                        }
                        return '<span class="text-bold">' + parts[0] + '</span><br>(' + row.ppn_rate + '%)';
                    },
                },
                {
                    data: 'price_after_ppn',
                    name: 'price_after_ppn',
                    orderable: true,
                    searchable: true,
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (data == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        var formattedAmount = numberFormat(data, 3, ',', '.'); 
                        var parts = formattedAmount.split(',');
                        if (parts.length > 1) {
                            return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span>';
                        }
                        return '<span class="text-bold">' + parts[0] + '</span>';
                    },
                },
                {
                    data: 'total_price_before_ppn',
                    name: 'total_price_before_ppn',
                    orderable: true,
                    searchable: true,
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (data == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        var formattedAmount = numberFormat(data, 3, ',', '.'); 
                        var parts = formattedAmount.split(',');
                        if (parts.length > 1) {
                            return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span>';
                        }
                        return '<span class="text-bold">' + parts[0] + '</span>';
                    },
                },
                {
                    data: 'total_price_after_ppn',
                    name: 'total_price_after_ppn',
                    orderable: true,
                    searchable: true,
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (data == null) {
                            return '<span class="badge bg-secondary">Null</span>';
                        }
                        var formattedAmount = numberFormat(data, 3, ',', '.'); 
                        var parts = formattedAmount.split(',');
                        if (parts.length > 1) {
                            return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span>';
                        }
                        return '<span class="text-bold">' + parts[0] + '</span>';
                    },
                },
            ]
        });

        dataTable.on('xhr.dt', function(e, settings, json, xhr) {
            if (json) {
                $('#njPrice').html(json.nj ? formatPriceWithStyle(json.nj) : 0);
                $('#dppPrice').html(json.dpp ? formatPriceWithStyle(json.dpp) : 0);
                $('#ppnPrice').html(json.ppn ? formatPriceWithStyle(json.ppn) : 0);
                $('#totalPrice').html(json.total ? formatPriceWithStyle(json.total) : 0);
                $('#labelPPNRate').html(json.ppn_rate ?? 0);
                totalPriceGlobal = formatPrice(json.total) || 0;
            }
        });

        $('select[name="id_delivery_notes"]').on('change', function() {
            var selected = $(this).find('option:selected');
            $('input[name="dn_number"]').val(selected.data('dn-number'));
            $('input[name="dn_date"]').val(selected.data('dn-date'));
            $('input[name="po_number"]').val(selected.data('po-number'));
            $('input[name="ko_number"]').val(selected.data('ko-number'));

            var idDN    = $(this).val();
            let initPPN = parseFloat('{{ $initPPN }}') || 0;

            $('#customer_name, #sales_name, #id_master_customers').val("");
            resetPPNRate();
            resetPrice();
            
            if(idDN) {
                let urlGetCust = '{{ route("transsales.getCustomerFromDN", ":id") }}'.replace(':id', idDN);
                $.ajax({
                    url: urlGetCust,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#id_master_customers').val(data.id_master_customers ?? '-');
                        $('#customer_name').val(data.customer_name ?? '-');
                        $('#sales_name').val(data.salesman_name ?? '-');
                    }
                });

                // To Update Datatable Display
                reloadDataSO(idDN, initPPN);
                enablePPNRate();
            }
        });

        function resetPPNRate(){
            let initPPN = parseFloat('{{ $initPPN }}') || 0;
            $('#buttonMinusPPNRate, #buttonPlusPPNRate').prop('disabled', true);
            $('#ppn_rate').val(initPPN).css('background-color', '#EAECF4');
        }
        function enablePPNRate(){
            $('#buttonMinusPPNRate, #buttonPlusPPNRate').prop('disabled', false);
            $('#ppn_rate').css('background-color', '');
        }
        function resetPrice(){
            $('#njPrice, #dppPrice, #ppnPrice, #totalPrice').html(0);
        }

        function reloadDataSO(idDN, ppnRate){
            // To Update Datatable Display
            data.idDN       = idDN;
            data.ppnRate    = ppnRate;
            dataTable.ajax.reload();
        }

        // plus
        $('#buttonPlusPPNRate').on('click', function() {
            resetPrice();
            let idDN    = $('select[name="id_delivery_notes"]').val();
            let ppnRate = parseFloat($('#ppn_rate').val()) || 0;
            if(ppnRate < 100) {
                ppnRate++;
                $('#ppn_rate').val(ppnRate);
                reloadDataSO(idDN, ppnRate);
            }
        });
        // minus
        $('#buttonMinusPPNRate').on('click', function() {
            resetPrice();
            let idDN    = $('select[name="id_delivery_notes"]').val();
            let ppnRate = parseFloat($('#ppn_rate').val()) || 0;
            if(ppnRate > 0) {
                ppnRate--;
                $('#ppn_rate').val(ppnRate);
                reloadDataSO(idDN, ppnRate);
            }
        });
    });
</script>

@endsection