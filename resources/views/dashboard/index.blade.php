@extends('layouts.master')

@section('konten')

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
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center mt-3">
                            <div class="col-12">
                                <div class="text-center">
                                    <h5>Welcome to the "Dashboard Accounting"</h5>
                                    <p class="text-muted">Here you can Manage Accounting on the system PT Olefina Tifaplas Polikemindo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3">
                                        <h6>Sales Transaction (Local)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <h4 class="mb-3">
                                                    <span class="counter-value" id="st_local" data-target="">0</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3">
                                        <h6>Sales Transaction (Export)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <h4 class="mb-3">
                                                    <span class="counter-value" id="st_export" data-target="">0</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3">
                                        <h6>Purchase Transaction</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <h4 class="mb-3">
                                                    <span class="counter-value" id="pt" data-target="">0</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-h-100">
                                    <div class="card-header bg-light p-3">
                                        <h6>Import Transaction</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <h4 class="mb-3">
                                                    <span class="counter-value" id="it" data-target="">0</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(function() {
                        function loadSummary(start, end) {
                            $("#processing").removeClass("hidden");
                            $.ajax({
                                url: "{{ route('getDataSummary') }}",
                                type: "GET",
                                data: {
                                    dateFrom: start.format('YYYY-MM-DD'),
                                    dateTo: end.format('YYYY-MM-DD')
                                },
                                success: function(res) {
                                    $("#st_local").attr("data-target", res.countSTLocal).text(res.countSTLocal);
                                    $("#st_export").attr("data-target", res.countSTExport).text(res.countSTExport);
                                    $("#pt").attr("data-target", res.countPT).text(res.countPT);
                                    $("#it").attr("data-target", res.countIT).text(res.countIT);
                                },
                                complete: function() {
                                    $("#processing").addClass("hidden");
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
                        }, function(start, end, label) {
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