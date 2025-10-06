<!doctype html>
<html lang="en">
<head>
    <title>Sales Transaction - {{ $transSales->ref_number }}</title>
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
        <table class="header-table">
            <tr>
                <td style="width:15%">
                    <img class="header-logo" src="{{ public_path('img/icon-otp.png') }}" alt="Logo">
                </td>
                <td style="width:70%; text-align:center; font-size:8px;">
                    <div class="company-name">{{ $dataCompany->company_name }}</div>
                    <div class="company-info">{{ $dataCompany->address }}</div>
                    <div class="company-info">{{ $dataCompany->city }}, {{ $dataCompany->postal_code }}, {{ $dataCompany->province }}, {{ $dataCompany->country }}</div>
                    <div class="company-info">Phone : {{ $dataCompany->telephone ?? '-' }}</div>
                    <div class="company-info">E-mail : {{ $approvalInfo['email'] ?? '-' }}</div>
                    <br>
                </td>
                <td style="width:15%"></td>
            </tr>
        </table>
    </header>

    <main>
        <table class="mt-n2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
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
                            <td class="align-top" style="width:8%;"><b>To</b></td>
                            <td class="text-right align-top" style="width:2%;"><b>:</b></td>
                            <td class="align-top" style="width:90%;">
                                <b>{{ $deliveryNote->customer_name }}</b>
                                <br>
                                {{ $deliveryNote->address.', '.$deliveryNote->postal_code.', '.$deliveryNote->city.', '.$deliveryNote->province.', '.$deliveryNote->country }}
                                <br>
                                Telephone : {{ $deliveryNote->telephone ?? '-' }},
                                Mobile Phone : {{ $deliveryNote->mobile_phone ?? '-' }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:8%;"><b>From</b></td>
                            <td class="text-right align-top" style="width:2%;"><b>:</b></td>
                            <td class="align-top" style="width:90%;">
                                <b>{{ $dataCompany->company_name }}</b>
                                <br>
                                {{ $dataCompany->address }}, {{ $dataCompany->city }}, {{ $dataCompany->postal_code }}, {{ $dataCompany->province }}, {{ $dataCompany->country }}
                                <br>
                                Phone : {{ $dataCompany->telephone ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:15%;"></td>
                <td style="width:35%; font-size:10px; vertical-align:top; text-align:right;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="text-right align-top" style="width:43%;">No.</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="text-right align-top" style="width:55%;">
                                {{ $transSales->ref_number }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="text-right align-top" style="width:43%;">Date</td>
                            <td class="text-right align-top" style="width:2%;">:</td>
                            <td class="text-right align-top" style="width:55%;">
                                {{ $dateInvoice }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="styled-table">
            <thead>
                <tr style="font-size: 11px;">
                    <th class="align-middle text-center"><b>No.</b></th>
                    <th class="align-middle text-center"><b>Items</b></th>
                    <th class="align-middle text-center"><b>Qty ({{ strtoupper($datas[0]->unit) }})</b></th>
                    <th class="align-middle text-center"><b>UOM</b></th>
                    <th class="align-middle text-center"><b>Price/{{ strtoupper($datas[0]->unit) }}</b></th>
                    <th class="align-middle text-center"><b>Total Price</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $item)
                    <tr>
                        <td class="px-2 text-center">{{ $loop->iteration }}</td>
                        <td class="px-2">{{ $item->product }}</td>
                        <td class="px-2 text-center">{{ $item->qty }}</td>
                        <td class="px-2 text-center">-</td>
                        <td class="px-2 text-right">{{ $deliveryNote->currency_code.'  ' . number_format($item->price, 2, ',', '.') }}</td>
                        <td class="px-2 text-right">{{ $deliveryNote->currency_code.'  ' . number_format($item->total_price, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="py-2" style="width:100%; border-collapse:collapse;" id="totalNTerm">
            <tr>
                <td style="width:65%; font-size:10px; vertical-align:top;">
                    <div style="height: 15px;"></div>
                    <div style="font-size: 12px;">TERMS</div>
                    @php
                        $terms = str_replace(['<p>', '</p>'],['', '<br>'],$transSales->term);
                    @endphp
                    <div style="font-size: 10px;">{!! $terms !!}</div>
                </td>
                <td style="width:5%;"></td>
                <td style="width:30%; font-size:10px; vertical-align:top; text-align:right;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:40%;"><b>Amount</b></td>
                            <td class="text-right align-top" style="width:60%;">
                                {{ $deliveryNote->currency_code.'  ' . number_format($transSales->sales_value, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:40%;"><b>Tax</b></td>
                            <td class="text-right align-top" style="width:60%;">
                                {{ $deliveryNote->currency_code.'  ' . number_format($transSales->tax_sales, 2, ',', '.') }}
                            </td>
                        </tr>
                        <tr style="font-size: 10px;">
                            <td class="align-top" style="width:40%;"><b>Total</b></td>
                            <td class="text-right align-top" style="width:60%;">
                                {{ $deliveryNote->currency_code.'  ' . number_format($transSales->total, 2, ',', '.') }}
                            </td>
                        </tr>
                    </table>
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
                    <div style="height: 90px;"></div>
                    <div class="font-weight-bold" style="font-size: 12px; text-decoration: underline;">{{ $approvalInfo['name'] ?? '-' }}</div>
                    <div style="font-size: 12px;">{{ $approvalInfo['position'] ?? '-' }}</div>
                </td>
                <td style="width:2%;"></td>
            </tr>
        </table>
    </main>
</body>
</html>