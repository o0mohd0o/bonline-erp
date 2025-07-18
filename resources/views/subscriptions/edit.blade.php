@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2 text-primary fw-semibold">Edit Subscription</h1>
                    <p class="text-muted mb-0">Subscription #{{ $subscription->subscription_number }}</p>
                </div>
                <x-action-button 
                    href="{{ route('subscriptions.index') }}"
                    icon="arrow-left"
                    variant="secondary"
                    outline
                >
                    Back to List
                </x-action-button>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('subscriptions.update', $subscription) }}" method="POST" id="subscriptionForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Customer & Service Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ (old('customer_id') ?? $subscription->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->full_name }} ({{ $customer->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="service_template_id" class="form-label">Service Template <span class="text-danger">*</span></label>
                                <select name="service_template_id" id="service_template_id" class="form-select @error('service_template_id') is-invalid @enderror" required>
                                    <option value="">Select Service Template</option>
                                    @foreach($serviceTemplates as $template)
                                        <option value="{{ $template->id }}" 
                                                data-price="{{ $template->default_price }}"
                                                data-currency="{{ $template->currency }}"
                                                {{ (old('service_template_id') ?? $subscription->service_template_id) == $template->id ? 'selected' : '' }}>
                                            {{ $template->getName() }} - {{ $template->currency }} {{ number_format($template->default_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_template_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ (old('status') ?? $subscription->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ (old('status') ?? $subscription->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="cancelled" {{ (old('status') ?? $subscription->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="expired" {{ (old('status') ?? $subscription->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Billing Cycle Selection -->
                        <div class="mb-4">
                            <label class="form-label d-block mb-3 text-secondary fw-medium">Billing Cycle <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="billing-cycle-card">
                                        <input 
                                            type="radio" 
                                            name="billing_cycle" 
                                            id="monthly" 
                                            value="monthly"
                                            class="cycle-input"
                                            {{ (old('billing_cycle') ?? $subscription->billing_cycle) == 'monthly' ? 'checked' : '' }}
                                        >
                                        <label class="cycle-label" for="monthly">
                                            <div class="text-center">
                                                <div class="cycle-icon bg-primary-subtle text-primary mb-2">
                                                    <i class="fas fa-calendar-day"></i>
                                                </div>
                                                <h6 class="mb-1">Monthly</h6>
                                                <small class="text-muted">Billed every month</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="billing-cycle-card">
                                        <input 
                                            type="radio" 
                                            name="billing_cycle" 
                                            id="every_6_months" 
                                            value="every_6_months"
                                            class="cycle-input"
                                            {{ (old('billing_cycle') ?? $subscription->billing_cycle) == 'every_6_months' ? 'checked' : '' }}
                                        >
                                        <label class="cycle-label" for="every_6_months">
                                            <div class="text-center">
                                                <div class="cycle-icon bg-warning-subtle text-warning mb-2">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                                <h6 class="mb-1">Every 6 Months</h6>
                                                <small class="text-muted">Billed twice yearly</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="billing-cycle-card">
                                        <input 
                                            type="radio" 
                                            name="billing_cycle" 
                                            id="yearly" 
                                            value="yearly"
                                            class="cycle-input"
                                            {{ (old('billing_cycle') ?? $subscription->billing_cycle) == 'yearly' ? 'checked' : '' }}
                                        >
                                        <label class="cycle-label" for="yearly">
                                            <div class="text-center">
                                                <div class="cycle-icon bg-success-subtle text-success mb-2">
                                                    <i class="fas fa-calendar"></i>
                                                </div>
                                                <h6 class="mb-1">Yearly</h6>
                                                <small class="text-muted">Billed annually</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('billing_cycle')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pricing -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="price" id="price" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price') ?? $subscription->price }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                    <option value="">Select Currency</option>
                                    <option value="USD" {{ (old('currency') ?? $subscription->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="SAR" {{ (old('currency') ?? $subscription->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                                    <option value="EGP" {{ (old('currency') ?? $subscription->currency) == 'EGP' ? 'selected' : '' }}>EGP</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date') ?? $subscription->start_date->format('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="text" class="form-control" value="{{ $subscription->end_date->format('Y-m-d') }}" disabled>
                                <div class="form-text">End date will be recalculated if start date or billing cycle changes</div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-bell text-primary me-2"></i>
                                    Notification Settings
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="notification_email" class="form-label">Notification Email <span class="text-danger">*</span></label>
                                        <input type="email" name="notification_email" id="notification_email" 
                                               class="form-control @error('notification_email') is-invalid @enderror" 
                                               value="{{ old('notification_email') ?? $subscription->notification_email }}" required>
                                        @error('notification_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="notify_before_days" class="form-label">Notify Before Days <span class="text-danger">*</span></label>
                                        <input type="number" min="1" max="90" name="notify_before_days" id="notify_before_days" 
                                               class="form-control @error('notify_before_days') is-invalid @enderror" 
                                               value="{{ old('notify_before_days') ?? $subscription->notify_before_days }}" required>
                                        @error('notify_before_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Number of days before expiry to send notification (1-90)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-cog text-primary me-2"></i>
                                    Additional Settings
                                </h6>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" 
                                           {{ (old('auto_renew') ?? $subscription->auto_renew) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_renew">
                                        Auto-renew subscription
                                        <small class="text-muted d-block">Automatically renew this subscription when it expires</small>
                                    </label>
                                </div>

                                <div class="mb-0">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              placeholder="Add any additional notes about this subscription...">{{ old('notes') ?? $subscription->notes }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill price when service template changes
    const serviceSelect = document.getElementById('service_template_id');
    const priceInput = document.getElementById('price');
    const currencySelect = document.getElementById('currency');

    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const price = selectedOption.getAttribute('data-price');
            const currency = selectedOption.getAttribute('data-currency');
            
            if (price) priceInput.value = price;
            if (currency) currencySelect.value = currency;
        }
    });
});
</script>

<style>
.billing-cycle-card {
    position: relative;
    height: 100%;
}

.cycle-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.cycle-label {
    display: block;
    padding: 1.5rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 100%;
}

.cycle-input:checked + .cycle-label {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.cycle-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 1.25rem;
}
</style>
@endpush
@endsection 