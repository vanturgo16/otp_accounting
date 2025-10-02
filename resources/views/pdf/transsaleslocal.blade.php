<!doctype html>
<html lang="en">
<head>
    <title>Sales Transaction - {{ $transSales->ref_number }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <style>
        #approval {
            page-break-inside: avoid;
        }
        .styled-table {
            width: 100%;
            font-size: 9px;
        }
        .styled-table th,
        .styled-table td {
            border: 0.75px solid black;
            text-align: left;
        }
        .styled-table th {
            border-bottom: 3px double black;
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
            padding-top: 170px;
            padding-bottom: 25px;
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
        <table style="height: 90px; width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td style="width:15%">
                        <img style="width: 80%; height: auto;" src="{{ public_path('img/icon-otp.png') }}" alt="" />
                    </td>
                    <td class="align-middle text-left" style="width:55%; font-size: 8px;">
                        <div class="font-weight-bold" style="font-size: 12px;">
                            {{ $dataCompany->company_name }}
                        </div>
                        <div style="font-size: 10px;">
                            {{ $dataCompany->address }}
                        </div>
                        <div style="font-size: 10px;">
                            {{ $dataCompany->city }}, {{ $dataCompany->province }} – {{ $dataCompany->postal_code }}
                        </div>
                    </td>
                    <td style="width:30%; font-size: 8px; padding: 0; position: relative;">
                        <div style="position: absolute; top: 0; right: 0; font-size: 10px;">
                            {{ $docNo }}
                        </div>
                        <div style="font-size: 10px;" class="mt-4">
                            Kepada Yth,
                        </div>
                        <div class="card p-1" style="border: 1px solid black; width: 100%;">
                            <div style="font-size: 10px;">
                                <b>{{ $deliveryNote->customer_name ?? '-' }}</b>
                            </div>  
                            <div style="font-size: 8px;">
                                @if($deliveryNote->address)
                                    {{  $deliveryNote->address . ', ' . 
                                        ($deliveryNote->postal_code ?? '-') . ', ' . 
                                        ($deliveryNote->city ?? '-') . ', ' . 
                                        ($deliveryNote->province ?? '-') . ', ' . 
                                        ($deliveryNote->country ?? '-') }}
                                @else
                                    Address: -
                                @endif
                            </div>
                            <br>
                            <div style="font-size: 8px;">
                                NPWP : {{ $deliveryNote->tax_number ?? '-' }}
                            </div>
                        </div>
                        <div style="font-size: 10px;">
                            Tangerang, {{ $dateInvoice ?? '-' }}
                        </div>             
                    </td>
                </tr>
            </tbody>
        </table>
    </header>

    <main>
        <table class="mt-n2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td class="align-middle text-center" style="width:100%; line-height: 1; width: 70%;">
                        <div class="font-weight-bold" style="margin: 0 !important; padding: 0 !important; line-height: 1;">
                            FAKTUR PENJUALAN
                        </div>
                        <small style="font-size: 10px; margin: 0 !important; padding: 0 !important; line-height: 1;">
                            {{ $transSales->ref_number }}
                        </small>
                    </td>
                    <td style="width:30%;"></td>   
                </tr>
            </tbody>
        </table>

        <table class="styled-table">
            <thead>
                <tr style="font-size: 11px;">
                    <th class="align-middle text-center" style="border-left: none;">URAIAN BARANG</th>
                    <th class="align-middle text-center">BANYAKNYA</th>
                    <th class="align-middle text-center">HARGA SATUAN (Rp)</th>
                    <th class="align-middle text-center" style="border-right: none;">JUMLAH (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $item)
                    <tr>
                        <td class="px-2" style="border-left: none;">{{ $item->product }}</td>
                        <td class="px-2 text-center">{{ $item->qty. ' '. $item->unit }}</td>
                        <td class="px-2 text-right">{{ number_format($item->price, 2, ',', '.') }}</td>
                        <td class="px-2 text-right" style="border-right: none;">{{ number_format($item->total_price, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="py-2" style="width:100%; border-collapse:collapse;" id="approval">
            <tr>
                <td style="width:52%; font-size:10px; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:27%;">Terbilang</td>
                            <td class="align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:71%; height:50px;">
                                <u><i>"{{ $terbilangString }}"</i></u>
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:27%;">No. Surat Jalan</td>
                            <td class="align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:71%;">
                                {{ $deliveryNote->dn_number ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:27%;">No. KO / PO</td>
                            <td class="align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:71%;">
                                {{ $deliveryNote->ko_number ?? $deliveryNote->po_number ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:27%;">Jatuh Tempo</td>
                            <td class="align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:71%;">
                                {{ $dueDate ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:27%;">Pembayaran Ke</td>
                            <td class="align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:71%;">
                                {{ $dataCompany->company_name ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td colspan="3" class="align-top" style="width:100%;">
                                A/C. 764 188 4999&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BCA KCP Citra Raya Tangerang
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:12%;"></td>
                <td style="width:36%; font-size:10px; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:50%;">Nilai Jual</td>
                            <td class="text-right align-top" style="width:50%;">
                                <b>{{ number_format($transSales->sales_value, 2, ',', '.') }}</b>
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:50%;">DPP Lain-lain</td>
                            <td class="text-right align-top" style="width:50%;">
                                <b>{{ number_format($transSales->dpp, 2, ',', '.') }}</b>
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:50%;">PPN</td>
                            <td class="text-right align-top" style="width:50%;">
                                <b>{{ number_format($transSales->tax_sales, 2, ',', '.') }}</b>
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:50%;">Total Nilai Jual + PPN</td>
                            <td class="text-right align-top" style="width:50%;">
                                <b>{{ number_format($transSales->total, 2, ',', '.') }}</b>
                            </td>
                        </tr>
                    </table>
                    <div style="margin-top: 90px"></div>
                    <table style="width:100%; border-collapse:collapse;">
                        <tbody>
                            <tr>
                                <td style="width:5%;"></td>
                                <td style="width:95%; font-size: 11px;">
                                    PT. OLEFINA TIFAPLAS POLIKEMINDO
                                    
                                    <div style="margin-top: 90px"></div>
                                    <hr style="border: 1px solid black;">   
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </main>
</body>
</html>