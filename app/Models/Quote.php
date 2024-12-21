<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_number',
        'customer_id',
        'quote_date',
        'status',
        'subtotal',
        'vat_rate',
        'vat_amount',
        'total',
        'currency',
        'notes',
        'valid_until',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function terms()
    {
        return $this->belongsToMany(QuoteTerm::class, 'quote_quote_terms');
    }

    public static function generateQuoteNumber()
    {
        $prefix = 'Q-' . date('Y-m');
        
        // Get the highest number for the current month, including soft-deleted records
        $lastNumber = self::withTrashed()
            ->where('quote_number', 'like', $prefix . '-%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(quote_number, "-", -1) AS UNSIGNED) DESC')
            ->value(\DB::raw('CAST(SUBSTRING_INDEX(quote_number, "-", -1) AS UNSIGNED)'));
        
        $newNumber = ($lastNumber ?? 0) + 1;
        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
