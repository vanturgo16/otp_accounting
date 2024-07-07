<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstCustomers extends Model
{
    use HasFactory;
    protected $table = 'master_customers';
    protected $guarded=[
        'id'
    ];
}
