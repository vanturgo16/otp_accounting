<!doctype html>
<html lang="en">
<head>
    <title>HPP Report - ( {{ $report->report_period_date }} )</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <style>
        #approval {
            page-break-inside: avoid;
        }

        .styled-table-service {
            width: 100%;
            font-size: 9px;
        }

        .styled-table-service th,
        .styled-table-service td {
            border: 0.75px solid black;
            text-align: left;
        }
    </style>
    <style>
        @page { 
            margin: 20px 25px 30px 25px;
        }
        header { position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgb(255, 255, 255); height: 90px; }
        footer { position: fixed; bottom: 0px; left: 0px; right: 0px; background-color: rgb(255, 255, 255); height: 50px; }
        p { page-break-after: always; }
        p:last-child { page-break-after: never; }

        .padded-element {
            padding: 10px 20px;
        }

        .padded-element-main {
            padding-top: 160px;
            padding-bottom: 55px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .page-number::before {
            content: "Page " counter(page);
        }
    </style>

</head>
<body class="padded-element-main">
  <header class="padded-element">
    <table style="height: 5px; width: 100%; border-collapse: collapse;" cellspacing="1">
      <tbody>
        <tr style="height: 5px;">
          <td style="width:20%; font-size: 12px; text-align: right;">
            <span class="page-number"></span>
          </td>
        </tr>
      </tbody>
    </table>
    <table style="height: 90px; width: 100%; border-collapse: collapse; border-bottom: 3px double black;" cellspacing="1">
      <tbody>
          <tr>
            <td style="width:15%">
                <img style="width: 80%; height: auto;" src="{{ public_path('img/icon-otp.png') }}" alt="" />
            </td>
            <td class="align-middle text-center" style="width:70%; font-size: 8px;">
                <div class="font-weight-bold" style="font-size: 20px; color:#010066;">
                    PT. OLEFINA TIFAPLAS POLIKEMINDO
                </div>
                <div style="font-size: 10px;">
                    Jl. Raya Serang KM 16.8, Desa Talaga
                </div>
                <div style="font-size: 10px;">
                    Kec. Cikupa, Kab. Tangerang, Prov. Banten
                </div>
                <div style="font-size: 10px;">
                    Phone : +62 21 5966 3567
                </div>
                <div style="font-size: 10px;">
                    E-mail : budi.triadi@olefinatifaplas.com
                </div>
                <br>
            </td>
            <td style="width:15%">
                
            </td>
          </tr>
      </tbody>
    </table>
  </header>

    <footer>
        <div style="font-size: 8px; text-align: right; margin-top: 50px;">
            <span class="badge bg-secondary text-white">Generate By : {{ $report->report_by }} <b>at</b> {{ $report->report_period_date }}</span>
        </div>
    </footer>


<main>

    @php
        use Carbon\Carbon;
    @endphp

    <table class="mt-n2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
        <tbody>
                <tr>
                    <td class="align-middle text-center font-weight-bold" style="width:100%; font-size: 14px; text-decoration: underline;">HARGA POKOK PENJUALAN</td>
                </tr>
                <tr>
                    <td class="align-middle text-center font-weight-bold" style="width:100%; font-size: 11px;">Periode {{ Carbon::parse($report->report_period_date)->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
        </tbody>
    </table>
    <br>

    <table cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 10px;" class="mb-0">
        <thead style="width: 100%">
            <tr>
                <th style="width: 45%"></th>
                <th>Bulan Ini</th>
                <th style="width: 10%"></th>
                <th>S/D Bulan Ini</th>
            </tr>
        </thead>
        
        <tbody style="width: 100%">
            @foreach ($result as $index => $group)
                <tr>
                    <td colspan="4" style="font-weight: bold; text-decoration: underline;">
                        {{ strtoupper($group['head']) }}
                    </td>
                </tr>
                @foreach ($group['accounts'] as $account)
                    <tr>
                        <td>- {{ $account['account'] }}</td>
                        <td>Rp. {{ number_format($account['monthly_sum'], 3, ',', '.') }}</td>
                        <td></td>
                        <td>Rp. {{ number_format($account['sum'], 3, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;">+</td>
                    <td></td>
                    <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;">+</td>
                </tr>
                <tr>
                    <td><strong>Jumlah</strong></td>
                    <td><strong>Rp. {{ number_format($group['total_monthly'], 3, ',', '.') }}</strong></td>
                    <td></td>
                    <td><strong>Rp. {{ number_format($group['total'], 3, ',', '.') }}</strong></td>
                </tr>
                @if($group['head'] == 'Penyusutan')
                    <tr><td colspan="4"><br></td></tr>
                    <tr>
                        <td></td>
                        <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                        <td></td>
                        <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                    </tr>
                    <tr>
                        <td><strong>BIAYA PRODUKSI ------------------------------------</strong></td>
                        <td><strong>Rp. {{ number_format($totalAmountProdMonthly, 3, ',', '.') }}</strong></td>
                        <td></td>
                        <td><strong>Rp. {{ number_format($totalAmountProd, 3, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
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
                        <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                        <td></td>
                        <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                    </tr>
                    <tr>
                        <td><strong>HARGA POKOK PRODUKSI ------------------------</strong></td>
                        <td><strong>Rp. {{ number_format($totalAmountHPProdMonthly, 3, ',', '.') }}</strong></td>
                        <td></td>
                        <td><strong>Rp. {{ number_format($totalAmountHPProd, 3, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
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
                        <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                        <td></td>
                        <td style="border-bottom: 1.5px solid #000000; text-align: right; padding: 0px; padding-right: 5px; font-weight: bold;"></td>
                    </tr>
                    <tr>
                        <td><strong>HARGA POKOK PENJUALAN -----------------------</strong></td>
                        <td><strong>Rp. {{ number_format($totalAmountHPPMonthly, 3, ',', '.') }}</strong></td>
                        <td></td>
                        <td><strong>Rp. {{ number_format($totalAmountHPP, 3, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
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

  </main>
</body>
</html>