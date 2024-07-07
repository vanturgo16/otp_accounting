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

        .styled-table-service th {
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
                    PT. OLEFINA TIFAPLAS POLIKEMINDO
                </div>
                <div style="font-size: 10px;">
                    Jl. Raya Serang KM 16.8, Desa Talaga
                </div>
                <div style="font-size: 10px;">
                    Cikupa, Tangerang – 15710
                </div>
            </td>
            <td style="width:30%; font-size: 8px; padding: 0; position: relative;">
                <div style="position: absolute; top: 0; right: 0; font-size: 10px;">
                    FM-SM-ACC-06, Rev. 0, 01 September 2021
                </div>
                <div style="font-size: 10px;" class="mt-4">
                    Kepada Yth,
                </div>
                @if($deliveryNote->address == null)
                    <div class="card p-1" style="border: 1px solid black; width: 100%; height: 60px;">
                        <div style="font-size: 10px;">
                            <b>{{ $deliveryNote->customer_name }}</b>
                        </div> 
                    </div>
                @else
                    <div class="card p-1" style="border: 1px solid black; width: 100%;">
                        <div style="font-size: 10px;">
                            <b>{{ $deliveryNote->customer_name }}</b>
                        </div>  
                        <div style="font-size: 8px;">
                            {{ $deliveryNote->address.', '.$deliveryNote->postal_code.', '.$deliveryNote->city.', '.$deliveryNote->province.', '.$deliveryNote->country }}
                        </div>  
                    </div>
                @endif
                <div style="font-size: 10px;">
                    Tangerang, {{ $date }}
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
              <td class="align-middle text-center font-weight-bold" style="width:100%;">INVOICE</td>
          </tr>
          <tr>
              <td class="align-middle text-center" style="width:100%; font-size: 10px;">No. {{ $transSales->ref_number }}</td>
          </tr>
      </tbody>
    </table>
    <div style="font-size: 10px;">
        Tax Sales : -
    </div>

    <table class="styled-table-service">
        <thead>
            <tr style="font-size: 11px;">
                <th class="align-middle text-center"><b>No.</b></th>
                <th class="align-middle text-center"><b>Delivery Number</b></th>
                <th class="align-middle text-center"><b>Item Product</b></th>
                <th class="align-middle text-center"><b>Qty</b></th>
                <th class="align-middle text-center"><b>Unit Price (Rp)</b></th>
                <th class="align-middle text-center"><b>Total Price (Rp)</b></th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 0; ?>
            @foreach ($datas as $data)
                <?php $no++; ?>
                <tr>
                    <td class="text-center">{{ $no }}</td>
                    <td class="text-center">{{ $deliveryNote->dn_number }}</td>
                    <td class="text-center">{{ $data->product }}</td>
                    <td class="text-center">{{ $data->qty }}</td>
                    <td class="text-center">{{ 'Rp. ' . number_format($data->price, 2, ',', '.') }}</td>
                    <td class="text-center">{{ 'Rp. ' . number_format($data->total_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="approval">
        <table class="mt-2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td style="width:5%;">
                        <div style="font-size: 10px;">
                            TERBILANG
                        </div>
                    </td>
                    <td style="width:55%;">
                        <div style="font-size: 10px;">
                            : <b>"{{ $terbilangString }}"</b>
                        </div>
                    </td>
                    <td class="text-right" style="width:20%; font-size: 12px;">
                        <b>AMOUNT</b>
                    </td>
                    <td class="text-right" style="width:20%; font-size: 12px;">
                        <b>{{ 'Rp. ' . number_format($totalAllAmount, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%;"></td>
                    <td style="width:55%;"></td>
                    <td class="text-right" style="width:20%; font-size: 12px;">
                        <b>PPN {{ $ppn }}%</b>
                    </td>
                    <td class="text-right" style="width:20%; font-size: 12px;">
                        <b>{{ 'Rp. ' . number_format($ppn_val, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%;">
                        <div style="font-size: 10px;">
                            PO Number
                        </div>
                    </td>
                    <td style="width:55%;">
                        <div style="font-size: 10px;">
                            : {{ $deliveryNote->po_number }}
                        </div>
                    </td>
                    <td class="text-right" style="width:20%; font-size: 12px;">
                        <b>Total</b>
                    </td>
                    <td class="text-right" style="width:20%; font-size: 12px;">
                        <b>{{ 'Rp. ' . number_format($total, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%;">
                        <div style="font-size: 10px;">
                            Due Date
                        </div>
                    </td>
                    <td style="width:55%;">
                        @php
                            $due_date = (new DateTime($transSales->due_date))->format('d F Y');
                        @endphp
                        <div style="font-size: 10px;">
                            : {{ $due_date }}
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width:5%;">
                        <div style="font-size: 10px;">
                            Payment to
                        </div>
                    </td>
                    <td style="width:55%;">
                        <div style="font-size: 10px;">
                            : PT. OLEFINA TIFAPLAS POLIKEMINDO
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" style="width:55%;">
                        <div style="font-size: 10px;">
                            <b>A/C. 764 188 4999&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BCA KCP Citra Raya Tangerang</b>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <table class="mt-2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td style="width:65%;"></td>
                    <td style="width:35%; font-size: 11px;">
                        PT. OLEFINA TIFAPLAS POLIKEMINDO
                        
                        <div class="card" style="border:0px; border-bottom: 1.5px solid black; width: 100%; height: 80px;"></div>   
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

  </main>
</body>
</html>