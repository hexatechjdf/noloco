<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvMappingLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapping_id',
        'account_id',
        'location_id',
    ];

    public function account()
    {
     return $this->belongsTo(FtpAccount::class, 'account_id');
    }
}
