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
                    <form class="formLoad" action="{{ route('generalledger.index') }}" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body md-body-scroll">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Ref. Number</label>
                                    <input class="form-control" name="ref_number" type="text" value="{{ $ref_number }}" placeholder="Filter Ref. Number..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Account Code</label>
                                    <select class="form-select select2" name="id_account_code">
                                        <option value="" selected>-- Select --</option>
                                        @foreach($acccodes as $item)
                                            <option value="{{ $item->id }}" @if($id_account_code == $item->id) selected="selected" @endif>{{ $item->account_code }} - {{ $item->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Source</label>
                                    <select class="form-select select2" name="source">
                                        <option value="" selected>-- Select --</option>
                                        <option value="Sales (Local)"  @if($source == "Sales (Local)") selected="selected" @endif>Sales (Local)</option>
                                        <option value="Sales (Export)"  @if($source == "Sales (Export)") selected="selected" @endif>Sales (Export)</option>
                                        <option value="Purchase Transaction"  @if($source == "Purchase Transaction") selected="selected" @endif>Purchase Transaction</option>
                                        <option value="Cash Book"  @if($source == "Cash Book") selected="selected" @endif>Cash Book</option>
                                    </select>
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
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <!-- Title -->
                                    <h5 class="fw-bold">General Ledgers</h5>
                                    <!-- Filter Info -->
                                    <small class="text-muted mt-0">
                                        List of 
                                        @if($ref_number != null)
                                            (Ref. Number<b> - {{ $ref_number }}</b>)
                                        @endif
                                        @if($id_account_code != null)
                                            (Account Code<b> - {{ $id_account_code }}</b>)
                                        @endif
                                        @if($source != null)
                                            (Source<b> - {{ $source }}</b>)
                                        @endif
                                        @if($searchDate == 'Custom')
                                            (Date From<b> {{ $startdate }} </b>Until <b>{{ $enddate }}</b>)
                                        @else
                                            (<b>All Date</b>)
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered small w-100" id="ssTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Ref. Number</th>
                                    <th class="align-middle text-center">Kategory</th>
                                    <th class="align-middle text-center">Transaction Date</th>
                                    <th class="align-middle text-center">Account Code</th>
                                    <th class="align-middle text-center">Nominal</th>
                                    <th class="align-middle text-center">Debit / Kredit</th>
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
    var url = '{{ route('generalledger.index') }}';
    var data = {
        ref_number: '{{ $ref_number }}',
        id_account_code: '{{ $id_account_code }}',
        source: '{{ $source }}',
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
            className: 'align-top',
            render: function(data, type, row) {
                return '<b>' + data + '</b><br><small>source: <b>' + row.source + '</b></small>';
            },
        },
        {
            data: 'source',
            name: 'source',
            orderable: true,
            searchable: true,
            className: 'align-top',
        },
        {
            data: 'date_transaction',
            searchable: true,
            orderable: true,
            className: 'align-top text-center',
            render: function(data, type, row) {
                var date_transaction = new Date(row.date_transaction);
                return date_transaction.toLocaleDateString('es-CL').replace(/\//g, '-');
            },
        },
        {
            data: 'account_name',
            searchable: true,
            orderable: true,
            render: function(data, type, row) {
                return row.account_code + ' - <b>' + row.account_name + '</b>';
            },
        },
        {
            data: 'amount',
            orderable: true,
            className: 'align-top text-end',
            render: function(data, type, row) {
                var formattedAmount = numberFormat(row.amount, 3, ',', '.'); 
                var parts = formattedAmount.split(',');
                if (parts.length > 1) {
                    return '<span class="text-bold">' + parts[0] + '</span><span class="text-muted">,' + parts[1] + '</span>';
                }
                return '<span class="text-bold">' + parts[0] + '</span>';
            },
        },
        {
            data: 'transaction',
            orderable: true,
            className: 'align-top text-center',
            render: function(data, type, row) {
                var html
                if(row.transaction == 'D'){
                    html = '<span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>';
                } else {
                    html = '<span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>';
                } 
                return html;
            },
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            className: 'text-center',
        },
        {
            data: 'ref_source',
            name: 'ref_source',
            orderable: false,
            searchable: true,
            visible: false,
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
                mergeByColumn(api, [1, 2, 3, 7], "id_ref", "ref_number");
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
            var fileName = "Master Account Code Export - " + formattedDate + ".xlsx";
            var requestData = Object.assign({}, data);
            requestData.flag = 1;
            handleExport(url, requestData, fileName);
        });
    });
</script>

@endsection