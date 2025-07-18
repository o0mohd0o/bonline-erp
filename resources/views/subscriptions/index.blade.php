@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Subscriptions</h1>
                    <p class="text-muted small mb-0">Manage customer subscriptions and billing cycles</p>
                </div>
                <x-action-button 
                    href="{{ route('subscriptions.create') }}"
                    icon="plus"
                >
                    Add New Subscription
                </x-action-button>
            </div>

            <!-- Filters -->
            <x-filters 
                :filters="[
                    [
                        'id' => 'billingCycleFilter',
                        'placeholder' => 'Filter by Billing Cycle',
                        'options' => [
                            ['value' => 'monthly', 'label' => 'Monthly'],
                            ['value' => 'every_6_months', 'label' => 'Every 6 Months'],
                            ['value' => 'yearly', 'label' => 'Yearly']
                        ]
                    ],
                    [
                        'id' => 'statusFilter',
                        'placeholder' => 'Filter by Status',
                        'options' => [
                            ['value' => 'active', 'label' => 'Active'],
                            ['value' => 'inactive', 'label' => 'Inactive'],
                            ['value' => 'cancelled', 'label' => 'Cancelled'],
                            ['value' => 'expired', 'label' => 'Expired']
                        ]
                    ]
                ]"
                search-placeholder="Search by subscription number, customer name, or service..."
            />

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Active Subscriptions</h6>
                                    <h3 class="mb-0">{{ $subscriptions->where('status', 'active')->count() }}</h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Expiring Soon</h6>
                                    <h3 class="mb-0">{{ $subscriptions->filter(function($s) { return $s->isExpiringSoon(); })->count() }}</h3>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Expired</h6>
                                    <h3 class="mb-0">{{ $subscriptions->where('status', 'expired')->count() }}</h3>
                                </div>
                                <i class="fas fa-times-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-1">Total Revenue</h6>
                                    <h3 class="mb-0">${{ number_format($subscriptions->where('status', 'active')->sum('price'), 2) }}</h3>
                                </div>
                                <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <x-table :headers="['Subscription #', 'Customer', 'Service', 'Billing Cycle', 'Price', 'Status', 'End Date', 'Actions']">
                        @forelse($subscriptions as $subscription)
                        <tr>
                            <td data-billingCycleFilter="{{ $subscription->billing_cycle }}" data-statusFilter="{{ $subscription->status }}">
                                <div class="fw-semibold text-primary">{{ $subscription->subscription_number }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $subscription->customer->full_name }}</div>
                                <div class="text-muted small">{{ $subscription->customer->email }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $subscription->serviceTemplate->getName() }}</div>
                                <div class="text-muted small">{{ Str::limit($subscription->serviceTemplate->getDescription(), 50) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $subscription->billing_cycle_display }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $subscription->currency }} {{ number_format($subscription->price, 2) }}</div>
                            </td>
                            <td>
                                @if($subscription->status === 'active')
                                    @if($subscription->isExpiringSoon())
                                        <span class="badge bg-warning">Expiring Soon</span>
                                    @else
                                        <span class="badge bg-success">{{ $subscription->status_display }}</span>
                                    @endif
                                @elseif($subscription->status === 'expired')
                                    <span class="badge bg-danger">{{ $subscription->status_display }}</span>
                                @elseif($subscription->status === 'cancelled')
                                    <span class="badge bg-secondary">{{ $subscription->status_display }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $subscription->status_display }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $subscription->end_date->format('M j, Y') }}</div>
                                <div class="text-muted small">{{ $subscription->end_date->diffForHumans() }}</div>
                            </td>
                            <td>
                                <x-table.actions>
                                    <x-table.action-button
                                        href="{{ route('subscriptions.show', $subscription) }}"
                                        icon="eye"
                                        variant="outline-primary"
                                        size="sm"
                                    >
                                        View
                                    </x-table.action-button>
                                    
                                    <x-table.action-button
                                        href="{{ route('subscriptions.edit', $subscription) }}"
                                        icon="edit"
                                        variant="outline-secondary"
                                        size="sm"
                                    >
                                        Edit
                                    </x-table.action-button>

                                    @if($subscription->status === 'active')
                                        <form action="{{ route('subscriptions.renew', $subscription) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Are you sure you want to renew this subscription?')">
                                                <i class="fas fa-redo"></i> Renew
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subscription?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </x-table.actions>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p class="mb-0">No subscriptions found.</p>
                                    <a href="{{ route('subscriptions.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Create First Subscription
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 