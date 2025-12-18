<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use PDF;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\MstBankAccount;
use App\Models\MstRule;
use App\Models\TransCashBook;

class TransCashBookController extends Controller
{
    use AuditLogsTrait, GeneralLedgerTrait;

    // MODAL VIEW
    public function modalInfo($id)
    {
        $id = decrypt($id);
        $detail = TransCashBook::where('id', $id)->first();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id)
            ->where('general_ledgers.ref_number', $detail->transaction_number)
            ->where('general_ledgers.source', 'Cash Book')
            ->get();

        return view('cashbook.modal.info',compact('detail', 'generalLedgers'));
    }
    public function modalDelete($id)
    {
        $id = decrypt($id);
        $detail = TransCashBook::where('id', $id)->first();

        return view('cashbook.modal.delete',compact('detail'));
    }

    public function index(Request $request)
    {
        $typeManuals = $this->getManualType();
        $trans_number = $request->get('trans_number');
        $invoice_number = $request->get('invoice_number');
        $tax_invoice_number = $request->get('tax_invoice_number');
        $type = $request->get('type');
        $searchDate = $request->get('searchDate');
        $startdate = $request->get('startdate');
        $enddate = $request->get('enddate');
        $flag = $request->get('flag');
        if (is_null($searchDate)) {
            $searchDate = "Custom";
            $startdate = now()->startOfYear()->format('Y-m-d');
            $enddate = now()->endOfYear()->format('Y-m-d');
        }

        // Datatables
        if ($request->ajax()) {
            $datas = TransCashBook::select(
                    'trans_cash_book.*', 
                    'master_account_codes.account_code', 
                    'master_account_codes.account_name',
                    'general_ledgers.transaction',
                    'general_ledgers.amount',
                    'general_ledgers.note',
                    DB::raw("'Cash Book' as source")
                )
                ->leftjoin('general_ledgers', 'trans_cash_book.id', 'general_ledgers.id_ref')
                ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
                ->where('general_ledgers.source', 'Cash Book')
                ->orderBy('trans_cash_book.created_at', 'desc')
                ->orderBy('general_ledgers.created_at', 'asc');
            
            if($trans_number != null){
                $datas = $datas->where('transaction_number', 'like', '%'.$trans_number.'%');
            }
            if($invoice_number != null){
                $datas = $datas->where('invoice_number', 'like', '%'.$invoice_number.'%');
            }
            if($tax_invoice_number != null){
                $datas = $datas->where('tax_invoice_number', 'like', '%'.$tax_invoice_number.'%');
            }
            if($type != null){
                $datas = $datas->where('trans_cash_book.type', $type);
            }
            if($startdate != null && $enddate != null){
                $datas = $datas->whereDate('trans_cash_book.created_at','>=',$startdate)->whereDate('trans_cash_book.created_at','<=',$enddate);
            }
            
            if($request->flag != null){
                $datas = $datas->get()->makeHidden(['id', 'id_master_bank_account', 'total_transaction']);
                return $datas;
            }
            
            $datas = $datas->get();

            return DataTables::of($datas)
                ->addColumn('action', function ($data){
                    return view('cashbook.action', compact('data'));
                })->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Cash Book');

        return view('cashbook.index',compact('typeManuals', 'trans_number', 'invoice_number', 'tax_invoice_number', 'type', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function generateCBNumber($type, $codeBank = null, $currency = null)
    {
        $year  = date('y');
        $month = now()->format('n');
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        // Get last sequence for current month & year
        $lastData = TransCashBook::where('type', $type)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest('created_at')
            ->first();
        $seq = $lastData ? $lastData->seq + 1 : 1;
        $seqFormatted = str_pad($seq, 4, '0', STR_PAD_LEFT);

        // Determine transaction code
        switch ($type) {
            case 'Bukti Kas Keluar':
                $code = 'BKK';
                break;

            case 'Bukti Kas Masuk':
                $code = 'BKM';
                break;

            case 'Bukti Bank Keluar':
                $bank = strtoupper("{$codeBank}-{$currency}");
                $code = "BBK/{$bank}";
                break;

            case 'Bukti Bank Masuk':
                $bank = strtoupper("{$codeBank}-{$currency}");
                $code = "BBM/{$bank}";
                break;

            default:
                throw new \Exception('Invalid cash book type');
        }

        return [
            'trans_number' => "{$code}/{$year}/{$romanMonths[$month]}/{$seqFormatted}",
            'seq_number'   => $seq,
        ];
    }

    public function create(Request $request)
    {
        $typeManuals  = $this->getManualType();
        $bankAccounts = MstBankAccount::where('is_active', 1)->get();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();

        //Audit Log
        $this->auditLogsShort('View Create New Cash Book');

        return view('cashbook.create',compact('typeManuals', 'bankAccounts', 'accountcodes'));
    }

    public function store(Request $request)
    {
        // dd(($request->all()));
        $request->validate([
            'date_invoice'           => 'required',
            'invoice_number'         => 'required',
            'tax_invoice_number'     => 'required',
            'type'                   => 'required',
            'addmore.*.account_code' => 'required',
            'addmore.*.nominal'      => 'required',
            'addmore.*.type'         => 'required',
        ]);

        $codeBank = null;
        $currency = $request->currency;
        $type     = $request->type;
        $idMBA    = $request->id_master_bank_account;
        if (in_array($type, ['Bukti Bank Keluar', 'Bukti Bank Masuk'])) {
            $bankAccount = MstBankAccount::where('id', $idMBA)->first();
            $codeBank    = $bankAccount->code;
            $currency    = $bankAccount->currency;
            $category    = "BANK ". strtoupper($currency);
        } else {
            $category    = "KAS";
        }

        $genNumber   = $this->generateCBNumber($type, $codeBank, $currency);
        $transNumber = $genNumber['trans_number'];
        $seqNumber   = $genNumber['seq_number'];
        $docNo       = optional(MstRule::where('rule_name', 'DocNo. Invoice')->first())->rule_value;

        $total = 0;
        foreach ($request->addmore as $row) {
            $nominal = $this->normalizeOpeningBalance($row['nominal']);
            if ($row['type'] === 'D') {
                $total += $nominal;
            } elseif ($row['type'] === 'K') {
                $total -= $nominal;
            }
        }
        if ($total > 0) {
            $transactionType = 'D';
            $totalAmount = $total;
        } else {
            $transactionType = 'K';
            $totalAmount = abs($total);
        }

        DB::beginTransaction();
        try{
            $trans = TransCashBook::create([
                'seq' => $seqNumber,
                'transaction_number' => $transNumber,
                'invoice_number'     => $request->invoice_number,
                'tax_invoice_number' => $request->tax_invoice_number,
                'date_invoice'       => $request->date_invoice,
                'type'               => $type,
                'category'           => $category,
                'currency'           => $currency,
                'code_bank'          => $codeBank,
                'id_master_bank_account' => $idMBA,
                'total_transaction'  => $request->addmore ? count($request->addmore) : 0,
                'transaction'        => $transactionType,
                'total'              => $totalAmount,
                'doc_no'             => $docNo,
                'created_by'         => auth()->user()->email
            ]);

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = $this->normalizeOpeningBalance($item['nominal']);
                        // Create General Ledger
                        $this->storeGeneralLedger(
                            $trans->id, $transNumber, $request->date_invoice, 
                            $item['account_code'], $item['type'], $nominal, $item['note'], 
                            'Cash Book', null);
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            //Audit Log
            $this->auditLogsShort('Create New Cash Book Transaction Number ('. $transNumber . ')');
            DB::commit();
            return redirect()->route('cashbook.index')->with(['success' => 'Success Create New Cash Book Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Cash Book Transaction!']);
        }
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $detail = TransCashBook::where('id', $id)->first();
        $bankAccountsUsed = MstBankAccount::where('id', $detail->id_master_bank_account)->first();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id)
            ->where('general_ledgers.ref_number', $detail->transaction_number)
            ->where('general_ledgers.source', 'Cash Book')
            ->get();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();

        //Audit Log
        $this->auditLogsShort('View Edit Cash Book Transaction Trans. Number ('. $detail->transaction_number . ')');

        return view('cashbook.edit',compact('detail', 'bankAccountsUsed', 'accountcodes', 'generalLedgers'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'date_invoice'              => 'required',
            'invoice_number'            => 'required',
            'tax_invoice_number'        => 'required',
            'addmore.*.account_code'    => 'required',
            'addmore.*.nominal'         => 'required',
            'addmore.*.type'            => 'required',
        ]);

        $total = 0;
        foreach ($request->addmore as $row) {
            $nominal = $this->normalizeOpeningBalance($row['nominal']);
            if ($row['type'] === 'D') {
                $total += $nominal;
            } elseif ($row['type'] === 'K') {
                $total -= $nominal;
            }
        }
        if ($total > 0) {
            $transactionType = 'D';
            $totalAmount = $total;
        } else {
            $transactionType = 'K';
            $totalAmount = abs($total);
        }
        
        $id          = decrypt($id);
        $detail      = TransCashBook::where('id', $id)->lockForUpdate()->first();
        $transNumber = $detail->transaction_number;
        $invNumber   = $request->invoice_number;

        // Validation
        $trxDate = Carbon::parse($detail->date_invoice);
        $now     = Carbon::now();
        if (!$trxDate->isSameMonth($now)) {
            return back()->with('error', 'This transaction cannot be updated because the transaction month has already passed.');
        }
        $isDuplicate = TransCashBook::where('invoice_number', $invNumber)->where('id', '!=', $id)->exists();
        if ($isDuplicate) {
            return back()->withInput()->with(['error' => 'Invoice number already in use, please use another invoice number']);
        }
        
        $detail->date_invoice       = $request->date_invoice . ' 00:00:00';
        $detail->invoice_number     = $invNumber;
        $detail->tax_invoice_number = $request->tax_invoice_number;
        $detail->transaction        = $transactionType;
        $detail->total              = $this->decimal3($totalAmount);
        $detail->total_transaction  = $request->addmore ? count($request->addmore) : 0;
        $isChangedDetail            = $detail->isDirty();


        $existingLedgers = GeneralLedger::where('id_ref', $id)->where('ref_number', $transNumber)->where('source', 'Cash Book')->get();
        $existing = $existingLedgers->map(function ($item) {
            return [
                'account_code' => $item->id_account_code,
                'type'         => $item->transaction,
                'nominal'      => (float) $item->amount,
                'note'         => $item->note,
            ];
        })->values()->toArray();
        $requestData = collect($request->addmore)->map(function ($row) {
            return [
                'account_code' => $row['account_code'],
                'type'         => $row['type'],
                'nominal'      => (float) $this->normalizeOpeningBalance($row['nominal']),
                'note'         => $row['note'],
            ];
        })->values()->toArray();
        $isChangedTransaction = $existing != $requestData;

        if($isChangedDetail || $isChangedTransaction) {
            DB::beginTransaction();
            try{
                if($isChangedDetail) {
                    TransCashBook::where('id', $id)->update([
                        'invoice_number'     => $invNumber,
                        'tax_invoice_number' => $request->tax_invoice_number,
                        'date_invoice'       => $request->date_invoice,
                        'total_transaction'  => $request->addmore ? count($request->addmore) : 0,
                        'transaction'        => $transactionType,
                        'total'              => $totalAmount,
                        'updated_by'         => auth()->user()->email
                    ]);
                }

                if($isChangedTransaction) {
                    // Reset Balance Account
                    foreach($existing as $item) {
                        $reverseType = ($item['type'] === 'D') ? 'K' : 'D';
                        $nominal = $this->normalizeOpeningBalance($item['nominal']);
                        $this->updateBalanceAccount($item['account_code'], $nominal, $reverseType);
                    }
                    // Delete General Ledger
                    GeneralLedger::where('id_ref', $id)->where('ref_number', $transNumber)->where('source', 'Cash Book')->delete();

                    // Insert New
                    foreach($request->addmore as $item){
                        if($item['account_code'] != null && $item['nominal'] != null){
                            $nominal = $this->normalizeOpeningBalance($item['nominal']);
                            // Create General Ledger
                            $this->storeGeneralLedger(
                                $id, $transNumber, $request->date_invoice, 
                                $item['account_code'], $item['type'], $nominal, $item['note'], 
                                'Cash Book', null);
                            // Update & Calculate Balance Account Code
                            $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                        }
                    }
                }

                //Audit Log
                $this->auditLogsShort('Update Cash Book Trans. Number ('. $transNumber . ')');
                DB::commit();
                return redirect()->route('cashbook.index')->with(['success' => 'Success Update Cash Book Transaction']);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Cash Book Transaction!']);
            }
        } else {
            return back()->with('info', 'No Changes Detected!');
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $id = decrypt($id);
            $detail  = TransCashBook::findOrFail($id);

            // Validation
            $trxDate = Carbon::parse($detail->date_invoice);
            $now     = Carbon::now();
            if (!$trxDate->isSameMonth($now)) {
                return back()->with('error', 'This transaction cannot be deleted because the transaction month has already passed.');
            }

            $generalLedgers = GeneralLedger::where('id_ref', $id)
                ->where('ref_number', $detail->transaction_number)
                ->where('source', 'Cash Book')
                ->get();
            foreach ($generalLedgers as $item) {
                $reverseType = $item->transaction === 'D' ? 'K' : 'D';
                $nominal = $item->amount;
                $this->updateBalanceAccount($item->id_account_code, $nominal, $reverseType);
            }
            GeneralLedger::where('id_ref', $id)
                ->where('ref_number', $detail->transaction_number)
                ->where('source', 'Cash Book')
                ->delete();
            $detail->delete();

            //Audit Log
            $this->auditLogsShort('Delete Cash Book Transaction ID ('. $id . ')');
            DB::commit();
            return back()->with('success', 'Success Delete Selected Cash Book Transaction');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Failed to Delete Selected Cash Book Transaction!']);
        }
    }

    public function print($id)
    {
        $id = decrypt($id);
        $detail = TransCashBook::where('id', $id)->first();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id)
            ->where('general_ledgers.ref_number', $detail->transaction_number)
            ->where('general_ledgers.source', 'Cash Book')
            ->get();

        $total = rtrim(rtrim($detail->total, '0'), '.');
        $terbilangString = $this->terbilangWithDecimal($total) . " Rupiah.";
        $dateInvoice = $this->formatDateToIndonesian($detail->date_invoice);

        switch ($detail->type) {
            case 'Bukti Kas Keluar':
                $view = 'pdf.cashbookBKK';
                break;

            case 'Bukti Kas Masuk':
                $view = 'pdf.cashbookBKM';
                break;

            case 'Bukti Bank Keluar':
                $view = 'pdf.cashbookBBK';
                break;

            case 'Bukti Bank Masuk':
                $view = 'pdf.cashbookBBM';
                break;

            default:
                throw new \Exception('Invalid cash book type');
        }

        $pdf = PDF::loadView($view, [
            'detail'            => $detail,
            'dateInvoice'       => $dateInvoice,
            'generalLedgers'    => $generalLedgers,
            'terbilangString'   => $terbilangString
        ])->setPaper('a4', 'portrait');

        //Audit Log
        $this->auditLogsShort('Generate PDF Cash Book Transaction ('. $detail->transaction_number . ')');
        return $pdf->stream('Cash Book Transaction ('. $detail->transaction_number . ').pdf', array("Attachment" => false));
    }
}
