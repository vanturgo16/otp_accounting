<!doctype html>
<html lang="en">
<head>
    <title>Sales Transaction - {{ $detail->ref_number }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    
    <style>
        #totalNTerm {
            page-break-inside: avoid;
        }
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
        .page-number::before {
            content: "Page " counter(page);
        }
    </style>
    <style>
        .header-table {
            width: 100%;
            height: 90px;
            border-collapse: collapse;
            border-bottom: 3px double black;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-logo {
            width: 80%;
            height: auto;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #010066;
        }
        .company-info {
            font-size: 10px;
        }
    </style>

    <style>
        .terms-content ul,
        .terms-content ol {
            margin-left: 8px !important;
            padding-left: 8px !important;
        }

        .terms-content li {
            margin-left: 2px !important;
            padding-left: 2px !important;
        }
    </style>

</head>
<body class="padded-element-main">
    <header class="padded-element">
        {{-- <table style="height: 5px; width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr style="height: 5px;">
                    <td style="width:20%; font-size: 12px; text-align: right;">
                        <span class="page-number"></span>
                    </td>
                </tr>
            </tbody>
        </table> --}}
        <table class="header-table">
            <tr>
                <td style="width:15%">
                    <img class="header-logo" src="{{ public_path('img/icon-otp.png') }}" alt="Logo">
                    <br>
                </td>
                <td style="width:70%; text-align:center; font-size:8px;">
                    <div class="company-name">{{ $dataCompany->company_name }}</div>
                    <div class="company-info">{{ $dataCompany->address }}, {{ $dataCompany->city }}, {{ $dataCompany->province }}, {{ $dataCompany->postal_code }}</div>
                    <div class="company-info">Phone : {{ $dataCompany->telephone ?? '-' }}, Email: {{ $dataCompany->email ??'-' }}</div>
                    <br>
                </td>
                <td style="width:15%"></td>
            </tr>
        </table>
    </header>

    <main>
        <table class="mt-n4 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td class="align-middle text-center font-weight-bold" style="width:100%; font-size: 14px; text-decoration: underline;">INVOICE</td>
                </tr>
            </tbody>
        </table>
        
        <table class="py-2" style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; font-size:10px; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:10%;"><b>To</b></td>
                            <td class="text-center align-top" style="width:4%;"><b>:</b></td>
                            <td class="align-top" style="width:86%;">
                                <b>{{ $detailCust->customer_name }}</b>
                                <br>
                                {{ $detailCust->address.', '.$detailCust->postal_code.', '.$detailCust->city.', '.$detailCust->province.', '.$detailCust->country }}
                                <br>
                                <table style="border-collapse:collapse;">
                                    <tr style="font-size: 10px;">
                                        <td class="align-top">Email</td>
                                        <td class="align-top">&nbsp;:</td>
                                        <td class="align-top">{{ $detailCust->email_customer ?? '-' }}</td>
                                    </tr>
                                    <tr style="font-size: 10px;">
                                        <td class="align-top">Phone</td>
                                        <td class="align-top">&nbsp;:</td>
                                        <td class="align-top">{{ $detailCust->phone_customer ?? '-' }}</td>
                                    </tr>
                                    <tr style="font-size: 10px;">
                                        <td class="align-top">Fax</td>
                                        <td class="align-top">&nbsp;:</td>
                                        <td class="align-top">{{ $detailCust->fax_customer ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <br>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:10%;"><b>From</b></td>
                            <td class="text-center align-top" style="width:4%;"><b>:</b></td>
                            <td class="align-top" style="width:86%;">
                                <b>{{ $dataCompany->company_name }}</b>
                                <br>
                                {{ $dataCompany->address }}, {{ $dataCompany->city }}, {{ $dataCompany->postal_code }}, {{ $dataCompany->province }}, {{ $dataCompany->country }}
                                <br>
                                <table style="border-collapse:collapse;">
                                    <tr style="font-size: 10px;">
                                        <td class="align-top">Phone</td>
                                        <td class="align-top">&nbsp;:</td>
                                        <td class="align-top">{{ $dataCompany->telephone ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:15%;"></td>
                <td style="width:35%; font-size:10px; vertical-align:top; text-align:right;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="text-left align-top" style="width:10%;"></td>
                            <td class="text-left align-top" style="width:30%;">No.</td>
                            <td class="text-center align-top" style="width:10%;">:</td>
                            <td class="text-left align-top" style="width:50%;">
                                <b>{{ $detail->ref_number }}</b>
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="text-left align-top" style="width:10%;"></td>
                            <td class="text-left align-top" style="width:30%;">Date</td>
                            <td class="text-center align-top" style="width:10%;">:</td>
                            <td class="text-left align-top" style="width:50%;">
                                {{ $dateInvoice }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="text-left align-top" style="width:10%;"></td>
                            <td class="text-left align-top" style="width:30%;">PO No.</td>
                            <td class="text-center align-top" style="width:10%;">:</td>
                            <td class="text-left align-top" style="width:50%;">
                                {{ $detail->po_number ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="text-left align-top" style="width:10%;"></td>
                            <td class="text-left align-top" style="width:30%;">Destination</td>
                            <td class="text-center align-top" style="width:10%;">:</td>
                            <td class="text-left align-top" style="width:50%;">
                                {{ $detail->destination ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="styled-table">
            <thead>
                <tr style="font-size: 11px;">
                    <th class="align-middle text-center"><b>NO.</b></th>
                    <th class="align-middle text-center"><b>ITEMS</b></th>
                    <th class="align-middle text-center"><b>QTY ({{ strtoupper($detailTransSales[0]->unit) }})</b></th>
                    <th class="align-middle text-center"><b>UOM</b></th>
                    <th class="align-middle text-center"><b>PRICE / {{ strtoupper($detailTransSales[0]->unit) }}</b></th>
                    <th class="align-middle text-center"><b>TOTAL PRICE</b></th>
                </tr>
            </thead>
            <tbody>
                @php $qtyTotal = 0; @endphp
                @foreach ($detailTransSales as $item)
                    @php $qtyTotal += $item->qty; @endphp
                    <tr>
                        <td class="px-2 text-center" style="border-bottom: none; border-top: none;">{{ $loop->iteration }}</td>
                        <td class="px-2" style="border-bottom: none; border-top: none;">{{ $item->product }}</td>
                        <td class="px-2 text-center" style="border-bottom: none; border-top: none;">
                            {{ fmod($item->qty, 1) == 0 
                                ? number_format($item->qty, 0, ',', '.') 
                                : number_format(floor($item->qty), 0, ',', '.') . ',' . rtrim(str_replace('.', '', explode('.', (string)$item->qty)[1]), '0') }}
                        </td>
                        <td class="px-2 text-center" style="border-bottom: none; border-top: none;">{{ $item->unit ?? '-' }}</td>
                        <td class="px-2 text-right" style="border-bottom: none; border-top: none;">{{ $detail->currency.'  ' . number_format($item->price_before_ppn, 2, ',', '.') }}</td>
                        <td class="px-2 text-right" style="border-bottom: none; border-top: none;">{{ $detail->currency.'  ' . number_format($item->total_price_before_ppn, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td style="border-top: none; border-bottom: none; height: 40px;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                </tr>
                <tr>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;" class="p-2">
                        @php
                            $note = str_replace(['<p>', '</p>'],['', '<br>'], $detail->note);
                        @endphp
                        {!! $note ?? '' !!}
                    </td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                    <td style="border-top: none; border-bottom: none;"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="px-2 text-center"></td>
                    <td class="px-2 text-center"><b>TOTAL</b></td>
                    <td class="px-2 text-center">
                        {{ fmod($qtyTotal, 1) == 0 
                                ? number_format($qtyTotal, 0, ',', '.') 
                                : number_format(floor($qtyTotal), 0, ',', '.') . ',' . rtrim(str_replace('.', '', explode('.', (string)$qtyTotal)[1]), '0') }}
                    </td>
                    <td class="px-2 text-center">{{ $detailTransSales[0]->unit }}</td>
                    <td class="px-2 text-center"></td>
                    <td class="px-2 text-right">{{ $detail->currency.'  ' . number_format($detail->sales_value, 2, ',', '.') }}</td>
                </tr>

            </tfoot>
        </table>

        <table class="py-2" style="width:100%; border-collapse:collapse;" id="totalNTerm">
            <tr>
                <td style="width:65%; font-size:10px; vertical-align:top;">
                    <div style="height: 5px;"></div>
                    <div style="font-size: 11px;">TERMS :</div>
                    @php
                        $terms = str_replace(['<p>', '</p>'],['', '<br>'],$detail->term);
                    @endphp
                    <div class="terms-content" style="font-size: 10px;">
                        {!! $terms !!}
                    </div>
                </td>
                <td style="width:5%;"></td>
                <td style="width:30%; font-size:10px; vertical-align:top; text-align:right;">
                    {{-- <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:40%;"><b>Amount</b></td>
                            <td class="text-right align-top" style="width:60%;">
                                {{ $detail->currency.'  ' . number_format($detail->sales_value, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:40%;"><b>Tax</b></td>
                            <td class="text-right align-top" style="width:60%;">
                                {{ $detail->currency.'  ' . number_format($detail->ppn_value, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:40%;"><b>Total</b></td>
                            <td class="text-right align-top" style="width:60%;">
                                {{ $detail->currency.'  ' . number_format($detail->total, 2, ',', '.') }}
                            </td>
                        </tr>
                    </table> --}}
                </td>
            </tr>
        </table>
        
        <table class="py-2" style="width:100%; border-collapse:collapse;" id="approval">
            <tr>
                <td style="width:50%; font-size:10px; vertical-align:top;">
                    <div style="font-size: 11px;">Please transferred to our Bank Account as details :</div>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:30%;">Bank Name</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:68%;">
                                {{ $bankAccount['bank_name'] ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:30%;">Account Name</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:68%;">
                                {{ $bankAccount['account_name'] ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:30%;">Account Number</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:68%;">
                                {{ $bankAccount['account_number'] ?? '-' }} ({{ $bankAccount['currency'] ?? '-' }})
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:30%;">Swift Code</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:68%;">
                                {{ $bankAccount['swift_code'] ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:30%;">Branch</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="align-top" style="width:68%;">
                                {{ $bankAccount['branch'] ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:28%;"></td>
                <td class="text-center" style="width:20%; font-size:11px; vertical-align:top;">
                    <div style="height: 90px;"></div>
                    Regards,
                    <div style="height: 120px;"></div>
                    <div class="font-weight-bold" style="font-size: 12px; text-decoration: underline;">{{ $approvalInfo['name'] ?? '-' }}</div>
                    <div style="font-size: 12px;">{{ $approvalInfo['position'] ?? '-' }}</div>
                </td>
                <td style="width:2%;"></td>
            </tr>
        </table>
    </main>
</body>
</html>