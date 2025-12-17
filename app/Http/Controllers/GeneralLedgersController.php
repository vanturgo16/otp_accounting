<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\MstDropdowns;
use App\Models\TransSales;
use App\Models\TransSalesDetailPrice;

class GeneralLedgersController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    public function index(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $id_account_code = $request->get('id_account_code');
        $source = $request->get('source');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        if (is_null($searchDate)) {
            $searchDate = "Custom";
            // $startdate = now()->startOfMonth()->format('Y-m-d');
            // $enddate = now()->endOfMonth()->format('Y-m-d');
            $startdate = now()->startOfYear()->format('Y-m-d');
            $enddate = now()->endOfYear()->format('Y-m-d');
        }
        
        if ($request->ajax()) {
            $datas = GeneralLedger::select(
                'general_ledgers.id', 'general_ledgers.ref_number', 'general_ledgers.source', 'general_ledgers.ref_source', 'general_ledgers.date_transaction', 'master_account_codes.account_code', 
                'master_account_codes.account_name',  'general_ledgers.amount',  'general_ledgers.transaction',  'general_ledgers.note',  'general_ledgers.created_at',  'general_ledgers.updated_at',
            )
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->orderBy('general_ledgers.date_transaction','desc')
            ->orderBy('general_ledgers.ref_number');

            if($ref_number != null){
                $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
            }
            if($id_account_code != null){
                $datas = $datas->where('id_account_code', $id_account_code);
            }
            if($source != null){
                $datas = $datas->where('source', $source);
            }
            if($startdate != null && $enddate != null){
                $datas = $datas->whereDate('general_ledgers.created_at','>=',$startdate)->whereDate('general_ledgers.created_at','<=',$enddate);
            }
            
            if($request->flag != null){
                $datas = $datas->get()->makeHidden(['id']);
                return $datas;
            }
            
            $datas = $datas->get();

            return DataTables::of($datas)
            ->addColumn('action', function ($data) {
                return view('generalledger.action', compact('data'));
            })
            ->make(true);
        }
        
        $acccodes = MstAccountCodes::get();
        //Audit Log
        $this->auditLogsShort('View List General Ledgers');

        return view('generalledger.index',compact('acccodes', 'ref_number', 'id_account_code', 'source', 'searchDate', 'startdate', 'enddate', 'flag'));
    }
    
    public function modalInfo($id)
    {
        $id = decrypt($id);

        $data = GeneralLedger::where('id', $id)->first();
        $idRef = $data->id_ref;
        $source = $data->source;
        $transactions = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $idRef)
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->where('general_ledgers.source', $source)
            ->get();
        $detailRef = [];

        if ($source = 'Sales (Local)') {
            $detail = TransSales::where('id', $idRef)->first();
            $bankAccount = json_decode($detail->bank_account, true);
            $detailCust = $this->getCustomerFromDN($detail->id_delivery_notes);
            $detailTransSales = TransSalesDetailPrice::where('id_trans_sales_parent', $idRef)->where('type_sales', 'Local')->get();
            $detailRef = [
                'detail' => $detail,
                'bankAccount' => $bankAccount,
                'detailCust' => $detailCust,
                'detailTransSales' => $detailTransSales,
            ];
        } elseif ($source = 'Sales (Export)') {
            $detail = TransSalesExport::where('id', $idRef)->first();
            $bankAccount = json_decode($detail->bank_account, true);
            $approvalDetail = json_decode($detail->approval_detail, true);
            $detailCust = $this->getCustomerFromDN($detail->id_delivery_notes);
            $detailTransSales = TransSalesDetailPrice::where('id_trans_sales_parent', $idRef)->where('type_sales', 'Export')->get();
            $detailRef = [
                'detail' => $detail,
                'bankAccount' => $bankAccount,
                'approvalDetail' => $approvalDetail,
                'detailCust' => $detailCust,
                'detailTransSales' => $detailTransSales,
            ];
        }
        
        return view('generalledger.modal.info', compact('data', 'source', 'transactions', 'detailRef'));
    }

    public function indexOld(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $id_account_code = $request->get('id_account_code');
        $source = $request->get('source');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $acccodes = MstAccountCodes::get();

        $datas = GeneralLedger::select(
                'general_ledgers.ref_number', 'general_ledgers.source', 'general_ledgers.date_transaction', 'master_account_codes.account_code', 
                'master_account_codes.account_name',  'general_ledgers.amount',  'general_ledgers.transaction',  'general_ledgers.created_at',
            )
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->orderBy('general_ledgers.date_transaction','desc')
            ->orderBy('general_ledgers.ref_number');

        if($ref_number != null){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($id_account_code != null){
            $datas = $datas->where('id_account_code', $id_account_code);
        }
        if($source != null){
            if ($source === "Manual") {
                $datas = $datas->whereNotIn('source', [
                    'Sales Transaction',
                    'Purchase Transaction',
                    'Import Transaction',
                ]);
            } else {
                $datas = $datas->where('source', $source);
            }
        }
        if($startdate != null && $enddate != null){
            $datas = $datas->whereDate('general_ledgers.created_at','>=',$startdate)->whereDate('general_ledgers.created_at','<=',$enddate);
        }
        
        if($request->flag != null){
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        
        $datas = $datas->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                // ->addColumn('bulk-action', function ($data) {
                //     $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                //     return $checkBox;
                // })
                // ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List General Ledgers');

        return view('generalledger.index',compact('datas', 'acccodes',
            'ref_number', 'id_account_code', 'source', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function create(Request $request)
    {
        $source = MstDropdowns::where('category', 'Source Accounting')->get();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();

        //Audit Log
        $this->auditLogsShort('View Create New Transaction General Ledger');

        return view('generalledger.create',compact('source', 'accountcodes'));
    }
    
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'transaction_number' => 'unique:general_ledgers,ref_number|required',
            'transaction_date' => 'required',
            'source' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);
        
        $source = ($request->source == "AddNew") ? $request->addsource : $request->source;

        DB::beginTransaction();
        try{
            if($request->source == "AddNew"){
                MstDropdowns::create([
                    'category' => 'Source Accounting',
                    'name_value' => $request->addsource,
                    'code_format' => 'SA',
                ]);
            }

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace('.', '', $item['nominal']);
                        $nominal = str_replace(',', '.', $nominal);
    
                        // Create General Ledger
                        $this->storeGeneralLedger(null, $request->transaction_number, $request->transaction_date,$item['account_code'], $item['type'], $nominal, $source);
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Transaction General Ledger ('. $request->transaction_number . ')');

            DB::commit();
            return redirect()->route('generalledger.index')->with(['success' => 'Success Create New Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Transaction!']);
        }
    }

    public function getData(Request $request)
    {
        $id_ref     = $request->id_ref;
        $ref_number = $request->ref_number;
        $source     = $request->source;
        
        $data = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id_ref)
            ->where('general_ledgers.ref_number', $ref_number)
            ->where('general_ledgers.source', $source)
            ->get();

        return response()->json($data);
    }
}
