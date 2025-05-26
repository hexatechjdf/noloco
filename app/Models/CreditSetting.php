<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'client_id',
        'client_secret',
        'account',
        'password',
    ];

}
