<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransPurchaseDetailPrice extends Model
{
    use HasFactory;
    protected $table = 'trans_purchase_detail_prices';
    protected $guarded=[
        'id'
    ];
}
