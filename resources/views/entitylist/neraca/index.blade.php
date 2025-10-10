@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-3">
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <h5 class="fw-bold">Neraca Entity List</h5>
                                </div>
                            </div>
                            <div class="col-lg-4"></div>
                            <div class="col-lg-12"><div class="text-center"></div></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                            <thead class="table-light">
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