<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransImport extends Model
{
    use HasFactory;
    protected $table = 'trans_import';
    protected $guarded=[
        'id'
    ];
}
