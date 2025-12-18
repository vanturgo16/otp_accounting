<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Detail of <b>{{ $detail->invoice_number }}</b></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body md-body-scroll">
    <div class="row">
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Transaction Number</label>
            <br><h4><span class="badge bg-info">{{ $detail->transaction_number ?? '-' }}</span></h4>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Type</label>
            <br><span>{{ $detail->type ?? '-' }} ({{ $detail->currency ?? '-' }})</span></h4>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Category</label>
            <br><span>{{ $detail->category ?? '-' }}</span></h4>
        </div>
        @if(in_array($detail->type, ['Bukti Bank Keluar', 'Bukti Bank Masuk']))
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Bank</label>
            <br><span>{{ $detail->code_bank ?? '-' }}</span></h4>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Invoice Number</label>
            <br><span>{{ $detail->invoice_number ?? '-' }}</span></h4>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Tax Invoice Number / No. Faktur</label>
            <br><span>{{ $detail->tax_invoice_number ?? '-' }}</span>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Created By</label>
            <br><span>{{ $detail->created_by ?? '-' }}</span>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Invoice Date</label>
            <br><span>{{ $detail->date_invoice ? \Carbon\Carbon::parse($detail->date_invoice)->format('d-m-Y') : '-' }}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h5>Transaction</h5>
        </div>
        <div class="card-body">
            <div class="table-repsonsive">
                <table class="table table-bordered" id="dynamicTable">
                    <thead>
                        <tr>
                            <th class="text-center">Account Code</th>
                            <th class="text-center">Nominal</th>
                            <th class="text-center">Debit / Kredit</th>
                            <th class="text-center">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($generalLedgers as $item)
                        <tr>
                            <td>
                                {{ $item->account_code." - ".$item->account_name }}
                            </td>
                            <td class="text-end">
                                @php
                                    $formatted = number_format($item->amount, 2, ',', '.');
                                    [$before, $after] = explode(',', $formatted);
                                @endphp
                                <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->transaction == 'D')
                                    <span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>
                                @else
                                    <span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->note ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <hr>
    <div class="row">
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Created At :</span></div>
            <span>
                {{ $detail->created_at ?? '-' }}
            </span>
        </div>
        <div class="col-lg-6 mb-2">
            <div><span class="fw-bold">Last Updated At :</span></div>
            <span>
                {{ $detail->updated_at ?? '-' }}
            </span>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
</div>