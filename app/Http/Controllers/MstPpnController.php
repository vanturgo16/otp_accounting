<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstPpn;

class MstPpnController extends Controller
{
    use AuditLogsTrait;

    public function index(Request $request)
    {
        $datas = MstPpn::orderBy('created_at','desc')->get();
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst PPN');

        return view('ppn.index');
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'tax_name' => 'required',
            'value' => 'required'
        ]);

        DB::beginTransaction();
        try{
            MstPpn::where('tax_name', $request->tax_name)->where('is_active', 1)->update(['is_active' => 0]);
            MstPpn::create([
                'tax_name' => $request->tax_name,
                'value' => $request->value,
                'is_active' => 1,
                'created_by' => auth()->user()->email
            ]);

            //Audit Log
            $this->auditLogsShort('Update Tax ('. $request->tax_name . ' = '.$request->value.')');

            DB::commit();

            return redirect()->back()->with(['success' => 'Success Update Tax']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Update!']);
        }
    }
}
