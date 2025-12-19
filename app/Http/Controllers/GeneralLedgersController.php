<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

// Trait
use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;

class GeneralLedgersController extends Controller
{
    use AuditLogsTrait, GeneralLedgerTrait;

    // MODAL VIEW
    public function modalInfo($source, $id)
    {
        if ($source === 'Sales(Local)') {
            return redirect()->route('transsales.local.modal.info', $id);
        } elseif ($source === 'Sales(Export)') {
            return redirect()->route('transsales.export.modal.info', $id);
        } elseif ($source === 'Purchase') {
            return redirect()->route('transpurchase.modal.info', $id);
        } elseif ($source === 'CashBook') {
            return redirect()->route('cashbook.modal.info', $id);
        }
        abort(400, 'Unknown source type');
    }

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
            $startdate = now()->startOfYear()->format('Y-m-d');
            $enddate = now()->endOfYear()->format('Y-m-d');
        }
        
        if ($request->ajax()) {
            $datas = GeneralLedger::select(
                'general_ledgers.id', 'general_ledgers.id_ref', 'general_ledgers.ref_number', 'general_ledgers.source', 'general_ledgers.ref_source', 'general_ledgers.date_transaction', 'master_account_codes.account_code', 
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
}
