<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\TransPurchase;

class TransPurchaseController extends Controller
{
    use AuditLogsTrait;

    public function index(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $datas = TransPurchase::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'trans_purchase.*'
            );

        if($ref_number != null){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
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
                ->addColumn('action', function ($data){
                    return view('transpurchase.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Purchase');

        return view('transpurchase.index',compact('datas',
            'ref_number', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function create(Request $request)
    {
        $accountcodes = MstAccountCodes::get();

        //Audit Log
        $this->auditLogsShort('View Create New Purchase Transaction');

        return view('transpurchase.create',compact('accountcodes'));
    }

    // Get Purchase Invoice (Not Yet Initiate Where To Get)
    // public function getpurchaseinvoice($id)
    // {
    //     $purchaseinvoice = 

    //     return json_encode($purchaseinvoice);
    // }

    function generateRefNumber()
    {
        // Get current year and month
        $year = date('y');
        $month = date('m');
        // Get the last reference number for the current year and month from the database
        $lastRefNumber = TransPurchase::where('ref_number', 'like', "PRC-$year$month%")->orderBy('ref_number', 'desc')->first();
        // If there are no existing reference numbers for the current year and month, start from 1
        if (!$lastRefNumber) {
            $counter = 1;
        } else {
            // Extract the counter from the last reference number and increment it
            $lastCounter = intval(substr($lastRefNumber->ref_number, 9)); // Assuming the format is fixed as "PRC-YYMMXXXXX"
            $counter = $lastCounter + 1;
        }
        // Format the counter with leading zeros
        $counterFormatted = str_pad($counter, 5, '0', STR_PAD_LEFT);
        // Generate the reference number
        $refNumber = "PRC-$year$month$counterFormatted";
    
        return $refNumber;
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date_transaction' => 'required',
        ]);
        
        $refNumber = $this->generateRefNumber();

        DB::beginTransaction();
        try{
            TransPurchase::create([
                'ref_number' => $refNumber,
                'created_by' => auth()->user()->email
            ]);

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace(',', '', $item['nominal']);
                        $nominal = number_format((float)$nominal, 3, '.', '');

                        if($item['type'] == 'Debit'){
                            $debit = $nominal;
                            $kredit = null;
                        } else {
                            $debit = null;
                            $kredit = $nominal;
                        }
    
                        GeneralLedger::create([
                            'ref_number' => $refNumber,
                            'date_transaction' => $request->transaction_date,
                            'id_account_code' => $item['account_code'],
                            'debit' => $debit,
                            'kredit' => $kredit,
                            'source' => 'Purchase Transaction',
                        ]);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Purchase Transaction Ref. Number ('. $refNumber . ')');

            DB::commit();
            return redirect()->route('transpurchase.index')->with(['success' => 'Success Create New Purchase Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Purchase Transaction!']);
        }
    }

    public function info($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransPurchase::where('id', $id)->first();
        
        $general_ledgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->get();
        
        $transaction_date = date('Y-m-d', strtotime($general_ledgers[0]->date_transaction));
        
        //Audit Log
        $this->auditLogsShort('View Info Purchase Transaction Ref Number ('. $data->ref_number . ')');

        return view('transpurchase.info',compact('data', 'general_ledgers', 'transaction_date'));
    }

    public function edit($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransPurchase::where('id', $id)->first();

        $general_ledger = GeneralLedger::where('ref_number', $data->ref_number)->first();
        if($general_ledger != []){
            $general_ledgers = GeneralLedger::where('ref_number', $data->ref_number)->where('id', '!=', $general_ledger->id)->get();
        } else {
            $general_ledgers = [];
        }

        $transaction_date = date('Y-m-d', strtotime($general_ledger->date_transaction));

        $accountcodes = MstAccountCodes::get();
        
        //Audit Log
        $this->auditLogsShort('View Edit Purchase Transaction Ref Number ('. $data->ref_number . ')');

        return view('transpurchase.edit',compact('data', 'general_ledger', 'general_ledgers', 'transaction_date', 'accountcodes'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $id = decrypt($id);

        $request->validate([
            'transaction_date' => 'required',
        ]);

        $databefore = TransPurchase::where('id', $id)->first();

        // Compare Transaction
        $transbefore = GeneralLedger::where('ref_number', $databefore->ref_number)->get();
        $inputtrans = $request->addmore;
        $updatetrans = false;
        if ($transbefore->isNotEmpty() && is_array($inputtrans)) {
            // Check if lengths are different
            if (count($transbefore) != count($inputtrans)) {
                $updatetrans = true;
            } else {
                $updatetrans = false;
                // Iterate and compare
                foreach ($transbefore as $index => $trans) {
                    // Ensure index exists
                    if (!isset($inputtrans[$index])) {
                        $updatetrans = true;
                        break;
                    }
                    $detail = $inputtrans[$index];
                    // Compare attributes (also remove formatting from amount_fee for accurate comparison)
                    if($detail['type'] == 'Debit'){
                        $debit = str_replace(',', '', $detail['nominal']);
                        $debit = number_format((float)$debit, 3, '.', '');
                        $kredit = null;
                    } else {
                        $debit = null;
                        $kredit = str_replace(',', '', $detail['nominal']);
                        $kredit = number_format((float)$kredit, 3, '.', '');
                    }

                    if ($trans->id_account_code != $detail['account_code'] || $trans->debit != $debit || $trans->kredit != $kredit) {
                        $updatetrans = true;
                        break;
                    }
                }
            }
        } elseif($transbefore->isEmpty() && $inputtrans[0]['account_code'] != null || $transbefore->isNotEmpty() && $inputtrans[0]['account_code'] === null ) {
            $updatetrans = true;
        } else {
            $updatetrans = false;
        }

        $date_transaction = GeneralLedger::where('ref_number', $databefore->ref_number)->first()->date_transaction;
        $date_transaction = date('Y-m-d', strtotime($date_transaction));
        if($date_transaction != $request->transaction_date){
            $updatetrans = true;
        }

        if($databefore->isDirty() || $updatetrans == true){
            DB::beginTransaction();
            try{
                //Update Trans Sales
                if($databefore->isDirty()){
                    TransPurchase::where('id', $id)->update([
                        'updated_by' => auth()->user()->email
                    ]);
                }
                //Update General Ledgers
                if($updatetrans == true){
                    TransPurchase::where('id', $id)->update([
                        'updated_by' => auth()->user()->email
                    ]);
                    //Delete Data Before
                    GeneralLedger::where('ref_number', $databefore->ref_number)->delete();
                    //Add New Input
                    if($request->addmore != null){
                        foreach($request->addmore as $item){
                            if($item['account_code'] != null && $item['nominal'] != null){
                                $nominal = str_replace(',', '', $item['nominal']);
                                $nominal = number_format((float)$nominal, 3, '.', '');
                                if($item['type'] == 'Debit'){
                                    $debit = $nominal;
                                    $kredit = null;
                                } else {
                                    $debit = null;
                                    $kredit = $nominal;
                                }
                                GeneralLedger::create([
                                    'ref_number' => $databefore->ref_number,
                                    'date_transaction' => $request->transaction_date,
                                    'id_account_code' => $item['account_code'],
                                    'debit' => $debit,
                                    'kredit' => $kredit,
                                    'source' => 'Sales Transaction',
                                ]);
                            }
                        }
                    }
                }

                //Audit Log
                $this->auditLogsShort('Update Purchase Transaction Ref. Number ('. $databefore->ref_number . ')');

                DB::commit();
                return redirect()->route('transpurchase.index')->with(['success' => 'Success Update Purchase Transaction']);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Purchase Transaction!']);
            }
        } else {
            return redirect()->route('transpurchase.index')->with(['info' => 'Nothing Change, The data entered is the same as the previous one!']);
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try{
            $data = TransPurchase::where('id', $id)->first();
            GeneralLedger::where('ref_number', $data->ref_number)->delete();
            TransPurchase::where('id', $id)->delete();
            
            //Audit Log
            $this->auditLogsShort('Delete Purchase Transaction Ref. Number = '.$data->ref_number);

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Delete Purchase Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Delete Purchase Transaction!']);
        }
    }
}
