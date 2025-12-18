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
                    <form class="formLoad" action="{{ route('cashbook.index') }}" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body md-body-scroll">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Trans Number</label>
                                    <input class="form-control" name="trans_number" type="text" value="{{ $trans_number }}" placeholder="Filter Trans Number..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Type</label>
                                    <select class="form-control select2" name="type">
                                        <option value="" selected>All</option>
                                        @foreach($typeManuals as $item)
                                            <option value="{{ $item }}" @if($type == $item) selected @endif>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Invoice Number</label>
                                    <input class="form-control" name="invoice_number" type="text" value="{{ $invoice_number }}" placeholder="Filter Invoice Number..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Tax Invoice Number / No. Faktur</label>
                                    <input class="form-control" name="tax_invoice_number" type="text" value="{{ $tax_invoice_number }}" placeholder="Filter Tax Invoice Number..">
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
                                <a href="{{ route('cashbook.create') }}" type="button" class="btn btn-primary waves-effect btn-label waves-light">
                                    <i class="mdi mdi-plus-box label-icon"></i> Add New
                                </a>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <!-- Title -->
                                    <h5 class="fw-bold">Cash Book Transaction</h5>
                                    <!-- Filter Info -->
                                    <small class="text-muted mt-0">
                                        List of 
                                        @if($trans_number)
                                            (Trans Number<b> - {{ $trans_number }}</b>)
                                        @endif
                                        @if($type)
                                            (Type<b> - {{ $type }}</b>)
                                        @endif
                                        @if($invoice_number)
                                            (No. Faktur<b> - {{ $invoice_number }}</b>)
                                        @endif
                                        @if($tax_invoice_number)
                                            (Tax Inv Number<b> - {{ $tax_invoice_number }}</b>)
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
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Trans. Number</th>
                                    <th class="align-middle text-center">Invoice Date</th>
                                    <th class="align-middle text-center">Account Code</th>
                                    <th class="align-middle text-center">Account Name</th>
                                    <th class="align-middle text-center">Note</th>
                                    <th class="align-middle text-center">Category</th>
                                    <th class="align-middle text-center">Currency</th>
                                    <th class="align-middle text-center">Amount</th>
                                    <th class="align-middle text-center">Debit / Kredit</th>
                                    <th class="align-middle text-center">Created By</th>
                                    <th class="align-middle text-center">Action</th>
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
    var url = '{{ route('cashbook.index') }}';
    var data = {
        trans_number: '{{ $trans_number }}',
        type: '{{ $type }}',
        invoice_number: '{{ $invoice_number }}',
        tax_invoice_number: '{{ $tax_invoice_number }}',
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
            data: 'transaction_number',
            name: 'transaction_number',
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
            data: 'account_code',
            name: 'account_code',
            orderable: true,
            searchable: true,
            className: 'align-top text-center'
        },
        {
            data: 'account_name',
            name: 'account_name',
            orderable: true,
            searchable: true,
            className: 'align-top'
        },
        {
            data: 'note',
            name: 'note',
            orderable: true,
            searchable: true
        },
        {
            data: 'category',
            name: 'category',
            orderable: true,
            searchable: true,
            className: 'text-center'
        },
        {
            data: 'currency',
            name: 'currency',
            orderable: true,
            searchable: true,
            className: 'text-center'
        },
        {
            data: 'amount',
            orderable: true,
            className: 'align-top text-end',
            render: (data, type, row) => formatAmountDT(data),
        },
        {
            data: 'transaction',
            orderable: true,
            className: 'align-top text-center',
            render: (data, type, row) =>
                data === 'D'
                    ? badgeDT('success', 'Debit', 'mdi-plus-circle')
                    : badgeDT('danger', 'Kredit', 'mdi-minus-circle'),
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
    ];

    $(function() {
        initDTUI({
            idTable: "#ssTable",
            columns: columns,
            showExport: true,
            showFilter: true,
            url: url,
            requestData: data,
            customDrawCallback: function(api, settings) {
                mergeByColumn(api, [1, 2, 6, 7, 10, 11], "id", "transaction_number");
            },
        });

        function mergeByColumn(api, columnIndexes, idField, refField) {
            var rows = api.rows({ page: 'current' }).indexes().toArray();
            var lastGroup = null;
            var rowspan = 1;

            rows.forEach(function (rowIdx, i) {
                var rowData = api.row(rowIdx).data();
                var groupKey = rowData[idField] + "|" + rowData[refField];

                if (groupKey === lastGroup) {
                    rowspan++;

                    columnIndexes.forEach(function (colIdx) {
                        var cell = api.cell(rowIdx, colIdx).node();
                        $(cell).remove();
                    });

                } else {
                    if (lastGroup !== null) {
                        var startRowIdx = rows[i - rowspan];

                        columnIndexes.forEach(function (colIdx) {
                            var cell = api.cell(startRowIdx, colIdx).node();
                            $(cell).attr('rowspan', rowspan);
                        });
                    }

                    lastGroup = groupKey;
                    rowspan = 1;
                }
            });

            // apply rowspan for last group
            if (lastGroup !== null) {
                var startRowIdx = rows[rows.length - rowspan];

                columnIndexes.forEach(function (colIdx) {
                    var cell = api.cell(startRowIdx, colIdx).node();
                    $(cell).attr('rowspan', rowspan);
                });
            }
        }
        
        $(document).on('click', '#btnExport', function () {
            var currentDate = new Date();
            var formattedDate = currentDate.toISOString().split('T')[0];
            var fileName = "Cash Book Export - " + formattedDate + ".xlsx";
            var requestData = Object.assign({}, data);
            requestData.flag = 1;
            handleExport(url, requestData, fileName);
        });
    });
</script>

@endsection