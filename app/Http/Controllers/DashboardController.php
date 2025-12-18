<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

// Model
use App\Models\TransCashBook;
use App\Models\TransPurchase;
use App\Models\TransSales;
use App\Models\TransSalesExport;

class DashboardController extends Controller
{
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
}
