@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <a href="{{ route('transsales.local.index') }}" class="btn btn-light waves-effect btn-label waves-light">
                            <i class="mdi mdi-arrow-left label-icon"></i> Back To List Sales Transaction (Local)
                        </a>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Accounting</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transsales.local.index') }}">Sales (Local)</a></li>
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
                            <div class="col-lg-3 mb-3">
                                <label class="form-label mb-0">Ref Number</label>
                                <br><span class="badge bg-info">{{ $data->ref_number }}</span>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label mb-0">Invoice Date</label>
                                <br><span>{{ $data->date_invoice ? \Carbon\Carbon::parse($data->date_invoice)->format('d-m-Y') : '-' }}</span>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label mb-0">Transaction Date</label>
                                <br><span>{{ $data->date_transaction ? \Carbon\Carbon::parse($data->date_transaction)->format('d-m-Y') : '-' }}</span>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label class="form-label mb-0">Due Date</label>
                                <br><span>{{ $data->due_date ? \Carbon\Carbon::parse($data->due_date)->format('d-m-Y') : '-' }}</span>
                            </div>                            
                            <hr>
                        </div>
                        <div class="card p-2" style="background-color:#f0f2f7">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Delivery Note</label>
                                    <br><span>{{ $data->dn_number }}</span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Customer Name</label>
                                    <br>
                                    <span>
                                        @if($data->customer_name == null)
                                        -
                                        @else
                                            {{ $data->customer_name }}
                                        @endif
                                    </span>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label mb-0">Sales Name</label>
                                    <br>
                                    <span>
                                        @if($data->salesman_name == null)
                                        -
                                        @else
                                            {{ $data->salesman_name }}
                                        @endif
                                    </span>
                                </div>

                                <div class="col-12">
                                    <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                                        <thead>
                                            <tr>
                                                <th class="align-middle text-center">No.</th>
                                                <th class="align-middle text-center">SO Number</th>
                                                <th class="align-middle text-center">Product</th>
                                                <th class="align-middle text-center">Qty</th>
                                                <th class="align-middle text-center">Unit</th>
                                                <th class="align-middle text-center">Price</th>
                                                <th class="align-middle text-center">Total Price</th>
                                            </tr>
                                        </thead>
                                    </table>
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
    $(function() {
        var data = {
            id_delivery_notes: '{!! $data->id_delivery_notes !!}'
        };
        var dataTable = $('#server-side-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('transsales.getsalesorder') !!}',
                type: 'GET',
                data: function(d) {
                    d.id_delivery_notes = data.id_delivery_notes;
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
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.so_number == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = '<span class="text-bold">' + row.so_number + '</span>';
                        }
                        return html;
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
                        var html;
                        if (row.qty == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.qty;
                        }
                        return html;
                    },
                },
                {
                    data: 'unit',
                    name: 'unit',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.unit == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.unit;
                        }
                        return html;
                    },
                },
                {
                    data: 'price',
                    name: 'price',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.price == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.price;
                        }
                        return html;
                    },
                },
                {
                    data: 'total_price',
                    name: 'total_price',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html;
                        if (row.total_price == null) {
                            html = '<span class="badge bg-secondary">Null</span>';
                        } else {
                            html = row.total_price;
                        }
                        return html;
                    },
                },
            ]
        });
    });
</script>

@endsection