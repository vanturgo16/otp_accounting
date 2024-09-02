<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHpp extends Model
{
    use HasFactory;
    protected $table = 'report_hpp';
    protected $guarded=[
        'id'
    ];
}
