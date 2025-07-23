<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTemplate extends Model
{
    use HasFactory;

    // Subscription type constants
    const SUBSCRIPTION_TYPE_ONE_TIME = 'one_time';
    const SUBSCRIPTION_TYPE_MONTHLY = 'monthly';
    const SUBSCRIPTION_TYPE_EVERY_6_MONTHS = 'every_6_months';
    const SUBSCRIPTION_TYPE_YEARLY = 'yearly';

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
        'subscription_type',
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

    /**
     * Get subscription type display label
     */
    public function getSubscriptionTypeLabel()
    {
        return match ($this->subscription_type) {
            self::SUBSCRIPTION_TYPE_ONE_TIME => app()->getLocale() === 'ar' ? 'مرة واحدة' : 'One Time',
            self::SUBSCRIPTION_TYPE_MONTHLY => app()->getLocale() === 'ar' ? 'شهرياً' : 'Monthly',
            self::SUBSCRIPTION_TYPE_EVERY_6_MONTHS => app()->getLocale() === 'ar' ? 'كل 6 أشهر' : 'Every 6 Months',
            self::SUBSCRIPTION_TYPE_YEARLY => app()->getLocale() === 'ar' ? 'سنوياً' : 'Yearly',
            default => app()->getLocale() === 'ar' ? 'غير محدد' : 'Unknown',
        };
    }

    /**
     * Check if service is recurring (subscription-based)
     */
    public function isRecurring()
    {
        return $this->subscription_type !== self::SUBSCRIPTION_TYPE_ONE_TIME;
    }
}
