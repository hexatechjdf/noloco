<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoborrowMaping extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'url',
        'title',
        'attributes',
        'mapping',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
