<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'details_ar',
        'details_en',
        'icon',
        'default_price',
        'currency',
        'is_vat_free',
        'is_active',
    ];

    protected $casts = [
        'details_ar' => 'array',
        'details_en' => 'array',
        'is_active' => 'boolean',
        'is_vat_free' => 'boolean',
        'default_price' => 'decimal:2',
    ];

    /**
     * Get the name based on the current locale
     */
    public function getName()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the description based on the current locale
     */
    public function getDescription()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * Get the details based on the current locale
     */
    public function getDetails()
    {
        return app()->getLocale() === 'ar' ? $this->details_ar : $this->details_en;
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
