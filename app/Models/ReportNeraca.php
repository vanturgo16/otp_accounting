<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportNeraca extends Model
{
    use HasFactory;
    protected $table = 'report_neraca';
    protected $guarded=[
        'id'
    ];
}
