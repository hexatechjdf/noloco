<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrudSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'content',
    ];
}
