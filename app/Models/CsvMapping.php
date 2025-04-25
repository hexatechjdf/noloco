<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'content',
        'password',
        'unique_field',
    ];

   public function accounts()
   {
    return $this->hasMany(FtpAccount::class, 'mapping_id');
   }
   public function locations()
   {
    return $this->hasMany(CsvMappingLocation::class, 'mapping_id');
   }

   public function outboundAccount()
   {
    return $this->hasOne(FtpAccount::class, 'mapping_id');
   }
}
