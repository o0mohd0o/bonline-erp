<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class Customer extends Model
{
    use HasFactory;

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'customer_type',
        'first_name',
        'last_name',
        'company_name',
        'contact_person_name',
        'contact_person_phone',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'customer_type' => 'string',
        'status' => 'string',
    ];

    public function getFullNameAttribute()
    {
        if ($this->customer_type === self::TYPE_COMPANY) {
            return $this->company_name;
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->customer_type === self::TYPE_COMPANY) {
            return $this->company_name . ' (' . $this->contact_person_name . ')';
        }
        return $this->full_name;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => '<span class="badge bg-success">نشط</span>',
            self::STATUS_INACTIVE => '<span class="badge bg-danger">غير نشط</span>',
            default => '<span class="badge bg-secondary">غير معروف</span>',
        };
    }

    public function isCompany()
    {
        return $this->customer_type === self::TYPE_COMPANY;
    }

    public function isIndividual()
    {
        return $this->customer_type === self::TYPE_INDIVIDUAL;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
