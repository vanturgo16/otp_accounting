@extends('layouts.master')
@section('konten')

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/skeleton.css') }}"/>
<!-- daterangepicker -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/daterangepicker/css/daterangepicker.css') }}"/>
<script src="{{ asset('assets/libs/moment/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/libs/daterangepicker/js/daterangepicker.min.js') }}"></script>


<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Transaction Summary</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Welcome section --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mt-3">
                            <h4>Welcome to the <b>Accounting Dashboard</b></h4>
                            <p class="text-muted">
                                Easily manage accounting records and financial transactions for PT Olefina Tifaplas Polikemindo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- SUMMARY SECTION --}}
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <div class="text-center">
                            <h5 class="text-bold">Total Transaction Summary</h5>

                            <div class="d-flex justify-content-center">
                                <div class="flex-shrink-1">
                                    <input type="text" id="dateRange" class="form-control text-center" style="width:250px"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row" id="summaryWrapper">

                            {{-- SALES LOCAL --}}
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3"><h6>Sales Transaction (Local)</h6></div>
                                    <div class="card-body">
                                        <div class="summary-skeleton d-none">
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="summary-value">
                                            <h4 class="mb-3">
                                                <span id="st_local">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SALES EXPORT --}}
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3"><h6>Sales Transaction (Export)</h6></div>
                                    <div class="card-body">
                                        <div class="summary-skeleton d-none">
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="summary-value">
                                            <h4 class="mb-3">
                                                <span id="st_export">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PURCHASE --}}
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3"><h6>Purchase Transaction</h6></div>
                                    <div class="card-body">
                                        <div class="summary-skeleton d-none">
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="summary-value">
                                            <h4 class="mb-3">
                                                <span id="pt">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- IMPORT --}}
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3"><h6>Cash Book Transaction</h6></div>
                                    <div class="card-body">
                                        <div class="summary-skeleton d-none">
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="summary-value">
                                            <h4 class="mb-3">
                                                <span id="cb">0</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div> {{-- row --}}
                    </div> {{-- card-body --}}
                </div> {{-- card --}}

                {{-- SCRIPT --}}
                <script>
                    $(function() {

                        function showSkeleton() {
                            $(".summary-value").addClass("d-none");
                            $(".summary-skeleton").removeClass("d-none");
                        }

                        function hideSkeleton() {
                            $(".summary-value").removeClass("d-none");
                            $(".summary-skeleton").addClass("d-none");
                        }

                        function loadSummary(start, end) {
                            showSkeleton();

                            $.ajax({
                                url: "{{ route('getDataSummary') }}",
                                type: "GET",
                                data: {
                                    dateFrom: start.format('YYYY-MM-DD'),
                                    dateTo: end.format('YYYY-MM-DD')
                                },
                                success: function(res) {
                                    $("#st_local").text(res.countSTLocal);
                                    $("#st_export").text(res.countSTExport);
                                    $("#pt").text(res.countPT);
                                    $("#cb").text(res.countCB);
                                },
                                complete: function() {
                                    hideSkeleton();
                                }
                            });
                        }

                        // default: this month
                        let start = moment().startOf('month');
                        let end = moment().endOf('month');

                        $('#dateRange').daterangepicker({
                            startDate: start,
                            endDate: end,
                            ranges: {
                                'Today': [moment(), moment()],
                                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                'This Month': [moment().startOf('month'), moment().endOf('month')],
                                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                                'This Year': [moment().startOf('year'), moment().endOf('year')]
                            }
                        }, function(start, end) {
                            $('#dateRange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                            loadSummary(start, end);
                        });

                        loadSummary(start, end);
                    });
                </script>

            </div>
        </div>
    </div>
</div>

@endsection
