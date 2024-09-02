<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReportHpp extends Model
{
    use HasFactory;
    protected $table = 'detail_report_hpp';
    protected $guarded=[
        'id'
    ];
}
