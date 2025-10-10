<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountTypes;
use App\Models\MstAccountCodes;

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
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('accounttype.action', compact('data'));
                })
                // ->addColumn('bulk-action', function ($data) {
                //     $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                //     return $checkBox;
                // })
                // ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst Account Type');

        return view('accounttype.index',compact('datas',
            'account_type_code', 'account_type_name', 'status', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'account_type_code' => 'required',
            'account_type_name' => 'required'
        ]);

        DB::beginTransaction();
        try{
            $data = MstAccountTypes::create([
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

    public function edit($id)
    {
        $id = decrypt($id);

        $data = MstAccountTypes::where('id', $id)->first();

        //Audit Log
        $this->auditLogsShort('View Edit Mst Account Type ID : '. $id);

        return view('accounttype.edit',compact('data'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $id = decrypt($id);

        $request->validate([
            'account_type_code' => 'required',
            'account_type_name' => 'required',
        ]);

        $databefore = MstAccountTypes::where('id', $id)->first();
        $databefore->account_type_code = $request->account_type_code;
        $databefore->account_type_name = $request->account_type_name;

        if($databefore->isDirty()){
            DB::beginTransaction();
            try{
                $data = MstAccountTypes::where('id', $id)->update([
                    'account_type_code' => $request->account_type_code,
                    'account_type_name' => $request->account_type_name,
                ]);

                //Audit Log
                $this->auditLogsShort('Update Account Type ('. $request->account_type_name . ')');

                DB::commit();
                return redirect()->route('accounttype.index')->with(['success' => 'Success Update Account Type '. $request->account_type_name]);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Account Type!']);
            }
        } else {
            return redirect()->route('accounttype.index')->with(['info' => 'Nothing Change, The data entered is the same as the previous one!']);
        }
    }

    public function activate($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountType = MstAccountTypes::findOrFail($id);
            $accountType->update(['is_active' => 1]);

            // Audit Log
            $this->auditLogsShort('Activate Account Type (' . $accountType->account_type_name . ')');

            DB::commit();
            return redirect()->back()->with('success', 'Success Activate Account Type ' . $accountType->account_type_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to Activate Account Type!');
        }
    }

    public function deactivate($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountType = MstAccountTypes::findOrFail($id);
            $accountType->update(['is_active' => 0]);

            // Audit Log
            $this->auditLogsShort('Activate Account Type (' . $accountType->account_type_name . ')');

            DB::commit();
            return redirect()->back()->with('success', 'Success Deactivate Account Type ' . $accountType->account_type_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to Deactivate Account Type!');
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountType = MstAccountTypes::findOrFail($id);

            // Check if used in MstAccountCodes
            $isUsed = MstAccountCodes::where('id_master_account_types', $id)->exists();
            if ($isUsed) {
                return redirect()->back()->with('info', 'Cannot delete, Account Type "' . $accountType->account_type_name . '" is still used in Account Codes.');
            }

            $accountType->delete();

            // Audit Log
            $this->auditLogsShort('Delete Account Type (' . $accountType->account_type_name . ')');

            DB::commit();
            return redirect()->back()->with('success', 'Success Delete Account Type ' . $accountType->account_type_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to Delete Account Type!');
        }
    }

    public function deleteselected(Request $request)
    {
        $idSelected = $request->input('idChecked', []);

        DB::beginTransaction();
        try {
            // Get account types with codes
            $accountTypes = MstAccountTypes::whereIn('id', $idSelected)->get(['id', 'account_type_code', 'account_type_name']);

            if ($accountTypes->isEmpty()) {
                return response()->json(['message' => 'No data selected', 'type' => 'info'], 200);
            }

            // Check if any are used in MstAccountCodes
            $used = MstAccountCodes::whereIn('id_master_account_types', $accountTypes->pluck('id'))->exists();
            if ($used) {
                return response()->json(['message' => 'Cannot delete, one or more Account Types are still used in Account Codes.', 'type' => 'info'], 200);
            }

            // Proceed with delete
            MstAccountTypes::whereIn('id', $accountTypes->pluck('id'))->delete();

            // Audit Log
            $this->auditLogsShort('Delete Account Types Selected: ' . $accountTypes->pluck('account_type_code')->implode(', '));

            DB::commit();
            return response()->json([
                'message' => 'Successfully Deleted Data: ' . $accountTypes->pluck('account_type_code')->implode(', '),
                'type'    => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to Delete Data', 'type' => 'error'], 500);
        }
    }

    public function deactiveselected(Request $request)
    {
        $idSelected = $request->input('idChecked', []);

        DB::beginTransaction();
        try {
            $accountTypes = MstAccountTypes::whereIn('id', $idSelected)->get(['id', 'account_type_code']);

            if ($accountTypes->isEmpty()) {
                return response()->json(['message' => 'No data selected', 'type' => 'info'], 200);
            }

            // Bulk deactivate
            MstAccountTypes::whereIn('id', $accountTypes->pluck('id'))
                ->update(['is_active' => 0]);

            // Audit Log
            $this->auditLogsShort('Deactivate Account Types Selected: ' . $accountTypes->pluck('account_type_code')->implode(', '));

            DB::commit();
            return response()->json([
                'message' => 'Successfully Deactivated Data: ' . $accountTypes->pluck('account_type_code')->implode(', '),
                'type'    => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to Deactivate Data', 'type' => 'error'], 500);
        }
    }
}
