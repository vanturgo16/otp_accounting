<!doctype html>
<html lang="en">
<head>
    <title>Bukti Bank Keluar - {{ $detail->transaction_number }}</title>
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
                    <span style="font-size: 20px; font-weight: bold">BUKTI BANK KELUAR</span>
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
                        BBK NO. <strong style="text-decoration: underline">{{ $detail->transaction_number ?? '________' }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        <br>
        <br>
        <h3 class="text-center">FILE CONTOH PRINT BBK BELUM DIKIRIM</h3>
    </main>
</body>
</html>