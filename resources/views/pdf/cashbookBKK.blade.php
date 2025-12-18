<!doctype html>
<html lang="en">
<head>
    <title>Bukti Kas Keluar - {{ $detail->transaction_number }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    
    <style>
        #approval {
            page-break-inside: avoid;
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
            padding-top: 150px;
            padding-bottom: 25px;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>

</head>
<body class="padded-element-main">
    <header class="padded-element">
        <table style="border-collapse: collapse; width: 100%" cellspacing="0" cellpadding="0">
            <tr>
                <td rowspan="2" 
                    style="
                        width:20%; 
                        border-left: none; border-top: 0.75px solid black; border-bottom: 0.75px solid black; border-right: 0.75px solid black; 
                        text-align: center; height: 55px;
                        ">
                    <img src="{{ public_path('img/icon-otp.png') }}" alt="Logo">
                </td>
                <td 
                    style="
                        border-left: 0.75px solid black; border-top: 0.75px solid black; border-bottom: 0.75px solid black; border-right: none; 
                        text-align: center; height: 55px; background-color: #f0f0f0;
                        ">
                    <span style="font-size: 20px; font-weight: bold">BUKTI KAS KELUAR</span>
                </td>
            </tr>
            <tr>
                <td 
                    style="
                        border-left: 0.75px solid black; border-top: 0.75px solid black; border-bottom: 0.75px solid black; border-right: none;
                        text-align: center; padding: 0; line-height: 1;
                        ">
                    <span style="font-size: 12px;">
                        {{ $detail->doc_no }}
                    </span>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <table class="mt-n4 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td class="align-middle text-right" 
                        style="width:100%; font-size: 12px;">
                        BKK NO. <strong style="text-decoration: underline">{{ $detail->transaction_number ?? '________' }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="border-collapse: collapse; width: 100%" cellspacing="0" cellpadding="0">
            <thead>
                <tr style="font-size: 12px;">
                    <th class="align-middle text-center font-weight-bold" 
                        style="
                            width: 20%;
                            border-left: none; border-top: 0.75px solid black; border-bottom: 0.75px solid black; border-right: 0.75px solid black; 
                            text-align: center;
                            ">
                        NO. PERKIRAAN
                    </th>
                    <th class="align-middle text-center font-weight-bold" 
                        style="
                            border-left: 0.75px solid black; border-top: 0.75px solid black; border-bottom: 0.75px solid black; border-right: 0.75px solid black; 
                            text-align: center;
                            ">
                        KETERANGAN
                    </th>
                    <th class="align-middle text-center font-weight-bold" 
                        style="
                            width: 20%;
                            border-left: 0.75px solid black; border-top: 0.75px solid black; border-bottom: 0.75px solid black; border-right: none; 
                            text-align: center;
                            ">
                        JUMLAH
                    </th>
                </tr>
            </thead>
            <tbody style="font-size: 10px;">
                @foreach ($generalLedgers as $item)
                    <tr>
                        <td class="px-2 text-center"></td>
                        <td class="px-2" 
                            style="
                                border-left: 0.75px solid black; border-right: 0.75px solid black; 
                                ">
                            {{ $item->note ?? '-' }}
                        </td>
                        <td class="px-2 text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="px-2 text-center" style="height: 50px; border-bottom: 0.75px solid;"></td>
                    <td class="px-2" style="border-left: 0.75px solid black; border-right: 0.75px solid black; border-bottom: 0.75px solid;"></td>
                    <td class="px-2 text-center" style="border-bottom: 0.75px solid;"></td>
                </tr>
            </tbody>
            <tfoot style="font-size: 12px;">
                <tr>
                    <td class="text-right px-2">TERBILANG : </td>
                    <td class="px-2" style="text-decoration: underline;">{{ strtoupper($terbilangString ?? '-') }}</td>
                    <td class="px-2 text-right" style="border-bottom: 0.75px solid; border-left: 0.75px solid;">
                        {{ number_format($detail->total, 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <br>
        <br>
        
        <table style="border-collapse: collapse; width: 100%" cellspacing="0" cellpadding="0" id="approval">
            <tbody style="font-size: 12px;">
                <tr class="text-center">
                    <td style="width: 25%">MENYETUJUI</td>
                    <td style="width: 25%">MENGECEK</td>
                    <td style="width: 25%">KASIR</td>
                    <td style="width: 25%">PENERIMA</td>
                </tr>
                <tr class="text-center">
                    <td style="height: 80px;"></td>
                    <td style="height: 80px;"></td>
                    <td style="height: 80px;"></td>
                    <td style="height: 80px;"></td>
                </tr>
                <tr class="text-center">
                    <td class="font-weight-bold">___________________</td>
                    <td class="font-weight-bold">___________________</td>
                    <td class="font-weight-bold">___________________</td>
                    <td class="font-weight-bold">___________________</td>
                </tr>
                <tr>
                    <td class="px-4">TGL. </td>
                    <td class="px-4">TGL. </td>
                    <td class="px-2 text-center">TGL. {{ $dateInvoice }}</td>
                    <td class="px-4">TGL. </td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>