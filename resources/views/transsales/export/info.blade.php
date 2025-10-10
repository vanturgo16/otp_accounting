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
                            <li class="breadcrumb-item active">Info</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h5>Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label class="form-label mb-0">Ref. Number</label>
                                <br><h4><span class="badge bg-info">{{ $detail->ref_number }}</span></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label class="form-label mb-0">Created By</label>
                                <br><span>{{ $detail->created_by ?? '-' }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label class="form-label mb-0">Invoice Date</label>
                                <br><span>{{ $detail->date_invoice ? \Carbon\Carbon::parse($detail->date_invoice)->format('d-m-Y') : '-' }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <label class="form-label mb-0">Term</label>
                                    </div>
                                    <div class="card-body">
                                        <span>{!! $detail->term !!}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <label class="form-label mb-0">Additional Info</label>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <table style="width: 100%; border-collapse: collapse;" cellspacing="1">
                                                    <tbody style="font-size: 10px;">
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Bank Name</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $bankAccount['bank_name'] ?? '-' }}</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Account Name</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $bankAccount['account_name'] ?? '' }}</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Account Number</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $bankAccount['account_number'] ?? '-' }} ({{ $bankAccount['currency'] ?? '-' }})</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Swift Code</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $bankAccount['swift_code'] ?? '-' }}</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Branch</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $bankAccount['branch'] ?? '-' }}</label></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-6">
                                                <table style="width: 100%; border-collapse: collapse;" cellspacing="1">
                                                    <tbody style="font-size: 10px;">
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Approval Name</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $approvalDetail['name'] ?? '-' }}</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Approval Email</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $approvalDetail['email'] ?? '' }}</label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label class="form-label font-weight-bold">Approval Position</label></td>
                                                            <td><label class="form-label">:</label></td>
                                                            <td><label class="form-label">{{ $approvalDetail['position'] ?? '-' }}</label></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h5>Delivery Note Detail</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label mb-0">Delivery Note</label>
                                        <br><span>{{ $detail->dn_number }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label mb-0">DN Date</label>
                                        <br><span>{{ $detail->dn_date ?? '-' }}</span>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label mb-0">KO Number</label>
                                        <br><span>{{ $detail->ko_number ?? '-' }}</span>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label mb-0">PO Number</label>
                                        <br><span>{{ $detail->po_number ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label mb-0">Customer Name</label>
                                        <br><span>{{ $detailCust->customer_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label mb-0">Sales Name</label>
                                        <br><span>{{ $detailCust->salesman_name ?? '-' }}</span>
                                    </div>
                                    <div class="col-lg-4 mb-3"></div>

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
                                            <tbody>
                                                @foreach($detailTransSales as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="fw-bold">{{ $item->so_number }}</td>
                                                        <td>
                                                            {{ $item->product }}
                                                            <br><b>({{ $item->type_product }})</b>
                                                        </td>
                                                        <td class="text-center">
                                                            {{ fmod($item->qty, 1) == 0 
                                                                ? number_format($item->qty, 0, ',', '.') 
                                                                : number_format(floor($item->qty), 0, ',', '.') . ',' . rtrim(str_replace('.', '', explode('.', (string)$item->qty)[1]), '0') }} 
                                                            ({{ $item->unit }})
                                                        </td>
                                                        <td class="text-center">{{ $item->ppn_type }}</td>
                                                        <td class="text-end">
                                                            @php
                                                                $formatted = number_format($item->price_before_ppn, 3, ',', '.');
                                                                [$before, $after] = explode(',', $formatted);
                                                            @endphp
                                                            <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            @php
                                                                $formatted = number_format($item->ppn_value, 3, ',', '.');
                                                                [$before, $after] = explode(',', $formatted);
                                                            @endphp
                                                            <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                                            <br>({{ $item->ppn_rate }}%)
                                                        </td>
                                                        <td class="text-end">
                                                            @php
                                                                $formatted = number_format($item->price_after_ppn, 3, ',', '.');
                                                                [$before, $after] = explode(',', $formatted);
                                                            @endphp
                                                            <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            @php
                                                                $formatted = number_format($item->total_price_before_ppn, 3, ',', '.');
                                                                [$before, $after] = explode(',', $formatted);
                                                            @endphp
                                                            <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            @php
                                                                $formatted = number_format($item->total_price_after_ppn, 3, ',', '.');
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
                                                        <label class="form-label"><span class="text-muted">{{ $detail->currency }} </span>
                                                            @php
                                                                $formatted = number_format($detail->sales_value, 3, ',', '.');
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
                                                        <label class="form-label"><span class="text-muted">{{ $detail->currency }} </span>
                                                            @php
                                                                $formatted = number_format($detail->ppn_value, 3, ',', '.');
                                                                [$before, $after] = explode(',', $formatted);
                                                            @endphp
                                                            <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                                                        </label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-end">
                                                        <label class="form-label fw-bold">Total Nilai Jual + PPN :</label>
                                                    </td>
                                                    <td class="text-end">
                                                        <label class="form-label"><span class="text-muted">{{ $detail->currency }} </span>
                                                            @php
                                                                $formatted = number_format($detail->total, 3, ',', '.');
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

@endsection