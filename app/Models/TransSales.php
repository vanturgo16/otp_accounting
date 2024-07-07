<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransSales extends Model
{
    use HasFactory;
    protected $table = 'trans_sales';
    protected $guarded=[
        'id'
    ];
}
