<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_id',
        'location_id',
        'total_items',
        'active_items',
        'inactive_items',
        'sold_items',
        'content',
        'filters',
    ];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }
}
