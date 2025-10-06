@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transimport.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Import Transaction
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transimport.index') }}">Import Transaction</a></li>
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
                            <div class="col-lg-12 mb-3">
                                <label class="form-label mb-0">Ref. Number</label>
                                <br><h4><span class="badge bg-info">{{ $data->ref_number }}</span></h4>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Transaction Date</label>
                                <br><span>{{ $transaction_date }}</span>
                            </div>
                            <hr>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Tax Invoice Number</label>
                                <br><span>{{ $data->tax_invoice_number }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">External Doc. Number</label>
                                <br><span>{{ $data->ext_doc_number }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Invoice Received Date</label>
                                <br><span>{{ $data->inv_received_date }}</span>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label mb-0">Due Date</label>
                                <br><span>{{ $data->due_date }}</span>
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