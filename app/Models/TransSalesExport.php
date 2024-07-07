<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransSalesExport extends Model
{
    use HasFactory;
    protected $table = 'trans_sales_export';
    protected $guarded=[
        'id'
    ];
}
