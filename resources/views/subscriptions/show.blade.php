@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2 text-primary fw-semibold">Subscription Details</h1>
                    <p class="text-muted mb-0">Subscription #{{ $subscription->subscription_number }}</p>
                </div>
                <div class="d-flex gap-2">
                    @if($subscription->status === 'active')
                        <form action="{{ route('subscriptions.renew', $subscription) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to renew this subscription?')">
                                <i class="fas fa-redo me-1"></i> Renew
                            </button>
                        </form>
                    @endif
                    
                    <x-action-button 
                        href="{{ route('subscriptions.edit', $subscription) }}"
                        icon="edit"
                        variant="primary"
                    >
                        Edit
                    </x-action-button>
                    
                    <x-action-button 
                        href="{{ route('subscriptions.index') }}"
                        icon="arrow-left"
                        variant="secondary"
                        outline
                    >
                        Back to List
                    </x-action-button>
                </div>
            </div>

            <!-- Status Alert -->
            @if($subscription->isExpiringSoon())
                <div class="alert alert-warning d-flex align-items-center mb-4">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>
                        <strong>Expiring Soon!</strong> This subscription will expire on {{ $subscription->end_date->format('M j, Y') }} 
                        ({{ $subscription->end_date->diffForHumans() }}).
                    </div>
                </div>
            @elseif($subscription->hasExpired())
                <div class="alert alert-danger d-flex align-items-center mb-4">
                    <i class="fas fa-times-circle me-3"></i>
                    <div>
                        <strong>Expired!</strong> This subscription expired on {{ $subscription->end_date->format('M j, Y') }} 
                        ({{ $subscription->end_date->diffForHumans() }}).
                    </div>
                </div>
            @endif

            <div class="row">
                <!-- Subscription Information -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Subscription Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Subscription Number</label>
                                    <div class="fw-semibold">{{ $subscription->subscription_number }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Status</label>
                                    <div>
                                        @if($subscription->status === 'active')
                                            @if($subscription->isExpiringSoon())
                                                <span class="badge bg-warning fs-6">Expiring Soon</span>
                                            @else
                                                <span class="badge bg-success fs-6">{{ $subscription->status_display }}</span>
                                            @endif
                                        @elseif($subscription->status === 'expired')
                                            <span class="badge bg-danger fs-6">{{ $subscription->status_display }}</span>
                                        @elseif($subscription->status === 'cancelled')
                                            <span class="badge bg-secondary fs-6">{{ $subscription->status_display }}</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">{{ $subscription->status_display }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Billing Cycle</label>
                                    <div class="fw-semibold">{{ $subscription->billing_cycle_display }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Price</label>
                                    <div class="fw-semibold">{{ $subscription->currency }} {{ number_format($subscription->price, 2) }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Start Date</label>
                                    <div class="fw-semibold">{{ $subscription->start_date->format('M j, Y') }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">End Date</label>
                                    <div class="fw-semibold">{{ $subscription->end_date->format('M j, Y') }}</div>
                                    <small class="text-muted">{{ $subscription->end_date->diffForHumans() }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Next Billing Date</label>
                                    <div class="fw-semibold">{{ $subscription->next_billing_date->format('M j, Y') }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Auto Renewal</label>
                                    <div>
                                        @if($subscription->auto_renew)
                                            <span class="badge bg-success">Enabled</span>
                                        @else
                                            <span class="badge bg-secondary">Disabled</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($subscription->notes)
                                <div class="mt-3">
                                    <label class="form-label text-muted small">Notes</label>
                                    <div class="bg-light p-3 rounded">{{ $subscription->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-user text-primary me-2"></i>
                                Customer Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Customer Name</label>
                                    <div class="fw-semibold">{{ $subscription->customer->full_name }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Email</label>
                                    <div class="fw-semibold">{{ $subscription->customer->email }}</div>
                                </div>
                                @if($subscription->customer->phone)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small">Phone</label>
                                        <div class="fw-semibold">{{ $subscription->customer->phone }}</div>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Customer Type</label>
                                    <div class="fw-semibold">{{ ucfirst($subscription->customer->customer_type) }}</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('customers.show', $subscription->customer) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> View Customer Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service Template Information -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-cogs text-primary me-2"></i>
                                Service Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Service Name</label>
                                    <div class="fw-semibold">{{ $subscription->serviceTemplate->getName() }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Default Price</label>
                                    <div class="fw-semibold">{{ $subscription->serviceTemplate->currency }} {{ number_format($subscription->serviceTemplate->default_price, 2) }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">Service Type</label>
                                    <div class="fw-semibold">
                                        <span @class([
                                            'badge rounded-pill',
                                            'bg-info bg-opacity-10 text-info' => $subscription->serviceTemplate->subscription_type === 'one_time',
                                            'bg-primary bg-opacity-10 text-primary' => $subscription->serviceTemplate->subscription_type === 'monthly',
                                            'bg-warning bg-opacity-10 text-warning' => $subscription->serviceTemplate->subscription_type === 'every_6_months',
                                            'bg-success bg-opacity-10 text-success' => $subscription->serviceTemplate->subscription_type === 'yearly'
                                        ])>
                                            {{ $subscription->serviceTemplate->getSubscriptionTypeLabel() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted small">Description</label>
                                    <div class="fw-semibold">{{ $subscription->serviceTemplate->getDescription() }}</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('service-templates.show', $subscription->serviceTemplate) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> View Service Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Notification Settings -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-bell text-primary me-2"></i>
                                Notification Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Notification Email</label>
                                <div class="fw-semibold">{{ $subscription->notification_email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Notify Before</label>
                                <div class="fw-semibold">{{ $subscription->notify_before_days }} days</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Notifications</label>
                                <div>
                                    @if($subscription->notification_enabled)
                                        <span class="badge bg-success">Enabled</span>
                                    @else
                                        <span class="badge bg-secondary">Disabled</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($subscription->last_notification_sent)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Last Warning Sent</label>
                                    <div class="fw-semibold">{{ $subscription->last_notification_sent->format('M j, Y H:i') }}</div>
                                </div>
                            @endif
                            
                            @if($subscription->expiry_notification_sent)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Expiry Notice Sent</label>
                                    <div class="fw-semibold">{{ $subscription->expiry_notification_sent->format('M j, Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt text-primary me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($subscription->status === 'active')
                                    <form action="{{ route('subscriptions.updateStatus', $subscription) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" class="btn btn-outline-warning btn-sm w-100" onclick="return confirm('Are you sure you want to deactivate this subscription?')">
                                            <i class="fas fa-pause me-1"></i> Deactivate
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('subscriptions.updateStatus', $subscription) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Are you sure you want to cancel this subscription?')">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                    </form>
                                @elseif($subscription->status === 'inactive')
                                    <form action="{{ route('subscriptions.updateStatus', $subscription) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="btn btn-outline-success btn-sm w-100" onclick="return confirm('Are you sure you want to activate this subscription?')">
                                            <i class="fas fa-play me-1"></i> Activate
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Test Email Notifications -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0">
                                <i class="fas fa-envelope-open-text text-primary me-2"></i>
                                Test Email Notifications
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Send test emails to <strong>{{ $subscription->notification_email }}</strong> to verify email configuration.
                            </p>
                            
                            <div class="d-grid gap-2">
                                <form action="{{ route('subscriptions.test-warning', $subscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100" onclick="return confirm('Send test expiry warning email to {{ $subscription->notification_email }}?')">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Send Test Warning Email
                                    </button>
                                </form>
                                
                                <form action="{{ route('subscriptions.test-expired', $subscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Send test expired notification email to {{ $subscription->notification_email }}?')">
                                        <i class="fas fa-times-circle me-1"></i> Send Test Expired Email
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    These are test emails and won't affect notification tracking.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 