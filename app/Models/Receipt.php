<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'customer_id',
        'amount',
        'currency',
        'description',
        'receipt_date',
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    // Define relationship to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
