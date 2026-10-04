<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Address extends Model
{

    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'block',
        'lot',
        'street',
        'subdivision',
        'mobile_number',
    ];
}
