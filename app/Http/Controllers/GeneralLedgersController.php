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

        $acccodes = MstAccountCodes::get();

        $datas = GeneralLedger::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name'
            )
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->orderBy('general_ledgers.created_at','desc');

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
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }
        
        if($request->flag != null){
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        
        $datas = $datas->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
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
        $accountcodes = MstAccountCodes::get();

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
                        $this->storeGeneralLedger($request->transaction_number, $request->transaction_date,$item['account_code'], $item['type'], $nominal, $source);
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
}
