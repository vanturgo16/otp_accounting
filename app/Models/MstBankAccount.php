<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstBankAccount extends Model
{
    use HasFactory;
    protected $table = 'master_bank_account';
    protected $guarded=[
        'id'
    ];
}
