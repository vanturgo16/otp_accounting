<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

// Traits
use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\GoodReceiptNote;
use App\Models\GoodReceiptNoteDetail;
use App\Models\PurchaseOrder;
use App\Models\TransPurchase;
use App\Models\MstPpn;
use App\Models\PurchaseRequisitions;
use App\Models\TransPurchaseDetailPrice;

class TransPurchaseController extends Controller
{
    use AuditLogsTrait, GeneralLedgerTrait;

    // MODAL VIEW
    public function modalTransaction($id)
    {
        $id = decrypt($id);
        $data = TransPurchase::where('id', $id)->first();
        $datas = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id)
            ->where('general_ledgers.ref_number', $data->invoice_number)
            ->where('general_ledgers.source', 'Purchase')
            ->get();
        return view('transpurchase.modal.list_transaction', compact('data', 'datas'));
    }
    public function modalInfo($id)
    {
        $id = decrypt($id);
        $detail = TransPurchase::where('id', $id)->first();
        $detailTrans = TransPurchaseDetailPrice::where('id_trans_purchase', $id)->get();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id)
            ->where('general_ledgers.ref_number', $detail->invoice_number)
            ->where('general_ledgers.source', 'Purchase')
            ->get();

        return view('transpurchase.modal.info',compact('detail', 'detailTrans', 'generalLedgers'));
    }
    public function modalDelete($id)
    {
        $id = decrypt($id);
        $detail = TransPurchase::where('id', $id)->first();

        return view('transpurchase.modal.delete',compact('detail'));
    }

    // HELPER
    public function getListAvailGRN()
    {
        return GoodReceiptNote::query()
            ->leftJoin(
                'trans_purchase',
                'trans_purchase.id_good_receipt_notes',
                '=',
                'good_receipt_notes.id'
            )
            ->whereNull('trans_purchase.id_good_receipt_notes')
            ->whereIn('good_receipt_notes.status', ['Posted', 'Closed'])
            ->select(
                'good_receipt_notes.id',
                'good_receipt_notes.receipt_number as grn_number',
                'good_receipt_notes.date as grn_date',
                'good_receipt_notes.status'
            )
            ->get();
    }
    public function getDetailGRN($idGRN)
    {
        $prNumber = $poNumber = $supplierName = $requester = null;
        if($idGRN) {
            $grn = GoodReceiptNote::where('id', $idGRN)->first();
            $idPR = $grn->reference_number;
            $pr = PurchaseRequisitions::select('purchase_requisitions.request_number', 'master_suppliers.name', 'master_requester.nm_requester')
                ->leftJoin('master_suppliers', 'purchase_requisitions.id_master_suppliers', 'master_suppliers.id')
                ->leftJoin('master_requester', 'purchase_requisitions.requester', 'master_requester.id')
                ->where('purchase_requisitions.id', $idPR)
                ->first();
            if($pr){
                $prNumber = $pr->request_number;
                $supplierName = $pr->name;
                $requester = $pr->nm_requester;
            }
            $idPO = $grn->id_purchase_orders;
            if ($idPO) {
                $poNumber = optional(PurchaseOrder::where('id', $idPO)->first())->po_number;
            }
        }
        $response = [
            'prNumber'    => $prNumber,
            'poNumber'    => $poNumber,
            'supplierName'=> $supplierName,
            'requester'   => $requester,
        ];
        return $response;
    }
    public function getPriceFromGRN(Request $request)
    {
        $idGRN      = $request->idGRN;
        $ppnRate    = $request->ppnRate;

        $datas = GoodReceiptNoteDetail::select(
                'good_receipt_note_details.id',
                'good_receipt_note_details.id_purchase_requisition_details',
                'good_receipt_note_details.lot_number',
                'good_receipt_note_details.type_product',
                DB::raw("
                    CASE 
                        WHEN good_receipt_note_details.type_product = 'RM' THEN master_raw_materials.description
                        WHEN good_receipt_note_details.type_product = 'WIP' THEN master_wips.description
                        WHEN good_receipt_note_details.type_product = 'FG' THEN master_product_fgs.description
                        WHEN good_receipt_note_details.type_product IN ('TA', 'Other') THEN master_tool_auxiliaries.description
                    END as product
                "),
                'good_receipt_note_details.receipt_qty',
                'master_units.unit as unit',
                DB::raw("
                    COALESCE(
                        purchase_requisition_details.currency,
                        purchase_order_details.currency
                    ) as currency
                "),
                DB::raw("
                    COALESCE(
                        purchase_requisition_details.price,
                        purchase_order_details.price
                    ) as price_origin
                "),
                DB::raw("
                    COALESCE(
                        purchase_requisition_details.price,
                        purchase_order_details.price
                    ) as price
                "),
                DB::raw("
                    (
                        COALESCE(
                            purchase_requisition_details.price,
                            purchase_order_details.price
                        ) * good_receipt_note_details.receipt_qty
                    ) as total_price
                ")
            )
            ->leftJoin('master_raw_materials', function ($join) {
                $join->on('good_receipt_note_details.id_master_products', '=', 'master_raw_materials.id')
                    ->where('good_receipt_note_details.type_product', '=', 'RM');
            })
            ->leftJoin('master_wips', function ($join) {
                $join->on('good_receipt_note_details.id_master_products', '=', 'master_wips.id')
                    ->where('good_receipt_note_details.type_product', '=', 'WIP');
            })
            ->leftJoin('master_product_fgs', function ($join) {
                $join->on('good_receipt_note_details.id_master_products', '=', 'master_product_fgs.id')
                    ->where('good_receipt_note_details.type_product', '=', 'FG');
            })
            ->leftJoin('master_tool_auxiliaries', function ($join) {
                $join->on('good_receipt_note_details.id_master_products', '=', 'master_tool_auxiliaries.id')
                    ->whereIn('good_receipt_note_details.type_product', ['TA', 'Other']);
            })
            ->leftJoin('master_units', 'good_receipt_note_details.master_units_id', 'master_units.id')
            ->leftJoin(
                'purchase_requisition_details',
                'good_receipt_note_details.id_purchase_requisition_details',
                '=',
                'purchase_requisition_details.id'
            )
            ->leftJoin(
                'purchase_order_details',
                'good_receipt_note_details.id_purchase_requisition_details',
                '=',
                'purchase_order_details.id_purchase_requisition_details'
            )
            ->where('good_receipt_note_details.id_good_receipt_notes', $idGRN)
            ->get();

        $totalPrice     = round((float) $datas->sum('total_price'), 2);
        $ppnValue       = round((float) ($ppnRate/100) * $totalPrice, 2);
        $total          = round((float) $totalPrice + $ppnValue, 2);

        if ($request->ajax()) {
            return DataTables::of($datas)
                ->with([
                    'currency'  => optional($datas->first())->currency,
                    'nj'        => $totalPrice,
                    'ppn_rate'  => $ppnRate,
                    'ppn'       => $ppnValue,
                    'total'     => $total,
                ])
                ->toJson();
        }
    }
    public function getDetail(Request $request, $id)
    {
        $data = TransPurchase::where('id', $id)->first();
        $datas = TransPurchaseDetailPrice::select(
                'id_good_receipt_note_details as id',
                'id_good_receipt_note_details',
                'lot_number', 'type_product', 'product', 'receipt_qty', 'unit',
                'price_origin', 'price_edit as price', 'total_price'
            )
            ->where('id_trans_purchase', $id)
            ->get();

        if ($request->ajax()) {
            return DataTables::of($datas)
                ->with([
                    'currency'  => $data->currency,
                    'nj'        => round((float) $data->amount, 2),
                    'ppn_rate'  => $data->ppn_rate,
                    'ppn'       => round((float) $data->ppn_value, 2),
                    'discount'  => round((float) $data->total_discount, 2),
                    'total'     => round((float) $data->total, 2),
                ])
                ->toJson();
        }
    }

    public function index(Request $request)
    {
        $grn_number = $request->get('grn_number');
        $ref_number = $request->get('ref_number');
        $po_number = $request->get('po_number');
        $tax_invoice_number = $request->get('tax_invoice_number');
        $invoice_number = $request->get('invoice_number');
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
            $datas = TransPurchase::select('trans_purchase.*', DB::raw("'Purchase Transaction' as source"))->orderBy('created_at','desc');
            
            if($grn_number != null){
                $datas = $datas->where('grn_number', 'like', '%'.$grn_number.'%');
            }
            if($ref_number != null){
                $datas = $datas->where('ref_number', 'like', '%'.$ref_number.'%');
            }
            if($po_number != null){
                $datas = $datas->where('po_number', 'like', '%'.$po_number.'%');
            }
            if($tax_invoice_number != null){
                $datas = $datas->where('tax_invoice_number', 'like', '%'.$tax_invoice_number.'%');
            }
            if($invoice_number != null){
                $datas = $datas->where('invoice_number', 'like', '%'.$invoice_number.'%');
            }
            if($startdate != null && $enddate != null){
                $datas = $datas->whereDate('trans_purchase.created_at','>=',$startdate)->whereDate('trans_purchase.created_at','<=',$enddate);
            }
            
            if($request->flag != null){
                $datas = $datas->get()->makeHidden(['id']);
                return $datas;
            }
            
            $datas = $datas->get();

            return DataTables::of($datas)
                ->addColumn('count', function ($data){
                    return view('transpurchase.count', compact('data'));
                })
                ->addColumn('action', function ($data){
                    return view('transpurchase.action', compact('data'));
                })->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Purchase');

        return view('transpurchase.index',compact('grn_number', 'ref_number', 'po_number', 'tax_invoice_number', 'invoice_number', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

    public function create(Request $request)
    {
        $grns = $this->getListAvailGRN();
        $accountcodes = MstAccountCodes::where('is_active', 1)->get();
        $initPPN = MstPpn::where('tax_name', 'Trans. Purchase')->where('is_active', 1)->first()->value;

        //Audit Log
        $this->auditLogsShort('View Create New Sales Purchase');

        return view('transpurchase.create',compact('grns', 'accountcodes', 'initPPN'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_invoice'              => 'required',
            'id_good_receipt_notes'     => 'required',
            'grn_number'                => 'required',
            'grn_date'                  => 'required',
            'invoice_number'            => 'required',
            'tax_invoice_number'        => 'required',
            'ppn_rate'                  => 'required',
            'currency'                  => 'required',
            'addmore.*.account_code'    => 'required',
            'addmore.*.nominal'         => 'required',
            'addmore.*.type'            => 'required',
        ]);

        // Init Variable
        $listProduct = json_decode($request->listProduct, true);
        $idGRN       = $request->id_good_receipt_notes;
        $grnNumber   = $request->grn_number;
        $invNumber   = $request->invoice_number;
        if (TransPurchase::where('invoice_number', $invNumber)->exists()) {
            return back()->with(['fail' => 'Invoice Number already exists!']);
        }
        $listProduct = json_decode($request->listProduct, true);
        $disc        = $this->normalizePrice($request->discount);

        $lastStatusGRN = optional(GoodReceiptNote::where('id', $idGRN)->first())->status;

        DB::beginTransaction();
        try{
            $trans = TransPurchase::create([
                'id_good_receipt_notes'=> $idGRN,
                'grn_number'           => $grnNumber,
                'grn_date'             => $request->grn_date,
                'last_status_grn'      => $lastStatusGRN,
                'ref_number'           => $request->ref_number,
                'po_number'            => $request->po_number,
                'suppliers'            => $request->suppliers,
                'requester'            => $request->requester,
                'qty_item'             => $listProduct ? count($listProduct) : 0,
                'invoice_number'       => $invNumber,
                'tax_invoice_number'   => $request->tax_invoice_number,
                'date_invoice'         => $request->date_invoice,
                'note'                 => $request->note,
                'total_transaction'    => $request->addmore ? count($request->addmore) : 0,
                'currency'             => $request->currency,
                'ppn_rate'             => $request->ppn_rate,
                'amount'               => $request->njPrice,
                'ppn_value'            => $request->ppnPrice,
                'total_discount'       => $disc,
                'total'                => $request->totalPrice,
                'created_by'           => auth()->user()->email
            ]);

            foreach($listProduct as $item) {
                TransPurchaseDetailPrice::create([
                    'id_trans_purchase' => $trans->id,
                    'id_good_receipt_note_details'    => $item['idGRNDetail'],
                    'id_purchase_requisition_details' => $item['idPRDetail'],
                    'lot_number'        => $item['lot_number'],
                    'type_product'      => $item['type_product'],
                    'product'           => $item['product'],
                    'receipt_qty'       => $item['receipt_qty'],
                    'unit'              => $item['unit'],
                    'price_origin'      => $item['price_origin'],
                    'price_edit'        => $item['price_edit'],
                    'total_price'       => $item['total_price'],
                ]);
            }

            if($request->addmore != null){
                foreach($request->addmore as $item){
                    if($item['account_code'] != null && $item['nominal'] != null){
                        $nominal = $this->normalizeOpeningBalance($item['nominal']);
                        // Create General Ledger
                        $this->storeGeneralLedger(
                            $trans->id, $invNumber, $request->date_invoice, 
                            $item['account_code'], $item['type'], $nominal, $item['note'], 
                            'Purchase', $grnNumber);
                        // Update & Calculate Balance Account Code
                        $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                    }
                }
            }

            // Update Status GRN
            GoodReceiptNote::where('id', $idGRN)->update(['status' => 'Closed']);

            //Audit Log
            $this->auditLogsShort('Create New Purchase Transaction ID GRN ('. $idGRN . ')');

            DB::commit();
            return redirect()->route('transpurchase.index')->with(['success' => 'Success Create New Purchase Transaction']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New Purchase Transaction!']);
        }
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $detail = TransPurchase::where('id', $id)->first();
        $detailTrans = TransPurchaseDetailPrice::where('id_trans_purchase', $id)->get();
        $generalLedgers = GeneralLedger::select('general_ledgers.*', 'master_account_codes.account_code', 'master_account_codes.account_name')
            ->leftjoin('master_account_codes', 'general_ledgers.id_account_code', 'master_account_codes.id')
            ->where('general_ledgers.id_ref', $id)
            ->where('general_ledgers.ref_number', $detail->invoice_number)
            ->where('general_ledgers.source', 'Purchase')
            ->get();

        $accountcodes = MstAccountCodes::where('is_active', 1)->get();

        //Audit Log
        $this->auditLogsShort('View Edit Purchase Transaction Inv Number ('. $detail->invoice_number . ')');

        return view('transpurchase.edit',compact('detail', 'accountcodes', 'detailTrans', 'generalLedgers'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'date_invoice'              => 'required',
            'invoice_number'            => 'required',
            'tax_invoice_number'        => 'required',
            'ppn_rate'                  => 'required',
            'addmore.*.account_code'    => 'required',
            'addmore.*.nominal'         => 'required',
            'addmore.*.type'            => 'required',
        ]);
        
        $id        = decrypt($id);
        $detail    = TransPurchase::where('id', $id)->lockForUpdate()->first();
        $requestLP = json_decode($request->listProduct, true);
        $disc      = $this->normalizePrice($request->discount);
        $invNumber = $request->invoice_number;

        // Validation
        $trxDate = Carbon::parse($detail->date_invoice);
        $now     = Carbon::now();
        if (!$trxDate->isSameMonth($now)) {
            return back()->with('error', 'This transaction cannot be updated because the transaction month has already passed.');
        }
        $isDuplicate = TransPurchase::where('invoice_number', $invNumber)->where('id', '!=', $id)->exists();
        if ($isDuplicate) {
            return back()->withInput()->with(['error' => 'Invoice number already in use, please use another invoice number']);
        }
        
        $detail->date_invoice = $request->date_invoice . ' 00:00:00';
        $detail->invoice_number = $invNumber;
        $detail->tax_invoice_number = $request->tax_invoice_number;
        $detail->note = $request->note;
        $detail->ppn_rate = $request->ppn_rate;

        $detail->amount         = $this->decimal3($request->njPrice);
        $detail->ppn_value      = $this->decimal3($request->ppnPrice);
        $detail->total_discount = $this->decimal3($disc);
        $detail->total          = $this->decimal3($request->totalPrice);

        $isChangedDetail = $detail->isDirty();

        $existinglistProduct = TransPurchaseDetailPrice::where('id_trans_purchase', $id)->get();
        $existingLP = $existinglistProduct->map(function ($item) {
            return [
                'price_edit'  => $item->price_edit,
                'total_price' => $item->total_price,
            ];
        })->values()->toArray();
        $isChangedLP = $existingLP != $requestLP;

        $existingLedgers = GeneralLedger::where('id_ref', $id)->where('ref_number', $detail->invoice_number)->where('source', 'Purchase')->get();
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

        if($isChangedDetail || $isChangedLP || $isChangedTransaction) {
            DB::beginTransaction();
            try{
                if($isChangedDetail) {
                    TransPurchase::where('id', $id)->update([
                        'invoice_number'       => $invNumber,
                        'tax_invoice_number'   => $request->tax_invoice_number,
                        'date_invoice'         => $request->date_invoice,
                        'note'                 => $request->note,
                        'ppn_rate'             => $request->ppn_rate,
                        'amount'               => $this->decimal3($request->njPrice),
                        'ppn_value'            => $this->decimal3($request->ppnPrice),
                        'total_discount'       => $this->decimal3($disc),
                        'total'                => $this->decimal3($request->totalPrice),
                        'updated_by'           => auth()->user()->email
                    ]);
                }

                if ($isChangedLP) {
                    foreach ($existinglistProduct as $index => $existingItem) {
                        if (!isset($requestLP[$index])) {
                            continue;
                        }

                        $priceEdit  = $this->decimal3($existingItem->price_edit);
                        $totalPrice = $this->decimal3($existingItem->total_price);

                        $newItem = $requestLP[$index];
                        $newPriceEdit  = $this->decimal3($newItem['price_edit']);
                        $newTotalPrice = $this->decimal3($newItem['total_price']);

                        $isDirty = $priceEdit  !== $newPriceEdit || $totalPrice !== $newTotalPrice;
                        if ($isDirty) {
                            $existingItem->update([
                                'price_edit'  => $newPriceEdit,
                                'total_price' => $newTotalPrice,
                            ]);
                        }
                    }
                }

                if($isChangedTransaction) {
                    TransPurchase::where('id', $id)->update([
                        'total_transaction' => $request->addmore ? count($request->addmore) : 0,
                    ]);
                    // Reset Balance Account
                    foreach($existing as $item) {
                        $reverseType = ($item['type'] === 'D') ? 'K' : 'D';
                        $nominal = $this->normalizeOpeningBalance($item['nominal']);
                        $this->updateBalanceAccount($item['account_code'], $nominal, $reverseType);
                    }
                    // Delete General Ledger
                    GeneralLedger::where('id_ref', $id)->where('ref_number', $detail->invoice_number)->where('source', 'Purchase')->delete();

                    // Insert New
                    foreach($request->addmore as $item){
                        if($item['account_code'] != null && $item['nominal'] != null){
                            $nominal = $this->normalizeOpeningBalance($item['nominal']);
                            // Create General Ledger
                            $this->storeGeneralLedger(
                                $id, $invNumber, $request->date_invoice, 
                                $item['account_code'], $item['type'], $nominal, $item['note'], 
                                'Purchase', $detail->ref_number);
                            // Update & Calculate Balance Account Code
                            $this->updateBalanceAccount($item['account_code'], $nominal, $item['type']);
                        }
                    }
                }

                //Audit Log
                $this->auditLogsShort('Update Purchase Transaction Inv. Number ('. $invNumber . ')');
                DB::commit();
                return redirect()->route('transpurchase.index')->with(['success' => 'Success Update Purchase Transaction']);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->with(['fail' => 'Failed to Update Purchase Transaction!']);
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
            $detail  = TransPurchase::findOrFail($id);

            // Validation
            $trxDate = Carbon::parse($detail->date_invoice);
            $now     = Carbon::now();
            if (!$trxDate->isSameMonth($now)) {
                return back()->with('error', 'This transaction cannot be deleted because the transaction month has already passed.');
            }

            $lastStatusGRN = $detail->last_status_grn ?? 'Posted';
            GoodReceiptNote::where('id', $detail->id_good_receipt_notes)->update(['status' => $lastStatusGRN]);
            $generalLedgers = GeneralLedger::where('id_ref', $id)
                ->where('ref_number', $detail->invoice_number)
                ->where('source', 'Purchase')
                ->get();
            foreach ($generalLedgers as $item) {
                $reverseType = $item->transaction === 'D' ? 'K' : 'D';
                $nominal = $item->amount;
                $this->updateBalanceAccount($item->id_account_code, $nominal, $reverseType);
            }
            GeneralLedger::where('id_ref', $id)
                ->where('ref_number', $detail->invoice_number)
                ->where('source', 'Purchase')
                ->delete();
            TransPurchaseDetailPrice::where('id_trans_purchase', $id)->delete();
            $detail->delete();

            //Audit Log
            $this->auditLogsShort('Delete Purchase Transaction ID ('. $id . ')');
            DB::commit();
            return back()->with('success', 'Success Delete Selected Purchase Transaction');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Failed to Delete Selected Purchase Transaction!']);
        }
    }
}
