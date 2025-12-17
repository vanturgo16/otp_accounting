<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use App\Traits\GeneralLedgerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Model
use App\Models\MstAccountCodes;
use App\Models\GeneralLedger;
use App\Models\GoodReceiptNote;
use App\Models\GoodReceiptNoteDetail;
use App\Models\PurchaseOrder;
use App\Models\TransPurchase;
use App\Models\MstPpn;
use App\Models\PurchaseRequisitions;

class TransPurchaseController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

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
                ->addColumn('action', function ($data){
                    return view('transpurchase.action', compact('data'));
                })->make(true);
        }
        
        //Audit Log
        $this->auditLogsShort('View List Trans Purchase');

        return view('transpurchase.index',compact('grn_number', 'ref_number', 'po_number', 'tax_invoice_number', 'invoice_number', 'searchDate', 'startdate', 'enddate', 'flag'));
    }

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

        $totalPrice     = (float) $datas->sum('total_price');
        $ppnValue       = (float) ($ppnRate/100) * $totalPrice;
        $total          = (float) $totalPrice + $ppnValue;

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
        $listProduct = json_decode($request->listProduct, true);
        dd($request->all(), $listProduct);

    }
}
