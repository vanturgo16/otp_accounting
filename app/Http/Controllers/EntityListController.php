<?php

namespace App\Http\Controllers;

use App\Models\MasterNeraca;
use Illuminate\Http\Request;
use App\Traits\AuditLogsTrait;
use Yajra\DataTables\Facades\DataTables;

class EntityListController extends Controller
{
    use AuditLogsTrait;
    public function neraca(Request $request)
    {
        $datas = MasterNeraca::select(
                'master_neracas.account',
                'master_neracas.head2',
                'master_neracas.head1',
                'master_account_codes.account_name'
            )
            ->leftJoin('master_account_codes', 'master_neracas.account_sub', '=', 'master_account_codes.id')
            ->groupBy(
                'master_neracas.account',
                'master_neracas.head2',
                'master_neracas.head1',
                'master_account_codes.account_name'
            )
            ->orderBy('master_neracas.id')
            ->get();

        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Neraca Entity List');

        return view('entitylist.neraca.index');

    }
}
