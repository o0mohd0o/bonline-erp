<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    // Billing cycle constants
    const BILLING_CYCLE_MONTHLY = 'monthly';
    const BILLING_CYCLE_EVERY_6_MONTHS = 'every_6_months';
    const BILLING_CYCLE_YEARLY = 'yearly';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'customer_id',
        'service_template_id',
        'subscription_number',
        'billing_cycle',
        'price',
        'currency',
        'start_date',
        'end_date',
        'next_billing_date',
        'status',
        'notification_email',
        'notify_before_days',
        'notification_enabled',
        'last_notification_sent',
        'expiry_notification_sent',
        'auto_renew',
        'notes',
        'website',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'price' => 'decimal:2',
        'notification_enabled' => 'boolean',
        'auto_renew' => 'boolean',
        'last_notification_sent' => 'datetime',
        'expiry_notification_sent' => 'datetime',
        'notify_before_days' => 'integer',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with ServiceTemplate
     */
    public function serviceTemplate()
    {
        return $this->belongsTo(ServiceTemplate::class);
    }

    /**
     * Generate unique subscription number
     */
    public static function generateSubscriptionNumber()
    {
        $prefix = 'SUB';
        $year = date('Y');
        $month = date('m');
        
        $lastSubscription = self::where('subscription_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('subscription_number', 'desc')
            ->first();

        if ($lastSubscription) {
            $lastNumber = intval(substr($lastSubscription->subscription_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $year . $month . $newNumber;
    }

    /**
     * Calculate end date based on billing cycle
     */
    public function calculateEndDate($startDate = null)
    {
        $baseDate = Carbon::parse($startDate ?? $this->start_date)->copy()->startOfDay();

        switch ($this->billing_cycle) {
            case self::BILLING_CYCLE_MONTHLY:
                return $baseDate->addMonthNoOverflow()->subDay();
            case self::BILLING_CYCLE_EVERY_6_MONTHS:
                return $baseDate->addMonthsNoOverflow(6)->subDay();
            case self::BILLING_CYCLE_YEARLY:
                return $baseDate->addYear()->subDay();
            default:
                return $baseDate->addMonthNoOverflow()->subDay();
        }
    }

    /**
     * Check if subscription is expiring soon
     */
    public function isExpiringSoon()
    {
        if (!$this->end_date) {
            return false;
        }

        $endOfDay = $this->end_date->copy()->endOfDay();
        $notificationDays = max((int) $this->notify_before_days, 0);
        $notificationDate = $this->end_date->copy()->subDays($notificationDays)->startOfDay();
        $now = Carbon::now();

        return $now >= $notificationDate && $now <= $endOfDay;
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired()
    {
        if (!$this->end_date) {
            return false;
        }
        return Carbon::now()->greaterThan($this->end_date->copy()->endOfDay());
    }

    /**
     * Check if notification should be sent
     */
    public function shouldSendNotification()
    {
        if (!$this->notification_enabled) {
            return false;
        }

        // Check if we should send expiry warning
        if ($this->isExpiringSoon() && !$this->last_notification_sent) {
            return true;
        }

        // Check if we should send expiry notice
        if ($this->hasExpired() && !$this->expiry_notification_sent) {
            return true;
        }

        return false;
    }

    /**
     * Get billing cycle display name
     */
    public function getBillingCycleDisplayAttribute()
    {
        switch ($this->billing_cycle) {
            case self::BILLING_CYCLE_MONTHLY:
                return 'Monthly';
            case self::BILLING_CYCLE_EVERY_6_MONTHS:
                return 'Every 6 Months';
            case self::BILLING_CYCLE_YEARLY:
                return 'Yearly';
            default:
                return ucfirst($this->billing_cycle);
        }
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for expiring subscriptions
     */
    public function scopeExpiringSoon($query)
    {
        $today = Carbon::today();
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(15));
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        // Consider expired only if end_date is before today (inclusive end-of-day semantics)
        return $query->whereDate('end_date', '<', Carbon::today());
    }

    /**
     * Auto-update status based on dates
     */
    public function updateStatus()
    {
        if ($this->hasExpired() && $this->status === self::STATUS_ACTIVE) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    /**
     * Convert price to EGP for consistent revenue calculations
     */
    public function getPriceInEgpAttribute(): float
    {
        if ($this->currency === 'EGP') {
            return $this->price;
        }

        $rate = $this->getCachedExchangeRate($this->currency, 'EGP');
        return $this->price * $rate;
    }

    /**
     * Get cached exchange rate with API fallback
     */
    private function getCachedExchangeRate($fromCurrency, $toCurrency = 'EGP'): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        // Cache key for the exchange rate
        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";
        
        // Try to get from cache first (cache for 1 hour)
        $cachedRate = \Cache::remember($cacheKey, 3600, function() use ($fromCurrency, $toCurrency) {
            return $this->fetchExchangeRateFromAPI($fromCurrency, $toCurrency);
        });

        return $cachedRate;
    }

    /**
     * Fetch exchange rate from API with fallback
     */
    private function fetchExchangeRateFromAPI($fromCurrency, $toCurrency = 'EGP'): float
    {
        try {
            // Use exchangerate-api.com with API key for better reliability
            $apiKey = env('EXCHANGE_RATE_API_KEY');
            
            if ($apiKey) {
                // Use v6 API with key for higher rate limits
                $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$fromCurrency}";
            } else {
                // Fallback to free tier if no API key
                $url = "https://api.exchangerate-api.com/v4/latest/{$fromCurrency}";
            }
            
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            // Check for API errors
            if (isset($data['result']) && $data['result'] === 'error') {
                throw new \Exception('API Error: ' . ($data['error-type'] ?? 'Unknown error'));
            }
            
            if (isset($data['conversion_rates'][$toCurrency])) {
                $rate = $data['conversion_rates'][$toCurrency];
                \Log::info("Successfully fetched exchange rate: 1 {$fromCurrency} = {$rate} {$toCurrency}");
                return $rate;
            } elseif (isset($data['rates'][$toCurrency])) {
                // Fallback for v4 API format
                $rate = $data['rates'][$toCurrency];
                \Log::info("Successfully fetched exchange rate: 1 {$fromCurrency} = {$rate} {$toCurrency}");
                return $rate;
            }
            
        } catch (\Exception $e) {
            \Log::warning('Currency API failed, using fallback rates: ' . $e->getMessage());
        }

        // Fallback rates if API is unavailable (updated to current market rates)
        $fallbackRates = [
            'USD' => ['EGP' => 49.4, 'SAR' => 3.75, 'AUD' => 1.52],
            'SAR' => ['EGP' => 13.2, 'USD' => 0.27, 'AUD' => 0.405],
            'EGP' => ['USD' => 0.0202, 'SAR' => 0.076, 'AUD' => 0.031],
            'AUD' => ['USD' => 0.66, 'SAR' => 2.47, 'EGP' => 32.6]
        ];
        
        return $fallbackRates[$fromCurrency][$toCurrency] ?? 1.0;
    }

    /**
     * Get current exchange rate from currency to EGP (public method)
     */
    public static function getExchangeRate($fromCurrency, $toCurrency = 'EGP'): float
    {
        $subscription = new static();
        return $subscription->getCachedExchangeRate($fromCurrency, $toCurrency);
    }

    /**
     * Get total revenue in EGP for all active subscriptions
     */
    public static function getTotalRevenueInEgp(): float
    {
        return static::where('status', 'active')
            ->get()
            ->sum('price_in_egp');
    }
}
