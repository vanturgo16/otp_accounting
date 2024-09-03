<!doctype html>
<html lang="en">
<head>
    <title>Neraca Report - ( {{ $report->report_period_date }} )</title>
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

    <table class="mt-n2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
      <tbody>
          <tr>
              <td class="align-middle text-center font-weight-bold" style="width:100%; font-size: 14px; text-decoration: underline;">REPORT NERACA</td>
          </tr>
      </tbody>
    </table>

    <table cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 10px;" class="mb-0">
        <tbody style="width: 100%">
            @php $no = 0 @endphp
            @foreach($data as $head1 => $head2Groups)
                @php
                    $totalHead1 = $head2Groups->sum('total_balance');
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ ++$no }}.</td>
                    <td colspan="4" style="font-weight: bold; text-decoration: underline;">{{ strtoupper($head1) }}</td>
                </tr>
                @foreach($head2Groups->groupBy('head2') as $head2 => $accounts)
                    @php
                        $totalHead2 = $accounts->sum('total_balance');
                    @endphp
                    <tr>
                        <td></td>
                        <td colspan="4" style="font-style: italic; text-decoration: underline;">{{ $head2 }}</td>
                    </tr>
                    @foreach($accounts as $account)
                        <tr>
                            <td></td>
                            <td style="text-align: right;">-</td>
                            <td>{{ $account->account }}</td>
                            <td>Rp. {{ number_format($account->total_balance, 3, ',', '.') }}</td>
                            <td></td>
                        </tr>
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

  </main>
</body>
</html>