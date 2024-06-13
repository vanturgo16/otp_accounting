<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\SalesOrder;
use App\Models\TransSales;

class TransSalesController extends Controller
{
    use AuditLogsTrait;

    public function index(Request $request)
    {
        $ref_number = $request->get('ref_number');
        $id_sales_order = $request->get('id_sales_order');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');

        $sales = SalesOrder::select('id', 'so_number', 'status')->get();

        $datas = TransSales::select(
                DB::raw('ROW_NUMBER() OVER (ORDER BY id) as no'),
                'trans_sales.*', 'sales_orders.so_number'
            )
            ->leftjoin('sales_orders', 'trans_sales.id_sales_order', 'sales_orders.id');

        if($ref_number != null){
            $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
        }
        if($id_sales_order != null){
            $datas = $datas->where('id_sales_order', $id_sales_order);
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
                    return view('transsales.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = '<input type="checkbox" id="checkboxdt" name="checkbox" data-id-data="' . $data->id . '" />';
                    return $checkBox;
                })
                ->rawColumns(['bulk-action'])
                ->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Sales');

        return view('transsales.index',compact('datas', 'sales',
            'ref_number', 'id_sales_order', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function create(Request $request)
    {
        $sales = SalesOrder::select('id', 'so_number', 'status')->get();
        $accountcodes = MstAccountCodes::get();

        //Audit Log
        $this->auditLogsShort('View Create New Sales Transaction');

        return view('transsales.create',compact('sales', 'accountcodes'));
    }

    public function getsalesorder($id)
    {
        $salesorder = SalesOrder::select('sales_orders.*', 'master_customers.name as customer_name', 'master_customer_addresses.address as customer_address',
                'master_product_fgs.description as product', 'master_units.unit as unit')
            ->leftjoin('master_customers', 'sales_orders.id_master_customers', 'master_customers.id')
            ->leftjoin('master_customer_addresses', 'sales_orders.id_master_customer_addresses', 'master_customer_addresses.id')
            ->leftjoin('master_product_fgs', 'sales_orders.id_master_products', 'master_product_fgs.id')
            ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
            ->where('sales_orders.id', $id)
            ->first();

        return json_encode($salesorder);
    }

    function generateRefNumber()
    {
        // Get current year and month
        $year = date('y');
        $month = date('m');
        // Get the last reference number for the current year and month from the database
        $lastRefNumber = TransSales::where('ref_number', 'like', "SLS-$year$month%")->orderBy('ref_number', 'desc')->first();
        // If there are no existing reference numbers for the current year and month, start from 1
        if (!$lastRefNumber) {
            $counter = 1;
        } else {
            // Extract the counter from the last reference number and increment it
            $lastCounter = intval(substr($lastRefNumber->ref_number, 9)); // Assuming the format is fixed as "SLS-YYMMXXXXX"
            $counter = $lastCounter + 1;
        }
        // Format the counter with leading zeros
        $counterFormatted = str_pad($counter, 5, '0', STR_PAD_LEFT);
        // Generate the reference number
        $refNumber = "SLS-$year$month$counterFormatted";
    
        return $refNumber;
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_sales_order' => 'required',
            'transaction_date' => 'required',
            'no_delivery_note' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);
        
        $refNumber = $this->generateRefNumber();

        DB::beginTransaction();
        try{
            TransSales::create([
                'ref_number' => $refNumber,
                'id_sales_order' => $request->id_sales_order,
                'no_delivery_note' => $request->no_delivery_note,
                'created_by' => auth()->user()->email
            ]);

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
                            'ref_number' => $refNumber,
                            'date_transaction' => $request->transaction_date,
                            'id_account_code' => $item['account_code'],
                            'transaction' => $transaction,
                            'amount' => $nominal,
                            'source' => 'Sales Transaction',
                        ]);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Sales Transaction Ref. Number ('. $refNumber . ')');

            DB::commit();
            return redirect()->route('transsales.index')->with(['success' => 'Success Create New Sales Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Sales Transaction!']);
        }
    }

