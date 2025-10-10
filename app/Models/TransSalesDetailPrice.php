<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransSalesDetailPrice extends Model
{
    use HasFactory;
    protected $table = 'trans_sales_detail_prices';
    protected $guarded=[
        'id'
    ];
}
