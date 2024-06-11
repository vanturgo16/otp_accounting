@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transsales.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Sales Transaction
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transsales.index') }}">Sales Transaction</a></li>
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
                            <div class="col-lg-3 mb-3">
                                <label class="form-label mb-0">Transaction Date</label>
                                <br><span>{{ $transaction_date }}</span>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label mb-0">No. Delivery Note</label>
                                <br><span>
                                    @if($data->no_delivery_note == null)
                                        <span class="badge bg-secondary">Null</span>
                                    @else
                                        {{ $data->no_delivery_note }}
                                    @endif
                                </span>
                            </div>
                            <hr>
                        </div>
                        <div class="card p-2" style="background-color:#f0f2f7">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Sales Order Number</label>
                                    <br><span>{{ $data->so_number }}</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Customer Name</label>
                                    <br><span>{{ $data->customer_name }}</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Customer Address</label>
                                    <br><span>{{ $data->customer_address }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Date</label>
                                    <br><span>{{ $data->date }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Due Date</label>
                                    <br><span>{{ $data->due_date }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">PPN</label>
                                    <br><span>{{ $data->ppn }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Status</label>
                                    <br><span>{{ $data->status }}</span>
                                </div>
    
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">SO Type</label>
                                    <br><span>{{ $data->so_type }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">SO Category</label>
                                    <br><span>{{ $data->so_category }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Product</label>
                                    <br><span>{{ $data->product }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Type Product</label>
                                    <br><span>{{ $data->type_product }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Quantity</label>
                                    <br><span>{{ $data->qty }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Unit</label>
                                    <br><span>{{ $data->unit }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Price</label>
                                    <br><span>{{ $data->price }}</span>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label mb-0">Total Price</label>
                                    <br><span>{{ $data->total_price }}</span>
                                </div>    
                            </div>
                        </div>

                        <div class="row">
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