<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportMonthly extends Model
{
    use HasFactory;
    protected $table = 'report_montly';
    protected $guarded=[
        'id'
    ];
}
