<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceList extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'type',
        'status',
    ];
}
