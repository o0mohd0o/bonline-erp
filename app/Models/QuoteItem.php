<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'service_name',
        'description',
        'details',
        'icon',
        'quantity',
        'unit_price',
        'amount',
        'service_template_id'
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'amount' => 'float',
        'details' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Calculate amount from quantity and unit_price
            $model->amount = $model->quantity * $model->unit_price;
        });
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function serviceTemplate(): BelongsTo
    {
        return $this->belongsTo(ServiceTemplate::class);
    }
}
