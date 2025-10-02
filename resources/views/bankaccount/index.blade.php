@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">

        @include('layouts.alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#add-new"><i class="mdi mdi-update label-icon"></i> Update Bank Account</button>
                                {{-- Modal Add --}}
                                <div class="modal fade" id="add-new" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-top" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">Update Bank Account</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('bankaccount.store') }}" id="formadd" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body" style="max-height: 65vh; overflow:auto">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Bank Name</label><label style="color: darkred">*</label>
                                                                <input type="text" class="form-control" name="bank_name" value="{{ $data->bank_name ?? '' }}"  placeholder="Input Bank Name.." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Account Name</label><label style="color: darkred">*</label>
                                                                <input type="text" class="form-control" name="account_name" value="{{ $data->account_name ?? '' }}"  placeholder="Input Account Name.." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Account Number</label><label style="color: darkred">*</label>
                                                                <input type="text" class="form-control" name="account_number" value="{{ $data->account_number ?? '' }}"  placeholder="Input Account Number.." required>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $selectedCurrencyName = $data->currency ?? '';
                                                        @endphp
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Currency</label><label style="color: darkred">*</label>
                                                                <select class="form-control" name="currency" required>
                                                                    <option value="">- Choose -</option>
                                                                    @foreach($currencies as $item)
                                                                        <option value="{{ $item->currency_code }}" {{ $selectedCurrencyName == $item->currency_code ? 'selected' : '' }}>{{ $item->currency_code }} - {{ $item->currency }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Swift Code</label><label style="color: darkred">*</label>
                                                                <input type="text" class="form-control" name="swift_code" value="{{ $data->swift_code ?? '' }}"  placeholder="Input Swift Code.." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label">Branch</label><label style="color: darkred">*</label>
                                                                <input type="text" class="form-control" name="branch" value="{{ $data->branch ?? '' }}"  placeholder="Input Branch.." required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success waves-effect btn-label waves-light" name="sb"><i class="mdi mdi-update label-icon"></i>Update</button>
                                                </div>
                                            </form>
                                            <script>
                                                document.getElementById('formadd').addEventListener('submit', function(event) {
                                                    if (!this.checkValidity()) {
                                                        event.preventDefault(); // Prevent form submission if it's not valid
                                                        return false;
                                                    }
                                                    var submitButton = this.querySelector('button[name="sb"]');
                                                    submitButton.disabled = true;
                                                    submitButton.innerHTML  = '<i class="mdi mdi-reload label-icon"></i>Please Wait...';
                                                    return true; // Allow form submission
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <h5 class="fw-bold">Master Bank Account</h5>
                                </div>
                            </div>
                            <div class="col-lg-4"></div>
                            <div class="col-lg-12"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Bank Name</th>
                                    <th class="align-middle text-center">Account Name</th>
                                    <th class="align-middle text-center">Account Number</th>
                                    <th class="align-middle text-center">Swift Code</th>
                                    <th class="align-middle text-center">Branch</th>
                                    <th class="align-middle text-center">Status</th>
                                    <th class="align-middle text-center">Created By</th>
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
                url: '{!! route('bankaccount.index') !!}',
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
                    searchable: true,
                    render: function(data, type, row) {
                        return row.account_number + ' (' + row.currency + ')';
                    },
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
                    name: 'is_active',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var html
                        if(row.is_active == 1){
                            html = '<span class="badge bg-success text-white">Active</span>';
                        } else {
                            html = '<span class="badge bg-danger text-white">Non-Active</span>';
                        } 
                        return html;
                    },
                },
                {
                    data: 'created_by',
                    searchable: true,
                    orderable: true,
                    render: function(data, type, row) {
                        var created_at = new Date(row.created_at);
                        return row.created_by + '<br><b>At. </b>' + created_at.toLocaleDateString('es-CL').replace(/\//g, '-');
                    },
                },
            ]
        });
    });
</script>

@endsection