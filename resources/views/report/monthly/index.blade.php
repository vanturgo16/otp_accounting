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
                    <form class="formLoad" action="{{ route('report.monthly.index') }}" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body md-body-scroll">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Account Code</label>
                                    <input class="form-control" name="account_code" type="text" value="{{ $account_code }}" placeholder="Filter Code..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Account Name</label>
                                    <input class="form-control" name="account_name" type="text" value="{{ $account_name }}" placeholder="Filter Account Code..">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">DateTime</label> <label class="text-danger">*</label>
                                    <input type="month" class="form-control" name="monthYear" value="{{ $monthYear }}" required>
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
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <!-- Sync Info -->
                                <small class="text-muted d-block">
                                    <i class="mdi mdi-clock-outline"></i>
                                    Auto Sync Daily at <b>{{ $syncTime }}</b>
                                </small>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <!-- Title -->
                                    <h5 class="fw-bold">Monthly Summary</h5>
                                    <!-- Filter Info -->
                                    <small class="text-muted mt-0">
                                        List of 
                                        @if($account_code != null)
                                            (Code<b> - {{ $account_code }}</b>)
                                        @endif
                                        @if($account_name != null)
                                            (Account Name<b> - {{ $account_name }}</b>)
                                        @endif
                                        (PERIOD - {{ \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->translatedFormat('F Y') }})
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered small w-100" id="ssTable">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">No.</th>
                                    <th rowspan="2" class="align-middle text-center">Period</th>
                                    <th rowspan="2" class="align-middle text-center">Account Code</th>
                                    <th rowspan="2" class="align-middle text-center">Account Name</th>
                                    <th colspan="2" class="align-middle text-center">Opening / Start</th>
                                    <th colspan="2" class="align-middle text-center">Closing / End</th>
                                    <th rowspan="2" class="align-middle text-center">Last Updated At</th>
                                </tr>
                                <tr>
                                    <th class="align-middle text-center">Balance</th>
                                    <th class="align-middle text-center">Type</th>
                                    <th class="align-middle text-center">Balance</th>
                                    <th class="align-middle text-center">Type</th>
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
    var url = '{{ route('report.monthly.index') }}';
    var data = {
        account_code: '{{ $account_code }}',
        account_name: '{{ $account_name }}',
        monthYear   : '{{ $monthYear }}',
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
            data: 'period',
            name: 'period',
            orderable: true,
            searchable: true,
            className: 'text-center',
        },
        {
            data: 'account_code',
            name: 'account_code',
            orderable: true,
            searchable: true,
        },
        {
            data: 'account_name',
            name: 'account_name',
            orderable: true,
            searchable: true,
        },
        {
            data: 'opening_balance',
            orderable: true,
            className: 'align-top text-end',
            render: (data, type, row) => formatAmountDT(data),
        },
        {
            data: 'opening_balance_type',
            orderable: true,
            className: 'align-top text-center',
            render: (data, type, row) =>
                data === 'D'
                    ? badgeDT('success', 'Debit', 'mdi-plus-circle')
                    : badgeDT('danger', 'Kredit', 'mdi-minus-circle'),
        },
        {
            data: 'closing_balance',
            orderable: true,
            className: 'align-top text-end',
            render: (data, type, row) => formatAmountDT(data),
        },
        {
            data: 'closing_balance_type',
            orderable: true,
            className: 'align-top text-center',
            render: (data, type, row) =>
                data === 'D'
                    ? badgeDT('success', 'Debit', 'mdi-plus-circle')
                    : badgeDT('danger', 'Kredit', 'mdi-minus-circle'),
        },
        {
            data: 'updated_at',
            searchable: true,
            orderable: true,
            className: 'text-center',
            render: (data, type, row) => fmtTimeDT(data),
        },
    ];

    $(function() {
        initDTUI({
            idTable: "#ssTable",
            columns: columns,
            showExport: false,
            showFilter: true,
            url: url,
            requestData: data,
            customDrawCallback: null
        });
    });
</script>

@endsection