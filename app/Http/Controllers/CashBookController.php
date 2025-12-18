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
use App\Models\GoodReceiptNote;
use App\Models\GoodReceiptNoteDetail;
use App\Models\MstBankAccount;
use App\Models\TransCashBook;

class CashBookController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    public function index(Request $request)
    {
        $trans_number = $request->get('trans_number');
        $invoice_number = $request->get('invoice_number');
        $tax_invoice_number = $request->get('tax_invoice_number');
        $type = $request->get('type');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');
        if (is_null($searchDate)) {
            $searchDate = "Custom";
            $startdate = now()->startOfYear()->format('Y-m-d');
            $enddate = now()->endOfYear()->format('Y-m-d');
        }

        // Datatables
        if ($request->ajax()) {
            $datas = TransCashBook::select(
                    'trans_cash_book.*', 
                    'master_account_codes.account_code', 
                    'master_account_codes.account_name',
                    'general_ledgers.transaction',
                    'general_ledgers.amount',
                    'general_ledgers.note',
                    DB::raw("'Cash Book' as source")
                )
                ->leftjoin('general_ledgers', 'trans_cash_book.id', 'general_ledgers.id_ref')
                ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
                ->where('general_ledgers.source', 'Cash Book')
                ->orderBy('general_ledgers.created_at','desc');
            
            if($trans_number != null){
                $datas = $datas->where('transaction_number', 'like', '%'.$trans_number.'%');
            }
            if($invoice_number != null){
                $datas = $datas->where('invoice_number', 'like', '%'.$invoice_number.'%');
            }
            if($tax_invoice_number != null){
                $datas = $datas->where('tax_invoice_number', 'like', '%'.$tax_invoice_number.'%');
            }
            if($type != null){
                $datas = $datas->where('trans_cash_book.type', $type);
            }
            if($startdate != null && $enddate != null){
                $datas = $datas->whereDate('trans_cash_book.created_at','>=',$startdate)->whereDate('trans_cash_book.created_at','<=',$enddate);
            }
            
            if($request->flag != null){
                $datas = $datas->get()->makeHidden(['id']);
                return $datas;
            }
            
            $datas = $datas->get();

            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('cashbook.action', compact('data'));
                })->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Cash Book');

        return view('cashbook.index',compact('trans_number', 'invoice_number', 'tax_invoice_number', 'type', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function create(Request $request)
    {
        $bankAccounts = MstBankAccount::where('is_active', 1)->get();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();

        //Audit Log
        $this->auditLogsShort('View Create New Cash Book');

        return view('cashbook.create',compact('bankAccounts', 'accountcodes'));
    }

    public function store(Request $request)
    {
        dd(($request->all()));
    }
}
