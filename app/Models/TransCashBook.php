<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransCashBook extends Model
{
    use HasFactory;
    protected $table = 'trans_cash_book';
    protected $guarded=[
        'id'
    ];
}
