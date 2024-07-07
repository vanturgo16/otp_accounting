<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteDetail extends Model
{
    use HasFactory;
    protected $table = 'delivery_note_details';
    protected $guarded=[
        'id'
    ];
}
