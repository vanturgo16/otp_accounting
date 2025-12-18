@extends('layouts.master')
@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transpurchase.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Sales Purchase
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transpurchase.index') }}">Sales Purchase</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('transpurchase.store') }}" id="formstore" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="currency" value="IDR">
            <input type="hidden" name="listProduct">
            <input type="hidden" name="njPrice">
            <input type="hidden" name="ppnPrice">
            <input type="hidden" name="totalPrice">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Create New</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Invoice Date</label>
                                    <i class="mdi mdi-information-outline"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Tanggal hanya dapat dipilih dari awal bulan ini hingga hari ini.">
                                    </i>
                                    <input type="date" class="form-control" name="date_invoice" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-01') }}" max="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Invoice Number</label>
                                    <input type="text" class="form-control" name="invoice_number" placeholder="Input Invoice Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Tax Invoice Number / No Faktur</label>
                                    <input type="text" class="form-control" name="tax_invoice_number" placeholder="Input Tax Invoice Number.." required>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Good Receipt Notes</label>
                                    <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Daftar Good Receipt Notes (GRN) berstatus Posted yang belum dibuatkan invoice"></i>
                                    <select class="form-select select2" style="width: 100%" name="id_good_receipt_notes" required>
                                        <option value="" selected disabled>-- Select --</option>
                                        @foreach($grns as $item)
                                            <option value="{{ $item->id }}"
                                                data-grn-number="{{ $item->grn_number }}"
                                                data-grn-date="{{ $item->grn_date }}">
                                                {{ $item->grn_number }} || 
                                                {{ $item->grn_date }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="grn_number" value="">
                                    <input type="hidden" name="grn_date" value="">
                                    
                                    <input type="hidden" name="ref_number" value="">
                                    <input type="hidden" name="po_number" value="">
                                    <input type="hidden" name="suppliers" value="">
                                    <input type="hidden" name="requester" value="">
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body readonly-card">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Ref Number / PR</label>
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Otomatis terisi dari GRN yang dipilih"></i>
                                            <input class="form-control readonly-input" id="ref_number" type="text" value="" placeholder="Select Good Receipt Notes.." readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">PO Number</label>
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Otomatis terisi dari GRN yang dipilih"></i>
                                            <input class="form-control readonly-input" id="po_number" type="text" value="" placeholder="Select Good Receipt Notes.." readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Suppliers</label>
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Otomatis terisi dari GRN yang dipilih"></i>
                                            <input class="form-control readonly-input" id="suppliers" type="text" value="" placeholder="Select Good Receipt Notes.." readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Requester</label>
                                            <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Otomatis terisi dari GRN yang dipilih"></i>
                                            <input class="form-control readonly-input" id="requester" type="text" value="" placeholder="Select Good Receipt Notes.." readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">List Product</label>
                                            <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="align-middle text-center">No.</th>
                                                        <th class="align-middle text-center">Lot Number</th>
                                                        <th class="align-middle text-center">Product</th>
                                                        <th class="align-middle text-center">Receipt Qty (Unit)</th>
                                                        <th class="align-middle text-center">Price</th>
                                                        <th class="align-middle text-center">Total Price</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="col-lg-6 mt-4"></div>
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
                                                                <input type="text" name="ppn_rate" class="form-control readonly-input text-center" value="{{ $initPPN }}" id="ppn_rate" required readonly>
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
                                                            <label class="form-label"> <span class="currency text-muted">IDR</span> <span id="njPrice">0</span></label>
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
                                                            <label class="form-label"> <span class="currency text-muted">IDR</span> <span id="ppnPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">Diskon :</label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> 
                                                                <input type="text" name="discount" class="form-control form-control-sm text-end currency-input" value="0">
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">(Total Nilai Jual + PPN) - Diskon :</label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> <span class="currency text-muted">IDR</span> <span id="totalPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Note</label>
                                    <textarea class="summernote-editor-simple" name="note" placeholder="Input Note (Opsional)..."></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 mt-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <h6 class="mb-0">
                                                Transaction
                                                <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Aturan Transaksi: Total Debit harus sama dengan Total Kredit, serta sama dengan Total (Nilai Purchase + PPN) - Diskon"></i>
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
                                                            <th>Note</th>
                                                            <th style="text-align:center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <select class="form-select select2 addpayment" style="width: 100%" name="addmore[0][account_code]" required>
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
                                                                <select class="form-select select2 addpayment" style="width: 100%" name="addmore[0][type]" required>
                                                                    <option value="">- Select Type -</option>
                                                                    <option value="D">Debit</option>
                                                                    <option value="K">Kredit</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control" name="addmore[0][note]" cols="20" rows="3" placeholder="Input Note (Optional).."></textarea>
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
                <div class="row" id="modalBodyContent"></div>
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
                // Add listProduct to hidden input
                let listProduct = [];
                dataTable.rows().every(function () {
                    let d = this.data();
                    listProduct.push({
                        idGRNDetail  : d.id,
                        idPRDetail   : d.id_purchase_requisition_details,
                        lot_number   : d.lot_number,
                        type_product : d.type_product,
                        product      : d.product,
                        receipt_qty  : d.receipt_qty,
                        unit         : d.unit,
                        price_origin : d.price_origin ?? d.price,
                        price_edit   : d.price_edit   ?? d.price,
                        total_price  : d.total_price
                    });
                });
                $('input[name="listProduct"]').val(JSON.stringify(listProduct));
                $('input[name="njPrice"]').val(normalizeNumber($('#njPrice').text()));
                $('input[name="ppnPrice"]').val(normalizeNumber($('#ppnPrice').text()));
                $('input[name="totalPrice"]').val(normalizeNumber($('#totalPrice').text()));


                var submitButton = this.querySelector('button[name="sb"]');
                submitButton.disabled = true;
                submitButton.innerHTML  = '<i class="mdi mdi-loading mdi-spin label-icon"></i>Please Wait...';
                $("#processing").removeClass("hidden");
                $("body").addClass("no-scroll");
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
                                    <th class="align-top text-center text-bold">Total Purchase Price</th>
                                </tr>
                                <tr>
                                    <th class="align-top text-center">${totals.debitTotal.toLocaleString('id-ID')}</th>
                                    <th class="align-top text-center text-danger">!=</th>
                                    <th class="align-top text-center">${totalPriceGlobal.toLocaleString('id-ID')}</th>
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
        $(".select2").select2();
        ++i;
        $("#dynamicTable").append(
            `<tr>
                <td>
                    <select class="form-select select2 addpayment" style="width: 100%" name="addmore[`+i+`][account_code]" required>
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
                    <select class="form-select select2 addpayment" style="width: 100%" name="addmore[`+i+`][type]" required>
                        <option value="">- Select Type -</option>
                        <option value="D">Debit</option>
                        <option value="K">Kredit</option>
                    </select>
                </td>
                <td>
                    <textarea class="form-control" name="addmore[`+i+`][note]" cols="20" rows="3" placeholder="Input Note (Optional).."></textarea>
                </td>
                <td style="text-align:center">
                    <button type="button" class="btn btn-danger remove-tr"><i class="fas fa-minus"></i></button>
                </td>
            </tr>`);

        $(".select2").select2();

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
    let dataTable;

    $(function() {
        let data = {
            idGRN   : '',
            ppnRate : '{{ $initPPN }}',
        };
        dataTable = $('#server-side-table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: '{!! route('transpurchase.getPriceFromGRN') !!}',
                type: 'GET',
                data: function(d) {
                    d.idGRN   = data.idGRN;
                    d.ppnRate = data.ppnRate;
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'lot_number',
                    name: 'lot_number',
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        return data
                            ? `<span class="text-bold">${data}</span>` 
                            : `<span class="badge bg-secondary">Null</span>`;
                    },
                },
                {
                    data: 'product',
                    render: function(data, type, row) {
                        if(!data) return '<span class="badge bg-secondary">Null</span>';
                        return data + '<br><b>(' + row.type_product + ')</b>';
                    }
                },
                {
                    data: 'receipt_qty',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data + ' (' + row.unit + ')';
                    }
                },
                // PRICE (EDITABLE)
                {
                    data: 'price',
                    className: 'text-end',
                    render: function(data, type, row, meta) {
                        let price = data ?? 0;
                        return `
                            <input type="text"
                                class="form-control form-control-sm text-end editable-price currency-input"
                                value="${numberFormat(price,2,',','.')}"
                                data-row="${meta.row}"
                                data-qty="${row.receipt_qty}">
                        `;
                    }
                },
                {
                    data: 'total_price',
                    className: 'text-end',
                    render: function(data, type, row, meta) {
                        let total = data ?? 0;
                        return `
                            <span class="row-total-price" data-row="${meta.row}">
                                ${formatPriceWithStyle(total)}
                            </span>
                        `;
                    }
                }
            ]
        });

        // ===============================
        // SUMMARY FROM SERVER (INITIAL)
        // ===============================
        dataTable.on('xhr.dt', function(e, settings, json) {
            if (!json) return;

            $('#njPrice').html(formatPriceWithStyle(json.nj ?? 0));
            $('#ppnPrice').html(formatPriceWithStyle(json.ppn ?? 0));
            $('#totalPrice').html(formatPriceWithStyle(json.total ?? 0));
            $('#labelPPNRate').html(json.ppn_rate ?? 0);
            $('.currency').html(json.currency ?? 'IDR');
            $('input[name="currency').val(json.currency ?? 'IDR');

            totalPriceGlobal = json.total ?? 0;
        });

        // ===============================
        // EDIT PRICE EVENT
        // ===============================
        $('#server-side-table').on('keyup', '.editable-price', function () {
            let $this = $(this);
            let rowIndex = $this.data('row');
            let qty      = parseFloat($this.data('qty')) || 0;

            let priceEdit = formatPrice($this.val());
            let totalRow  = priceEdit * qty;

            let rowData = dataTable.row(rowIndex).data();

            rowData.price_edit  = priceEdit;
            rowData.price       = priceEdit;
            rowData.total_price = totalRow;

            $('.row-total-price[data-row="'+rowIndex+'"]').html(formatPriceWithStyle(totalRow));
            recalculateSummary();
        });


        $('input[name="discount').on('keyup', function () {
            if (!$(this).val()) {
                $(this).val('0');
                recalculateSummary();
            }
            recalculateSummary();
        });


        // ===============================
        // SELECT GRN
        // ===============================
        $('select[name="id_good_receipt_notes"]').on('change', function() {
            var selected = $(this).find('option:selected'); 
            $('input[name="grn_number"]').val(selected.data('grn-number')); 
            $('input[name="grn_date"]').val(selected.data('grn-date'));
            
            let idGRN = $(this).val();
            let initPPN = parseFloat('{{ $initPPN }}') || 0;
            resetPPNRate();
            resetPrice();
            $('#ref_number,#po_number,#suppliers,#requester').val('🔃');
            $('input[name="ref_number"], input[name="po_number"], input[name="suppliers"], input[name="requester"]').val('');
            $('input[name="discount"]').val(0);

            if(idGRN) {
                let url = '{{ route("transpurchase.getDetailGRN", ":id") }}'.replace(':id', idGRN);

                $.get(url, function(res) {
                    $('#ref_number').val(res.prNumber ?? '-');
                    $('#po_number').val(res.poNumber ?? '-');
                    $('#suppliers').val(res.supplierName ?? '-');
                    $('#requester').val(res.requester ?? '-');
                    
                    $('input[name="ref_number"]').val(res.prNumber ?? '');
                    $('input[name="po_number"]').val(res.poNumber ?? '');
                    $('input[name="suppliers"]').val(res.supplierName ?? '');
                    $('input[name="requester"]').val(res.requester ?? '');
                });
                reloadDataSO(idGRN, initPPN);
                enablePPNRate();
            }
        });

        // ===============================
        // PPN BUTTONS
        // ===============================
        $('#buttonPlusPPNRate').on('click', function() {
            let rate = parseFloat($('#ppn_rate').val()) || 0;
            if(rate < 100){
                rate++;
                $('#labelPPNRate').html(rate);
                $('#ppn_rate').val(rate);
                recalculateSummary();
            }
        });
        $('#buttonMinusPPNRate').on('click', function() {
            let rate = parseFloat($('#ppn_rate').val()) || 0;
            if(rate > 0){
                rate--;
                $('#labelPPNRate').html(rate);
                $('#ppn_rate').val(rate);
                recalculateSummary();
            }
        });

        // ===============================
        // FUNCTIONS
        // ===============================
        function reloadDataSO(idGRN, ppnRate){
            data.idGRN = idGRN;
            data.ppnRate = ppnRate;
            dataTable.ajax.reload();
        }
        function resetPPNRate(){
            let initPPN = parseFloat('{{ $initPPN }}') || 0;
            $('#ppn_rate').val(initPPN);
            $('#buttonMinusPPNRate,#buttonPlusPPNRate').prop('disabled', true);
        }
        function enablePPNRate(){
            $('#buttonMinusPPNRate,#buttonPlusPPNRate').prop('disabled', false);
        }
        function resetPrice(){
            $('#njPrice,#ppnPrice,#totalPrice').html(0);
        }
        function recalculateSummary(){
            let totalNJ = 0;
            dataTable.rows().every(function(){
                let d = this.data();
                totalNJ += parseFloat(d.total_price) || 0;
            });
            let ppnRate = parseFloat($('#ppn_rate').val()) || 0;
            let ppn     = totalNJ * (ppnRate / 100);
            let discount = parseCurrency(
                $('input[name="discount"]').val() || 0
            );
            let total   = totalNJ + ppn - discount;
            $('#njPrice').html(formatPriceWithStyle(totalNJ));
            $('#ppnPrice').html(formatPriceWithStyle(ppn));
            $('#totalPrice').html(formatPriceWithStyle(total));
            totalPriceGlobal = total ?? 0;
        }
        function formatPrice(value){
            if(!value) return 0;
            return parseFloat(
                value.toString().replace(/\./g,'').replace(',','.')
            ) || 0;
        }
    });
</script>


@endsection