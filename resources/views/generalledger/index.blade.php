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
                    <form action="{{ route('generalledger.index') }}" id="formfilter" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body py-8 px-4" style="max-height: 67vh; overflow-y: auto;">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Ref. Number</label>
                                    <input class="form-control" name="ref_number" type="text" value="{{ $ref_number }}" placeholder="Filter Ref. Number..">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Account Code</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="id_account_code">
                                        <option value="" selected>-- Select --</option>
                                        @foreach($acccodes as $item)
                                            <option value="{{ $item->id }}" @if($id_account_code == $item->id) selected="selected" @endif>{{ $item->account_code }} - {{ $item->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Source</label>
                                    <select class="form-select js-example-basic-single" style="width: 100%" name="source">
                                        <option value="" selected>-- Select --</option>
                                        <option value="Sales Transaction"  @if($source == "Sales Transaction") selected="selected" @endif>Sales Transaction</option>
                                        <option value="Purchase Transaction"  @if($source == "Purchase Transaction") selected="selected" @endif>Purchase Transaction</option>
                                        <option value="Import Transaction"  @if($source == "Import Transaction") selected="selected" @endif>Import Transaction</option>
                                        <option value="Manual"  @if($source == "Manual") selected="selected" @endif>Manual</option>
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
                                {{-- <button class="btn btn-sm btn-secondary waves-effect btn-label waves-light"><i class="mdi mdi-file-excel label-icon"></i> Export Excel</button> --}}

                                <a href="{{ route('generalledger.create') }}" type="button" class="btn btn-primary waves-effect btn-label waves-light"><i class="mdi mdi-plus-box label-icon"></i> Add New Transaction</a>
                            </div>
                            <div class="col-lg-4">
                                <div class="text-center">
                                    <h5 class="fw-bold">General Ledgers</h5>
                                </div>
                            </div>
                            <div class="col-lg-4"></div>
                            <div class="col-lg-12">
                                <div class="text-center">
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered dt-responsive w-100" id="server-side-table" style="font-size: small">
                            <thead>
                                <tr>
                                    <th class="align-middle text-center">No.</th>
                                    <th class="align-middle text-center">Ref. Number</th>
                                    <th class="align-middle text-center">Transaction Date</th>
                                    <th class="align-middle text-center">Account Code</th>
                                    <th class="align-middle text-center">Nominal</th>
                                    <th class="align-middle text-center">Debit / Kredit</th>
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
        var url = '{!! route('generalledger.index') !!}';
        var currentDate = new Date();
        var formattedDate = currentDate.toISOString().split('T')[0];
        var fileName = "General Ledgers Export - " + formattedDate + ".xlsx";
        var data = {
                ref_number: '{{ $ref_number }}',
                id_account_code: '{{ $id_account_code }}',
                source: '{{ $source }}',
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
                {
                data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'align-middle text-center',
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
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var lastCategory = null;
                var rowspan = 1;
                api.column(1, { page: 'current' }).data().each(function(category, i) {
                    if (lastCategory === category) {
                        rowspan++;
                        $(rows).eq(i).find('td:eq(1)').remove();
                    } else {
                        if (lastCategory !== null) {
                            $(rows).eq(i - rowspan).find('td:eq(1)').attr('rowspan', rowspan);
                        }
                        lastCategory = category;
                        rowspan = 1;
                    }
                });
                if (lastCategory !== null) {
                    $(rows).eq(api.column(1, { page: 'current' }).data().length - rowspan).find('td:eq(1)').attr('rowspan', rowspan);
                }
            },
        });

        $(document).on('keyup', '#custom-search-input', function () {
            dataTable.search(this.value).draw();
        });
        $('.dataTables_processing').css('z-index', '9999');
    });
</script>

@endsection