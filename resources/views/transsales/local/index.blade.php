@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">

        @include('layouts.alert')

        <!-- Modal Search -->
        <div class="modal fade" id="sort" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel"><i class="mdi mdi-filter label-icon"></i> Search & Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('transsales.local.index') }}" id="formfilter" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body py-8 px-4" style="max-height: 67vh; overflow-y: auto;">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Ref Number</label>
                                    <input class="form-control" name="ref_number" type="text" value="{{ $ref_number }}" placeholder="Filter Ref Number..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Delivery Note</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_delivery_notes">
                                        <option value="" selected>--Select Type--</option>
                                        @foreach($deliveryNotes as $item)
                                            <option value="{{ $item->id }}" @if($id_delivery_notes == $item->id) selected="selected" @endif>{{ $item->dn_number." - ". $item->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <hr class="mt-2">
                                <div class="col-4 mb-2">
                                    <label class="form-label">Filter Date</label>
                                    <select class="form-control" name="searchDate">
                                        <option value="All" @if($searchDate == 'All') selected @endif>All</option>
                                        <option value="Custom" @if($searchDate == 'Custom') selected @endif>Custom Date</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="startdate" id="search1" class="form-control" placeholder="from" value="{{ $startdate }}">
                                </div>
                                <div class="col-4 mb-2">
                                    <label class="form-label">Date To</label>
                                    <input type="Date" name="enddate" id="search2" class="form-control" placeholder="to" value="{{ $enddate }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info waves-effect btn-label waves-light" name="sbfilter"><i class="mdi mdi-filter label-icon"></i> Filter</button>
                        </div>
                    </form>
                    <script>
                        $('select[name="searchDate"]').on('change', function() {
                            var date = $(this).val();
                            if(date == 'All'){
                                $('#search1').val(null);
                                $('#search2').val(null);
                                $('#search1').attr("required", false);
                                $('#search2').attr("required", false);
                                $('#search1').attr("readonly", true);
                                $('#search2').attr("readonly", true);
                            } else {
                                $('#search1').attr("required", true);
                                $('#search2').attr("required", true);
                                $('#search1').attr("readonly", false);
                                $('#search2').attr("readonly", false);
                            }
                        });
                        var searchDate = $('select[name="searchDate"]').val();
                        if(searchDate == 'All'){
                            $('#search1').attr("required", false);
                            $('#search2').attr("required", false);
                            $('#search1').attr("readonly", true);
                            $('#search2').attr("readonly", true);
                        }

                        document.getElementById('formfilter').addEventListener('submit', function(event) {
                            if (!this.checkValidity()) {
                                event.preventDefault(); // Prevent form submission if it's not valid
                                return false;
                            }
                            var submitButton = this.querySelector('button[name="sbfilter"]');
                            submitButton.disabled = true;
                            submitButton.innerHTML  = '<i class="mdi mdi-reload label-icon"></i>Please Wait...';
                            return true; // Allow form submission
                        });
                    </script>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <a href="{{ route('transsales.local.create') }}" type="button" class="btn btn-primary waves-effect btn-label waves-light"><i class="mdi mdi-plus-box label-icon"></i> Add New Sales Transaction (Local)</a>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <h5 class="fw-bold">Sales Transaction (Local)</h5>
                                </div>
                            </div>
                            <div class="col-lg-4"></div>
                            <div class="col-lg-12">
                                <div class="text-center">
                                    List of 
                                    @if($ref_number != null)
                                        (Ref. Number<b> - {{ $ref_number }}</b>)
                                    @endif
                                    @if($searchDate == 'Custom')
                                        (Date From<b> {{ $startdate }} </b>Until <b>{{ $enddate }}</b>)
                                    @else
                                        (<b>All Date</b>)
                                    @endif 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                            <thead>
                                <tr>
                                    {{-- <th class="align-middle text-center">
                                        <input type="checkbox" id="checkAllRows">
                                    </th> --}}
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Ref. Number</th>
                                    <th class="align-middle text-center">Transaction Date</th>
                                    <th class="align-middle text-center">DN Number</th>
                                    <th class="align-middle text-center">Total Transaction</th>
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
    $(function() {
        var i = 1;
        var url = '{!! route('transsales.local.index') !!}';
        var currentDate = new Date();
        var formattedDate = currentDate.toISOString().split('T')[0];
        var fileName = "Sales Transaction Export - " + formattedDate + ".xlsx";
        var data = {
                ref_number: '{{ $ref_number }}',
                id_delivery_notes: '{{ $id_delivery_notes }}',
                searchDate: '{{ $searchDate }}',
                startdate: '{{ $startdate }}',
                enddate: '{{ $enddate }}'
            };
        var requestData = Object.assign({}, data);
        requestData.flag = 1;

        var dataTable = $('#server-side-table').DataTable({
            dom: '<"top d-flex"<"position-absolute top-0 end-0 d-flex"fl><"pull-left col-sm-12 col-md-5"B>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>><"clear:both">',
            initComplete: function(settings, json) {
                $('.dataTables_filter').html('<div class="input-group">' +
                '<button class="btn btn-sm btn-light me-1" type="button" id="custom-button" data-bs-toggle="modal" data-bs-target="#sort"><i class="mdi mdi-filter label-icon"></i> Sort & Filter</button>' +
                '<input class="form-control me-1" id="custom-search-input" type="text" placeholder="Search...">' +
                '</div>');
                // $('.top').prepend(
                //     `<div class='pull-left'>
                //         <div class="btn-group mb-2" style="margin-right: 10px;"> <!-- Added inline style for margin -->
                //             <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                //                 <i class="mdi mdi-checkbox-multiple-marked-outline"></i> Bulk Actions <i class="fas fa-caret-down"></i>
                //             </button>
                //             <ul class="dropdown-menu">
                //                 <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteselected"><i class="mdi mdi-trash-can"></i> Delete Selected</button></li>
                //             </ul>
                //         </div>
                //     </div>`
                // );
            },
            buttons: [
                {
                    extend: "excel",
                    text: '<i class="mdi mdi-file-excel label-icon"></i> Export to Excel',
                    className: 'btn btn-light waves-effect btn-label waves-light mb-2',
                    action: function (e, dt, button, config) {
                        $.ajax({
                            url: url,
                            method: "GET",
                            data: requestData,
                            success: function (response) {
                                generateExcel(response, fileName);
                            },
                            error: function (error) {
                                console.error(
                                    "Error sending data to server:",
                                    error
                                );
                            },
                        });
                    },
                },
            ],
            language: {
                processing: '<div id="custom-loader" class="dataTables_processing"></div>'
            },
            processing: true,
            serverSide: true,
            pageLength: 5,
            lengthMenu: [
                [5, 10, 20, 25, 50, 100, 200, -1],
                [5, 10, 20, 25, 50, 100, 200, "All"]
            ],
            language: {
                lengthMenu: '<select class="form-select" style="width: 100%">' +
                            '<option value="5">5</option>' +
                            '<option value="10">10</option>' +
                            '<option value="20">20</option>' +
                            '<option value="25">25</option>' +
                            '<option value="50">50</option>' +
                            '<option value="100">100</option>' +
                            '<option value="200">200</option>' +
                            '<option value="-1">All</option>' +
                            '</select>'
            },
            aaSorting: [],
            ajax: {
                url: url,
                type: 'GET',
                data: data
            },
            columns: [
                // {
                //     data: 'bulk-action',
                //     name: 'bulk-action',
                //     className: 'text-center',
                //     orderable: false,
                //     searchable: false
                // },
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
                    data: 'ref_number',
                    name: 'ref_number',
                    orderable: true,
                    searchable: true,
                    className: 'text-bold'
                },
                {
                    data: 'date_transaction',
                    searchable: true,
                    orderable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        var date_transaction = new Date(row.date_transaction);
                        return date_transaction.toLocaleDateString('es-CL').replace(/\//g, '-');
                    },
                },
                {
                    data: 'dn_number',
                    name: 'dn_number',
                    orderable: true,
                    searchable: true,
                    className: 'text-center'
                },
                {
                    data: 'count',
                    name: 'count',
                    orderable: true,
                    searchable: true,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<h5><span class="badge bg-info">'+ row.count +'</span></h5>';
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
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'align-middle text-center',
                },
            ],
            bAutoWidth: false,
            columnDefs: [{
                width: "1%",
                targets: [0]
            }]
        });

        $(document).on('keyup', '#custom-search-input', function () {
            dataTable.search(this.value).draw();
        });
        $('.dataTables_processing').css('z-index', '9999');
    });
</script>

@endsection