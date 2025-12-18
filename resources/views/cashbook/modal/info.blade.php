<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">Detail of <b>{{ $detail->invoice_number }}</b></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body md-body-scroll">
    <div class="row">
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Invoice Number</label>
            <br><h4><span class="badge bg-info">{{ $detail->invoice_number }}</span></h4>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Tax Invoice Number / No. Faktur</label>
            <br><span>{{ $detail->tax_invoice_number }}</span>
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
    {{-- <div class="row">
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Created By</label>
            <br><span>{{ $detail->created_by ?? '-' }}</span>
        </div>
        <div class="col-lg-3 mb-3">
            <label class="form-label mb-0">Invoice Date</label>
            <br><span>{{ $detail->date_invoice ? \Carbon\Carbon::parse($detail->date_invoice)->format('d-m-Y') : '-' }}</span>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-header bg-light">
            <h5>Good Receipt Note Detail</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <label class="form-label mb-0">GRN Number</label>
                    <br><span>{{ $detail->grn_number }}</span>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label mb-0">GRN Date</label>
                    <br><span>{{ $detail->grn_date ? \Carbon\Carbon::parse($detail->grn_date)->format('d-m-Y') : '-' }}</span>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label mb-0">Ref. Number</label>
                    <br><span>{{ $detail->ref_number ?? '-' }}</span>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label mb-0">PO Number</label>
                    <br><span>{{ $detail->po_number ?? '-' }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 mb-3">
                    <label class="form-label mb-0">Requester</label>
                    <br><span>{{ $detail->requester ?? '-' }}</span>
                </div>
                <div class="col-lg-6 mb-3">
                    <label class="form-label mb-0">Suppliers</label>
                    <br><span>{{ $detail->suppliers ?? '-' }}</span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <label class="form-label">List Product</label>
                    <table class="table table-bordered dt-responsive w-100" style="font-size: small">
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
                        <tbody>
                            @foreach($detailTrans as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $item->lot_number }}</td>
                                    <td>
                                        {{ $item->product }}
                                        <br><b>({{ $item->type_product }})</b>
                                    </td>
                                    <td class="text-center">
                                        {{ fmod($item->receipt_qty, 1) == 0 
                                            ? number_format($item->receipt_qty, 0, ',', '.') 
                                            : number_format(floor($item->receipt_qty), 0, ',', '.') . ',' . rtrim(str_replace('.', '', explode('.', (string)$item->receipt_qty)[1]), '0') }} 
                                        ({{ $item->unit }})
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $formatted = number_format($item->price_edit, 2, ',', '.');
                                            [$before, $after] = explode(',', $formatted);
                                        @endphp
                                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $formatted = number_format($item->total_price, 2, ',', '.');
                                            [$before, $after] = explode(',', $formatted);
                                        @endphp
                                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6 mt-4"></div>
                <div class="col-lg-6 mt-4">
                    <table style="width: 100%">
                        <tbody>
                            <tr>
                                <td class="text-end">
                                    <label class="form-label fw-bold">PPN Rate :</label>
                                </td>
                                <td class="text-end">
                                    <label class="form-label"> {{ $detail->ppn_rate ?? '0' }}%</label>
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
                                    <label class="form-label"> <span class="text-muted">{{ $detail->currency }} </span>
                                        @php
                                            $formatted = number_format($detail->amount, 2, ',', '.');
                                            [$before, $after] = explode(',', $formatted);
                                        @endphp
                                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end">
                                    <label class="form-label fw-bold">PPN :</label>
                                </td>
                                <td class="text-end">
                                    <label class="form-label"> <span class="text-muted">{{ $detail->currency }} </span>
                                        @php
                                            $formatted = number_format($detail->ppn_value, 2, ',', '.');
                                            [$before, $after] = explode(',', $formatted);
                                        @endphp
                                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end">
                                    <label class="form-label fw-bold">Diskon :</label>
                                </td>
                                <td class="text-end">
                                    <label class="form-label"> <span class="text-muted">{{ $detail->currency }} </span>
                                        @php
                                            $formatted = number_format($detail->total_discount, 2, ',', '.');
                                            [$before, $after] = explode(',', $formatted);
                                        @endphp
                                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end">
                                    <label class="form-label fw-bold">(Total Nilai Jual + PPN) - Diskon :</label>
                                </td>
                                <td class="text-end">
                                    <label class="form-label"> <span class="text-muted">{{ $detail->currency }} </span>
                                        @php
                                            $formatted = number_format($detail->total, 2, ',', '.');
                                            [$before, $after] = explode(',', $formatted);
                                        @endphp
                                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <label class="form-label mb-0">Note</label>
        </div>
        <div class="card-body">
            <span>{!! $detail->note ?? '-' !!}</span>
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