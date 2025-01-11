<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'script',
        'executer',
        'load_once',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
