<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\AuditLogsTrait;
use Yajra\DataTables\Facades\DataTables;

use App\Models\MasterHpp;
use App\Models\MasterNeraca;

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

    public function hpp(Request $request)
    {
        $datas = MasterHpp::select(
                'master_hpps.account',
                'master_hpps.head2',
                'master_hpps.head1',
                'master_account_codes.account_name'
            )
            ->leftJoin('master_account_codes', 'master_hpps.account_sub', '=', 'master_account_codes.id')
            ->groupBy(
                'master_hpps.account',
                'master_hpps.head2',
                'master_hpps.head1',
                'master_account_codes.account_name'
            )
            ->orderBy('master_hpps.id')
            ->get();

        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List HPP Entity List');

        return view('entitylist.hpp.index');

    }
}
