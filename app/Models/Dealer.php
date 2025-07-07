<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'name',
        'role',
        'address',
        'latitude',
        'longitude',
        'email',
        'phone',
        'postal_address',
        'country',
        'city',
    ];
}
