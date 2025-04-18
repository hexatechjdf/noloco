<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropdownMatchable extends Model
{
    use HasFactory;

    protected $fillable = [
        'table',
        'column',
        'content',
    ];
}
