@extends('layouts.master')
@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('cashbook.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Cash Book Transaction
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('cashbook.index') }}">Cash Book</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form class="formLoad" action="{{ route('cashbook.update', encrypt($detail->id)) }}" method="POST" enctype="multipart/form-data">
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
                                    <label class="form-label">Transaction Number</label>
                                    <input class="form-control readonly-input" type="text" value="{{ $detail->transaction_number }}" placeholder="Auto Generate" readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label required-label">Invoice Date</label>
                                    <i class="mdi mdi-information-outline"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Tanggal hanya dapat dipilih dari awal bulan ini hingga hari ini.">
                                    </i>
                                    <input type="date" class="form-control" name="date_invoice" value="{{ \Carbon\Carbon::parse($detail->date_invoice)->format('Y-m-d') }}" min="{{ date('Y-m-01') }}" max="{{ date('Y-m-d') }}" required>
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
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label required-label">Type</label>
                                    <input class="form-control readonly-input" type="text" value="{{ $detail->type }}" placeholder="Auto Generate" readonly>
                                </div>

                                @if(in_array($detail->type, ['Bukti Kas Keluar', 'Bukti Kas Masuk']))
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label required-label">Currency</label>
                                        <input class="form-control readonly-input" type="text" value="{{ $detail->currency }}" placeholder="Auto Generate" readonly>
                                    </div>
                                @else
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label required-label">Bank Account</label>
                                        <input class="form-control readonly-input" type="text" value="{{ $bankAccountsUsed->currency.' || '.$bankAccountsUsed->account_number.' || '.$bankAccountsUsed->account_name }}" placeholder="Auto Generate" readonly>
                                    </div>
                                @endif

                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-lg-12 mt-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <h6 class="mb-0">
                                                Transaction
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
                                    <a href="{{ route('cashbook.index') }}" type="button" class="btn btn-light waves-effect btn-label waves-light">
                                        <i class="mdi mdi-arrow-left-circle label-icon"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-info waves-effect btn-label waves-light">
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

@endsection