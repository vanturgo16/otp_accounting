@extends('layouts.master')
@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <!-- Modal Search -->
        <div class="modal fade" id="sort" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel"><i class="mdi mdi-filter label-icon"></i> Search & Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="formLoad" action="{{ route('transsales.local.index') }}" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body md-body-scroll">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Ref. Number</label>
                                    <input class="form-control" name="ref_number" type="text" value="{{ $ref_number }}" placeholder="Filter Ref. Number..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Delivery Note</label>
                                    <input class="form-control" name="dn_number" type="text" value="{{ $dn_number }}" placeholder="Filter DN Number..">
                                </div>
                            </div>
                            <div class="row">
                                <hr class="mt-2">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Filter Date</label>
                                    <select class="form-control select2" name="searchDate" id="filterDate">
                                        <option value="All" @if($searchDate == 'All') selected @endif>All</option>
                                        <option value="Custom" @if($searchDate == 'Custom') selected @endif>Custom Date</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="startdate" id="filterStartDate" class="form-control" placeholder="from" value="{{ $startdate }}">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Date To</label>
                                    <input type="Date" name="enddate" id="filterEndDate" class="form-control" placeholder="to" value="{{ $enddate }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info waves-effect btn-label waves-light">
                                <i class="mdi mdi-filter label-icon"></i> Filter
                            </button>
                        </div>
                    </form>
                    <script>
                        const dateSelect = $('#filterDate');
                        const dateFrom   = $('#filterStartDate');
                        const dateTo     = $('#filterEndDate');
                        function toggleDateFields(value) {
                            const isAll = value === 'All';
                            dateFrom.prop({ required: !isAll, readonly: isAll }).val(isAll ? null : dateFrom.val());
                            dateTo.prop({ required: !isAll, readonly: isAll }).val(isAll ? null : dateTo.val());
                        }
                        // On change
                        dateSelect.on('change', function() {
                            toggleDateFields($(this).val());
                        });
                        // On load
                        toggleDateFields(dateSelect.val());
                    </script>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <a href="{{ route('transsales.local.create') }}" type="button" class="btn btn-primary waves-effect btn-label waves-light">
                                    <i class="mdi mdi-plus-box label-icon"></i> Add New
                                </a>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <!-- Title -->
                                    <h5 class="fw-bold">Sales Transaction (Local)</h5>
                                    <!-- Filter Info -->
                                    <small class="text-muted mt-0">
                                        List of 
                                        @if($ref_number)
                                            (Ref. Number<b> - {{ $ref_number }}</b>)
                                        @endif
                                        @if($dn_number)
                                            (DN. Number<b> - {{ $dn_number }}</b>)
                                        @endif
                                        @if($searchDate == 'Custom')
                                            (Date From<b> {{ $startdate }} </b>Until <b>{{ $enddate }}</b>)
                                        @else
                                            (<b>All Date</b>)
                                        @endif 
                                    </small>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="alert alert-warning mb-0 small d-none d-lg-block" role="alert">
                                    <ul class="mb-0 ps-3">
                                        <li>
                                            <b>Edit</b> & <b>Delete</b> actions are only available for <b>Super Admin</b> and only for transactions within the <b>current month.</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="text-end d-block d-lg-none">
                                    <i class="mdi mdi-information-outline text-muted" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit and Delete actions are only available for Super Admin and only for transactions within the current month.">
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered small w-100" id="ssTable">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">No.</th>
                                    <th rowspan="2" class="align-middle text-center">Ref. Number</th>
                                    <th colspan="2" class="align-middle text-center">Date</th>
                                    <th rowspan="2" class="align-middle text-center">DN Number (Customer)</th>
                                    <th rowspan="2" class="align-middle text-center">Transaction Count</th>
                                    <th rowspan="2" class="align-middle text-center">Total Amount</th>
                                    <th rowspan="2" class="align-middle text-center">Created By</th>
                                    <th rowspan="2" class="align-middle text-center">Action</th>
                                </tr>
                                <tr>
                                    <th class="align-middle text-center">Invoice</th>
                                    <th class="align-middle text-center">Due Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var url = '{{ route('transsales.local.index') }}';
    var data = {
        ref_number: '{{ $ref_number }}',
        dn_number: '{{ $dn_number }}',
        searchDate: '{{ $searchDate }}',
        startdate: '{{ $startdate }}',
        enddate: '{{ $enddate }}'
    };
    var columns = [
        {
            data: null,
            orderable: false,
            searchable: false,
            className: 'text-center fw-bold',
            render: function(data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
        },
        {
            data: 'ref_number',
            name: 'ref_number',
            orderable: true,
            searchable: true,
            className: 'text-bold'
        },
        {
            data: 'date_invoice',
            searchable: true,
            orderable: true,
            className: 'text-center',
            render: function(data, type, row) {
                var date_invoice = new Date(data);
                return date_invoice.toLocaleDateString('es-CL').replace(/\//g, '-');
            },
        },
        {
            data: 'due_date',
            searchable: true,
            orderable: true,
            className: 'text-center',
            render: function(data, type, row) {
                var date = new Date(data);
                return date.toLocaleDateString('es-CL').replace(/\//g, '-');
            },
        },
        {
            data: 'dn_number',
            name: 'dn_number',
            orderable: true,
            searchable: true,
            render: function(data, type, row) {
                let dn = data ?? '';
                let customer = row.customer_name ? `<br>${row.customer_name}` : '';
                return `<b>${dn}</b>${customer}`;
            },
        },
        {
            data: 'count',
            name: 'count',
            orderable: true,
            searchable: true,
            className: 'text-center',
        },
        {
            data: 'total',
            name: 'total',
            orderable: true,
            searchable: true,
            className: 'text-end',
            render: function(data, type, row) {
                if (data == null) {
                    return '<span class="badge bg-secondary">Null</span>';
                }
                var formattedAmount = numberFormat(data, 2, ',', '.'); 
                var parts = formattedAmount.split(',');
                if (parts.length > 1) {
                    return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span>';
                }
                return '<span class="text-bold">' + parts[0] + '</span>';
            },
        },
        {
            data: 'created_by',
            searchable: true,
            orderable: true,
            render: (data, type, row) => fmtActionBy(data, row.created_at),
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'text-center',
        },
        {
            data: 'customer_name',
            name: 'customer_name',
            searchable: true,
            visible: false
        },
    ];

    $(function() {
        initDTUI({
            idTable: "#ssTable",
            columns: columns,
            showExport: true,
            showFilter: true,
            url: url,
            requestData: data,
        });
        
        $(document).on('click', '#btnExport', function () {
            var currentDate = new Date();
            var formattedDate = currentDate.toISOString().split('T')[0];
            var fileName = "Sales (Local) Export - " + formattedDate + ".xlsx";
            var requestData = Object.assign({}, data);
            requestData.flag = 1;
            handleExport(url, requestData, fileName);
        });
    });
</script>

@endsection