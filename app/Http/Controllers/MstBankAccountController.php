<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstBankAccount;
use App\Models\MstCurrencies;

class MstBankAccountController extends Controller
{
    use AuditLogsTrait;

    public function index(Request $request)
    {
        $data = MstBankAccount::where('is_active', 1)->first();
        $currencies = MstCurrencies::get();

        $datas = MstBankAccount::orderBy('created_at','desc')->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst Bank Account');

        return view('bankaccount.index', compact('data', 'currencies'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'currency' => 'required',
            'swift_code' => 'required',
            'branch' => 'required',
        ]);

        DB::beginTransaction();
        try{
            MstBankAccount::where('is_active', 1)->update(['is_active' => 0]);
            MstBankAccount::create([
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'currency' => $request->currency,
                'swift_code' => $request->swift_code,
                'branch' => $request->branch,
                'is_active' => 1,
                'created_by' => auth()->user()->email
            ]);

            //Audit Log
            $this->auditLogsShort('Update Bank Account');

            DB::commit();

            return redirect()->back()->with(['success' => 'Success Update Bank Account']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Update!']);
        }
    }
}
