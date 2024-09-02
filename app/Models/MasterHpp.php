<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterHpp extends Model
{
    use HasFactory;
    protected $table = 'master_hpps';
    protected $guarded=[
        'id'
    ];
}
