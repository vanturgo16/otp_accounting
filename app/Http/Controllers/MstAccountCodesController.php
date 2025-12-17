<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;

// Model
use App\Models\MstAccountCodes;
use App\Models\MstAccountTypes;

class MstAccountCodesController extends Controller
{
    use AuditLogsTrait, GeneralLedgerTrait;

    public function index(Request $request)
    {
        $account_code = $request->get('account_code');
        $account_name = $request->get('account_name');
        $id_master_account_types = $request->get('id_master_account_types');
        $status = $request->get('status');
        $is_used = $request->get('is_used');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        if ($request->ajax()) {
            $datas = MstAccountCodes::select(
                'master_account_types.account_type_code', 'master_account_types.account_type_name', 'master_account_codes.*'
            )
            ->leftjoin('master_account_types', 'master_account_codes.id_master_account_types', 'master_account_types.id')
            ->orderBy('master_account_types.account_type_code', 'asc')
            ->orderBy('master_account_codes.account_code', 'asc');
            

            if($account_code != null){
                $datas = $datas->where('account_code', 'like', '%'.$account_code.'%');
            }
            if($account_name != null){
                $datas = $datas->where('account_name', 'like', '%'.$account_name.'%');
            }
            if($id_master_account_types != null){
                $datas = $datas->where('id_master_account_types', $id_master_account_types);
            }
            if($status != null){
                $datas = $datas->where('master_account_codes.is_active', $status);
            }
            if($is_used != null){
                $statusIsUsed = $is_used == 0 ? null : 1;
                $datas = $datas->where('master_account_codes.is_used', $statusIsUsed);
            }
            if($startdate != null && $enddate != null){
                $datas = $datas->whereDate('master_account_codes.created_at','>=',$startdate)->whereDate('master_account_codes.created_at','<=',$enddate);
            }
            
            if($request->flag != null){
                $datas = $datas->get()->makeHidden(['id', 'id_master_account_types']);
                return $datas;
            }
            
            $datas = $datas->get();

            return DataTables::of($datas)
                ->addColumn('action', function ($data) {
                    return view('accountcode.action', compact('data'));
                })
                ->make(true);
        }
        $all_acctypes = MstAccountTypes::get();
        $code_account_types = null;
        if($id_master_account_types) {
            $code_account_types = MstAccountTypes::where('id', $id_master_account_types)->first()->account_type_code;
        }

        //Audit Log
        $this->auditLogsShort('View List Mst Account Code');

        return view('accountcode.index',compact('all_acctypes',
            'account_code', 'account_name', 'id_master_account_types', 'code_account_types', 'status', 'is_used', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_code' => 'required',
            'account_name' => 'required',
            'id_master_account_types' => 'required',
            'opening_balance' => 'required',
            'type' => 'required'
        ]);

        $opening_balance = $this->normalizeOpeningBalance($request->opening_balance);

        DB::beginTransaction();
        try{
            MstAccountCodes::create([
                'account_code' => $request->account_code,
                'account_name' => $request->account_name,
                'id_master_account_types' => $request->id_master_account_types,
                'opening_balance' => $opening_balance,
                'opening_balance_type' => $request->type,
                'balance' => $opening_balance,
                'balance_type' => $request->type,
                'is_active' => '1'
            ]);

            //Audit Log
            $this->auditLogsShort('Create New Account Code ('. $request->account_name . ')');

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Create New Account Code']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Account Code!']);
        }
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $data = MstAccountCodes::where('id', $id)->first();
        $acctypesActive = MstAccountTypes::where('is_active', 1)->get();
        $acctypesUsed = MstAccountTypes::where('id', $data->id_master_account_types)->first();
        $acctypes = $acctypesActive->merge(collect([$acctypesUsed]))->unique('id')->values();

        //Audit Log
        $this->auditLogsShort('View Edit Account Code ID ='. $id);

        return view('accountcode.edit',compact('data', 'acctypesUsed', 'acctypes'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $request->validate([
            'account_code' => 'required',
            'account_name' => 'required',
            'id_master_account_types' => 'required',
            'opening_balance' => 'required',
            'type' => 'required'
        ]);

        $opening_balance = $this->normalizeOpeningBalance($request->opening_balance);
        $databefore = MstAccountCodes::findOrFail($id);
        if ($databefore->is_used == 1) {
            $databefore->account_name = $request->account_name;
        } 
        else {
            $databefore->account_code = $request->account_code;
            $databefore->account_name = $request->account_name;
            $databefore->id_master_account_types = (int) $request->id_master_account_types;
            $databefore->opening_balance = $opening_balance;
            $databefore->opening_balance_type = $request->type;
            $databefore->balance = $opening_balance;
            $databefore->balance_type = $request->type;
        }

        if (! $databefore->isDirty()) {
            return back()->with('info', 'No changes detected.');
        }

        DB::beginTransaction();
        try {
            $databefore->save();
            
            //Audit Log
            $this->auditLogsShort("Update Account Code ID : $id");
            DB::commit();
            return back()->with(['success' => 'Success Update Account Code']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
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
            $data = MstAccountCodes::findOrFail($id);
            $data->update(['is_active' => $status]);

            $this->auditLogsShort("$action Account Code ({$data->account_type_name})");

            DB::commit();
            return redirect()->back()->with('success', "Success $action Account Code {$data->account_type_name}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', "Failed to $action Account Code!");
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountCode = MstAccountCodes::findOrFail($id);
            // Check
            if ($accountCode->is_used == 1) {
                return redirect()->back()->with('info', 'Cannot Delete, Account Code has used in transaction.');
            }
            $accountCode->delete();

            // Audit Log
            $this->auditLogsShort("Delete Account Code ID : $id");

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Delete Account Code']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Delete Account Code!']);
        }
    }


    // MODAL SECTION
    public function modalAdd()
    {
        $accTypeActives = MstAccountTypes::where('is_active', 1)->get();
        return view('accountcode.modal.new', compact('accTypeActives'));
    }
    public function modalInfo($id)
    {
        $id = decrypt($id);
        $data = MstAccountCodes::select(
                'master_account_types.account_type_code', 'master_account_types.account_type_name', 'master_account_codes.*'
            )
            ->leftjoin('master_account_types', 'master_account_codes.id_master_account_types', 'master_account_types.id')
            ->where('master_account_codes.id', $id)
            ->first();

        return view('accountcode.modal.info', compact('data'));
    }
    public function modalEdit($id)
    {
        $id = decrypt($id);
        $data = MstAccountCodes::select(
                'master_account_types.account_type_code', 'master_account_types.account_type_name', 'master_account_codes.*'
            )
            ->leftjoin('master_account_types', 'master_account_codes.id_master_account_types', 'master_account_types.id')
            ->where('master_account_codes.id', $id)
            ->first();
        
        // Get active acc type
        $active = MstAccountTypes::where('is_active', 1)->get();
        // Get current acc type (only if not inside active)
        $current = $active->firstWhere('id', $data->id_master_account_types) ?? MstAccountTypes::where('id', $data->id_master_account_types)->first();
        // Merge + remove duplicate by id_master_account_types
        $accTypeActives = collect([$current])->merge($active)->unique('id')->values();

        return view('accountcode.modal.edit', compact('data', 'accTypeActives'));
    }
    public function modalActivate($id)
    {
        $id = decrypt($id);
        $data = MstAccountCodes::findOrFail($id);
        return view('accountcode.modal.activate', compact('data'));
    }
    public function modalDeactivate($id)
    {
        $id = decrypt($id);
        $data = MstAccountCodes::findOrFail($id);
        return view('accountcode.modal.deactivate', compact('data'));
    }
    public function modalDelete($id)
    {
        $id = decrypt($id);
        $data = MstAccountCodes::findOrFail($id);
        return view('accountcode.modal.delete', compact('data'));
    }
}
