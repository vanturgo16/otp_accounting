<?php

namespace App\Http\Controllers;

use App\Models\MasterNeraca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditLogsTrait;
use Yajra\DataTables\Facades\DataTables;

class MasterNeracaController extends Controller
{
    use AuditLogsTrait;
    public function index(Request $request){
        
    }
}
