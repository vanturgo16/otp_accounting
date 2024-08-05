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
            padding-top: 150px;
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

  <main>

    <table class="mt-n2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
      <tbody>
          <tr>
              <td class="align-middle text-center font-weight-bold" style="width:100%; font-size: 14px; text-decoration: underline;">INVOICE</td>
          </tr>
      </tbody>
    </table>
    <table class="mt-2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
        <tbody>
            <tr>
                <td style="width:4%;">
                    <div style="font-size: 10px;">
                        <b>To</b>
                    </div>
                </td>
                <td style="width:1%;">
                    <div style="font-size: 10px;">
                        <b>:</b>
                    </div>
                </td>
                <td style="width:40%;">
                    <div style="font-size: 10px;">
                        <b>{{ $deliveryNote->customer_name }}</b>
                    </div>
                </td>
                <td style="width:15%;"></td>
                <td class="text-right align-bottom" style="width:20%; font-size: 10px;">
                    No.
                </td>
                <td class="text-right align-bottom" style="width:1%; font-size: 10px;">
                    :
                </td>
                <td class="text-right align-bottom" style="width:19%; font-size: 10px;">
                    {{ $transSales->ref_number }}
                </td>
            </tr>
            <tr>
                <td style="width:4%;">
                    <div style="font-size: 10px;"></div>
                </td>
                <td style="width:1%;">
                    <div style="font-size: 10px;"></div>
                </td>
                <td style="width:40%;">
                    <div style="font-size: 10px;">
                        {{ $deliveryNote->address.', '.$deliveryNote->postal_code.', '.$deliveryNote->city.', '.$deliveryNote->province.', '.$deliveryNote->country }}
                        @if($deliveryNote->telephone != null)
                            Phone : {{ $deliveryNote->telephone }}
                        @endif
                        @if($deliveryNote->mobile_phone != null)
                            Phone : {{ $deliveryNote->mobile_phone }}
                        @endif
                    </div>
                </td>
                <td style="width:15%;"></td>
                <td class="text-right align-top" style="width:20%; font-size: 10px;">
                    Date
                </td>
                <td class="text-right align-top" style="width:1%; font-size: 10px;">
                    :
                </td>
                <td class="text-right align-top" style="width:19%; font-size: 10px;">
                    {{ $date }}
                </td>
            </tr>
            <tr>
                <td style="width:4%;">
                    <div style="font-size: 10px;">
                        <b>From</b>
                    </div>
                </td>
                <td style="width:1%;">
                    <div style="font-size: 10px;">
                        <b>:</b>
                    </div>
                </td>
                <td style="width:40%;">
                    <div style="font-size: 10px;">
                        <b>PT. OLEFINA TIFAPLAS POLIKEMINDO</b>
                    </div>
                </td>
                <td style="width:15%;"></td>
                <td class="text-right align-bottom" style="width:20%; font-size: 10px;"></td>
                <td class="text-right align-bottom" style="width:1%; font-size: 10px;"></td>
                <td class="text-right align-bottom" style="width:19%; font-size: 10px;"></td>
            </tr>
            <tr>
                <td style="width:4%;">
                    <div style="font-size: 10px;"></div>
                </td>
                <td style="width:1%;">
                    <div style="font-size: 10px;"></div>
                </td>
                <td style="width:40%;">
                    <div style="font-size: 10px;">
                        Jl. Raya Serang KM 16.8 Cikupa
                        Tangerang - Indonesia
                        Phone : +62 21 5966 3567
                    </div>
                </td>
                <td style="width:15%;"></td>
                <td class="text-right align-top" style="width:20%; font-size: 10px;"></td>
                <td class="text-right align-top" style="width:1%; font-size: 10px;"></td>
                <td class="text-right align-top" style="width:19%; font-size: 10px;"></td>
            </tr>
        </tbody>
    </table>

    <table class="styled-table-service">
        <thead>
            <tr style="font-size: 11px;">
                <th class="align-middle text-center"><b>No.</b></th>
                <th class="align-middle text-center"><b>Items</b></th>
                <th class="align-middle text-center"><b>Qty (ROLL)</b></th>
                <th class="align-middle text-center"><b>UOM</b></th>
                <th class="align-middle text-center"><b>Price/ROLL</b></th>
                <th class="align-middle text-center"><b>Total Price</b></th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 0; ?>
            @foreach ($datas as $data)
                <?php $no++; ?>
                <tr>
                    <td class="text-center">{{ $no }}</td>
                    <td class="align-top text-left p-1">{{ $data->product }}</td>
                    <td class="text-center">{{ $data->qty }}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">{{ $deliveryNote->currency_code.'  ' . number_format($data->price, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $deliveryNote->currency_code.'  ' . number_format($data->total_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            
            @if($transSales->is_tax == 1)
            <tr>
                <td class="text-center" style="border-right: none; border-bottom: none;"></td>
                <td class="align-top text-left" style="border-left: none; border-right: none; border-bottom: none;"></td>
                <td class="text-center" style="border-left: none; border-right: none; border-bottom: none;"></td>
                <td class="text-center" style="border-left: none; border-right: none; border-bottom: none;"></td>
                <td class="text-right" style="border-left: none; border-right: none; border-bottom: none;"><b>Amount</b></td>
                <td class="text-center p-1" style="border-left: none; border-bottom: none;">{{ $deliveryNote->currency_code.'  ' . number_format($totalAllAmount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-center" style="border-right: none; border-bottom: none; border-top: none;"></td>
                <td class="align-top text-left" style="border-left: none; border-right: none; border-bottom: none; border-top: none;"></td>
                <td class="text-center" style="border-left: none; border-right: none; border-bottom: none; border-top: none;"></td>
                <td class="text-center" style="border-left: none; border-right: none; border-bottom: none; border-top: none;"></td>
                <td class="text-right" style="border-left: none; border-right: none; border-bottom: none; border-top: none;"><b>PPN {{ $ppn }}%</b></td>
                <td class="text-center p-1" style="border-left: none; border-bottom: none; border-top: none;">{{ $deliveryNote->currency_code.'  ' . number_format($ppn_val, 2, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="text-center" style="border-right: none; border-top: none;"></td>
                <td class="align-top text-left" style="border-left: none; border-right: none; border-top: none;"></td>
                <td class="text-center" style="border-left: none; border-right: none; border-top: none;"></td>
                <td class="text-center" style="border-left: none; border-right: none; border-top: none;"></td>
                <td class="text-right" style="border-left: none; border-right: none; border-top: none;"><b>Total</b></td>
                <td class="text-center p-1" style="border-left: none; border-top: none;">{{ $deliveryNote->currency_code.'  ' . number_format($total, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <div class="text-left mt-2" style="font-size: 12px;">TERMS</div>
    @php 
        $find = '<p>';
        $replaceWith = '';
        $newString = str_replace($find, $replaceWith, $transSales->term);
        $find = '</p>';
        $replaceWith = '<br>';
        $newString = str_replace($find, $replaceWith, $newString);
    @endphp
    <div class="text-left" style="font-size: 10px;">{!! $newString !!}</div>
    
    <div id="approval">
        <div class="text-left mt-1" style="font-size: 11px;">Please transferred to our Bank Account as details :</div>
        <table style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td class="text-left" style="width:14%; font-size: 10px;">Bank Name</td>
                    <td class="text-left" style="width:1%; font-size: 10px;">:</td>
                    <td class="text-left" style="width:85%; font-size: 10px;">{{ $bankAccount['bank_name'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-left" style="width:14%; font-size: 10px;">Account Name</td>
                    <td class="text-left" style="width:1%; font-size: 10px;">:</td>
                    <td class="text-left" style="width:85%; font-size: 10px;">{{ $bankAccount['account_name'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-left" style="width:14%; font-size: 10px;">Account Number</td>
                    <td class="text-left" style="width:1%; font-size: 10px;">:</td>
                    <td class="text-left" style="width:85%; font-size: 10px;">{{ $bankAccount['account_number'] ?? '-' }} ({{ $bankAccount['currency'] ?? '-' }})</td>
                </tr>
                <tr>
                    <td class="text-left" style="width:14%; font-size: 10px;">Swift Code</td>
                    <td class="text-left" style="width:1%; font-size: 10px;">:</td>
                    <td class="text-left" style="width:85%; font-size: 10px;">{{ $bankAccount['swift_code'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-left" style="width:14%; font-size: 10px;">Branch</td>
                    <td class="text-left" style="width:1%; font-size: 10px;">:</td>
                    <td class="text-left" style="width:85%; font-size: 10px;">{{ $bankAccount['branch'] ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <table class="mt-2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td style="width:75%;"></td>
                    <td class="text-center" style="width:25%; font-size: 11px;">
                        Regards,
                        
                        <div class="card" style="border:0px; width: 100%; height: 60px;"></div>
                        <div class="text-center font-weight-bold" style="font-size: 12px; text-decoration: underline;">BUDI TRIADI</div>
                        <div class="text-center" style="font-size: 12px;">General Manager</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

  </main>
</body>
</html>