<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use DateTime;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\TransImport;

class TransImportController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    function generateRefNumber()
    {
        // Get current year and month
        $year = date('y');
        $month = date('m');
        // Get the last reference number for the current year and month from the database
        $lastRefNumber = TransImport::where('ref_number', 'like', "IMP-$year$month%")->orderBy('ref_number', 'desc')->first();
        // If there are no existing reference numbers for the current year and month, start from 1
        if (!$lastRefNumber) {
            $counter = 1;
        } else {
            // Extract the counter from the last reference number and increment it
            $lastCounter = intval(substr($lastRefNumber->ref_number, 9)); // Assuming the format is fixed as "IMP-YYMMXXXXX"
            $counter = $lastCounter + 1;
        }
        // Format the counter with leading zeros
        $counterFormatted = str_pad($counter, 5, '0', STR_PAD_LEFT);
        // Generate the reference number
        $refNumber = "IMP-$year$month$counterFormatted";
    
        return $refNumber;
    }

    public function index(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $datas = TransImport::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'trans_import.*'
            )
            ->orderBy('trans_import.created_at','desc');

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

        foreach($datas as $data){
            $count = GeneralLedger::where('ref_number', $data->ref_number)->count();
            $data->count = $count;
        }
        
        // Datatables
        if ($request->ajax()) {
            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('transimport.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Import');

        return view('transimport.index',compact('datas',
            'ref_number', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function create(Request $request)
    {
        $accountcodes = MstAccountCodes::get();

        //Audit Log
        $this->auditLogsShort('View Create New Import Transaction');

        return view('transimport.create',compact('accountcodes'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'date_transaction' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);
        
        $refNumber = $this->generateRefNumber();

        DB::beginTransaction();
        try{
            TransImport::create([
                'ref_number' => $refNumber,
                'tax_invoice_number' => $request->tax_invoice_number,
                'ext_doc_number' => $request->ext_doc_number,
                'inv_received_date' => $request->inv_received_date,
                'due_date' => $request->due_date,
                'created_by' => auth()->user()->email
            ]);

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = str_replace('.', '', $item['nominal']);
                        $nominal = str_replace(',', '.', $nominal);

                        // Create General Ledger
                        $this->storeGeneralLedger($refNumber, $request->date_transaction, $item['account_code'], $item['type'], $nominal, 'Import Transaction');
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Import Transaction Ref. Number ('. $refNumber . ')');

            DB::commit();
            return redirect()->route('transimport.index')->with(['success' => 'Success Create New Import Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Import Transaction!']);
        }
    }

    public function info($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransImport::where('id', $id)->first();
        
        $general_ledgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->get();
        
        $transaction_date = date('Y-m-d', strtotime($general_ledgers[0]->date_transaction));
        $inv_received_date = date('Y-m-d', strtotime($data->inv_received_date));
        $due_date = date('Y-m-d', strtotime($data->due_date));
        
        //Audit Log
        $this->auditLogsShort('View Info Import Transaction Ref Number ('. $data->ref_number . ')');

        return view('transimport.info',compact('data', 'general_ledgers', 'transaction_date', 'inv_received_date', 'due_date'));
    }

    public function edit($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransImport::where('id', $id)->first();

        $general_ledger = GeneralLedger::where('ref_number', $data->ref_number)->first();
        if($general_ledger != []){
            $general_ledgers = GeneralLedger::where('ref_number', $data->ref_number)->where('id', '!=', $general_ledger->id)->get();
        } else {
            $general_ledgers = [];
        }

        $transaction_date = date('Y-m-d', strtotime($general_ledger->date_transaction));
        $inv_received_date = date('Y-m-d', strtotime($data->inv_received_date));
        $due_date = date('Y-m-d', strtotime($data->due_date));

        $accountcodes = MstAccountCodes::get();
        
        //Audit Log
        $this->auditLogsShort('View Edit Import Transaction Ref Number ('. $data->ref_number . ')');

        return view('transimport.edit',compact('data', 'general_ledger', 'general_ledgers', 'transaction_date', 'inv_received_date', 'due_date', 'accountcodes'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $id = decrypt($id);

        $request->validate([
            'transaction_date' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);

        $inv_received_date = (new DateTime($request->inv_received_date))->format('Y-m-d H:i:s');
        $due_date = (new DateTime($request->due_date))->format('Y-m-d H:i:s');
        
        $databefore = TransImport::where('id', $id)->first();
        $databefore->tax_invoice_number = $request->tax_invoice_number;
        $databefore->ext_doc_number = $request->ext_doc_number;
        $databefore->inv_received_date = $inv_received_date;
        $databefore->due_date = $due_date;

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
                    $nominal = str_replace('.', '', $detail['nominal']);
                    $nominal = str_replace(',', '.', $nominal);
                    $type = ($detail['type'] == 'Debit') ? 'D' : 'K';
                    if ($trans->id_account_code != $detail['account_code'] || $trans->amount != $nominal || $trans->transaction != $type) {
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
                    TransImport::where('id', $id)->update([
                        'tax_invoice_number' => $request->tax_invoice_number,
                        'ext_doc_number' => $request->ext_doc_number,
                        'inv_received_date' => $request->inv_received_date,
                        'due_date' => $request->due_date,
                        'updated_by' => auth()->user()->email
                    ]);
                }
                //Update General Ledgers
                if($updatetrans == true){
                    TransImport::where('id', $id)->update([
                        'updated_by' => auth()->user()->email
                    ]);
                    //Delete Data Before
                    GeneralLedger::where('ref_number', $databefore->ref_number)->delete();
                    //Add New Input
                    if($request->addmore != null){
                        foreach($request->addmore as $item){
                            if($item['account_code'] != null && $item['nominal'] != null){
                                $nominal = str_replace('.', '', $item['nominal']);
                                $nominal = str_replace(',', '.', $nominal);

                                if($item['type'] == 'Debit'){
                                    $transaction = "D";
                                } else {
                                    $transaction = "K";
                                }

                                GeneralLedger::create([
                                    'ref_number' => $databefore->ref_number,
                                    'date_transaction' => $request->transaction_date,
                                    'id_account_code' => $item['account_code'],
                                    'transaction' => $transaction,
                                    'amount' => $nominal,
                                    'source' => 'Import Transaction',
                                ]);
                            }
                        }
                    }
                }

                //Audit Log
                $this->auditLogsShort('Update Import Transaction Ref. Number ('. $databefore->ref_number . ')');

                DB::commit();
                return redirect()->route('transimport.index')->with(['success' => 'Success Update Import Transaction']);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Import Transaction!']);
            }
        } else {
            return redirect()->route('transimport.index')->with(['info' => 'Nothing Change, The data entered is the same as the previous one!']);
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try{
            $data = TransImport::where('id', $id)->first();
            GeneralLedger::where('ref_number', $data->ref_number)->delete();
            TransImport::where('id', $id)->delete();
            
            //Audit Log
            $this->auditLogsShort('Delete Import Transaction Ref. Number = '.$data->ref_number);

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Delete Import Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Delete Import Transaction!']);
        }
    }
}
