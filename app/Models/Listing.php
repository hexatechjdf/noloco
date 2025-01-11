<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Onboarding\App\Enums\DescriptionEnum;

class Listing extends Model
{
    protected $table = 'onboarding_steps';
    use HasFactory;

    protected $casts = [
        'custom_values' => 'array',
    ];
    protected $fillable = [
        'title',
        'description',
        'helping_video',
        'tooltip',
        'action',
        'custom_menu_link',
        'is_mark_complete',
        'is_required',
        'completed_by',
        'custom_values',
        'force_completion',
    ];

    public function goto()
    {
        return $this->belongsTo(OnboardingPage::class, 'action', 'title');
    }

    public function getSubDescriptionAttribute()
    {
        $size = DescriptionEnum::getSize();
        return Str::limit(strip_tags($this->description), $size);
    }
}
