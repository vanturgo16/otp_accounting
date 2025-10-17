<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;
use DateTime;

// Traits
use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\MstApproval;
use App\Models\MstBankAccount;
use App\Models\MstCompanies;
use App\Models\MstPpn;
use App\Models\TransSales;
use App\Models\TransSalesExport;
use App\Models\MstRule;
use App\Models\TransSalesDetailPrice;

class TransSalesController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    public function getListPostedDN()
    {
        return DeliveryNote::select(
            'delivery_notes.id',
            'delivery_notes.dn_number',
            'delivery_notes.date',
            DB::raw('MIN(sales_orders.id_order_confirmations) as ko_number'),
            DB::raw('MIN(sales_orders.reference_number) as po_number'),
            'delivery_notes.status'
        )
        ->leftJoin('delivery_note_details', 'delivery_notes.id', 'delivery_note_details.id_delivery_notes')
        ->leftJoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
        ->where('delivery_notes.status', 'Posted')
        ->groupBy(
            'delivery_notes.id',
            'delivery_notes.dn_number',
            'delivery_notes.date',
            'delivery_notes.status'
        )->get();
    }
    public function checkAvailableDN($id)
    {
        // Check if DN status is not Posted
        if (DeliveryNote::where('id', $id)->where('status', '!=', 'Posted')->exists()) {
            return ['success' => false, 'message' => 'Status DN not Posted'];
        }
        // Check if DN already used in Local Invoice
        if (TransSales::where('id_delivery_notes', $id)->exists()) {
            return ['success' => false, 'message' => 'DN has been used in Invoice Sales Local'];
        }
        // Check if DN already used in Export Invoice
        if (TransSalesExport::where('id_delivery_notes', $id)->exists()) {
            return ['success' => false, 'message' => 'DN has been used in Invoice Sales Export'];
        }
        // If all checks passed
        return ['success' => true, 'message' => 'DN is available'];
    }
    public function getCustomerFromDN($id)
    {
        return DeliveryNote::select(
                'delivery_notes.dn_number', 
                'delivery_notes.id_master_customers',
                'master_customers.name as customer_name',
                'master_customers.tax_number',
                'master_currencies.currency_code',
                'master_salesmen.name as salesman_name',
                'master_customer_addresses.*',
                'master_provinces.province',
                'master_countries.country'
            )
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_currencies', 'master_customers.id_master_currencies', 'master_currencies.id')
            // ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->leftjoin('sales_orders', 'delivery_notes.id_sales_orders', 'sales_orders.id')
            ->leftjoin('master_salesmen', 'sales_orders.id_master_salesmen', 'master_salesmen.id')
            ->leftjoin('master_customer_addresses', 'master_customers.id', 'master_customer_addresses.id_master_customers')
            ->leftjoin('master_provinces', 'master_customer_addresses.id_master_provinces', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_customer_addresses.id_master_countries', 'master_countries.id')
            ->whereIn('master_customer_addresses.type_address', ['Same As (Invoice, Shipping)', 'Invoice'])
            ->where('delivery_notes.id', $id)
            ->first();
    }
    public function getSOPriceFromDN(Request $request)
    {
        $idDN       = $request->idDN;
        $ppnRate    = $request->ppnRate;

        $datas = DeliveryNoteDetail::select(
                'sales_orders.id as id_sales_orders',
                'sales_orders.so_number',
                'sales_orders.type_product',
                DB::raw("
                    CASE 
                        WHEN sales_orders.type_product = 'RM' THEN master_raw_materials.description
                        WHEN sales_orders.type_product = 'WIP' THEN master_wips.description
                        WHEN sales_orders.type_product = 'FG' THEN master_product_fgs.description
                        WHEN sales_orders.type_product IN ('TA', 'Other') THEN master_tool_auxiliaries.description
                    END as product
                "),
                'sales_orders.qty',
                'master_units.unit as unit',
                'sales_orders.ppn as ppn_type',
                'sales_orders.price as price_origin',
                'sales_orders.total_price as total_price_origin',
                DB::raw("$ppnRate as ppn_rate"),

                // PPN value
                DB::raw("
                    ROUND(
                        CASE 
                            WHEN sales_orders.ppn = 'Exclude' 
                                THEN (sales_orders.price * $ppnRate / 100)
                            WHEN sales_orders.ppn = 'Include' 
                                THEN (sales_orders.price - (sales_orders.price / (1 + ($ppnRate / 100))))
                            ELSE 0
                        END
                    , 3) as ppn_value
                "),
                // Price before PPN (for Include)
                DB::raw("
                    ROUND(
                        CASE 
                            WHEN sales_orders.ppn = 'Include' 
                                THEN (sales_orders.price / (1 + ($ppnRate / 100)))
                            ELSE sales_orders.price
                        END
                    , 3) as price_before_ppn
                "),
                // Total price before PPN (for Include)
                DB::raw("
                    ROUND(
                        CASE 
                            WHEN sales_orders.ppn = 'Include' 
                                THEN (sales_orders.total_price / (1 + ($ppnRate / 100)))
                            ELSE sales_orders.total_price
                        END
                    , 3) as total_price_before_ppn
                "),
                // Price after PPN (for Exclude)
                DB::raw("
                    ROUND(
                        CASE 
                            WHEN sales_orders.ppn = 'Exclude' 
                                THEN (sales_orders.price * (1 + ($ppnRate / 100)))
                            ELSE sales_orders.price
                        END
                    , 3) as price_after_ppn
                "),
                // Total price after PPN (for Exclude)
                DB::raw("
                    ROUND(
                        CASE 
                            WHEN sales_orders.ppn = 'Exclude' 
                                THEN (sales_orders.total_price * (1 + ($ppnRate / 100)))
                            ELSE sales_orders.total_price
                        END
                    , 3) as total_price_after_ppn
                ")
            )
            ->leftJoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
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
                    ->whereIn('sales_orders.type_product', ['TA', 'Other']);
            })
            ->leftJoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
            ->where('delivery_note_details.id_delivery_notes', $idDN)
            ->get();

        $ppnType       = $datas->first() ? $datas->first()->ppn_type : null;
        $totalPrice     = (float) $datas->sum('total_price_before_ppn');
        $dppFactor      = (float) 11/12;
        $dppValue       = (float) ($totalPrice) * $dppFactor;
        $ppnValue       = (float) ($ppnRate/100) * $totalPrice;
        $total          = (float) $totalPrice + $ppnValue;
        
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->with([
                    'nj'        => $totalPrice,
                    'dpp'       => $dppValue,
                    'ppn_rate'  => $ppnRate,
                    'ppn'       => $ppnValue,
                    'total'     => $total,
                ])
                ->toJson();
        }
        
        $response = [
            'datas'     => $datas,
            'ppnType'   => $ppnType,
            'nj'        => $totalPrice,
            'dpp'       => $dppValue,
            'ppn_rate'  => $ppnRate,
            'ppn'       => $ppnValue,
            'total'     => $total,
        ];
        return $response;
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
        $dn_number = $request->get('dn_number');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $datas = TransSales::select(
                'trans_sales.id',
                'trans_sales.ref_number',
                'trans_sales.date_invoice',
                'trans_sales.date_transaction',
                'trans_sales.due_date',
                'master_customers.name as customer_name',
                'trans_sales.total_transaction',
                'trans_sales.dn_number',
                'trans_sales.dn_date',
                'trans_sales.po_number',
                'trans_sales.ko_number',
                'trans_sales.ppn_type',
                'trans_sales.ppn_rate',
                'trans_sales.sales_value',
                'trans_sales.dpp',
                'trans_sales.ppn_value',
                'trans_sales.total',
                'trans_sales.created_by',
                'trans_sales.created_at',
                'trans_sales.updated_at',
                DB::raw("'Sales (Local)' as source")
            )
            ->leftJoin('master_customers', 'trans_sales.id_master_customers', 'master_customers.id')
            ->orderBy('trans_sales.created_at','desc');

        if($ref_number){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($dn_number){
            $datas = $datas->where('dn_number', 'like', '%'.$dn_number.'%');
        }
        if($startdate && $enddate){
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }
        
        if($request->flag){
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        $datas = $datas->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('total_transaction', function ($data){
                    return view('generalledger.show_btn', compact('data'));
                })
                ->addColumn('action', function ($data){
                    return view('transsales.local.action', compact('data'));
                })->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Sales Local');

        return view('transsales.local.index',compact('datas', 'ref_number', 'dn_number', 'searchDate', 'startdate', 'enddate', 'flag'));
    }
    public function indexExport(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $dn_number = $request->get('dn_number');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $datas = TransSalesExport::select(
            'trans_sales_export.id',
            'trans_sales_export.ref_number',
            'trans_sales_export.date_invoice',
            'trans_sales_export.date_transaction',
            'master_customers.name as customer_name',
            'trans_sales_export.total_transaction',
            'trans_sales_export.dn_number',
            'trans_sales_export.dn_date',
            'trans_sales_export.po_number',
            'trans_sales_export.ko_number',
            'trans_sales_export.ppn_type',
            'trans_sales_export.ppn_rate',
            'trans_sales_export.currency',
            'trans_sales_export.sales_value',
            'trans_sales_export.ppn_value',
            'trans_sales_export.total',
            'trans_sales_export.created_by',
            'trans_sales_export.created_at',
            'trans_sales_export.updated_at',
            DB::raw("'Sales (Export)' as source")
        )
        ->leftJoin('master_customers', 'trans_sales_export.id_master_customers', 'master_customers.id')
        ->orderBy('trans_sales_export.created_at','desc');

        if($ref_number){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($dn_number){
            $datas = $datas->where('dn_number', 'like', '%'.$dn_number.'%');
        }
        if($startdate && $enddate){
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }
        
        if($request->flag){
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        $datas = $datas->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('total_transaction', function ($data){
                    return view('generalledger.show_btn', compact('data'));
                })
                ->addColumn('action', function ($data){
                    return view('transsales.export.action', compact('data'));
                })->make(true);
        }

        //Audit Log
        $this->auditLogsShort('View List Trans Sales Export');

        return view('transsales.export.index',compact('datas', 'ref_number', 'dn_number', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function infoLocal($id)
    {
        $idTS = decrypt($id);
        $detail = TransSales::where('id', $idTS)->first();
        $bankAccount = json_decode($detail->bank_account, true);
        $detailCust = $this->getCustomerFromDN($detail->id_delivery_notes);
        $detailTransSales = TransSalesDetailPrice::where('id_trans_sales_parent', $idTS)->where('type_sales', 'Local')->get();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $idTS)
            ->where('general_ledgers.ref_number', $detail->ref_number)
            ->where('general_ledgers.source', 'Sales (Local)')
            ->get();

        //Audit Log
        $this->auditLogsShort('View Info Sales Transaction Local Ref Number ('. $detail->ref_number . ')');

        return view('transsales.local.info',compact('detail', 'bankAccount', 'detailCust', 'detailTransSales', 'generalLedgers'));
    }
    public function infoExport($id)
    {
        $idTS = decrypt($id);
        $detail = TransSalesExport::where('id', $idTS)->first();
        $bankAccount = json_decode($detail->bank_account, true);
        $approvalDetail = json_decode($detail->approval_detail, true);
        $detailCust = $this->getCustomerFromDN($detail->id_delivery_notes);
        $detailTransSales = TransSalesDetailPrice::where('id_trans_sales_parent', $idTS)->where('type_sales', 'Export')->get();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $idTS)
            ->where('general_ledgers.ref_number', $detail->ref_number)
            ->where('general_ledgers.source', 'Sales (Export)')
            ->get();

        //Audit Log
        $this->auditLogsShort('View Info Sales Transaction Export Ref Number ('. $detail->ref_number . ')');

        return view('transsales.export.info',compact('detail', 'bankAccount', 'approvalDetail', 'detailCust', 'detailTransSales', 'generalLedgers'));
    }

    public function createLocal(Request $request)
    {
        $deliveryNotes = $this->getListPostedDN();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();
        $initPPN = MstPpn::where('tax_name', 'Trans. Sales (Local)')->where('is_active', 1)->first()->value;

        //Audit Log
        $this->auditLogsShort('View Create New Sales Transaction');

        return view('transsales.local.create',compact('deliveryNotes', 'accountcodes', 'initPPN'));
    }
    public function createExport(Request $request)
    {
        $deliveryNotes = $this->getListPostedDN();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();
        $initPPN = MstPpn::where('tax_name', 'Trans. Sales (Export)')->where('is_active', 1)->first()->value;
        $bankaccount = MstBankAccount::where('type', 'Export')->where('is_active', 1)->first();

        $approvalInfo = MstApproval::select('master_employees.name', 'master_employees.email', 'master_departements.name as dept_name')
            ->leftJoin('master_employees', 'master_approvals.id_master_employees', 'master_employees.id')
            ->leftJoin('master_departements', 'master_employees.id_master_departements', 'master_departements.id')
            ->where('master_approvals.type', 'Accounting')
            ->where('master_approvals.status', 'Active')
            ->first();
        $approvalInfo = [
            'name'      => $approvalInfo->name ?? null,
            'email'     => $approvalInfo->email ?? null,
            'position'  => $approvalInfo->dept_name ?? null
        ];

        //Audit Log
        $this->auditLogsShort('View Create New Sales Transaction');

        return view('transsales.export.create',compact('deliveryNotes', 'accountcodes', 'initPPN', 'bankaccount', 'approvalInfo'));
    }

    public function storeLocal(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date_invoice'              => 'required',
            'due_date'                  => 'required|date|after_or_equal:today',
            'id_delivery_notes'         => 'required',
            'dn_number'                 => 'required',
            'dn_date'                   => 'required',
            'id_master_customers'       => 'required',
            'ppn_rate'                  => 'required',
            'addmore.*.account_code'    => 'required',
            'addmore.*.nominal'         => 'required',
            'addmore.*.type'            => 'required',
        ]);
        
        $idDN = $request->id_delivery_notes;
        $ppnRate = $request->ppn_rate;
        $docNo = optional(MstRule::where('rule_name', 'DocNo. Invoice')->first())->rule_value;
        $bankAccount = MstBankAccount::where('type', 'Local')->where('is_active', 1)->first();

        // Re-Check Available DN
        $check = $this->checkAvailableDN($idDN);
        if (!$check['success']) {
            return redirect()->back()->with(['fail' => $check['message']]);
        }

        // Get SO Detail With Calculate Price From DN
        $detailSOnPrice = $this->getSOPriceFromDN(new Request([
            'idDN'    => $idDN,
            'ppnRate' => $ppnRate,
        ]));
        $detailSO = $detailSOnPrice ? $detailSOnPrice['datas'] : collect();
        // Generate Ref Number
        $refNumber = $this->generateRefNumber(substr($request->dn_number ?? '000000', -6));
    
        DB::beginTransaction();
        try{
            $refParent = TransSales::create([
                'ref_number'            => $refNumber,
                'total_transaction'     => $request->addmore ? count($request->addmore) : 0,
                'date_invoice'          => $request->date_invoice,
                'date_transaction'      => $request->date_invoice,
                'due_date'              => $request->due_date,
                'id_delivery_notes'     => $idDN,
                'id_master_customers'   => $request->id_master_customers,
                'dn_number'             => $request->dn_number,
                'dn_date'               => $request->dn_date,
                'po_number'             => $request->po_number,
                'ko_number'             => $request->ko_number,
                'ppn_type'              => $detailSOnPrice['ppnType'] ?? null,
                'ppn_rate'              => $detailSOnPrice['ppn_rate'] ?? null,
                'sales_value'           => $detailSOnPrice['nj'] ?? null,
                'dpp'                   => $detailSOnPrice['dpp'] ?? null,
                'ppn_value'             => $detailSOnPrice['ppn'] ?? null,
                'total'                 => $detailSOnPrice['total'] ?? null,
                'doc_no'                => $docNo,
                'bank_account'          => json_encode([
                    'bank_name'         => $bankAccount->bank_name,
                    'account_name'      => $bankAccount->account_name,
                    'account_number'    => $bankAccount->account_number,
                    'currency'          => $bankAccount->currency,
                    'swift_code'        => $bankAccount->swift_code,
                    'branch'            => $bankAccount->branch,
                ]),
                'created_by'            => auth()->user()->email
            ]);

            foreach($detailSO as $item) {
                TransSalesDetailPrice::create([
                    'id_trans_sales_parent' => $refParent->id,
                    'type_sales'            => 'Local',
                    'id_sales_orders'       => $item->id_sales_orders,
                    'so_number'             => $item->so_number,
                    'type_product'          => $item->type_product,
                    'product'               => $item->product,
                    'qty'                   => $item->qty,
                    'unit'                  => $item->unit,
                    'ppn_type'              => $item->ppn_type,
                    'price_origin'          => $item->price_origin,
                    'total_price_origin'    => $item->total_price_origin,
                    'ppn_rate'              => $item->ppn_rate,
                    'ppn_value'             => $item->ppn_value,
                    'price_before_ppn'      => $item->price_before_ppn,
                    'total_price_before_ppn'=> $item->total_price_before_ppn,
                    'price_after_ppn'       => $item->price_after_ppn,
                    'total_price_after_ppn' => $item->total_price_after_ppn,
                ]);
            }

            // Create General Ledger & Update Account
            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace('.', '', $item['nominal']);
                        $nominal = str_replace(',', '.', $nominal);
                        // Create General Ledger
                        $this->storeGeneralLedger($refParent->id, $refNumber, $request->date_invoice, $item['account_code'], $item['type'], $nominal, 'Sales (Local)');
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            // Update Status DN
            DeliveryNote::where('id', $idDN)->update(['status' => 'Closed']);

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
            'date_invoice'              => 'required',
            'term'                      => 'required',
            'id_delivery_notes'         => 'required',
            'dn_number'                 => 'required',
            'dn_date'                   => 'required',
            'id_master_customers'       => 'required',
            'ppn_rate'                  => 'required',
            'currency'                  => 'required',
            'addmore.*.account_code'    => 'required',
            'addmore.*.nominal'         => 'required',
            'addmore.*.type'            => 'required',
        ]);

        $idDN = $request->id_delivery_notes;
        $ppnRate = $request->ppn_rate;

        // Re-Check Available DN
        $check = $this->checkAvailableDN($idDN);
        if (!$check['success']) {
            return redirect()->back()->with(['fail' => $check['message']]);
        }

        // Get SO Detail With Calculate Price From DN
        $detailSOnPrice = $this->getSOPriceFromDN(new Request([
            'idDN'    => $idDN,
            'ppnRate' => $ppnRate,
        ]));
        $detailSO = $detailSOnPrice ? $detailSOnPrice['datas'] : collect();
        // Generate Ref Number
        $refNumber = $this->generateRefNumberExport();

        DB::beginTransaction();
        try{
            $refParent = TransSalesExport::create([
                'ref_number'            => $refNumber,
                'total_transaction'     => $request->addmore ? count($request->addmore) : 0,
                'date_invoice'          => $request->date_invoice,
                'date_transaction'      => $request->date_invoice,
                'term'                  => $request->term,
                'id_delivery_notes'     => $idDN,
                'id_master_customers'   => $request->id_master_customers,
                'dn_number'             => $request->dn_number,
                'dn_date'               => $request->dn_date,
                'po_number'             => $request->po_number,
                'ko_number'             => $request->ko_number,
                'ppn_type'              => $detailSOnPrice['ppnType'] ?? null,
                'ppn_rate'              => $detailSOnPrice['ppn_rate'] ?? null,
                'currency'              => $request->currency,
                'sales_value'           => $detailSOnPrice['nj'] ?? null,
                'ppn_value'             => $detailSOnPrice['ppn'] ?? null,
                'total'                 => $detailSOnPrice['total'] ?? null,
                'bank_account'          => json_encode([
                    'bank_name'         => $request->input('bank_name'),
                    'account_name'      => $request->input('account_name'),
                    'account_number'    => $request->input('account_number'),
                    'currency'          => $request->input('currency'),
                    'swift_code'        => $request->input('swift_code'),
                    'branch'            => $request->input('branch'),
                ]),
                'approval_detail'       => json_encode([
                    'name'              => $request->input('app_name'),
                    'email'             => $request->input('app_email'),
                    'position'          => $request->input('app_position'),
                ]),
                'created_by'            => auth()->user()->email
            ]);

            foreach($detailSO as $item) {
                TransSalesDetailPrice::create([
                    'id_trans_sales_parent' => $refParent->id,
                    'type_sales'            => 'Export',
                    'id_sales_orders'       => $item->id_sales_orders,
                    'so_number'             => $item->so_number,
                    'type_product'          => $item->type_product,
                    'product'               => $item->product,
                    'qty'                   => $item->qty,
                    'unit'                  => $item->unit,
                    'ppn_type'              => $item->ppn_type,
                    'price_origin'          => $item->price_origin,
                    'total_price_origin'    => $item->total_price_origin,
                    'ppn_rate'              => $item->ppn_rate,
                    'ppn_value'             => $item->ppn_value,
                    'price_before_ppn'      => $item->price_before_ppn,
                    'total_price_before_ppn'=> $item->total_price_before_ppn,
                    'price_after_ppn'       => $item->price_after_ppn,
                    'total_price_after_ppn' => $item->total_price_after_ppn,
                ]);
            }

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace('.', '', $item['nominal']);
                        $nominal = str_replace(',', '.', $nominal);
                        // Create General Ledger
                        $this->storeGeneralLedger($refParent->id, $refNumber, $request->date_invoice, $item['account_code'], $item['type'], $nominal, 'Sales (Export)');
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            // Update Status DN
            DeliveryNote::where('id', $idDN)->update(['status' => 'Closed']);

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
    function formatDateToEnglish($date)
    {
        return (new DateTime($date))->format('d F Y');
    }
    function terbilang($number) 
    {
        $number = abs($number);
        $words = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($number < 12) {
            $temp = " " . $words[$number];
        } else if ($number < 20) {
            $temp = $this->terbilang($number - 10) . " Belas ";
        } else if ($number < 100) {
            $temp = $this->terbilang(intval($number / 10)) . " Puluh " . $this->terbilang($number % 10);
        } else if ($number < 200) {
            $temp = " Seratus " . $this->terbilang($number - 100);
        } else if ($number < 1000) {
            $temp = $this->terbilang(intval($number / 100)) . " Ratus " . $this->terbilang($number % 100);
        } else if ($number < 2000) {
            $temp = " Seribu " . $this->terbilang($number - 1000);
        } else if ($number < 1000000) {
            $temp = $this->terbilang(intval($number / 1000)) . " Ribu " . $this->terbilang($number % 1000);
        } else if ($number < 1000000000) {
            $temp = $this->terbilang(intval($number / 1000000)) . " Juta " . $this->terbilang($number % 1000000);
        } else if ($number < 1000000000000) {
            $temp = $this->terbilang(intval($number / 1000000000)) . " Milyar " . $this->terbilang(fmod($number, 1000000000));
        } else if ($number < 1000000000000000) {
            $temp = $this->terbilang(intval($number / 1000000000000)) . " Trilyun " . $this->terbilang(fmod($number, 1000000000000));
        }
        return trim($temp);
    }
    function terbilangWithDecimal($number)
    {
        if (strpos($number, '.') !== false) {
            [$intPart, $decPart] = explode('.', (string)$number, 2);
            $result = $this->terbilang((int)$intPart) . " Koma";
            // Read Each Digits
            $decWords = [];
            $words = array("Nol", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            foreach (str_split($decPart) as $digit) {
                $decWords[] = $words[$digit];
            }
            $result .= " " . implode(" ", $decWords);
            return trim($result);
        } else {
            return $this->terbilang((int)$number);
        }
    }

    public function printLocal($id)
    {
        $idTS = decrypt($id);
        
        $dataCompany = MstCompanies::select('master_companies.company_name', 'master_companies.telephone', 'master_companies.address', 'master_companies.postal_code', 'master_companies.city',
                'master_provinces.province', 'master_countries.country')
            ->leftjoin('master_provinces', 'master_companies.id_master_provinces', '=', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_companies.id_master_countries', '=', 'master_countries.id')
            ->leftjoin('master_currencies', 'master_companies.id_master_currencies', '=', 'master_currencies.id')
            ->where('master_companies.is_active', 1)
            ->first();

        $detail = TransSales::where('id', $idTS)->first();
        $detailCust = $this->getCustomerFromDN($detail->id_delivery_notes);
        $detailTransSales = TransSalesDetailPrice::where('id_trans_sales_parent', $idTS)->where('type_sales', 'Local')->get();

        $total = rtrim(rtrim($detail->total, '0'), '.');
        $terbilangString = $this->terbilangWithDecimal($total) . " Rupiah.";
        
        $dateInvoice = $this->formatDateToIndonesian($detail->date_invoice);
        $dueDate = $this->formatDateToIndonesian($detail->due_date);
        $bankAccount = json_decode($detail->bank_account, true);

        $pdf = PDF::loadView('pdf.transsaleslocal', [
            'dataCompany'       => $dataCompany,
            'detail'            => $detail,
            'detailCust'        => $detailCust,
            'dateInvoice'       => $dateInvoice,
            'dueDate'           => $dueDate,
            'bankAccount'       => $bankAccount,
            'detailTransSales'  => $detailTransSales,
            'terbilangString'   => $terbilangString
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Sales Transaction Local ('. $detail->ref_number . ')');

        return $pdf->stream('Sales Transaction Local ('. $detail->ref_number . ').pdf', array("Attachment" => false));
    }
    public function printExport($id)
    {
        $idTS = decrypt($id);
        
        $dataCompany = MstCompanies::select('master_companies.company_name', 'master_companies.telephone', 'master_companies.address', 'master_companies.postal_code', 'master_companies.city',
                'master_provinces.province', 'master_countries.country')
            ->leftjoin('master_provinces', 'master_companies.id_master_provinces', '=', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_companies.id_master_countries', '=', 'master_countries.id')
            ->leftjoin('master_currencies', 'master_companies.id_master_currencies', '=', 'master_currencies.id')
            ->where('master_companies.is_active', 1)
            ->first();

        $detail = TransSalesExport::where('id', $idTS)->first();
        $detailCust = $this->getCustomerFromDN($detail->id_delivery_notes);
        $detailTransSales = TransSalesDetailPrice::where('id_trans_sales_parent', $idTS)->where('type_sales', 'Export')->get();
        
        $dateInvoice = $this->formatDateToEnglish($detail->date_invoice);
        $bankAccount = json_decode($detail->bank_account, true);
        $approvalInfo = json_decode($detail->approval_detail, true);

        $pdf = PDF::loadView('pdf.transsalesexport', [
            'dataCompany'       => $dataCompany,
            'detail'            => $detail,
            'detailCust'        => $detailCust,
            'detailTransSales'  => $detailTransSales,
            'dateInvoice'       => $dateInvoice,
            'bankAccount'       => $bankAccount,
            'approvalInfo'      => $approvalInfo,
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Sales Transaction Export ('. $detail->ref_number . ')');

        return $pdf->stream('Sales Transaction Export ('. $detail->ref_number . ').pdf', array("Attachment" => false));
    }
}
