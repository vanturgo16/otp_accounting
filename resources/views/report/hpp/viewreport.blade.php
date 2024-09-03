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
                                    <form action="{{ route('report.hpp.generate') }}" id="formgenerate" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="result" value="{{ json_encode($result) }}">
                                        <input type="hidden" name="totalAmountProd" value="{{ $totalAmountProd }}">
                                        <input type="hidden" name="totalAmountProdMonthly" value="{{ $totalAmountProdMonthly }}">
                                        <input type="hidden" name="totalAmountHPProd" value="{{ $totalAmountHPProd }}">
                                        <input type="hidden" name="totalAmountHPProdMonthly" value="{{ $totalAmountHPProdMonthly }}">
                                        <input type="hidden" name="totalAmountHPP" value="{{ $totalAmountHPP }}">
                                        <input type="hidden" name="totalAmountHPPMonthly" value="{{ $totalAmountHPPMonthly }}">
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
        @php
            use Carbon\Carbon;
            // Set locale to Indonesian
            Carbon::setLocale('id');
            // Get today's date in the desired format
            $today = Carbon::now()->translatedFormat('j F Y');
        @endphp
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center py-3">
                        {{-- <h5 class="mb-0"><b>Report This Month</b></h5><br> --}}
                        <h5 class="mb-0"><b>HARGA POKOK PENJUALAN</b></h5>
                        <h6 class="mt-1"><b>Periode {{ $today }}</b></h6>
                    </div>
                    <div class="card-body">
                        <table cellspacing="0" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th style="width: 45%"></th>
                                    <th>Amount This Month</th>
                                    <th style="width: 10%"></th>
                                    <th>Amount Until This Month</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result as $index => $group)
                                    <tr>
                                        <td><strong>{{ $index + 1 }}. </strong></td>
                                        <td colspan="4" style="font-weight: bold; text-decoration: underline;">
                                            {{ strtoupper($group['head']) }}
                                        </td>
                                    </tr>
                                    @foreach ($group['accounts'] as $account)
                                        <tr>
                                            <td></td>
                                            <td>- {{ $account['account'] }}</td>
                                            <td>Rp. {{ number_format($account['monthly_sum'], 3, ',', '.') }}</td>
                                            <td></td>
                                            <td>Rp. {{ number_format($account['sum'], 3, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;">+</td>
                                        <td></td>
                                        <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;">+</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><strong>Jumlah</strong></td>
                                        <td><strong>Rp. {{ number_format($group['total_monthly'], 3, ',', '.') }}</strong></td>
                                        <td></td>
                                        <td><strong>Rp. {{ number_format($group['total'], 3, ',', '.') }}</strong></td>
                                    </tr>
                                    @if($group['head'] == 'Penyusutan')
                                        <tr><td colspan="4"><br></td></tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><strong>BIAYA PRODUKSI -----------------------------------</strong></td>
                                            <td><strong>Rp. {{ number_format($totalAmountProdMonthly, 3, ',', '.') }}</strong></td>
                                            <td></td>
                                            <td><strong>Rp. {{ number_format($totalAmountProd, 3, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 5px double #000000; font-weight: bold;"></td>
                                            <td></td>
                                            <td style="border-bottom: 5px double #000000; font-weight: bold;"></td>
                                        </tr>
                                    @endif
                                    @if($group['head'] == 'Barang Dalam Proses')
                                        <tr><td colspan="4"><br></td></tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><strong>HARGA POKOK PRODUKSI ------------------------</strong></td>
                                            <td><strong>Rp. {{ number_format($totalAmountHPProdMonthly, 3, ',', '.') }}</strong></td>
                                            <td></td>
                                            <td><strong>Rp. {{ number_format($totalAmountHPProd, 3, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 5px double #000000; font-weight: bold;"></td>
                                            <td></td>
                                            <td style="border-bottom: 5px double #000000; font-weight: bold;"></td>
                                        </tr>
                                    @endif
                                    
                                    @if($group['head'] == 'Waste')
                                        <tr><td colspan="4"><br></td></tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                                            <td></td>
                                            <td style="border-bottom: 2.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><strong>HARGA POKOK PENJUALAN -----------------------</strong></td>
                                            <td><strong>Rp. {{ number_format($totalAmountHPPMonthly, 3, ',', '.') }}</strong></td>
                                            <td></td>
                                            <td><strong>Rp. {{ number_format($totalAmountHPP, 3, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="border-bottom: 5px double #000000; font-weight: bold;"></td>
                                            <td></td>
                                            <td style="border-bottom: 5px double #000000; font-weight: bold;"></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4"><br></td>
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