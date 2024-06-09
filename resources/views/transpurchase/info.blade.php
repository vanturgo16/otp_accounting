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
                                <label class="form-label mb-0">Ref Number</label>
                                <br><span class="badge bg-info">{{ $data->ref_number }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Transaction Date</label>
                                <br><span>{{ $transaction_date }}</span>
                            </div>
                            <hr>
                            {{-- <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Purchase Invoices</label>
                                <br><span></span>
                            </div> --}}

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
                                                        <th>Account Code</th>
                                                        <th>Nominal</th>
                                                        <th>Debit / Kredit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($general_ledgers as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->account_code." - ".$item->account_name }}
                                                        </td>
                                                        <td>
                                                            {{ number_format($item->amount, 3, ',', '.') }}
                                                        </td>
                                                        <td>
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