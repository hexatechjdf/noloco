<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMapingUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'attributes',
        'mapping',
        'searchable_fields',
        'displayable_fields',
        'listed_attributes',
        'related_urls',
        'table',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
