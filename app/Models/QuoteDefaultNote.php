<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteDefaultNote extends Model
{
    protected $fillable = [
        'note_ar',
        'note_en',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public static function getDefaults()
    {
        return static::where('is_active', true)
            ->orderBy('display_order')
            ->pluck('note_ar')
            ->toArray();
    }
}
