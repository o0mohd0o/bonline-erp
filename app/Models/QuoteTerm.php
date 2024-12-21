<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_default',
        'display_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function quotes()
    {
        return $this->belongsToMany(Quote::class, 'quote_quote_terms');
    }

    public static function getDefaults()
    {
        return self::where('is_default', true)
            ->orderBy('display_order')
            ->get();
    }
}
