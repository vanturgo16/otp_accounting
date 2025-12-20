@extends('layouts.master')
@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('cashbook.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Cash Book
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('cashbook.index') }}">Cash Book</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form id="formstore" action="{{ route('cashbook.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <label class="form-label">Transaction Number</label>
                                    <input class="form-control readonly-input" type="text" value="" placeholder="Auto Generate" readonly>
                                </div>
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
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label required-label">Type</label>
                                    <select class="form-control select2" name="type" required>
                                        <option value="" selected>Choose</option>
                                        @foreach($typeManuals as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3 d-none" id="currencyWrapper">
                                    <label class="form-label required-label">Currency</label>
                                    <input class="form-control readonly-input" name="currency" type="text" value="IDR" readonly>
                                </div>
                                <div class="col-lg-6 mb-3 d-none" id="bankAccountWrapper">
                                    <label class="form-label required-label">Bank Account</label>
                                    <i class="mdi mdi-information-outline"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Select the bank account that will be generate for transaction number.">
                                    </i>
                                    <select class="form-select select2" style="width: 100%" name="id_master_bank_account">
                                        <option value="" selected disabled>Select Bank</option>
                                        @foreach($bankAccounts as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->currency }} || {{ $item->account_number }} || {{ $item->bank_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-lg-12 mt-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <h6 class="mb-0">
                                                Transaction
                                                <i class="mdi mdi-information-outline" data-bs-toggle="tooltip" data-bs-placement="top" title="Aturan Transaksi: Total Debit harus sama dengan Total Kredit"></i>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Transaction Note -->
                                            <div class="alert alert-info small mb-3">
                                                <strong>Note:</strong><br>
                                                Pada bagian <b>Index</b> dan <b>Print Invoice</b>, akun yang ditampilkan adalah:
                                                <ul class="mb-0 ps-3">
                                                    <li><b>Bukti Kas Masuk & Bank Masuk</b> → List akun <b>Kredit</b> saja</li>
                                                    <li><b>Bukti Kas Keluar & Bank Keluar</b> → List akun <b>Debit</b> saja</li>
                                                </ul>
                                            </div>
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
                                                                <textarea class="form-control" name="addmore[0][note]" cols="20" rows="3" placeholder="Input Note..(Optional)"></textarea>
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
                                    <a href="{{ route('cashbook.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
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
            var submitButton = this.querySelector('button[name="sb"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<i class="mdi mdi-loading mdi-spin label-icon"></i>Please Wait...';
            }
            $("#processing").removeClass("hidden");
            $("body").addClass("no-scroll");
            this.submit();
            return true;
        }
    });
</script>

{{-- Toogle Type --}}
<script>
    $(document).ready(function () {
        function toggleBankAccount() {
            const type = $('select[name="type"]').val();
            if (type === 'Bukti Bank Keluar' || type === 'Bukti Bank Masuk') {
                $('#currencyWrapper').addClass('d-none');
                $('#bankAccountWrapper').removeClass('d-none');
                $('select[name="id_master_bank_account"]').prop('required', true);
            } else {
                $('#currencyWrapper').removeClass('d-none');
                $('#bankAccountWrapper').addClass('d-none');
                $('select[name="id_master_bank_account"]')
                    .prop('required', false)
                    .val(null)
                    .trigger('change'); // reset select2
            }
        }
        // Initial state (page load)
        toggleBankAccount();
        // On change
        $('select[name="type"]').on('change', function () {
            toggleBankAccount();
        });
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
                    <textarea class="form-control" name="addmore[`+i+`][note]" cols="20" rows="3" placeholder="Input Note..(Optional)"></textarea>
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

@endsection