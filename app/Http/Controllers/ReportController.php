<?php

namespace App\Http\Controllers;

use App\Models\DetailReportNeraca;
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
}
