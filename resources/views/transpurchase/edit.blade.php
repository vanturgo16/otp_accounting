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
                            <li class="breadcrumb-item"><a href="{{ route('transpurchase.index') }}">Purchase</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('transpurchase.update', encrypt($detail->id)) }}" id="formUpdate" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="listProduct">
            <input type="hidden" name="njPrice">
            <input type="hidden" name="ppnPrice">
            <input type="hidden" name="totalPrice">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Update Transaction</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    use Carbon\Carbon;
                                    // Example:
                                    // Today = 2026-05-25
                                    // Previous month last day = 2026-04-30
                                    // Minus 20 days = 2026-04-10
                                    $minDate = Carbon::today()->subMonthNoOverflow()->endOfMonth()->subDays(20)->format('Y-m-d');
                                    $maxDate = Carbon::today()->format('Y-m-d');
                                @endphp
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Invoice Date</label>
                                    <i class="mdi mdi-information-outline"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Tanggal invoice hanya dapat dipilih mulai 20 hari sebelum akhir bulan sebelumnya hingga tanggal hari ini.">
                                    </i>
                                    <input type="date" class="form-control" name="date_invoice"
                                        value="{{ \Carbon\Carbon::parse($detail->date_invoice)->format('Y-m-d') }}"
                                        min="{{ $minDate }}"
                                        max="{{ $maxDate }}"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Invoice Number</label>
                                    <input type="text" class="form-control" name="invoice_number" value="{{ $detail->invoice_number }}" placeholder="Input Invoice Number.." required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Tax Invoice Number / No Faktur</label>
                                    <input type="text" class="form-control" name="tax_invoice_number" value="{{ $detail->tax_invoice_number }}" placeholder="Input Tax Invoice Number.." required>
                                </div>
                            </div>
                            <hr>
                            
                            <div class="card">
                                <div class="card-body readonly-card">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label required-label">Good Receipt Notes</label>
                                            <input class="form-control readonly-input" type="text"
                                                value="{{ $detail->grn_number . ' || ' . \Carbon\Carbon::parse($detail->grn_date)->format('Y-m-d') }}" 
                                                readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">External Doc. Number</label>
                                            <input class="form-control readonly-input" id="external_doc_number" type="text" value="{{ $detail->external_doc_number ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Ref Number / PR</label>
                                            <input class="form-control readonly-input" id="ref_number" type="text" value="{{ $detail->ref_number ?? '-' }}" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">PO Number</label>
                                            <input class="form-control readonly-input" id="po_number" type="text" value="{{ $detail->po_number ?? '-' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Suppliers</label>
                                            <input class="form-control readonly-input" id="suppliers" type="text" value="{{ $detail->suppliers ?? '-' }}" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Requester</label>
                                            <input class="form-control readonly-input" id="requester" type="text" value="{{ $detail->requester ?? '-' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label">List Product</label>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-0 d-none d-lg-block" style="font-size:0.5rem;" role="alert">
                                                <ul class="mb-0 ps-3">
                                                    <li>
                                                        <b>Coretax rule:</b> Jika desimal ≥ 0,5 dibulatkan ke atas, jika < 0,5 dibulatkan ke bawah.
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="text-end d-block d-lg-none">
                                                <i class="mdi mdi-information-outline text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Coretax: ≥0,5 naik, <0,5 turun.">
                                                </i>
                                            </div>
                                        </div>
                                        <div class="col-12">
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
                                                            <label class="form-label fw-bold">Set PPN Rate :</label>
                                                        </td>
                                                        <td class="text-end" style="width: 50%;">
                                                            <div class="input-group" style="width: 150px; margin-left: auto;">
                                                                <button class="btn btn-outline-secondary" type="button" id="buttonMinusPPNRate">-</button>
                                                                <input type="text" name="ppn_rate" class="form-control readonly-input text-center" value="{{ $detail->ppn_rate }}" id="ppn_rate" required readonly>
                                                                <button class="btn btn-outline-secondary" type="button" id="buttonPlusPPNRate">+</button>
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
                                                                PPN (<span id="labelPPNRate">{{ $detail->ppn_rate }}</span><span>%)</span>
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
                                                                {{-- <input type="text" name="discount" class="form-control form-control-sm text-end currency-input" value="{{ number_format($detail->total_discount, 2, ',', '.') }}"> --}}
                                                                <input type="text" name="discount" class="form-control form-control-sm text-end currency-input-no-comma" value="{{ number_format(round($detail->total_discount), 0, ',', '.') }}">
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
                                    <textarea class="summernote-editor-simple" name="note" placeholder="Input Note (Opsional)...">{!! $detail->note !!}</textarea>
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
                                            <div class="table-responsive">
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
                                                        @foreach ($generalLedgers as $index => $ledger)
                                                        <tr>
                                                            <td>
                                                                <select class="form-select select2 addpayment" name="addmore[{{ $index }}][account_code]" style="width:100%" required>
                                                                    <option value="">- Select Account Code -</option>
                                                                    @foreach ($accountcodes as $item)
                                                                        <option value="{{ $item->id }}"
                                                                            {{ $item->id == $ledger->id_account_code ? 'selected' : '' }}>
                                                                            {{ $item->account_code }} - {{ $item->account_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control rupiah-input-no-comma addpayment"
                                                                    name="addmore[{{ $index }}][nominal]"
                                                                    {{-- value="{{ number_format($ledger->amount, 2, ',', '.') }}" --}}
                                                                    value="{{ number_format($ledger->amount, 0, ',', '.') }}"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <select class="form-select select2 addpayment" name="addmore[{{ $index }}][type]" style="width:100%" required>
                                                                    <option value="">- Select Type -</option>
                                                                    <option value="D" {{ $ledger->transaction == 'D' ? 'selected' : '' }}>Debit</option>
                                                                    <option value="K" {{ $ledger->transaction == 'K' ? 'selected' : '' }}>Kredit</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control" name="addmore[{{ $index }}][note]" rows="3" placeholder="Input Note (Optional)..">{{ $ledger->note }}</textarea>
                                                            </td>
                                                            <td style="text-align:center">
                                                                @if ($index == 0)
                                                                    <button type="button" id="adds" class="btn btn-success">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn btn-danger remove-tr">
                                                                        <i class="fas fa-minus"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
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
                                    <button type="submit" class="btn btn-info waves-effect btn-label waves-light" name="sb">
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

    document.getElementById('formUpdate').addEventListener('submit', function(event) {
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
    // start index based on existing data
    var i = {{ count($generalLedgers) - 1 }};

    function initPlugins() {
        $('.select2').select2({ width: '100%' });
        document.querySelectorAll(".rupiah-input-no-comma").forEach((input) => {
            input.removeEventListener("input", formatCurrencyInputNoComma);
            input.addEventListener("input", formatCurrencyInputNoComma);
        });
    }

    $(document).ready(function () {
        initPlugins();
    });

    // ADD ROW
    $(document).on('click', '#adds', function () {
        i++;
        let row = `
        <tr>
            <td>
                <select class="form-select select2 addpayment"
                    name="addmore[${i}][account_code]"
                    style="width:100%" required>
                    <option value="">- Select Account Code -</option>
                    @foreach ($accountcodes as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->account_code }} - {{ $item->account_name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text"
                    class="form-control rupiah-input-no-comma addpayment"
                    name="addmore[${i}][nominal]"
                    placeholder="Input Amount.." required>
            </td>
            <td>
                <select class="form-select select2 addpayment"
                    name="addmore[${i}][type]"
                    style="width:100%" required>
                    <option value="">- Select Type -</option>
                    <option value="D">Debit</option>
                    <option value="K">Kredit</option>
                </select>
            </td>
            <td>
                <textarea class="form-control"
                    name="addmore[${i}][note]"
                    rows="3"
                    placeholder="Input Note (Optional).."></textarea>
            </td>
            <td style="text-align:center">
                <button type="button" class="btn btn-danger remove-tr">
                    <i class="fas fa-minus"></i>
                </button>
            </td>
        </tr>`;

        $('#dynamicTable tbody').append(row);
        initPlugins();
    });

    // REMOVE ROW
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
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
        let = idTrans = '{{ $detail->id }}'
        let url = '{{ route("transpurchase.getDetail", ":id") }}'.replace(':id', idTrans);

        dataTable = $('#server-side-table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            ajax: {
                url: url,
                type: 'GET'
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
                                class="form-control form-control-sm text-end editable-price currency-input-no-comma"
                                value="${numberFormat(price,0,',','.')}"
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
                                ${formatPriceWithStyleCoretax(total)}
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

            $('#njPrice').html(formatPriceWithStyleCoretax(json.nj ?? 0));
            $('#ppnPrice').html(formatPriceWithStyleCoretax(json.ppn ?? 0));
            $('#totalPrice').html(formatPriceWithStyleCoretax(json.total ?? 0));
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

            $('.row-total-price[data-row="'+rowIndex+'"]').html(formatPriceWithStyleCoretax(totalRow));
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
            $('#njPrice').html(formatPriceWithStyleCoretax(totalNJ));
            $('#ppnPrice').html(formatPriceWithStyleCoretax(ppn));
            $('#totalPrice').html(formatPriceWithStyleCoretax(total));
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