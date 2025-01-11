<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationStep extends Model
{
    use HasFactory;
    protected $fillable = [
        'crm_token_id',
        'step_id',
    ];
}
