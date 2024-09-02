<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstPpn extends Model
{
    use HasFactory;
    protected $table = 'master_ppn';
    protected $guarded=[
        'id'
    ];
}
