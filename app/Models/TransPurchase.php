<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransPurchase extends Model
{
    use HasFactory;
    protected $table = 'trans_purchase';
    protected $guarded=[
        'id'
    ];

    protected $casts = [
        'amount'          => 'decimal:3',
        'ppn_value'       => 'decimal:3',
        'total_discount'  => 'decimal:3',
        'total'           => 'decimal:3',
    ];
}
