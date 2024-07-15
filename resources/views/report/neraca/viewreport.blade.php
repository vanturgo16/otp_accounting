@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-left">
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#generate"><i class="mdi mdi-sync label-icon"></i> Generate This Report</button>
                        {{-- Modal Generate --}}
                        <div class="modal fade" id="generate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-top" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Generate Report</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('report.neraca.generate') }}" id="formgenerate" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="dataeloquent" value="{{ json_encode($groupedData) }}">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3 text-center">
                                                        <p>Are You Sure To Generate Report For This Month?</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success waves-effect btn-label waves-light" name="sb"><i class="mdi mdi-sync label-icon"></i>Generate</button>
                                        </div>
                                    </form>
                                    <script>
                                        document.getElementById('formgenerate').addEventListener('submit', function(event) {
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
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Report</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('report.neraca') }}">Neraca</a></li>
                            <li class="breadcrumb-item active">This Month</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center py-3">
                        <h5 class="mb-0"><b>Report This Month</b></h5>
                    </div>
                    <div class="card-body">
                        <table cellspacing="0" style="width: 100%; border-collapse: collapse;">
                            <tbody style="width: 100%">
                                @php $no = 0 @endphp
                                @foreach ($groupedData as $head1 => $head1Group)
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">{{ ++$no }}</td>
                                        <td colspan="4" style="font-weight: bold; text-decoration: underline;">{{ strtoupper($head1) }}</td>
                                    </tr>
                                    @php
                                        $totalHead1 = 0;
                                    @endphp
                                    @foreach ($head1Group as $head2 => $head2Group)
                                        <tr>
                                            <td></td>
                                            <td colspan="4" style="font-style: italic; text-decoration: underline;">{{ $head2 }}</td>
                                        </tr>
                                        @php
                                            $totalHead2 = 0;
                                        @endphp
                                        @foreach ($head2Group as $account)
                                            <tr>
                                                <td></td>
                                                <td style="text-align: right;">-</td>
                                                <td>{{ $account->account }}</td>
                                                <td>Rp. {{ number_format($account->total_balance, 3, ',', '.') }}</td>
                                                <td></td>
                                            </tr>
                                            @php
                                                $totalHead2 += $account->total_balance;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;">+</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td colspan="2">Jumlah {{ $head2 }}</td>
                                            <td>Rp. {{ number_format($totalHead2, 3, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $totalHead1 += $totalHead2;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;">+</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="font-weight: bold;">JUMLAH {{ strtoupper($head1) }}</td>
                                        <td style="border-bottom: 5px double #000000; font-weight: bold;">Rp. {{ number_format($totalHead1, 3, ',', '.') }}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="5"><br><br></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection