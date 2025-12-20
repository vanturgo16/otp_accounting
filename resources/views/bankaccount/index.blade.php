@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <button type="button" class="btn btn-primary waves-effect btn-label waves-light openAjaxModal"
                                    data-id="addNew" data-size="lg" data-url="{{ route('bankaccount.modal.new') }}">
                                    <i class="mdi mdi-plus-box label-icon"></i> Add New Bank Account
                                </button>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <h5 class="fw-bold">Master Bank Account</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered small w-100" id="ssTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Code</th>
                                    <th class="align-middle text-center">Bank Name</th>
                                    <th class="align-middle text-center">Account Name</th>
                                    <th class="align-middle text-center">Account Number</th>
                                    <th class="align-middle text-center">Currency</th>
                                    <th class="align-middle text-center">Swift Code</th>
                                    <th class="align-middle text-center">Branch</th>
                                    <th class="align-middle text-center">Status</th>
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
    var url = '{{ route('bankaccount.index') }}';
    var data = {};
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
            data: 'code',
            name: 'code',
            orderable: true,
            searchable: true
        },
        {
            data: 'bank_name',
            name: 'bank_name',
            orderable: true,
            searchable: true,
            className: 'text-bold'
        },
        {
            data: 'account_name',
            name: 'account_name',
            orderable: true,
            searchable: true,
        },
        {
            data: 'account_number',
            name: 'account_number',
            orderable: true,
            searchable: true
        },
        {
            data: 'currency',
            name: 'currency',
            orderable: true,
            searchable: true,
        },
        {
            data: 'swift_code',
            name: 'swift_code',
            orderable: true,
            searchable: true,
        },
        {
            data: 'branch',
            name: 'branch',
            orderable: true,
            searchable: true,
        },
        {
            data: 'is_active',
            orderable: true,
            className: 'text-center',
            render: (data, type, row) =>
                data == 1
                    ? badgeDT('success', 'Active')
                    : badgeDT('danger', 'Inactive'),
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
            showExport: false,
            showFilter: false,
            url: url,
            requestData: data,
            customDrawCallback: null
        });
    });
</script>

@endsection