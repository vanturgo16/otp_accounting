<?php

namespace App\Http\Controllers;

use App\Models\DetailReportHpp;
use App\Models\DetailReportNeraca;
use App\Models\MasterHpp;
use App\Models\ReportHpp;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Traits\AuditLogsTrait;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon; 
use PDF;

// Model
use App\Models\ReportNeraca;

class ReportController extends Controller
{
    use AuditLogsTrait;
    // NERACA 
    public function neraca(Request $request)
    {
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $datas = ReportNeraca::orderBy('created_at','desc');

        if($startdate != null && $enddate != null){
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }

        $datas = $datas->get();

        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('report.neraca.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Report Neraca');

        return view('report.neraca.index', compact('searchDate', 'startdate', 'enddate', 'flag'));
    }
    public function neracaDetail($id)
    {
        $id = decrypt($id);
        $report = ReportNeraca::findOrFail($id);

        $report = ReportNeraca::where('id', $id)->first();
        $period = date('Y-m-d', strtotime($report->report_period_date));
        $data = DetailReportNeraca::where('id_report_neraca', $id)->get();

        $data = DetailReportNeraca::select('head1', 'head2', 'account', DB::raw('SUM(total_balance) as total_balance'))
            ->groupBy('head1', 'head2', 'account')
            ->where('id_report_neraca', $id)
            ->get()
            ->groupBy('head1');

        $pdf = PDF::loadView('pdf.neracareport', [
            'report' => $report,
            'data' => $data,
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('View PDF Report Neraca ID : ('. $id .')');

        return $pdf->stream('Report Neraca Date ('. $period . ').pdf', array("Attachment" => false));

    }
    public function neracaView(Request $request)
    {
        $results = DB::table('master_neracas')
            ->join('master_account_codes', 'master_neracas.account_sub', '=', 'master_account_codes.id')
            ->select(
                'master_neracas.head1',
                'master_neracas.head2',
                'master_neracas.account',
                DB::raw("SUM(CASE WHEN master_account_codes.balance_type = 'D' THEN master_account_codes.balance WHEN master_account_codes.balance_type = 'K' THEN -master_account_codes.balance ELSE 0 END) as total_balance")
            )
            ->groupBy('master_neracas.head1', 'master_neracas.head2', 'master_neracas.account')
            ->get();

        $groupedData = $results->groupBy('head1')->map(function ($head1Group) {
            return $head1Group->groupBy('head2');
        });

        //Audit Log
        $this->auditLogsShort('View Report Neraca This Month');

        return view('report.neraca.viewreport', compact('groupedData'));
    }
    public function neracaGenerate(Request $request)
    {
        // dd($request->all());
        $report_by = auth()->user()->email;
        $report_period_date = Carbon::now();

        // Check Has Generate Report Neraca Or Not
        $date = Carbon::now();
        $month = $date->format('m');
        $year = $date->format('Y');
        $check = ReportNeraca::whereMonth('report_period_date', $month)->whereYear('report_period_date', $year)->first();
        if($check){
            return redirect()->back()->with(['warning' => 'You Have Generate Neraca Report For This Month!']);
        }

        DB::beginTransaction();
        try{
            // Create Report Neraca
            $report = ReportNeraca::create([
                'report_by' => $report_by,
                'report_period_date' => $report_period_date,
            ]);

            $groupedData = json_decode($request->input('dataeloquent'), true);
            foreach ($groupedData as $head1 => $head1Group) {
                foreach ($head1Group as $head2 => $head2Group) {
                    foreach ($head2Group as $account) {
                        DetailReportNeraca::create([
                            'id_report_neraca' => $report->id,
                            'head1' => $head1,
                            'head2' => $head2,
                            'account' => $account['account'],
                            'total_balance' => $account['total_balance'],
                        ]);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Generate Neraca Report');

            DB::commit();
            return redirect()->route('report.neraca')->with(['success' => 'Success, Report Neraca Generated']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Generate Report Neraca!']);
        }
    }

    //HPP
    public function hpp(Request $request)
    {
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $datas = ReportHpp::orderBy('created_at','desc');

        if($startdate != null && $enddate != null){
            $datas = $datas->whereDate('created_at','>=',$startdate)->whereDate('created_at','<=',$enddate);
        }

        $datas = $datas->get();

        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('report.hpp.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Report HPP');

        return view('report.hpp.index', compact('searchDate', 'startdate', 'enddate', 'flag'));
    }
    public function hppDetail($id)
    {
        $id = decrypt($id);
        $report = ReportHpp::findOrFail($id);

        $report = ReportHpp::where('id', $id)->first();
        $period = date('Y-m-d', strtotime($report->report_period_date));
        $data = DetailReportHpp::where('id_report_hpp', $id)->first();

        $result = json_decode($data->result, true);
        $totalAmountProd = $data->total_amount_prod;
        $totalAmountProdMonthly = $data->total_amount_prod_monthly;
        $totalAmountHPProd = $data->total_amount_hp_prod;
        $totalAmountHPProdMonthly = $data->total_amount_hp_prod_monthly;
        $totalAmountHPP = $data->total_amount_hpp;
        $totalAmountHPPMonthly = $data->total_amount_hpp_monthly;

        $pdf = PDF::loadView('pdf.hppreport', [
            'report' => $report,
            'result' => $result,
            'totalAmountProd' => $totalAmountProd,
            'totalAmountProdMonthly' => $totalAmountProdMonthly,
            'totalAmountHPProd' => $totalAmountHPProd,
            'totalAmountHPProdMonthly' => $totalAmountHPProdMonthly,
            'totalAmountHPP' => $totalAmountHPP,
            'totalAmountHPPMonthly' => $totalAmountHPPMonthly,
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('View PDF Report HPP ID : ('. $id .')');

        return $pdf->stream('Report HPP Date ('. $period . ').pdf', array("Attachment" => false));

    }
    public function hppView(Request $request)
    {
        // Fetch data from the tables and perform necessary calculations
        $data = MasterHpp::select(
                'master_hpps.head1',
                'master_hpps.account',
                'master_hpps.account_sub',
                'master_account_codes.balance',
                'master_account_codes.balance_type'
            )
            ->leftjoin('master_account_codes', 'master_hpps.account_sub', '=', 'master_account_codes.id')
            ->get()
            ->groupBy('head1');

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Fetch data from general_ledgers filtered by the current month
        $monthlyData = DB::table('general_ledgers')
            // ->whereBetween('date_transaction', [$currentMonthStart, $currentMonthEnd])
            ->select(
                'id_account_code',
                DB::raw('SUM(CASE WHEN transaction = "D" THEN amount ELSE -amount END) as monthly_sum')
            )
            ->groupBy('id_account_code')
            ->pluck('monthly_sum', 'id_account_code');

        // Prepare the grouped data for display
        $result = [];
        // Total for "Barang Dalam Proses"
        $totalAmountBDP = 0; $totalAmountBDPMonthly = 0;
        // Total for "Barang Jadi"
        $totalAmountBJ = 0; $totalAmountBJMonthly = 0;
        // Total for "Waste"
        $totalAmountWaste = 0; $totalAmountWasteMonthly = 0;

        // Total excluding "Barang Dalam Proses" and "Barang Jadi" and "Waste"
        $totalAmountProd = 0; $totalAmountProdMonthly = 0;
        // Total for "HPProd"
        $totalAmountHPProd = 0; $totalAmountHPProdMonthly = 0;
        // Total for "HPP"
        $totalAmountHPP = 0; $totalAmountHPPMonthly = 0;

        foreach ($data as $head => $items) {
            $accounts = [];
            $totalSum = 0;
            $totalMonthlySum = 0;

            // Group by account and calculate the balance
            foreach ($items->groupBy('account') as $account => $transactions) {
                $accountSum = 0;
                $monthlySum = 0;

                foreach ($transactions as $transaction) {
                    // Get current month transaction if exists with same account_sub
                    if ($monthlyData->has($transaction['account_sub'])) {
                        $monthlySum = $monthlyData->get($transaction['account_sub']);
                    }
                    // Add or subtract balance based on balance type (D = Debit, K = Credit)
                    $balance = $transaction->balance;
                    if ($transaction->balance_type === 'D') {
                        $accountSum += $balance;
                    } else {
                        $accountSum -= $balance;
                    }
                }

                $accounts[] = [
                    'account' => $account,
                    'sum' => $accountSum,
                    'monthly_sum' => $monthlySum,
                ];
                $totalMonthlySum += $monthlySum;
                $totalSum += $accountSum;
            }

            // Check head type and accumulate accordingly
            if ($head === 'Barang Dalam Proses') {
                $totalAmountBDP += $totalSum;
                $totalAmountBDPMonthly += $totalMonthlySum;
            } elseif ($head === 'Barang Jadi') {
                $totalAmountBJ += $totalSum;
                $totalAmountBJMonthly += $totalMonthlySum;
            } elseif ($head === 'Waste') {
                $totalAmountWaste += $totalSum;
                $totalAmountWasteMonthly += $totalMonthlySum;
            } else {
                $totalAmountProd += $totalSum;
                $totalAmountProdMonthly += $totalMonthlySum;
            }

            $result[] = [
                'head' => $head,
                'accounts' => $accounts,
                'total_monthly' => $totalMonthlySum,
                'total' => $totalSum,
            ];
        }

        // Result HPP
        $totalAmountProd = $totalAmountProd;
        $totalAmountProdMonthly = $totalAmountProdMonthly;
        $totalAmountHPProd = $totalAmountProd + $totalAmountBDP;
        $totalAmountHPProdMonthly = $totalAmountProdMonthly + $totalAmountBDPMonthly;
        $totalAmountHPP = $totalAmountHPProd + $totalAmountBJ + $totalAmountWaste;
        $totalAmountHPPMonthly = $totalAmountHPProdMonthly + $totalAmountBJMonthly + $totalAmountWasteMonthly;

        // First sort by created_at date
        usort($result, function ($a, $b) {
            return strcmp($a['head'], $b['head']);
        });

        // Then sort by custom head order
        usort($result, function ($a, $b) {
            // Define the custom order for specific heads
            $customOrder = [
                'Biaya Produksi Tak Langsung' => 1,
                'Penyusutan' => 2,
                'Barang Dalam Proses' => 3,
                'Barang Jadi' => 4,
                'Waste' => 5,
            ];
            // Check if heads exist in the custom order array
            $aOrder = $customOrder[$a['head']] ?? 0;
            $bOrder = $customOrder[$b['head']] ?? 0;

            // Sort by custom order if found, otherwise keep existing order
            if ($aOrder && $bOrder) {
                return $aOrder - $bOrder;
            } elseif ($aOrder) {
                return 1;
            } elseif ($bOrder) {
                return -1;
            }
            return 0;
        });

        //Audit Log
        $this->auditLogsShort('View Report HPP This Month');

        return view('report.hpp.viewreport', compact('result', 'totalAmountProd', 'totalAmountProdMonthly',
            'totalAmountHPProd', 'totalAmountHPProdMonthly', 'totalAmountHPP', 'totalAmountHPPMonthly'));
    }
    public function hppGenerate(Request $request)
    {
        $report_by = auth()->user()->email;
        $report_period_date = Carbon::now();

        // Check Has Generate Report Neraca Or Not
        $date = Carbon::now();
        $month = $date->format('m');
        $year = $date->format('Y');
        $check = ReportHpp::whereMonth('report_period_date', $month)->whereYear('report_period_date', $year)->first();
        if($check){
            return redirect()->back()->with(['warning' => 'You Have Generate Neraca Report For This Month!']);
        }

        DB::beginTransaction();
        try{
            // Create Report HPP
            $report = ReportHpp::create([
                'report_by' => $report_by,
                'report_period_date' => $report_period_date,
            ]);

            DetailReportHpp::create([
                'id_report_hpp' => $report->id,
                'result' => $request->result,
                'total_amount_prod' => $request->totalAmountProd,
                'total_amount_prod_monthly' => $request->totalAmountProdMonthly,
                'total_amount_hp_prod' => $request->totalAmountHPProd,
                'total_amount_hp_prod_monthly' => $request->totalAmountHPProdMonthly,
                'total_amount_hpp' => $request->totalAmountHPP,
                'total_amount_hpp_monthly' => $request->totalAmountHPPMonthly,
            ]);

            //Audit Log
            $this->auditLogsShort('Generate HPP Report');

            DB::commit();
            return redirect()->route('report.hpp')->with(['success' => 'Success, Report HPP Generated']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Generate Report HPP!']);
        }
    }
}
