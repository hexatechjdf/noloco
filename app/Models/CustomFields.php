<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFields extends Model
{

    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'field_id',
        'key',
        'content',
    ];
}
