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
        $date = $startDate ? Carbon::parse($startDate) : Carbon::parse($this->start_date);

        switch ($this->billing_cycle) {
            case self::BILLING_CYCLE_MONTHLY:
                return $date->addMonth();
            case self::BILLING_CYCLE_EVERY_6_MONTHS:
                return $date->addMonths(6);
            case self::BILLING_CYCLE_YEARLY:
                return $date->addYear();
            default:
                return $date->addMonth();
        }
    }

    /**
     * Check if subscription is expiring soon
     */
    public function isExpiringSoon()
    {
        $notificationDate = Carbon::parse($this->end_date)->subDays($this->notify_before_days);
        return Carbon::now() >= $notificationDate && Carbon::now() < $this->end_date;
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired()
    {
        return Carbon::now() > $this->end_date;
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
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('end_date', '<=', Carbon::now()->addDays(15))
            ->where('end_date', '>', Carbon::now());
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::now());
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
}
