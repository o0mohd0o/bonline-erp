@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-primary">Customer Details</h1>
                    <div class="d-flex gap-2">
                        <x-action-button 
                            href="{{ route('customers.edit', $customer->id) }}"
                            icon="edit"
                            variant="light"
                        >
                            Edit
                        </x-action-button>

                        <x-action-button 
                            href="{{ route('customers.index') }}"
                            icon="arrow-left"
                            variant="secondary"
                            outline
                        >
                            Back to List
                        </x-action-button>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-auto">
                        <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-{{ $customer->customer_type === 'individual' ? 'user' : 'building' }} fa-lg"></i>
                        </div>
                    </div>
                    <div class="col">
                        @if($customer->customer_type === 'individual')
                            <h2 class="h4 mb-1">{{ $customer->first_name }} {{ $customer->last_name }}</h2>
                            <p class="text-muted mb-3">
                                <span class="badge bg-info bg-opacity-10 text-info">Individual Customer</span>
                            </p>
                        @else
                            <h2 class="h4 mb-1">{{ $customer->company_name }}</h2>
                            <p class="text-muted mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary">Company</span>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <!-- Contact Information -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <h3 class="h6 text-muted mb-3">Contact Information</h3>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">{{ $customer->email }}</a>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <a href="tel:{{ $customer->phone }}" class="text-decoration-none">{{ $customer->phone }}</a>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                                <span>{{ $customer->address ?: 'No address provided' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($customer->customer_type === 'company')
                    <!-- Company Contact Person -->
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <h3 class="h6 text-muted mb-3">Company Contact Person</h3>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-muted me-2"></i>
                                <span>{{ $customer->contact_person_name ?: 'Not specified' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-muted me-2"></i>
                                @if($customer->contact_person_phone)
                                    <a href="tel:{{ $customer->contact_person_phone }}" class="text-decoration-none">
                                        {{ $customer->contact_person_phone }}
                                    </a>
                                @else
                                    <span>Not specified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Customer History -->
                <div class="mt-4">
                    <h3 class="h5 mb-3">Recent Activity</h3>
                    @if($customer->invoices->count() > 0)
                        <x-table :headers="['Invoice #', 'Date', 'Amount', 'Status']">
                            @foreach($customer->invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                                <td>{{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    <span @class([
                                        'badge rounded-pill',
                                        'bg-success bg-opacity-10 text-success' => $invoice->status === 'paid',
                                        'bg-warning bg-opacity-10 text-warning' => $invoice->status === 'pending',
                                        'bg-info bg-opacity-10 text-info' => $invoice->status === 'unpaid',
                                        'bg-danger bg-opacity-10 text-danger' => $invoice->status === 'cancelled'
                                    ])>
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </x-table>
                    @else
                        <div class="alert alert-info">
                            No invoices found for this customer.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fw-500 {
    font-weight: 500;
}
.avatar {
    font-size: 24px;
}
</style>
@endsection
