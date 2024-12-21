@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Customers</h1>
                    <p class="text-muted small mb-0">Manage your customer database</p>
                </div>
                <x-action-button 
                    href="{{ route('customers.create') }}"
                    icon="plus"
                >
                    Add New Customer
                </x-action-button>
            </div>

            <!-- Filters -->
            <x-filters 
                :filters="[
                    [
                        'id' => 'typeFilter',
                        'placeholder' => 'Filter by Type',
                        'options' => [
                            ['value' => 'individual', 'label' => 'Individual'],
                            ['value' => 'company', 'label' => 'Company']
                        ]
                    ],
                    [
                        'id' => 'statusFilter',
                        'placeholder' => 'Filter by Status',
                        'options' => [
                            ['value' => 'active', 'label' => 'Active'],
                            ['value' => 'inactive', 'label' => 'Inactive']
                        ]
                    ]
                ]"
                search-placeholder="Search by name, email, or phone..."
            />

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <x-table :headers="['Type', 'Name', 'Contact', 'Status', 'Actions']">
                        @foreach($customers as $customer)
                        <tr>
                            <td data-typeFilter="{{ $customer->customer_type }}">
                                <span @class([
                                    'badge rounded-pill',
                                    'bg-info bg-opacity-10 text-info' => $customer->customer_type === 'individual',
                                    'bg-primary bg-opacity-10 text-primary' => $customer->customer_type === 'company'
                                ])>
                                    {{ ucfirst($customer->customer_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2 bg-{{ $customer->customer_type === 'individual' ? 'info' : 'primary' }} bg-opacity-10 text-{{ $customer->customer_type === 'individual' ? 'info' : 'primary' }} rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-{{ $customer->customer_type === 'individual' ? 'user' : 'building' }} fa-sm"></i>
                                    </div>
                                    <div>
                                        @if($customer->customer_type === 'individual')
                                            <div class="fw-medium" data-search="{{ $customer->first_name }} {{ $customer->last_name }}">
                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                            </div>
                                        @else
                                            <div class="fw-medium" data-search="{{ $customer->company_name }}">
                                                {{ $customer->company_name }}
                                            </div>
                                            @if($customer->contact_person_name)
                                                <div class="small text-muted" data-search="{{ $customer->contact_person_name }}">
                                                    Contact: {{ $customer->contact_person_name }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="vstack gap-1">
                                    @if($customer->email)
                                        <div data-search="{{ $customer->email }}">
                                            <i class="fas fa-envelope text-muted me-1 fa-sm"></i>
                                            <a href="mailto:{{ $customer->email }}" class="text-decoration-none">{{ $customer->email }}</a>
                                        </div>
                                    @endif
                                    @if($customer->phone)
                                        <div data-search="{{ $customer->phone }}">
                                            <i class="fas fa-phone text-muted me-1 fa-sm"></i>
                                            <a href="tel:{{ $customer->phone }}" class="text-decoration-none">{{ $customer->phone }}</a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td data-statusFilter="{{ $customer->status }}">
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="dropdown">
                                        {!! $customer->status_badge !!}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('customers.update-status', $customer) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $customer->status === 'active' ? 'inactive' : 'active' }}">
                                                <button type="submit" class="dropdown-item">
                                                    @if($customer->status === 'active')
                                                        <i class="fas fa-ban text-danger me-1"></i>Deactivate
                                                    @else
                                                        <i class="fas fa-check text-success me-1"></i>Activate
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <x-table.actions>
                                    <x-table.action-button 
                                        href="{{ route('customers.show', $customer->id) }}"
                                        icon="eye"
                                        title="View Details"
                                    />
                                    <x-table.action-button 
                                        href="{{ route('customers.edit', $customer->id) }}"
                                        icon="edit"
                                        title="Edit Customer"
                                    />
                                    <x-table.action-button 
                                        href="{{ route('customers.destroy', $customer->id) }}"
                                        method="DELETE"
                                        icon="trash"
                                        title="Delete Customer"
                                        text-color="danger"
                                        confirm
                                        confirm-message="Are you sure you want to delete this customer?"
                                    />
                                </x-table.actions>
                            </td>
                        </tr>
                        @endforeach
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
.vstack {
    font-size: 0.875rem;
}
</style>
@endsection
