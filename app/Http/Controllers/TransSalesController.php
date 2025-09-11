<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use DateTime;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\MstBankAccount;
use App\Models\MstPpn;
use App\Models\TransSales;
use App\Models\TransSalesExport;
use App\Models\MstRule;

class TransSalesController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    public function getDeliveryNote($id)
    {
        $deliveryNote = DeliveryNote::select('master_customers.name as customer_name', 'master_salesmen.name as salesman_name', 'master_currencies.currency_code')
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_currencies', 'master_customers.id_master_currencies', 'master_currencies.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->where('delivery_notes.id', $id)
            ->first();

        $tax = DeliveryNoteDetail::select('sales_orders.ppn')
            ->leftjoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
            ->where('delivery_note_details.id_delivery_notes', $id)
            ->first();

        if ($deliveryNote) {
            $deliveryNote->ppn = $tax->ppn ?? null;
        }

        return json_encode($deliveryNote);
    }

    public function getSalesOrder(Request $request)
    {
        $datas = DeliveryNoteDetail::select('sales_orders.so_number', 'sales_orders.type_product', 'sales_orders.qty',
                'master_units.unit as unit', 'sales_orders.price', 'sales_orders.total_price',
                DB::raw('
                    CASE 
                        WHEN sales_orders.type_product = "RM" THEN master_raw_materials.description 
                        WHEN sales_orders.type_product = "WIP" THEN master_wips.description 
                        WHEN sales_orders.type_product = "FG" THEN master_product_fgs.description 
                        WHEN sales_orders.type_product = "TA" THEN master_tool_auxiliaries.description 
                    END as product'),
                )
            ->leftjoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
            ->leftJoin('master_raw_materials', function ($join) {
                $join->on('sales_orders.id_master_products', '=', 'master_raw_materials.id')
                    ->where('sales_orders.type_product', '=', 'RM');
            })
            ->leftJoin('master_wips', function ($join) {
                $join->on('sales_orders.id_master_products', '=', 'master_wips.id')
                    ->where('sales_orders.type_product', '=', 'WIP');
            })
            ->leftJoin('master_product_fgs', function ($join) {
                $join->on('sales_orders.id_master_products', '=', 'master_product_fgs.id')
                    ->where('sales_orders.type_product', '=', 'FG');
            })
            ->leftJoin('master_tool_auxiliaries', function ($join) {
                $join->on('sales_orders.id_master_products', '=', 'master_tool_auxiliaries.id')
                    ->where('sales_orders.type_product', '=', 'TA');
            })
            // ->leftjoin('master_product_fgs', 'sales_orders.id_master_products', 'master_product_fgs.id')

            ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
            ->where('delivery_note_details.id_delivery_notes', $request->id_delivery_notes)
            ->get();
        
        if ($request->ajax()) {
            $data = DataTables::of($datas)->toJson();
            return $data;
        }
    }

    public function getTotalPrice($id)
    {
        $datas = DeliveryNoteDetail::select('sales_orders.total_price')
            ->leftjoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
            ->where('delivery_note_details.id_delivery_notes', $id)
            ->get();
        $totalPrice = $datas->sum('total_price');

        return json_encode($totalPrice);
    }

    function generateRefNumber($noUrutDN)
    {
        // Get current year and month
        $year = date('y');
        $month = date('m');
        // Convert the numeric month to a Roman numeral
        $romanMonths = [
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII'
        ];
        $romanMonth = $romanMonths[$month];
        // Create the formatted number
        $refNumber = "INV/{$romanMonth}/{$year}/{$noUrutDN}";
    
        return $refNumber;
    }
    function generateRefNumberExport()
    {
        // Get the last reference number from the database
        $lastRefNumber = TransSalesExport::orderBy('created_at', 'desc')->first();
        
        // Determine the new counter
        if (!$lastRefNumber) {
            $counter = 1;
        } else {
            // Extract the counter from the last reference number
            $lastCounter = (int)explode('/', $lastRefNumber->ref_number)[0];
            $counter = $lastCounter + 1;
        }
        // Format the counter to be three digits with leading zeros
        $counterFormatted = str_pad($counter, 3, '0', STR_PAD_LEFT);
        // Get the current month and convert to Roman numerals
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $currentMonth = $romanMonths[date('n')];
        // Get the current year
        $currentYear = date('Y');
        // Concatenate all parts to form the reference number
        $refNumber = "{$counterFormatted}/OTP/INV/{$currentMonth}/{$currentYear}";

        return $refNumber;
    }

    public function indexLocal(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $id_delivery_notes = $request->get('id_delivery_notes');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $deliveryNotes = DeliveryNote::select('id', 'dn_number', 'status')->get();

        $datas = TransSales::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'trans_sales.*', 'delivery_notes.dn_number'
            )
            ->leftjoin('delivery_notes', 'trans_sales.id_delivery_notes', 'delivery_notes.id')
            ->orderBy('trans_sales.created_at','desc');

        if($ref_number != null){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($id_delivery_notes != null){
            $datas = $datas->where('id_delivery_notes', $id_delivery_notes);
        }
        if($startdate != null && $enddate != null){
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }
        
        if($request->flag != null){
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        
        $datas = $datas->get();

        foreach($datas as $data){
            $count = GeneralLedger::where('ref_number', $data->ref_number)->count();
            $data->count = $count;
        }
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('transsales.local.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Sales Local');

        return view('transsales.local.index',compact('datas', 'deliveryNotes',
            'ref_number', 'id_delivery_notes', 'searchDate', 'startdate', 'enddate', 'flag'));
    }
    public function indexExport(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $id_delivery_notes = $request->get('id_delivery_notes');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $deliveryNotes = DeliveryNote::select('id', 'dn_number', 'status')->get();

        $datas = TransSalesExport::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'trans_sales_export.*', 'delivery_notes.dn_number'
            )
            ->leftjoin('delivery_notes', 'trans_sales_export.id_delivery_notes', 'delivery_notes.id')
            ->orderBy('trans_sales_export.created_at','desc');

        if($ref_number != null){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($id_delivery_notes != null){
            $datas = $datas->where('id_delivery_notes', $id_delivery_notes);
        }
        if($startdate != null && $enddate != null){
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }
        
        if($request->flag != null){
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        
        $datas = $datas->get();

        foreach($datas as $data){
            $count = GeneralLedger::where('ref_number', $data->ref_number)->count();
            $data->count = $count;
        }
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('transsales.export.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Sales Local');

        return view('transsales.export.index',compact('datas', 'deliveryNotes',
            'ref_number', 'id_delivery_notes', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function infoLocal($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransSales::select('delivery_notes.id as id_delivery_notes', 'trans_sales.ref_number', 'trans_sales.date_invoice', 'trans_sales.date_transaction',
                'trans_sales.due_date', 'delivery_notes.dn_number', 'master_customers.name as customer_name', 'master_salesmen.name as salesman_name')
            ->leftjoin('delivery_notes', 'trans_sales.id_delivery_notes', 'delivery_notes.id')
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->where('trans_sales.id', $id)
            ->first();
        
        $general_ledgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->get();

        //Audit Log
        $this->auditLogsShort('View Info Sales Transaction Local Ref Number ('. $data->ref_number . ')');

        return view('transsales.local.info',compact('data', 'general_ledgers'));
    }
    public function infoExport($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransSalesExport::select('delivery_notes.id as id_delivery_notes', 'trans_sales_export.ref_number', 'trans_sales_export.date_invoice', 'trans_sales_export.date_transaction',
                'trans_sales_export.term', 'delivery_notes.dn_number', 'master_customers.name as customer_name', 'master_salesmen.name as salesman_name')
            ->leftjoin('delivery_notes', 'trans_sales_export.id_delivery_notes', 'delivery_notes.id')
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->where('trans_sales_export.id', $id)
            ->first();
        
        $general_ledgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->get();

        //Audit Log
        $this->auditLogsShort('View Info Sales Transaction Export Ref Number ('. $data->ref_number . ')');

        return view('transsales.export.info',compact('data', 'general_ledgers'));
    }

    public function createLocal(Request $request)
    {
        $deliveryNotes = DeliveryNote::select('id', 'dn_number', 'status')->get();
        $accountcodes = MstAccountCodes::get();
        $tax = MstPpn::where('tax_name', 'Trans. Sales (Local)')->where('is_active', 1)->first()->value;

        //Audit Log
        $this->auditLogsShort('View Create New Sales Transaction');

        return view('transsales.local.create',compact('deliveryNotes', 'accountcodes', 'tax'));
    }
    public function createExport(Request $request)
    {
        $deliveryNotes = DeliveryNote::select('id', 'dn_number', 'status')->get();
        $accountcodes = MstAccountCodes::get();
        $tax = MstPpn::where('tax_name', 'Trans. Sales (Export)')->where('is_active', 1)->first()->value;
        $bankaccount = MstBankAccount::where('is_active', 1)->first();

        //Audit Log
        $this->auditLogsShort('View Create New Sales Transaction');

        return view('transsales.export.create',compact('deliveryNotes', 'accountcodes', 'tax', 'bankaccount'));
    }

    public function storeLocal(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date_invoice' => 'required',
            'date_transaction' => 'required',
            'id_delivery_notes' => 'required',
            'due_date' => 'required|date|after_or_equal:today',
            'tax_sales' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);
        
        $noUrutDN = DeliveryNote::where('id', $request->id_delivery_notes)->first()->dn_number;
        $noUrutDN = substr($noUrutDN, -6);
        $refNumber = $this->generateRefNumber($noUrutDN);

        $tax = ($request->tax === "Exclude" || !isset($request->tax) || $request->tax === null) ? null : $request->tax;

        DB::beginTransaction();
        try{
            TransSales::create([
                'ref_number' => $refNumber,
                'date_invoice' => $request->date_invoice,
                'date_transaction' => $request->date_transaction,
                'id_delivery_notes' => $request->id_delivery_notes,
                'due_date' => $request->due_date,
                'tax' => $tax,
                'tax_sales' => $request->tax_sales,
                'created_by' => auth()->user()->email
            ]);

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace('.', '', $item['nominal']);
                        $nominal = str_replace(',', '.', $nominal);

                        // Create General Ledger
                        $this->storeGeneralLedger($refNumber, $request->date_transaction, $item['account_code'], $item['type'], $nominal, 'Sales Transaction');
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Sales Transaction Local Ref. Number ('. $refNumber . ')');

            DB::commit();
            return redirect()->route('transsales.local.index')->with(['success' => 'Success Create New Sales Transaction Local']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Sales Transaction Local!']);
        }
    }
    public function storeExport(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date_invoice' => 'required',
            'date_transaction' => 'required',
            'id_delivery_notes' => 'required',
            'term' => 'required',
            'tax' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);

        $refNumber = $this->generateRefNumberExport();
        
        $is_tax = 0;
        if($request->is_tax == "on"){ $is_tax = 1; }

        DB::beginTransaction();
        try{
            TransSalesExport::create([
                'ref_number' => $refNumber,
                'date_invoice' => $request->date_invoice,
                'date_transaction' => $request->date_transaction,
                'id_delivery_notes' => $request->id_delivery_notes,
                'term' => $request->term,
                'tax' => $request->tax,
                'is_tax' => $is_tax,
                'bank_account' => json_encode([
                    'bank_name' => $request->input('bank_name'),
                    'account_name' => $request->input('account_name'),
                    'account_number' => $request->input('account_number'),
                    'currency' => $request->input('currency'),
                    'swift_code' => $request->input('swift_code'),
                    'branch' => $request->input('branch'),
                ]),
                'created_by' => auth()->user()->email
            ]);

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace('.', '', $item['nominal']);
                        $nominal = str_replace(',', '.', $nominal);

                        // Create General Ledger
                        $this->storeGeneralLedger($refNumber, $request->date_transaction, $item['account_code'], $item['type'], $nominal, 'Sales Transaction');
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Sales Transaction Export Ref. Number ('. $refNumber . ')');

            DB::commit();
            return redirect()->route('transsales.export.index')->with(['success' => 'Success Create New Sales Transaction Export']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Sales Transaction Export!']);
        }
    }


    function formatDateToIndonesian($date)
    {
        $dateTime = new DateTime($date);
        $formattedDate = $dateTime->format('d F Y');
        $indonesianMonths = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];
        return str_replace(array_keys($indonesianMonths), array_values($indonesianMonths), $formattedDate);
    }
    public function printLocal($id)
    {
        $id = decrypt($id);

        $transSales = TransSales::where('id', $id)->first();
        $docNo = MstRule::where('rule_name', 'DocNo. Invoice')->first()->rule_value;

        $deliveryNote = DeliveryNote::select(
                'delivery_notes.dn_number', 
                'master_customers.name as customer_name', 'master_salesmen.name as salesman_name', 'master_customers.tax_number',
                'master_customer_addresses.*',
                'master_customer_addresses.address', 'master_provinces.province', 'master_countries.country',
            )
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->leftjoin('master_customer_addresses', 'master_customers.id', 'master_customer_addresses.id_master_customers')
            ->leftjoin('master_provinces', 'master_customer_addresses.id_master_provinces', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_customer_addresses.id_master_countries', 'master_countries.id')
            ->where('delivery_notes.id', $transSales->id_delivery_notes)
            ->whereIn('master_customer_addresses.type_address', ['Same As (Invoice, Shipping)', 'Invoice'])
            ->first();

        $datas = DeliveryNoteDetail::select('sales_orders.so_number', 'sales_orders.type_product', 'sales_orders.qty',
            'master_units.unit as unit', 'sales_orders.price', 'sales_orders.total_price', 'sales_orders.id_order_confirmations as ko_number', 'sales_orders.reference_number as po_number',
            DB::raw('
                CASE 
                    WHEN sales_orders.type_product = "RM" THEN master_raw_materials.description 
                    WHEN sales_orders.type_product = "WIP" THEN master_wips.description 
                    WHEN sales_orders.type_product = "FG" THEN master_product_fgs.description 
                    WHEN sales_orders.type_product = "TA" THEN master_tool_auxiliaries.description 
                END as product'),
            )
        ->leftjoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
        ->leftJoin('master_raw_materials', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_raw_materials.id')
                ->where('sales_orders.type_product', '=', 'RM');
        })
        ->leftJoin('master_wips', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_wips.id')
                ->where('sales_orders.type_product', '=', 'WIP');
        })
        ->leftJoin('master_product_fgs', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_product_fgs.id')
                ->where('sales_orders.type_product', '=', 'FG');
        })
        ->leftJoin('master_tool_auxiliaries', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_tool_auxiliaries.id')
                ->where('sales_orders.type_product', '=', 'TA');
        })
        ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
        ->where('delivery_note_details.id_delivery_notes', $transSales->id_delivery_notes)
        ->get();

        function terbilang($number) {
            $number = abs($number);
            $words = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            $temp = "";
            if ($number < 12) {
                $temp = " " . $words[$number];
            } else if ($number < 20) {
                $temp = terbilang($number - 10) . " Belas ";
            } else if ($number < 100) {
                $temp = terbilang(intval($number / 10)) . " Puluh " . terbilang($number % 10);
            } else if ($number < 200) {
                $temp = " seratus " . terbilang($number - 100);
            } else if ($number < 1000) {
                $temp = terbilang(intval($number / 100)) . " Ratus " . terbilang($number % 100);
            } else if ($number < 2000) {
                $temp = " seribu " . terbilang($number - 1000);
            } else if ($number < 1000000) {
                $temp = terbilang(intval($number / 1000)) . " Ribu " . terbilang($number % 1000);
            } else if ($number < 1000000000) {
                $temp = terbilang(intval($number / 1000000)) . " Juta " . terbilang($number % 1000000);
            } else if ($number < 1000000000000) {
                $temp = terbilang(intval($number / 1000000000)) . " Milyar " . terbilang(fmod($number, 1000000000));
            } else if ($number < 1000000000000000) {
                $temp = terbilang(intval($number / 1000000000000)) . " Trilyun " . terbilang(fmod($number, 1000000000000));
            }
            return trim($temp);
        }

        $totalAllAmount = 0;
        foreach($datas as $item){
            $totalAllAmount += $item->total_price;
        }
        $ppn = $transSales->tax ?? '0';
        $ppn_val = ($ppn !== '-') ? ($totalAllAmount * $ppn) / 100 : 0;

        $dpp = $totalAllAmount * (11/12);
        $total = $totalAllAmount + $ppn_val;
        $terbilangString = terbilang($total) . " Rupiah.";

        $dateTime = $this->formatDateToIndonesian($transSales->date_invoice);

        $pdf = PDF::loadView('pdf.transsaleslocal', [
            'date' => $dateTime,
            'transSales' => $transSales,
            'docNo' => $docNo,
            'deliveryNote' => $deliveryNote,
            'datas' => $datas,
            'totalAllAmount' => $totalAllAmount,
            'terbilangString' => $terbilangString,
            'ppn' => $ppn,
            'ppn_val' => $ppn_val,
            'dpp' => $dpp,
            'total' => $total,
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Sales Transaction Local ('. $transSales->ref_number . ')');

        return $pdf->stream('Sales Transaction Local ('. $transSales->ref_number . ').pdf', array("Attachment" => false));
    }
    public function printExport($id)
    {
        $id = decrypt($id);

        $transSales = TransSalesExport::where('id', $id)->first();
        $bankAccount = json_decode($transSales->bank_account, true);

        $deliveryNote = DeliveryNote::select(
                'delivery_notes.dn_number', 
                'master_customers.name as customer_name', 'master_salesmen.name as salesman_name',
                'master_customer_addresses.*',
                'master_customer_addresses.address', 'master_provinces.province', 'master_countries.country',
                'master_customer_addresses.telephone', 'master_customer_addresses.mobile_phone',
                'master_currencies.currency_code',
            )
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_currencies', 'master_customers.id_master_currencies', 'master_currencies.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->leftjoin('master_customer_addresses', 'master_customers.id', 'master_customer_addresses.id_master_customers')
            ->leftjoin('master_provinces', 'master_customer_addresses.id_master_provinces', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_customer_addresses.id_master_countries', 'master_countries.id')
            ->where('delivery_notes.id', $transSales->id_delivery_notes)
            ->whereIn('master_customer_addresses.type_address', ['Same As (Invoice, Shipping)', 'Invoice'])
            ->first();

        $datas = DeliveryNoteDetail::select('sales_orders.so_number', 'sales_orders.type_product', 'sales_orders.qty',
            'master_units.unit as unit', 'sales_orders.price', 'sales_orders.total_price', 'delivery_note_details.po_number',
            DB::raw('
                CASE 
                    WHEN sales_orders.type_product = "RM" THEN master_raw_materials.description 
                    WHEN sales_orders.type_product = "WIP" THEN master_wips.description 
                    WHEN sales_orders.type_product = "FG" THEN master_product_fgs.description 
                    WHEN sales_orders.type_product = "TA" THEN master_tool_auxiliaries.description 
                END as product'),
            )
        ->leftjoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
        ->leftJoin('master_raw_materials', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_raw_materials.id')
                ->where('sales_orders.type_product', '=', 'RM');
        })
        ->leftJoin('master_wips', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_wips.id')
                ->where('sales_orders.type_product', '=', 'WIP');
        })
        ->leftJoin('master_product_fgs', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_product_fgs.id')
                ->where('sales_orders.type_product', '=', 'FG');
        })
        ->leftJoin('master_tool_auxiliaries', function ($join) {
            $join->on('sales_orders.id_master_products', '=', 'master_tool_auxiliaries.id')
                ->where('sales_orders.type_product', '=', 'TA');
        })
        ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
        ->where('delivery_note_details.id_delivery_notes', $transSales->id_delivery_notes)
        ->get();
        
        $totalAllAmount = 0;
        foreach($datas as $item){
            $totalAllAmount += $item->total_price;
        }
        $ppn = 0;
        $ppn_val = 0;
        if($transSales->is_tax == 1){
            $ppn = $transSales->tax;
            $ppn_val = ($totalAllAmount * $ppn) / 100;
        }
        $total = $totalAllAmount + $ppn_val;

        $dateTime = $this->formatDateToIndonesian($transSales->date_invoice);

        $pdf = PDF::loadView('pdf.transsalesexport', [
            'date' => $dateTime,
            'transSales' => $transSales,
            'bankAccount' => $bankAccount,
            'deliveryNote' => $deliveryNote,
            'datas' => $datas,
            'totalAllAmount' => $totalAllAmount,
            'ppn' => $ppn,
            'ppn_val' => $ppn_val,
            'total' => $total,
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Sales Transaction Export ('. $transSales->ref_number . ')');

        return $pdf->stream('Sales Transaction Export ('. $transSales->ref_number . ').pdf', array("Attachment" => false));
    }

    // public function editLocal($id)
    // {
    //     $id = decrypt($id);
    //     // dd($id);
        
    //     $data = TransSales::select('trans_sales.*', 'sales_orders.*', 'master_customers.name as customer_name', 'master_customer_addresses.address as customer_address',
    //             'master_product_fgs.description as product', 'master_units.unit as unit')
    //         ->leftjoin('sales_orders', 'trans_sales.id_sales_order', 'sales_orders.id')
    //         ->leftjoin('master_customers', 'sales_orders.id_master_customers', 'master_customers.id')
    //         ->leftjoin('master_customer_addresses', 'sales_orders.id_master_customer_addresses', 'master_customer_addresses.id')
    //         ->leftjoin('master_product_fgs', 'sales_orders.id_master_products', 'master_product_fgs.id')
    //         ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
    //         ->where('trans_sales.id', $id)
    //         ->first();

    //     $general_ledger = GeneralLedger::where('ref_number', $data->ref_number)->first();
    //     if($general_ledger != []){
    //         $general_ledgers = GeneralLedger::where('ref_number', $data->ref_number)->where('id', '!=', $general_ledger->id)->get();
    //     } else {
    //         $general_ledgers = [];
    //     }
        
    //     $transaction_date = date('Y-m-d', strtotime($general_ledger->date_transaction));

    //     $sales = SalesOrder::select('id', 'so_number', 'status')->get();
    //     $accountcodes = MstAccountCodes::get();
        
    //     //Audit Log
    //     $this->auditLogsShort('View Edit Sales Transaction Ref Number ('. $data->ref_number . ')');

    //     return view('transsales.edit',compact('data', 'general_ledger', 'general_ledgers', 'transaction_date', 'sales', 'accountcodes'));
    // }
    // public function updateLocal(Request $request, $id)
    // {
    //     // dd($request->all());
    //     $id = decrypt($id);

    //     $request->validate([
    //         'id_sales_order' => 'required',
    //         'transaction_date' => 'required',
    //         'no_delivery_note' => 'required',
    //         'addmore.*.account_code' => 'required',
    //         'addmore.*.nominal' => 'required',
    //         'addmore.*.type' => 'required',
    //     ]);

    //     $databefore = TransSales::where('id', $id)->first();
    //     $databefore->id_sales_order = $request->id_sales_order;
    //     $databefore->no_delivery_note = $request->no_delivery_note;

    //     // Compare Transaction
    //     $transbefore = GeneralLedger::where('ref_number', $databefore->ref_number)->get();
    //     $inputtrans = $request->addmore;
    //     $updatetrans = false;
        
    //     if ($transbefore->isNotEmpty() && is_array($inputtrans)) {
    //         // Check if lengths are different
    //         if (count($transbefore) != count($inputtrans)) {
    //             $updatetrans = true;
    //         } else {
    //             $updatetrans = false;
    //             // Iterate and compare
    //             foreach ($transbefore as $index => $trans) {
    //                 // Ensure index exists
    //                 if (!isset($inputtrans[$index])) {
    //                     $updatetrans = true;
    //                     break;
    //                 }
    //                 $detail = $inputtrans[$index];
    //                 // Compare attributes (also remove formatting from amount_fee for accurate comparison)
    //                 $nominal = str_replace('.', '', $detail['nominal']);
    //                 $nominal = str_replace(',', '.', $nominal);
    //                 $type = ($detail['type'] == 'Debit') ? 'D' : 'K';
    //                 if ($trans->id_account_code != $detail['account_code'] || $trans->amount != $nominal || $trans->transaction != $type) {
    //                     $updatetrans = true;
    //                     break;
    //                 }
    //             }
    //         }
    //     } elseif($transbefore->isEmpty() && $inputtrans[0]['account_code'] != null || $transbefore->isNotEmpty() && $inputtrans[0]['account_code'] === null ) {
    //         $updatetrans = true;
    //     } else {
    //         $updatetrans = false;
    //     }

    //     $date_transaction = GeneralLedger::where('ref_number', $databefore->ref_number)->first()->date_transaction;
    //     $date_transaction = date('Y-m-d', strtotime($date_transaction));
    //     if($date_transaction != $request->transaction_date){
    //         $updatetrans = true;
    //     }

    //     if($databefore->isDirty() || $updatetrans == true){
    //         DB::beginTransaction();
    //         try{
    //             //Update Trans Sales
    //             if($databefore->isDirty()){
    //                 TransSales::where('id', $id)->update([
    //                     'id_sales_order' => $request->id_sales_order,
    //                     'no_delivery_note' => $request->no_delivery_note,
    //                     'updated_by' => auth()->user()->email
    //                 ]);
    //             }
    //             //Update General Ledgers
    //             if($updatetrans == true){
    //                 TransSales::where('id', $id)->update([
    //                     'updated_by' => auth()->user()->email
    //                 ]);
    //                 //Delete Data Before
    //                 GeneralLedger::where('ref_number', $databefore->ref_number)->delete();
    //                 //Add New Input
    //                 if($request->addmore != null){
    //                     foreach($request->addmore as $item){
    //                         if($item['account_code'] != null && $item['nominal'] != null){
    //                             $nominal = str_replace('.', '', $item['nominal']);
    //                             $nominal = str_replace(',', '.', $nominal);

    //                             if($item['type'] == 'Debit'){
    //                                 $transaction = "D";
    //                             } else {
    //                                 $transaction = "K";
    //                             }

    //                             GeneralLedger::create([
    //                                 'ref_number' => $databefore->ref_number,
    //                                 'date_transaction' => $request->transaction_date,
    //                                 'id_account_code' => $item['account_code'],
    //                                 'transaction' => $transaction,
    //                                 'amount' => $nominal,
    //                                 'source' => 'Sales Transaction',
    //                             ]);
    //                         }
    //                     }
    //                 }
    //             }

    //             //Audit Log
    //             $this->auditLogsShort('Update Sales Transaction Ref. Number ('. $databefore->ref_number . ')');

    //             DB::commit();
    //             return redirect()->route('transsales.local.index')->with(['success' => 'Success Update Sales Transaction']);
    //         } catch (Exception $e) {
    //             DB::rollback();
    //             return redirect()->back()->with(['fail' => 'Failed to Update Sales Transaction!']);
    //         }
    //     } else {
    //         return redirect()->route('transsales.local.index')->with(['info' => 'Nothing Change, The data entered is the same as the previous one!']);
    //     }
    // }
    // public function delete($id)
    // {
    //     $id = decrypt($id);

    //     DB::beginTransaction();
    //     try{
    //         $data = TransSales::where('id', $id)->first();
    //         GeneralLedger::where('ref_number', $data->ref_number)->delete();
    //         TransSales::where('id', $id)->delete();
            
    //         //Audit Log
    //         $this->auditLogsShort('Delete Sales Transaction Ref. Number = '.$data->ref_number);

    //         DB::commit();
    //         return redirect()->back()->with(['success' => 'Success Delete Sales Transaction']);
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return redirect()->back()->with(['fail' => 'Failed to Delete Sales Transaction!']);
    //     }
    // }
}
