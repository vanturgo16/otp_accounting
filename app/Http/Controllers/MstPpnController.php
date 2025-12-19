<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Traits
use App\Traits\AuditLogsTrait;

// Model
use App\Models\MstPpn;

class MstPpnController extends Controller
{
    use AuditLogsTrait;

    public function index(Request $request)
    {
        // Datatables
        if ($request->ajax()) {
            $datas = MstPpn::orderBy('created_at','desc')->get();
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('ppn.action', compact('data'));
                })
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Mst PPN');
        return view('ppn.index');
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $fields = ['value'];

        // Validation
        $request->validate(array_fill_keys($fields, 'required'));
        // Data
        $data = MstPpn::findOrFail($id);
        // Check Changes
        if ($data->only($fields) == $request->only($fields)) {
            return back()->with('info', 'No Changes Detected!');
        }

        DB::beginTransaction();
        try {
            $data->update($request->only($fields));

            // Audit Log
            $this->auditLogsShort("Update Default PPN ID : $id");
            DB::commit();
            return back()->with('success', 'Success Update Default PPN');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('fail', 'Failed to Update!');
        }
    }

    // MODAL SECTION
    public function modalEdit($id)
    {
        $id = decrypt($id);
        $data = MstPpn::findOrFail($id);

        return view('ppn.modal.edit', compact('data'));
    }
}
