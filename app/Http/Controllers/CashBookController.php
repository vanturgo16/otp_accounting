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

class CashBookController extends Controller
{
    use AuditLogsTrait;
    use GeneralLedgerTrait;

    public function index(Request $request)
    {
        
    }

    public function create(Request $request)
    {
        
    }

    public function store(Request $request)
    {
        
    }
}