    public function info($id)
    {
        $id = decrypt($id);
        // dd($id);

        $data = TransSales::select('trans_sales.*', 'sales_orders.*', 'master_customers.name as customer_name', 'master_customer_addresses.address as customer_address',
                'master_product_fgs.description as product', 'master_units.unit as unit')
            ->leftjoin('sales_orders', 'trans_sales.id_sales_order', 'sales_orders.id')
            ->leftjoin('master_customers', 'sales_orders.id_master_customers', 'master_customers.id')
            ->leftjoin('master_customer_addresses', 'sales_orders.id_master_customer_addresses', 'master_customer_addresses.id')
            ->leftjoin('master_product_fgs', 'sales_orders.id_master_products', 'master_product_fgs.id')
            ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
            ->where('trans_sales.id', $id)
            ->first();
        
        $general_ledgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.ref_number', $data->ref_number)
            ->get();

        $transaction_date = date('Y-m-d', strtotime($general_ledgers[0]->date_transaction));

        //Audit Log
        $this->auditLogsShort('View Info Sales Transaction Ref Number ('. $data->ref_number . ')');

        return view('transsales.info',compact('data', 'general_ledgers', 'transaction_date'));
    }

    public function edit($id)
    {
        $id = decrypt($id);
        // dd($id);
        
        $data = TransSales::select('trans_sales.*', 'sales_orders.*', 'master_customers.name as customer_name', 'master_customer_addresses.address as customer_address',
                'master_product_fgs.description as product', 'master_units.unit as unit')
            ->leftjoin('sales_orders', 'trans_sales.id_sales_order', 'sales_orders.id')
            ->leftjoin('master_customers', 'sales_orders.id_master_customers', 'master_customers.id')
            ->leftjoin('master_customer_addresses', 'sales_orders.id_master_customer_addresses', 'master_customer_addresses.id')
            ->leftjoin('master_product_fgs', 'sales_orders.id_master_products', 'master_product_fgs.id')
            ->leftjoin('master_units', 'sales_orders.id_master_units', 'master_units.id')
            ->where('trans_sales.id', $id)
            ->first();

        $general_ledger = GeneralLedger::where('ref_number', $data->ref_number)->first();
        if($general_ledger != []){
            $general_ledgers = GeneralLedger::where('ref_number', $data->ref_number)->where('id', '!=', $general_ledger->id)->get();
        } else {
            $general_ledgers = [];
        }
        
        $transaction_date = date('Y-m-d', strtotime($general_ledger->date_transaction));

        $sales = SalesOrder::select('id', 'so_number', 'status')->get();
        $accountcodes = MstAccountCodes::get();
        
        //Audit Log
        $this->auditLogsShort('View Edit Sales Transaction Ref Number ('. $data->ref_number . ')');

        return view('transsales.edit',compact('data', 'general_ledger', 'general_ledgers', 'transaction_date', 'sales', 'accountcodes'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $id = decrypt($id);

        $request->validate([
            'id_sales_order' => 'required',
            'transaction_date' => 'required',
            'no_delivery_note' => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal' => 'required',
            'addmore.*.type' => 'required',
        ]);

        $databefore = TransSales::where('id', $id)->first();
        $databefore->id_sales_order = $request->id_sales_order;
        $databefore->no_delivery_note = $request->no_delivery_note;

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
                    TransSales::where('id', $id)->update([
                        'id_sales_order' => $request->id_sales_order,
                        'no_delivery_note' => $request->no_delivery_note,
                        'updated_by' => auth()->user()->email
                    ]);
                }
                //Update General Ledgers
                if($updatetrans == true){
                    TransSales::where('id', $id)->update([
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
                                    'source' => 'Sales Transaction',
                                ]);
                            }
                        }
                    }
                }

                //Audit Log
                $this->auditLogsShort('Update Sales Transaction Ref. Number ('. $databefore->ref_number . ')');

                DB::commit();
                return redirect()->route('transsales.index')->with(['success' => 'Success Update Sales Transaction']);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Sales Transaction!']);
            }
        } else {
            return redirect()->route('transsales.index')->with(['info' => 'Nothing Change, The data entered is the same as the previous one!']);
        }
    }

    public function delete($id)
    {
        $id = decrypt($id);

        DB::beginTransaction();
        try{
            $data = TransSales::where('id', $id)->first();
            GeneralLedger::where('ref_number', $data->ref_number)->delete();
            TransSales::where('id', $id)->delete();
            
            //Audit Log
            $this->auditLogsShort('Delete Sales Transaction Ref. Number = '.$data->ref_number);

            DB::commit();
            return redirect()->back()->with(['success' => 'Success Delete Sales Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Delete Sales Transaction!']);
        }
    }
}
