<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReportNeraca extends Model
{
    use HasFactory;
    protected $table = 'detail_report_neraca';
    protected $guarded=[
        'id'
    ];
}
