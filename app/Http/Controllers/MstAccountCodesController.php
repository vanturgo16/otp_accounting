<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountCodes;
use App\Models\MstAccountTypes;

class MstAccountCodesController extends Controller
{
    use AuditLogsTrait;

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

        // $acctypes = MstAccountTypes::where('is_active', 1)->get();
        $acctypes = MstAccountTypes::get();

        $datas = MstAccountCodes::select(
                'master_account_codes.*', 'master_account_types.account_type_code', 'master_account_types.account_type_name'
            )
            ->leftjoin('master_account_types', 'master_account_codes.id_master_account_types', 'master_account_types.id')
            ->orderBy('master_account_codes.id','desc')
            ->orderBy('master_account_codes.id_master_account_types');

        if($account_code != null){
            $datas = $datas->where('account_code', 'like', '%'.$account_code.'%');
        }
        if($account_name != null){
            $datas = $datas->where('account_name', 'like', '%'.$account_name.'%');
        }
        if($account_name != null){
            $datas = $datas->where('id_master_account_types', 'like', '%'.$id_master_account_types.'%');
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
            $datas = $datas->get()->makeHidden(['id']);
            return $datas;
        }
        
        $datas = $datas->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data) use ($acctypes){
                    return view('accountcode.action', compact('data', 'acctypes'));
                })
                // ->addColumn('bulk-action', function ($data) {
                //     $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                //     return $checkBox;
                // })
                // ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst Account Code');

        return view('accountcode.index',compact('datas', 'acctypes',
            'account_code', 'account_name', 'id_master_account_types', 'status', 'is_used', 'searchDate', 'startdate', 'enddate', 'flag'));
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

        $opening_balance = str_replace('.', '', $request->opening_balance);
        $opening_balance = str_replace(',', '.', $opening_balance);

        DB::beginTransaction();
        try{
            $data = MstAccountCodes::create([
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
        $opening_balance = str_replace('.', '', $request->opening_balance);
        $opening_balance = str_replace(',', '.', $opening_balance);

        $databefore = MstAccountCodes::where('id', $id)->first();
        $databefore->account_code = $request->account_code;
        $databefore->account_name = $request->account_name;
        $databefore->id_master_account_types = $request->id_master_account_types;
        $databefore->opening_balance = $opening_balance;
        $databefore->balance_type = $request->type;

        if($databefore->isDirty()){
            DB::beginTransaction();
            try{
                $data = MstAccountCodes::where('id', $id)->update([
                    'account_code' => $request->account_code,
                    'account_name' => $request->account_name,
                    'id_master_account_types' => $request->id_master_account_types,
                    'opening_balance' => $opening_balance,
                    'opening_balance_type' => $request->type,
                    'balance' => $opening_balance,
                    'balance_type' => $request->type,
                ]);

                //Audit Log
                $this->auditLogsShort('Update Account Code ('. $request->account_name . ')');

                DB::commit();
                return redirect()->route('accountcode.index')->with(['success' => 'Success Update Account Code']);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Account Code!']);
            }
        } else {
            return redirect()->route('accountcode.index')->with(['info' => 'Nothing Change, The data entered is the same as the previous one!']);
        }
    }

    public function activate($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountCode = MstAccountCodes::findOrFail($id);
            $accountCode->update(['is_active' => 1]);

            // Audit Log
            $this->auditLogsShort('Activate Account Code (' . $accountCode->account_name . ')');

            DB::commit();
            return redirect()->back()->with('success', 'Success Activate Account Code ' . $accountCode->account_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to Activate Account Code!');
        }
    }

    public function deactivate($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try {
            $accountCode = MstAccountCodes::findOrFail($id);
            $accountCode->update(['is_active' => 0]);

            // Audit Log
            $this->auditLogsShort('Deactivate Account Code (' . $accountCode->account_name . ')');

            DB::commit();
            return redirect()->back()->with('success', 'Success Deactivate Account Code ' . $accountCode->account_name);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Failed to Deactivate Account Code!');
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
            $this->auditLogsShort('Delete Mst Account Code');

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Delete Account Code']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Delete Account Code!']);
        }
    }

    public function deleteselected(Request $request)
    {
        $idSelected = $request->input('idChecked', []);

        DB::beginTransaction();
        try {
            $accountCodes = MstAccountCodes::whereIn('id', $idSelected)->get(['id', 'account_code', 'is_used']);

            if ($accountCodes->isEmpty()) {
                return response()->json(['message' => 'No data selected', 'type' => 'info'], 200);
            }

            // Check if any account code is marked as used
            $usedCodes = $accountCodes->where('is_used', 1)->pluck('account_code');
            if ($usedCodes->isNotEmpty()) {
                return response()->json([
                    'message' => 'Cannot delete, these Account Codes are in use: ' . $usedCodes->implode(', '),
                    'type'    => 'info'
                ], 200);
            }

            // Proceed with delete
            MstAccountCodes::whereIn('id', $accountCodes->pluck('id'))->delete();

            // Audit Log
            $this->auditLogsShort('Delete Master Account Codes: ' . $accountCodes->pluck('account_code')->implode(', '));

            DB::commit();
            return response()->json([
                'message' => 'Successfully Deleted Data: ' . $accountCodes->pluck('account_code')->implode(', '),
                'type'    => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to Delete Data', 'type' => 'error'], 500);
        }
    }

    public function deactiveselected(Request $request)
    {
        $idselected = $request->input('idChecked');

        DB::beginTransaction();
        try{
            $account_code = MstAccountCodes::whereIn('id', $idselected)->pluck('account_code')->toArray();
            MstAccountCodes::whereIn('id', $idselected)
                ->update([
                    'is_active' => 0
                ]);

            //Audit Log
            $this->auditLogsShort('Deactive Master Account Code Selected : ' . implode(', ', $account_code));

            DB::commit();
            return response()->json(['message' => 'Successfully Deactivate Data : ' . implode(', ', $account_code), 'type' => 'success'], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to Deactive Data', 'type' => 'error'], 500);
        }
    }
}
