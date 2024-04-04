<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;

class GeneralLedgersController extends Controller
{
    use AuditLogsTrait;

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
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id');

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
}
