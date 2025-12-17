<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountTypes;
use App\Models\MstAccountCodes;

// Traits
use App\Traits\AuditLogsTrait;

class MstAccountTypesController extends Controller
{
    use AuditLogsTrait;

    public function index(Request $request)
    {
        $account_type_code = $request->get('account_type_code');
        $account_type_name = $request->get('account_type_name');
        $status = $request->get('status');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');
        
        // Datatables
        if ($request->ajax()) {
            $datas = MstAccountTypes::select(
                'master_account_types.*'
            );

            if($account_type_code != null){
                $datas = $datas->where('account_type_code', 'like', '%'.$account_type_code.'%');
            }
            if($account_type_name != null){
                $datas = $datas->where('account_type_name', 'like', '%'.$account_type_name.'%');
            }
            if($status != null){
                $datas = $datas->where('is_active', $status);
            }
            if($startdate != null && $enddate != null){
                $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
            }
            
            if($request->flag != null){
                $datas = $datas->orderBy('master_account_types.created_at','asc')->get()->makeHidden(['id']);
                return $datas;
            }

            $datas = $datas->orderBy('master_account_types.created_at','desc')->get();
            
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('accounttype.action', compact('data'));
                })
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst Account Type');

        return view('accounttype.index',compact('account_type_code', 'account_type_name', 'status', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_type_code' => 'required',
            'account_type_name' => 'required'
        ]);

        DB::beginTransaction();
        try{
            MstAccountTypes::create([
                'account_type_code' => $request->account_type_code,
                'account_type_name' => $request->account_type_name,
                'is_active' => '1'
            ]);

            //Audit Log
            $this->auditLogsShort('Create New Account Type ('. $request->account_type_name . ')');
            DB::commit();
            return redirect()->back()->with(['success' => 'Success Create New Account Type']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Account Type!']);
        }
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $fields = ['account_type_code', 'account_type_name'];

        // Validation
        $request->validate(array_fill_keys($fields, 'required'));
        // Data
        $data = MstAccountTypes::findOrFail($id);
        // Check Changes
        if ($data->only($fields) == $request->only($fields)) {
            return back()->with('info', 'No Changes Detected!');
        }

        DB::beginTransaction();
        try {
            $data->update($request->only($fields));

            // Audit Log
            $this->auditLogsShort("Update Account Type ID : $id");
            DB::commit();
            return back()->with('success', 'Success Update Account Type');
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
            $data = MstAccountTypes::findOrFail($id);
            $data->update(['is_active' => $status]);

            $this->auditLogsShort("$action Account Type ({$data->account_type_name})");

            DB::commit();
            return redirect()->back()->with('success', "Success $action Account Type {$data->account_type_name}");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', "Failed to $action Account Type!");
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountType = MstAccountTypes::findOrFail($id);
            // Check if related
            if (MstAccountCodes::where('id_master_account_types', $id)->exists()) {
                return redirect()->back()->with('info', 'Cannot delete, Account Type "' . $accountType->account_type_name . '" is still used in Account Codes.');
            }
            $accountType->delete();

            $this->auditLogsShort("Delete Account Type ({$accountType->account_type_name})");
            DB::commit();
            return redirect()->back()->with('success', 'Success Delete Account Type ' . $accountType->account_type_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to Delete Account Type!');
        }
    }

    // MODAL SECTION
    public function modalAdd()
    {
        return view('accounttype.modal.new');
    }
    public function modalInfo($id)
    {
        $id = decrypt($id);
        $data = MstAccountTypes::findOrFail($id);
        return view('accounttype.modal.info', compact('data'));
    }
    public function modalEdit($id)
    {
        $id = decrypt($id);
        $data = MstAccountTypes::findOrFail($id);
        return view('accounttype.modal.edit', compact('data'));
    }
    public function modalActivate($id)
    {
        $id = decrypt($id);
        $data = MstAccountTypes::findOrFail($id);
        return view('accounttype.modal.activate', compact('data'));
    }
    public function modalDeactivate($id)
    {
        $id = decrypt($id);
        $data = MstAccountTypes::findOrFail($id);
        return view('accounttype.modal.deactivate', compact('data'));
    }
    public function modalDelete($id)
    {
        $id = decrypt($id);
        $data = MstAccountTypes::findOrFail($id);
        return view('accounttype.modal.delete', compact('data'));
    }
}
