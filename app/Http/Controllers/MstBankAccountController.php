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
        // Datatables
        if ($request->ajax()) {
            $datas = MstBankAccount::orderBy('created_at','desc')->get();
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('bankaccount.action', compact('data'));
                })->make(true);
        }

        //Audit Log
        $this->auditLogsShort('View List Mst Bank Account');
        return view('bankaccount.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'bank_name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'currency' => 'required',
            'swift_code' => 'required',
            'branch' => 'required',
        ]);

        DB::beginTransaction();
        try{
            MstBankAccount::create([
                'code' => $request->code,
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
            $this->auditLogsShort('Add New Bank Account');

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Add New Bank Account']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Add!']);
        }
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $fields = ['code', 'bank_name', 'account_name', 'account_number', 'currency', 'swift_code', 'branch'];

        // Validation
        $request->validate(array_fill_keys($fields, 'required'));
        // Data
        $bankAccount = MstBankAccount::findOrFail($id);
        // Check Changes
        if ($bankAccount->only($fields) == $request->only($fields)) {
            return back()->with('info', 'No Changes Detected!');
        }

        DB::beginTransaction();
        try {
            $bankAccount->update($request->only($fields));

            // Audit Log
            $this->auditLogsShort("Update Bank Account ID : $id");
            DB::commit();
            return back()->with('success', 'Success Update Bank Account');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('fail', 'Failed to Update!');
        }
    }

    public function activate($id)
    {
        return $this->toggleStatus($id, 1, 'Activate');
    }
    public function deactivate($id)
    {
        return $this->toggleStatus($id, 0, 'Deactivate');
    }
    private function toggleStatus($id, $status, $action)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $data = MstBankAccount::findOrFail($id);
            $data->update(['is_active' => $status]);

            $this->auditLogsShort("$action Master Bank ({$data->bank_name})");

            DB::commit();
            return redirect()->back()->with('success', "Success $action Master Bank {$data->bank_name}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', "Failed to $action Master Bank!");
        }
    }


    // MODAL SECTION
    public function modalAdd()
    {
        $currencies = MstCurrencies::get();
        return view('bankaccount.modal.new', compact('currencies'));
    }
    public function modalInfo($id)
    {
        $id = decrypt($id);
        $data = MstBankAccount::findOrFail($id);
        return view('bankaccount.modal.info', compact('data'));
    }
    public function modalEdit($id)
    {
        $id = decrypt($id);
        $data = MstBankAccount::findOrFail($id);

        // Get active currencies
        $active = MstCurrencies::where('is_active', 1)->get();
        // Get current currency (only if not inside active)
        $current = $active->firstWhere('currency_code', $data->currency) ?? MstCurrencies::where('currency_code', $data->currency)->first();
        // Merge + remove duplicate by currency_code
        $currencies = collect([$current])->merge($active)->unique('currency_code')->values();

        return view('bankaccount.modal.edit', compact('data', 'currencies'));
    }
    public function modalActivate($id)
    {
        $id = decrypt($id);
        $data = MstBankAccount::findOrFail($id);

        return view('bankaccount.modal.activate', compact('data'));
    }
    public function modalDeactivate($id)
    {
        $id = decrypt($id);
        $data = MstBankAccount::findOrFail($id);

        return view('bankaccount.modal.deactivate', compact('data'));
    }
}
