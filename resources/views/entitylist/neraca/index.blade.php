@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left"></div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                            <li class="breadcrumb-item active">Neraca Entity List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center py-3">
                        <h5 class="mb-0"><b>Neraca Entity List</b></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Head 1</th>
                                    <th class="align-middle text-center">Head 2</th>
                                    <th class="align-middle text-center">Account Name</th>
                                    <th class="align-middle text-center">Account Sub Name</th>
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
    $(function() {
        var dataTable = $('#server-side-table').DataTable({
            language: {
                processing: '<div id="custom-loader" class="dataTables_processing"></div>'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('entitylist.neraca') !!}',
                type: 'GET',
            },
            columns: [
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
                    data: 'head1',
                    name: 'head1',
                    orderable: true,
                    searchable: true,
                    className: 'text-bold'
                },
                {
                    data: 'head2',
                    name: 'head2',
                    orderable: true,
                    searchable: true,
                    className: 'text-bold'
                },
                {
                    data: 'account',
                    name: 'account',
                    orderable: true,
                    searchable: true,
                },
                {
                    data: 'account_name',
                    name: 'account_name',
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        var html
                        if(row.account_name == null){
                            html = '<span class="badge bg-secondary text-white">Null</span>';
                        } else {
                            html = row.account_name;
                        } 
                        return html;
                    },
                },
            ]
        });
    });
</script>

@endsection