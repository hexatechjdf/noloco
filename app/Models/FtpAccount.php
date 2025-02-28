<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtpAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapping_id',
        'username',
        'password',
        'domain',
        'directory',
        'quota',
        'quota_limit',
        'status',
        'location_id',
    ];

    public function mapping()
    {
     return $this->belongsTo(CsvMapping::class, 'mapping_id');
    }

    public function location()
    {
     return $this->hasOne(CsvMappingLocation::class, 'account_id');
    }

    public function getMainUsernameAttribute()
    {
        return $this->username.'_'.$this->domain;
    }
}
