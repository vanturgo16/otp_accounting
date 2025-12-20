<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Traits
use App\Traits\AuditLogsTrait;

// Model
use App\Models\TransCashBook;
use App\Models\TransPurchase;
use App\Models\TransSales;
use App\Models\TransSalesExport;
use App\Models\User;

class DashboardController extends Controller
{
    use AuditLogsTrait;

    public function __construct()
    {
        $this->middleware(['permission:Akunting_dashboard']);
    }
    public function index(){
        return view('dashboard.index');
    }

    public function getDataSummary(Request $request)
    {
        $dateFrom = $request->dateFrom ? Carbon::parse($request->dateFrom)->startOfDay() : now()->startOfMonth();
        $dateTo   = $request->dateTo   ? Carbon::parse($request->dateTo)->endOfDay()   : now()->endOfMonth();

        if ($request->ajax()) {
            $countSTLocal = TransSales::whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $countSTExport = TransSalesExport::whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $countPT = TransPurchase::whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $countCB = TransCashBook::whereBetween('created_at', [$dateFrom, $dateTo])->count();

            return response()->json([
                'countSTLocal'  => $countSTLocal,
                'countSTExport' => $countSTExport,
                'countPT'       => $countPT,
                'countCB'       => $countCB,
            ]);
        }
    }

    public function switchTheme(Request $request)
    {
        DB::beginTransaction();
        try {
            $statusBefore = User::where('id', auth()->user()->id)->first()->is_darkmode;
            $status = ($statusBefore == 1) ? null : 1;
            User::where('id', auth()->user()->id)->update(['is_darkmode' => $status]);

            //Audit Log
            $this->auditLogsShort('Switch Theme');
            DB::commit();
            return redirect()->back()->with(['success' => 'Success Switch Theme']);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['fail' => 'Failed Switch Theme']);
        }
    }
}
