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
                            <li class="breadcrumb-item"><a href="{{ route('transpurchase.index') }}">Purchase Transaction</a></li>
                            <li class="breadcrumb-item active">Info</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center py-3">
                        <h5 class="mb-0">Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Ref. Number</label>
                                <br><h4><span class="badge bg-info">{{ $data->ref_number }}</span></h4>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Transaction Date</label>
                                <br><span>{{ $transaction_date }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="card p-2" style="background-color:#f0f2f7">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Good Receipt Note (Receipt Number)</label>
                                    <br><span>{{ $data->receipt_number }}</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Purchase Order Number</label>
                                    <br><span>{{ $data->po_number }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">PO Date</label>
                                    <br><span>{{ $data->date }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Supplier</label>
                                    <br><span>{{ $data->supplier }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Reference Number</label>
                                    <br><span>{{ $data->reference_number }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Status</label>
                                    <br><span>{{ $data->status }}</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Own Remark</label>
                                    <br><span>
                                        @if($data->own_remarks == null)
                                            <span class="badge bg-secondary">Null</span>
                                        @else
                                            {{ $data->own_remarks }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Supplier Remark</label>
                                    <br>
                                    <span>
                                        @if($data->supplier_remarks == null)
                                            <span class="badge bg-secondary">Null</span>
                                        @else
                                            {{ $data->supplier_remarks }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Sub Total</label>
                                    <br>
                                    <span>
                                        @if($data->sub_total == null)
                                            <span class="badge bg-secondary">Null</span>
                                        @else
                                            {{ $data->sub_total }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Total Discount</label>
                                    <br>
                                    <span>
                                        @if($data->total_discount == null)
                                            <span class="badge bg-secondary">Null</span>
                                        @else
                                            {{ $data->total_discount }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Total Tax</label>
                                    <br>
                                    <span>
                                        @if($data->total_ppn == null)
                                            <span class="badge bg-secondary">Null</span>
                                        @else
                                            {{ $data->total_ppn }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Total Amount</label>
                                    <br>
                                    <span>
                                        @if($data->total_amount == null)
                                            <span class="badge bg-secondary">Null</span>
                                        @else
                                            {{ $data->total_amount }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Delivery Note Date</label>
                                <br><span>{{ $data->delivery_note_date }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Delivery Note Number</label>
                                <br><span>{{ $data->delivery_note_number }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Invoice Date</label>
                                <br><span>{{ $data->invoice_date }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Invoice Number</label>
                                <br><span>{{ $data->invoice_number }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Tax Invoice Number</label>
                                <br><span>{{ $data->tax_invoice_number }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Quantity</label>
                                <br><span>{{ $data->quantity }}</span>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label class="form-label mb-0">Description</label>
                                <br><span>{{ $data->description }}</span>
                            </div>

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
                                                        <th class="text-center">Account Code</th>
                                                        <th class="text-center">Nominal</th>
                                                        <th class="text-center">Debit / Kredit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($general_ledgers as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->account_code." - ".$item->account_name }}
                                                        </td>
                                                        <td class="text-end">
                                                            @php
                                                                $formatted = number_format($item->amount, 3, ',', '.');
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
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection