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
                    <form class="formLoad" action="{{ route('accounttype.index') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body md-body-scroll">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Code</label>
                                    <input class="form-control" name="account_type_code" type="text" value="{{ $account_type_code }}" placeholder="Filter Code..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Account Type Name</label>
                                    <input class="form-control" name="account_type_name" type="text" value="{{ $account_type_name }}" placeholder="Filter Account Type..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-control select2" name="status">
                                        <option value="" selected>All</option>
                                        <option value="1" @if($status == '1') selected @endif>Active</option>
                                        <option value="0" @if($status == '0') selected @endif>Not Active</option>
                                    </select>
                                </div>
                                <hr class="mt-2">
                                <div class="col-4 mb-2">
                                    <label class="form-label">Filter Date</label>
                                    <select class="form-control select2" name="searchDate" id="filterDate">
                                        <option value="All" @if($searchDate == 'All') selected @endif>All</option>
                                        <option value="Custom" @if($searchDate == 'Custom') selected @endif>Custom Date</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="startdate" id="filterStartDate" class="form-control" placeholder="from" value="{{ $startdate }}">
                                </div>
                                <div class="col-4 mb-2">
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
                                <button type="button" class="btn btn-primary waves-effect btn-label waves-light openAjaxModal"
                                    data-id="addNew" data-size="md" data-url="{{ route('accounttype.modal.new') }}">
                                    <i class="mdi mdi-plus-box label-icon"></i> Add New
                                </button>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <!-- Title -->
                                    <h5 class="fw-bold">Manage Account Type</h5>
                                    <!-- Filter Info -->
                                    <small class="text-muted mt-0">
                                        List of 
                                        @if($account_type_code != null)
                                            (Code<b> - {{ $account_type_code }}</b>)
                                        @endif
                                        @if($account_type_name != null)
                                            (Account Type<b> - {{ $account_type_name }}</b>)
                                        @endif
                                        @if($status != null)
                                            (Status<b> - {{ $status }}</b>)
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
                                    <th class="align-middle text-center">Code</th>
                                    <th class="align-middle text-center">Account Type</th>
                                    <th class="align-middle text-center">Status</th>
                                    <th class="align-middle text-center">Created At</th>
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
    var url = '{{ route('accounttype.index') }}';
    var data = {
        account_type_code: '{{ $account_type_code }}',
        account_type_name: '{{ $account_type_name }}',
        status: '{{ $status }}',
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
            data: 'account_type_code',
            name: 'account_type_code',
            orderable: true,
            searchable: true,
        },
        {
            data: 'account_type_name',
            name: 'account_type_name',
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
            data: 'created_at',
            searchable: true,
            orderable: true,
            className: 'text-center',
            render: (data, type, row) => fmtTimeDT(data),
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
            customDrawCallback: null
        });
        
        $(document).on('click', '#btnExport', function () {
            var currentDate = new Date();
            var formattedDate = currentDate.toISOString().split('T')[0];
            var fileName = "Master Account Type Export - " + formattedDate + ".xlsx";
            var requestData = Object.assign({}, data);
            requestData.flag = 1;
            handleExport(url, requestData, fileName);
        });
    });
</script>

@endsection