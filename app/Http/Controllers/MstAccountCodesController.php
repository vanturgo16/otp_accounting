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
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        // $acctypes = MstAccountTypes::where('is_active', 1)->get();
        $acctypes = MstAccountTypes::get();

        $datas = MstAccountCodes::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'master_account_codes.*', 'master_account_types.account_type_code', 'master_account_types.account_type_name'
            )
            ->leftjoin('master_account_types', 'master_account_codes.id_master_account_types', 'master_account_types.id')
            ->orderBy('master_account_codes.created_at','desc');

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
                ->addColumn('action', function ($data) use ($acctypes){
                    return view('accountcode.action', compact('data', 'acctypes'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst Account Code');

        return view('accountcode.index',compact('datas', 'acctypes',
            'account_code', 'account_name', 'id_master_account_types', 'status', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'account_code' => 'required',
            'account_name' => 'required',
            'id_master_account_types' => 'required',
            'opening_balance' => 'required'
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
                'balance' => $opening_balance,
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
        // $acctypes = MstAccountTypes::where('is_active', 1)->get();
        $acctypes = MstAccountTypes::get();

        //Audit Log
        $this->auditLogsShort('View Edit Account Code ID ='. $id);

        return view('accountcode.edit',compact('data', 'acctypes'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $id = decrypt($id);

        $request->validate([
            'account_code' => 'required',
            'account_name' => 'required',
            'id_master_account_types' => 'required'
        ]);
        $opening_balance = str_replace('.', '', $request->opening_balance);
        $opening_balance = str_replace(',', '.', $opening_balance);

        $databefore = MstAccountCodes::where('id', $id)->first();
        $databefore->account_code = $request->account_code;
        $databefore->account_name = $request->account_name;
        $databefore->id_master_account_types = $request->id_master_account_types;
        $databefore->opening_balance = $opening_balance;

        if($databefore->isDirty()){
            DB::beginTransaction();
            try{
                $data = MstAccountCodes::where('id', $id)->update([
                    'account_code' => $request->account_code,
                    'account_name' => $request->account_name,
                    'id_master_account_types' => $request->id_master_account_types,
                    'opening_balance' => $opening_balance,
                    'balance' => $opening_balance
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
        try{
            $data = MstAccountCodes::where('id', $id)->update([
                'is_active' => 1
            ]);

            $name = MstAccountCodes::where('id', $id)->first();

            //Audit Log
            $this->auditLogsShort('Activate Account Type ('. $name->account_name . ')');

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Activate Account Code ' . $name->account_name]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Activate Account Code ' . $name->account_name .'!']);
        }
    }

    public function deactivate($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try{
            $data = MstAccountCodes::where('id', $id)->update([
                'is_active' => 0
            ]);

            $name = MstAccountCodes::where('id', $id)->first();
            
            //Audit Log
            $this->auditLogsShort('Deactivate Account Code ('. $name->account_name . ')');

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Deactivate Account Code ' . $name->account_name]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Deactivate Account Code ' . $name->account_name .'!']);
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try{
            $data = MstAccountCodes::where('id', $id)->delete();
            
            //Audit Log
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
        $idselected = $request->input('idChecked');

        DB::beginTransaction();
        try{
            $account_code = MstAccountCodes::whereIn('id', $idselected)->pluck('account_code')->toArray();
            $delete = MstAccountCodes::whereIn('id', $idselected)->delete();

            //Audit Log
            $this->auditLogsShort('Delete Master Account Code Selected : ' . implode(', ', $account_code));

            DB::commit();
            return response()->json(['message' => 'Successfully Deleted Data : ' . implode(', ', $account_code), 'type' => 'success'], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to Delete Data', 'type' => 'error'], 500);
        }
    }

    public function deactiveselected(Request $request)
    {
        $idselected = $request->input('idChecked');

        DB::beginTransaction();
        try{
            $account_code = MstAccountCodes::whereIn('id', $idselected)->pluck('account_code')->toArray();
            $update = MstAccountCodes::whereIn('id', $idselected)
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
