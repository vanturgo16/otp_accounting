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
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('transsales.export.update', encrypt($detail->id)) }}" id="formUpdate" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center py-3">
                            <h5 class="mb-0">Update Transaction</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input class="form-control readonly-input" type="text" value="{{ $detail->ref_number }}" placeholder="Auto Generate" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Invoice Date</label>
                                    <i class="mdi mdi-information-outline"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Pilih tanggal yang akan ditampilkan pada invoice. Tanggal hanya dapat dipilih dari awal bulan ini hingga hari ini.">
                                    </i>
                                    <input type="date" class="form-control" name="date_invoice" value="{{ \Carbon\Carbon::parse($detail->date_invoice)->format('Y-m-d') }}" min="{{ date('Y-m-01') }}" max="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Bank Account</label>
                                    <i class="mdi mdi-information-outline"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Pilih akun bank yang akan ditampilkan pada invoice. Informasi ini akan muncul sebagai detail pembayaran.">
                                    </i>
                                    <select class="form-select select2" style="width: 100%" name="id_master_bank_account" required>
                                        <option value="" selected disabled>Select Bank</option>
                                        @foreach($bankAccounts as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $detail->id_master_bank_account) selected @endif>
                                                {{ $item->account_number }} || {{ $item->bank_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                                
                            <div class="card w-100 bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        Approval Info
                                        <i class="mdi mdi-information-outline"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Informasi approval yang akan ditampilkan pada invoice. Data diambil dari menu Master Approval dan mencakup nama, email, serta posisi approver.">
                                        </i>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <table style="width: 100%">
                                                <input type="hidden" class="form-control" name="app_name" value="{{ $approvalInfo['name'] ?? '' }}" required>
                                                <input type="hidden" class="form-control" name="app_email" value="{{ $approvalInfo['email'] ?? '' }}" required>
                                                <input type="hidden" class="form-control" name="app_position" value="{{ $approvalInfo['position'] ?? '' }}" required>
                                                <tbody>
                                                    <tr>
                                                        <td><label class="form-label font-weight-bold">Approval Name</label></td>
                                                        <td><label class="form-label">:</label></td>
                                                        <td><label class="form-label">{{ $approvalInfo['name'] ?? '-' }}</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="form-label font-weight-bold">Approval Email</label></td>
                                                        <td><label class="form-label">:</label></td>
                                                        <td><label class="form-label">{{ $approvalInfo['email'] ?? '-' }}</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="form-label font-weight-bold">Approval Position</label></td>
                                                        <td><label class="form-label">:</label></td>
                                                        <td><label class="form-label">{{ $approvalInfo['position'] ?? '-' }}</label></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Delivery Note</label>
                                    <input class="form-control readonly-input" type="text"
                                        value="{{ 
                                            $detail->dn_number . ' || ' .
                                            \Carbon\Carbon::parse($detail->dn_date)->format('Y-m-d') . ' || KO/PO: ' .
                                            ($detail->ko_number ? $detail->ko_number : ($detail->po_number ? $detail->po_number : '-'))
                                        }}" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body readonly-card">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">PO Number</label>
                                            <input class="form-control readonly-input" id="po_number" type="text" value="{{ $detail->customer_name }}" placeholder="Select Delivery Notes.." readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Customer Name</label>
                                            <input class="form-control readonly-input" id="customer_name" type="text" value="{{ $detailCust->customer_name ?? '-' }}" placeholder="Select Delivery Notes.." readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Sales Name</label>
                                            <input class="form-control readonly-input" id="sales_name" type="text" value="{{ $detailCust->salesman_name ?? '-' }}" placeholder="Select Delivery Notes.." readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label">Customer Address</label>
                                            <input class="form-control readonly-input" id="customer_address" type="text" value="{{ $detailCust->address ?? '-' }}" placeholder="Select Delivery Notes.." readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label required-label">Destination</label>
                                            <input class="form-control" id="destination" name="destination" type="text" value="{{ $detail->destination }}" placeholder="Input Destination Invoice.." required>
                                        </div>
                                    </div>
                                    

                                    <div class="row">
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
                                            <label class="form-label">Note</label>
                                            <textarea class="summernote-editor-simple" name="note" placeholder="Input Note (Optional)...">{!! $detail->note ?? '' !!}</textarea>
                                        </div>
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
                                                            <label class="form-label"> <span class="currency text-muted">{{ $detail->currency }}</span> <span id="njPrice">0</span></label>
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
                                                            <label class="form-label"> <span class="currency text-muted">{{ $detail->currency }}</span> <span id="ppnPrice">0</span></label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-end">
                                                            <label class="form-label fw-bold">Total Nilai Jual + PPN :</label>
                                                        </td>
                                                        <td class="text-end">
                                                            <label class="form-label"> <span class="currency text-muted">{{ $detail->currency }}</span> <span id="totalPrice">0</span></label>
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
                                    <label class="form-label required-label">Term</label>
                                    <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Isi bagian ini dengan syarat dan ketentuan yang akan ditampilkan pada invoice. Informasi ini akan tampil sebagai catatan atau kebijakan pembayaran.">
                                    </i>
                                    <textarea class="summernote-editor-simple" name="term" placeholder="Input Term...">{!! $detail->term ?? '' !!}</textarea>
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
                                                                    class="form-control rupiah-input addpayment"
                                                                    name="addmore[{{ $index }}][nominal]"
                                                                    value="{{ number_format($ledger->amount, 2, ',', '.') }}"
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
                                    <a href="{{ route('transsales.export.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
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

<div class="modal fade" id="alertTerm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"><span class="mdi mdi-alert"></span> Term Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 p-2 text-center">
                        Term is required. Please fill in the Term field before submitting the form. 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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

        let term = document.querySelector('textarea[name="term"]').value.trim();
        if (term === "") {
            var modalTerm = new bootstrap.Modal(document.getElementById('alertTerm'));
            modalTerm.show();
            return;
        }

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
    // start index based on existing data
    var i = {{ count($generalLedgers) - 1 }};

    function initPlugins() {
        $('.select2').select2({ width: '100%' });
        document.querySelectorAll(".rupiah-input").forEach((input) => {
            input.removeEventListener("input", formatCurrencyInput);
            input.addEventListener("input", formatCurrencyInput);
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
                    class="form-control rupiah-input addpayment"
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

    // RUPIAH FORMATTER
    function formatCurrencyInput(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
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
            idDN    : '{{ $detail->id_delivery_notes }}',
            ppnRate : '{{ $detail->ppn_rate }}',
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
                        var formattedAmount = numberFormat(data, 2, ',', '.'); 
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
                        var formattedAmount = numberFormat(data, 2, ',', '.'); 
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
                        var formattedAmount = numberFormat(data, 2, ',', '.'); 
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
                        var formattedAmount = numberFormat(data, 2, ',', '.'); 
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
                        var formattedAmount = numberFormat(data, 2, ',', '.'); 
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
                $('#ppnPrice').html(json.ppn ? formatPriceWithStyle(json.ppn) : 0);
                $('#totalPrice').html(json.total ? formatPriceWithStyle(json.total) : 0);
                $('#labelPPNRate').html(json.ppn_rate ?? 0);
                totalPriceGlobal = formatPrice(json.total) || 0;
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
            $('#njPrice, #ppnPrice, #totalPrice').html(0);
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
            let idDN    = "{{ $detail->id_delivery_notes }}";
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
            let idDN    = "{{ $detail->id_delivery_notes }}";
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