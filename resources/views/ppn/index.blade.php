@extends('layouts.master')
@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <h5 class="fw-bold">Master Default PPN</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered small w-100" id="ssTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">PPN Name</th>
                                    <th class="align-middle text-center">Default Value</th>
                                    <th class="align-middle text-center">Last Updated By</th>
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
    var url = '{{ route('ppn.index') }}';
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
            data: 'tax_name',
            name: 'tax_name',
            orderable: true,
            searchable: true,
            className: 'fw-bold'
        },
        {
            data: 'value',
            name: 'value',
            orderable: true,
            searchable: true,
            className: 'text-center',
            render: function(data, type, row) {
                return row.value + ' %';
            },
        },
        {
            data: 'updated_by',
            searchable: true,
            orderable: true,
            render: (data, type, row) => fmtActionBy(data, row.updated_at),
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