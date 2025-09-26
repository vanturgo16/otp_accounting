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
            <td class="align-middle text-center" style="width:100%; line-height: 1; width: 70%;">
                <div class="font-weight-bold" style="margin: 0 !important; padding: 0 !important; line-height: 1;">
                    FAKTUR PENJUALAN
                </div>
                <small style="font-size: 10px; margin: 0 !important; padding: 0 !important; line-height: 1;">
                    {{ $transSales->ref_number }}
                </small>
            </td>
            <td style="width:30%; font-size: 8px; padding: 0; position: relative;"></td>   
          </tr>
      </tbody>
    </table>
    {{-- <div style="font-size: 10px;">
        Tax Sales : 
        @if($transSales->tax_sales == null)
        -
        @else
        {{ $transSales->tax_sales }}
        @endif
    </div> --}}

    <table class="styled-table-service">
        <thead>
            <tr style="font-size: 11px;">
                <th class="align-middle text-center" style="border-left: none;">URAIAN BARANG</th>
                <th class="align-middle text-center">BANYAKNYA</th>
                <th class="align-middle text-center">HARGA SATUAN (Rp)</th>
                <th class="align-middle text-center" style="border-right: none;">JUMLAH (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                <tr>
                    <td class="px-2" style="border-left: none;">{{ $data->product }}</td>
                    <td class="px-2 text-center">{{ $data->qty. ' '. $data->unit }}</td>
                    <td class="px-2 text-right">{{ number_format($data->price, 2, ',', '.') }}</td>
                    <td class="px-2 text-right" style="border-right: none;">{{ number_format($data->total_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="approval">
        <table class="mt-2 mb-2" style="width: 100%; border-collapse: collapse;" cellspacing="1">
            <tbody>
                <tr>
                    <td class="align-top" style="width:15%;">
                        <div style="font-size: 10px;">
                            Terbilang
                        </div>
                    </td>
                    <td class="align-top text-left" style="width:45%;">
                        <div style="font-size: 10px;">
                            : <u><i>"{{ $terbilangString }}"</i></u>
                        </div>
                    </td>
                    <td class="align-bottom text-left" style="width:20%; font-size: 10px;">
                        Nilai Jual
                    </td>
                    <td class="align-bottom text-right" style="width:20%; font-size: 10px;">
                        <b>{{ number_format($totalAllAmount, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%;"></td>
                    <td style="width:55%;"></td>
                    <td class="text-left" style="width:20%; font-size: 10px;">
                        DPP Lain-lain
                    </td>
                    <td class="text-right" style="width:20%; font-size: 10px;">
                        <b>{{ number_format($dpp, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:5%;"></td>
                    <td style="width:55%;"></td>
                    <td class="text-left" style="width:20%; font-size: 10px;">
                        PPN {{ $ppn }}%
                    </td>
                    <td class="text-right" style="width:20%; font-size: 10px;">
                        <b>{{ number_format($ppn_val, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:15%;">
                        <div style="font-size: 10px;">
                            No. Surat Jalan
                        </div>
                    </td>
                    <td style="width:45%;">
                        <div style="font-size: 10px;">
                            : {{ !empty($deliveryNote->dn_number) ? $deliveryNote->dn_number : '-' }}
                        </div>                        
                    </td>
                    <td class="text-left" style="width:20%; font-size: 10px;">
                        Total Nilai Jual + PPN
                    </td>
                    <td class="text-right" style="width:20%; font-size: 10px;">
                        <b>{{ number_format($total, 2, ',', '.') }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="width:15%;">
                        <div style="font-size: 10px;">
                            No. KO / PO
                        </div>
                    </td>
                    <td style="width:45%;">
                        <div style="font-size: 10px;">
                            : {{ $datas[0]->ko_number ?? $datas[0]->po_number ?? '-' }}
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width:15%;">
                        <div style="font-size: 10px;">
                            Jatuh Tempo
                        </div>
                    </td>
                    <td style="width:45%;">
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
                    <td style="width:15%;">
                        <div style="font-size: 10px;">
                            Pembayaran Ke
                        </div>
                    </td>
                    <td style="width:45%;">
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
                            A/C. 764 188 4999&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BCA KCP Citra Raya Tangerang
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
                        
                        <div style="margin-top: 90px"></div>
                        <hr style="border: 1px solid black;">   
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

  </main>
</body>
</html>