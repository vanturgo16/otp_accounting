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

class TransSalesController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    public function getListDN()
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
        )
        ->get();
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
    public function getDeliveryNote($id)
    {
        $deliveryNote = DeliveryNote::select('master_customers.name as customer_name', 'master_salesmen.name as salesman_name', 'master_currencies.currency_code')
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_currencies', 'master_customers.id_master_currencies', 'master_currencies.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->where('delivery_notes.id', $id)
            ->first();
        return json_encode($deliveryNote);
    }
    public function getSalesOrder(Request $request)
    {
        $datas = DeliveryNoteDetail::select('sales_orders.so_number', 'sales_orders.type_product', 'sales_orders.qty',
                'master_units.unit as unit', 'sales_orders.ppn', 'sales_orders.price', 'sales_orders.total_price',
                DB::raw('
                    CASE 
                        WHEN sales_orders.type_product = "RM" THEN master_raw_materials.description 
                        WHEN sales_orders.type_product = "WIP" THEN master_wips.description 
                        WHEN sales_orders.type_product = "FG" THEN master_product_fgs.description 
                        WHEN sales_orders.type_product = "TA" THEN master_tool_auxiliaries.description 
                        WHEN sales_orders.type_product IN ("TA", "Other") THEN master_tool_auxiliaries.description 
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
                    ->whereIn('sales_orders.type_product', ['TA', 'Other']);
            })

            ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
            ->where('delivery_note_details.id_delivery_notes', $request->id_delivery_notes)
            ->get();
        
        if ($request->ajax()) {
            $data = DataTables::of($datas)->toJson();
            return $data;
        }
        return $datas;
    }
    public function getTotalPrice($id, $ppnRate, $typeSales)
    {
        $datas = DeliveryNoteDetail::select('sales_orders.total_price','sales_orders.ppn')
            ->leftjoin('sales_orders', 'delivery_note_details.id_sales_orders', 'sales_orders.id')
            ->where('delivery_note_details.id_delivery_notes', $id)
            ->get();

        $totalPrice = (float) $datas->sum('total_price');
        $ppnRate    = (float) $ppnRate;
        $dppFactor  = (float) 11/12;
        $statusPpn  = null;
        $nj = $dpp = $ppn = $total = (float) 0;

        $first = $datas->first();
        if ($first && strtolower($first->ppn) === 'include') {
            $statusPpn  = 'Include';
            if($typeSales == 'Local'){
                // when price is tax-included, reverse using DPP = NJ*(11/12)
                $k      = ($ppnRate / 100.0) * $dppFactor;  // PPN = NJ * k  ->  total = NJ*(1 + k)
                $nj     = (float) $totalPrice / (1 + $k);
                $ppn    = (float) $totalPrice - $nj;        // same as $nj * $k
                $dpp    = (float) $nj * $dppFactor;
                $total  = (float) $totalPrice;
            } else {
                $nj     = (float) $totalPrice / (1 + ($ppnRate / 100));
                $ppn    = (float) $totalPrice - $nj;
                $total  = (float) $totalPrice;
            }
        } else {
            $statusPpn  = 'Exclude';
            if($typeSales == 'Local'){
                $nj     = (float) $totalPrice;
                $dpp    = (float) ($nj) * $dppFactor;
                $ppn    = (float) ($ppnRate/100) * $dpp;
                $total  = (float) $totalPrice + $ppn;
            } else {
                $nj     = (float) $totalPrice;
                $ppn    = (float) ($ppnRate/100) * $nj;
                $total  = (float) $totalPrice + $ppn;
            }
        }
        $result = [
            'statusPpn' => $statusPpn,
            'nj'    => round($nj, 3),
            'dpp'   => round($dpp, 3),
            'ppn'   => round($ppn, 3),
            'total' => round($total, 3),
        ];

        return json_encode($result);
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
                'trans_sales.*',
                'delivery_notes.dn_number',
                DB::raw('(SELECT COUNT(*) FROM general_ledgers WHERE general_ledgers.ref_number = trans_sales.ref_number) as count')
            )
            ->leftJoin('delivery_notes', 'trans_sales.id_delivery_notes', '=', 'delivery_notes.id')
            ->orderBy('trans_sales.created_at','desc');

        if($ref_number){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($id_delivery_notes){
            $datas = $datas->where('id_delivery_notes', $id_delivery_notes);
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
                ->addColumn('action', function ($data){
                    return view('transsales.local.action', compact('data'));
                })
                // ->addColumn('bulk-action', function ($data) {
                //     $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                //     return $checkBox;
                // })
                // ->rawColumns(['bulk-action'])
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
                'trans_sales_export.*', 'delivery_notes.dn_number',
                DB::raw('(SELECT COUNT(*) FROM general_ledgers WHERE general_ledgers.ref_number = trans_sales_export.ref_number) as count')
            )
            ->leftjoin('delivery_notes', 'trans_sales_export.id_delivery_notes', 'delivery_notes.id')
            ->orderBy('trans_sales_export.created_at','desc');

        if($ref_number){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($id_delivery_notes){
            $datas = $datas->where('id_delivery_notes', $id_delivery_notes);
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
                ->addColumn('action', function ($data){
                    return view('transsales.export.action', compact('data'));
                })
                // ->addColumn('bulk-action', function ($data) {
                //     $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                //     return $checkBox;
                // })
                // ->rawColumns(['bulk-action'])
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
        $data = TransSales::select('delivery_notes.id as id_delivery_notes', 'delivery_notes.dn_number', 'delivery_notes.date as dn_date',
                    DB::raw('(SELECT so.id_order_confirmations 
                        FROM delivery_note_details dnd
                        JOIN sales_orders so ON dnd.id_sales_orders = so.id
                        WHERE dnd.id_delivery_notes = delivery_notes.id
                        ORDER BY so.id ASC LIMIT 1) as ko_number'),
                    DB::raw('(SELECT so.reference_number 
                        FROM delivery_note_details dnd
                        JOIN sales_orders so ON dnd.id_sales_orders = so.id
                        WHERE dnd.id_delivery_notes = delivery_notes.id
                        ORDER BY so.id ASC LIMIT 1) as po_number'),
                    'trans_sales.ref_number', 'trans_sales.date_invoice', 'trans_sales.date_transaction', 'trans_sales.due_date',
                    'trans_sales.tax', 'trans_sales.sales_value', 'trans_sales.dpp', 'trans_sales.tax_sales', 'trans_sales.total',
                    'master_customers.name as customer_name', 'master_salesmen.name as salesman_name'
                )
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
        $data = TransSalesExport::select('delivery_notes.id as id_delivery_notes', 'delivery_notes.dn_number', 'delivery_notes.date as dn_date', 
                    DB::raw('(SELECT so.id_order_confirmations 
                        FROM delivery_note_details dnd
                        JOIN sales_orders so ON dnd.id_sales_orders = so.id
                        WHERE dnd.id_delivery_notes = delivery_notes.id
                        ORDER BY so.id ASC LIMIT 1) as ko_number'),
                    DB::raw('(SELECT so.reference_number 
                        FROM delivery_note_details dnd
                        JOIN sales_orders so ON dnd.id_sales_orders = so.id
                        WHERE dnd.id_delivery_notes = delivery_notes.id
                        ORDER BY so.id ASC LIMIT 1) as po_number'),
                    'trans_sales_export.ref_number', 'trans_sales_export.date_invoice', 'trans_sales_export.date_transaction', 'trans_sales_export.term', 'trans_sales_export.currency', 'trans_sales_export.bank_account', 'trans_sales_export.approval_detail',
                    'trans_sales_export.tax', 'trans_sales_export.sales_value', 'trans_sales_export.tax_sales', 'trans_sales_export.total',
                    'master_customers.name as customer_name', 'master_salesmen.name as salesman_name'
                )
            ->leftjoin('delivery_notes', 'trans_sales_export.id_delivery_notes', 'delivery_notes.id')
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->where('trans_sales_export.id', $id)
            ->first();
        $bankAccount = json_decode($data->bank_account, true);
        $approvalDetail = json_decode($data->approval_detail, true);
        
        $general_ledgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->get();

        //Audit Log
        $this->auditLogsShort('View Info Sales Transaction Export Ref Number ('. $data->ref_number . ')');

        return view('transsales.export.info',compact('data', 'bankAccount', 'approvalDetail', 'general_ledgers'));
    }

    public function createLocal(Request $request)
    {
        $deliveryNotes = $this->getListDN();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();
        $tax = MstPpn::where('tax_name', 'Trans. Sales (Local)')->where('is_active', 1)->first()->value;

        //Audit Log
        $this->auditLogsShort('View Create New Sales Transaction');

        return view('transsales.local.create',compact('deliveryNotes', 'accountcodes', 'tax'));
    }
    public function createExport(Request $request)
    {
        $deliveryNotes = $this->getListDN();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();
        $tax = MstPpn::where('tax_name', 'Trans. Sales (Export)')->where('is_active', 1)->first()->value;
        $bankaccount = MstBankAccount::where('is_active', 1)->first();

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

        return view('transsales.export.create',compact('deliveryNotes', 'accountcodes', 'tax', 'bankaccount', 'approvalInfo'));
    }

    public function storeLocal(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date_invoice' => 'required',
            'date_transaction' => 'required',
            'id_delivery_notes' => 'required',
            'due_date' => 'required|date|after_or_equal:today',
            'tax' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);
        
        $idDN = $request->id_delivery_notes;

        $check = $this->checkAvailableDN($idDN);
        if (!$check['success']) {
            return redirect()->back()->with(['fail' => $check['message']]);
        }

        $noUrutDN = DeliveryNote::where('id', $idDN)->first()->dn_number;
        $noUrutDN = substr($noUrutDN, -6);
        $refNumber = $this->generateRefNumber($noUrutDN);
        
        $tax = $request->tax ?? 0;
        $reGetPrice = $this->getTotalPrice($idDN, $tax, 'Local');
        $statusPpn = null;
        $nj = $dpp = $ppnVal = $total = 0;
        if($reGetPrice){
            $decodeData = json_decode($reGetPrice);
            $statusPpn = $decodeData->statusPpn ?? null;
            $nj = $decodeData->nj ?? 0;
            $dpp = $decodeData->dpp ?? 0;
            $ppnVal = $decodeData->ppn ?? 0;
            $total = $decodeData->total ?? 0;
        }

        DB::beginTransaction();
        try{
            TransSales::create([
                'ref_number' => $refNumber,
                'date_invoice' => $request->date_invoice,
                'date_transaction' => $request->date_transaction,
                'id_delivery_notes' => $idDN,
                'due_date' => $request->due_date,
                'status_tax_so' => $statusPpn,
                'tax' => $tax,
                'sales_value' => $nj,
                'dpp' => $dpp,
                'tax_sales' => $ppnVal,
                'total' => $total,
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
            'date_invoice' => 'required',
            'date_transaction' => 'required',
            'id_delivery_notes' => 'required',
            'term' => 'required',
            'tax' => 'required',
            'currency' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);
        
        $idDN = $request->id_delivery_notes;

        $check = $this->checkAvailableDN($idDN);
        if (!$check['success']) {
            return redirect()->back()->with(['fail' => $check['message']]);
        }

        $refNumber = $this->generateRefNumberExport();

        $tax = $request->tax ?? 0;
        $reGetPrice = $this->getTotalPrice($idDN, $tax, 'Export');
        $statusPpn = null;
        $nj = $ppnVal = $total = 0;
        if($reGetPrice){
            $decodeData = json_decode($reGetPrice);
            $statusPpn = $decodeData->statusPpn ?? null;
            $nj = $decodeData->nj ?? 0;
            $ppnVal = $decodeData->ppn ?? 0;
            $total = $decodeData->total ?? 0;
        }

        DB::beginTransaction();
        try{
            TransSalesExport::create([
                'ref_number' => $refNumber,
                'date_invoice' => $request->date_invoice,
                'date_transaction' => $request->date_transaction,
                'id_delivery_notes' => $idDN,
                'term' => $request->term,
                'status_tax_so' => $statusPpn,
                'tax' => $tax,
                'currency' => $request->currency,
                'sales_value' => $nj,
                'tax_sales' => $ppnVal,
                'total' => $total,
                'bank_account' => json_encode([
                    'bank_name' => $request->input('bank_name'),
                    'account_name' => $request->input('account_name'),
                    'account_number' => $request->input('account_number'),
                    'currency' => $request->input('currency'),
                    'swift_code' => $request->input('swift_code'),
                    'branch' => $request->input('branch'),
                ]),
                'approval_detail' => json_encode([
                    'name' => $request->input('app_name'),
                    'email' => $request->input('app_email'),
                    'position' => $request->input('app_position'),
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

            // full
            $result .= " " . $this->terbilang((int)$decPart);

            // // one by one
            // $digits = str_split($decPart);
            // foreach ($digits as $digit) {
            //     $result .= " " . $this->terbilang((int)$digit);
            // }
            return trim($result);
        } else {
            return $this->terbilang((int)$number);
        }
    }
    public function printLocal($id)
    {
        $id = decrypt($id);

        $dataCompany = MstCompanies::select('master_companies.company_name', 'master_companies.telephone', 'master_companies.address', 'master_companies.postal_code', 'master_companies.city',
                'master_provinces.province', 'master_countries.country')
            ->leftjoin('master_provinces', 'master_companies.id_master_provinces', '=', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_companies.id_master_countries', '=', 'master_countries.id')
            ->leftjoin('master_currencies', 'master_companies.id_master_currencies', '=', 'master_currencies.id')
            ->where('master_companies.is_active', 1)
            ->first();
        $transSales = TransSales::where('id', $id)->first();
        $docNo = MstRule::where('rule_name', 'DocNo. Invoice')->first()->rule_value;

        $deliveryNote = DeliveryNote::select(
                'delivery_notes.dn_number', 
                'master_customers.name as customer_name', 'master_salesmen.name as salesman_name', 'master_customers.tax_number',
                'master_customer_addresses.*',
                'master_customer_addresses.address', 'master_provinces.province', 'master_countries.country',
                    DB::raw('(SELECT so.id_order_confirmations 
                        FROM delivery_note_details dnd
                        JOIN sales_orders so ON dnd.id_sales_orders = so.id
                        WHERE dnd.id_delivery_notes = delivery_notes.id
                        ORDER BY so.id ASC LIMIT 1) as ko_number'),
                    DB::raw('(SELECT so.reference_number 
                        FROM delivery_note_details dnd
                        JOIN sales_orders so ON dnd.id_sales_orders = so.id
                        WHERE dnd.id_delivery_notes = delivery_notes.id
                        ORDER BY so.id ASC LIMIT 1) as po_number'),
                )
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->leftjoin('master_customer_addresses', 'master_customers.id', 'master_customer_addresses.id_master_customers')
            ->leftjoin('master_provinces', 'master_customer_addresses.id_master_provinces', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_customer_addresses.id_master_countries', 'master_countries.id')
            ->where('delivery_notes.id', $transSales->id_delivery_notes)
            ->whereIn('master_customer_addresses.type_address', ['Same As (Invoice, Shipping)', 'Invoice'])
            ->first();

        $request = new Request([
            'id_delivery_notes' => $transSales->id_delivery_notes
        ]);
        $datas = $this->getSalesOrder($request);

        $total = rtrim(rtrim($transSales->total, '0'), '.');
        $terbilangString = $this->terbilangWithDecimal($total) . " Rupiah.";
        $dateInvoice = $this->formatDateToIndonesian($transSales->date_invoice);
        $dueDate = $this->formatDateToIndonesian($transSales->due_date);

        $pdf = PDF::loadView('pdf.transsaleslocal', [
            'dateInvoice' => $dateInvoice,
            'dueDate' => $dueDate,
            'dataCompany' => $dataCompany,
            'transSales' => $transSales,
            'docNo' => $docNo,
            'deliveryNote' => $deliveryNote,
            'datas' => $datas,
            'terbilangString' => $terbilangString
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Sales Transaction Local ('. $transSales->ref_number . ')');

        return $pdf->stream('Sales Transaction Local ('. $transSales->ref_number . ').pdf', array("Attachment" => false));
    }
    public function printExport($id)
    {
        $id = decrypt($id);

        $transSales = TransSalesExport::where('id', $id)->first();
        $dataCompany = MstCompanies::select('master_companies.company_name', 'master_companies.telephone', 'master_companies.address', 'master_companies.postal_code', 'master_companies.city',
                'master_provinces.province', 'master_countries.country')
            ->leftjoin('master_provinces', 'master_companies.id_master_provinces', '=', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_companies.id_master_countries', '=', 'master_countries.id')
            ->leftjoin('master_currencies', 'master_companies.id_master_currencies', '=', 'master_currencies.id')
            ->where('master_companies.is_active', 1)
            ->first();
        $bankAccount = json_decode($transSales->bank_account, true);
        $approvalInfo = json_decode($transSales->approval_detail, true);

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

        $request = new Request([
            'id_delivery_notes' => $transSales->id_delivery_notes
        ]);
        $datas = $this->getSalesOrder($request);
        $dateInvoice = $this->formatDateToEnglish($transSales->date_invoice);

        $pdf = PDF::loadView('pdf.transsalesexport', [
            'dateInvoice' => $dateInvoice,
            'transSales' => $transSales,
            'dataCompany' => $dataCompany,
            'bankAccount' => $bankAccount,
            'approvalInfo' => $approvalInfo,
            'deliveryNote' => $deliveryNote,
            'datas' => $datas,
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Sales Transaction Export ('. $transSales->ref_number . ')');

        return $pdf->stream('Sales Transaction Export ('. $transSales->ref_number . ').pdf', array("Attachment" => false));
    }
}
