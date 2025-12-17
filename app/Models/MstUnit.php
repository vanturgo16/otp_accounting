<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstUnit extends Model
{
    use HasFactory;
    protected $table = 'master_units';
    protected $guarded = [
        'id'
    ];
}
