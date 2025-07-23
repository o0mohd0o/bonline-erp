@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Service Templates</h1>
                    <p class="text-muted small mb-0">Manage your predefined service templates</p>
                </div>
                <x-action-button 
                    href="{{ route('service-templates.create') }}"
                    icon="plus"
                >
                    Create New Template
                </x-action-button>
            </div>

            <!-- Filters -->
            <x-filters 
                :filters="[
                    [
                        'id' => 'statusFilter',
                        'placeholder' => 'Filter by Status',
                        'options' => [
                            ['value' => '1', 'label' => 'Active'],
                            ['value' => '0', 'label' => 'Inactive']
                        ]
                    ],
                    [
                        'id' => 'currencyFilter',
                        'placeholder' => 'Filter by Currency',
                        'options' => [
                            ['value' => 'USD', 'label' => 'USD - US Dollar'],
                            ['value' => 'EGP', 'label' => 'EGP - Egyptian Pound'],
                            ['value' => 'SAR', 'label' => 'SAR - Saudi Riyal'],
                            ['value' => 'AUD', 'label' => 'AUD - Australian Dollar']
                        ]
                    ],
                    [
                        'id' => 'subscriptionTypeFilter',
                        'placeholder' => 'Filter by Type',
                        'options' => [
                            ['value' => 'one_time', 'label' => 'One Time'],
                            ['value' => 'monthly', 'label' => 'Monthly'],
                            ['value' => 'every_6_months', 'label' => 'Every 6 Months'],
                            ['value' => 'yearly', 'label' => 'Yearly']
                        ]
                    ]
                ]"
                search-placeholder="Search by name or description..."
            />

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <x-table :headers="['Icon', 'Name', 'Description', 'Price', 'Currency', 'Type', 'Status', 'Actions']">
                        @forelse($serviceTemplates as $template)
                            <tr>
                                <td>
                                    <i class="{{ $template->icon }} fa-lg text-primary"></i>
                                </td>
                                <td class="fw-medium">{{ $template->getName() }}</td>
                                <td>{{ Str::limit($template->getDescription(), 100) }}</td>
                                <td class="fw-medium">{{ number_format($template->default_price, 2) }}</td>
                                <td data-currencyFilter="{{ $template->currency }}">{{ $template->currency }}</td>
                                <td data-subscriptionTypeFilter="{{ $template->subscription_type }}">
                                    <span @class([
                                        'badge rounded-pill',
                                        'bg-info bg-opacity-10 text-info' => $template->subscription_type === 'one_time',
                                        'bg-primary bg-opacity-10 text-primary' => $template->subscription_type === 'monthly',
                                        'bg-warning bg-opacity-10 text-warning' => $template->subscription_type === 'every_6_months',
                                        'bg-success bg-opacity-10 text-success' => $template->subscription_type === 'yearly'
                                    ])>
                                        {{ $template->getSubscriptionTypeLabel() }}
                                    </span>
                                </td>
                                <td data-statusFilter="{{ $template->is_active }}">
                                    <span @class([
                                        'badge rounded-pill',
                                        'bg-success bg-opacity-10 text-success' => $template->is_active,
                                        'bg-secondary bg-opacity-10 text-secondary' => !$template->is_active
                                    ])>
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <x-table.actions>
                                        <x-table.action-button 
                                            href="{{ route('service-templates.edit', $template) }}"
                                            icon="edit"
                                            title="Edit Template"
                                        />
                                        <x-table.action-button 
                                            href="{{ route('service-templates.duplicate', $template) }}"
                                            method="POST"
                                            icon="copy"
                                            title="Duplicate Template"
                                            :confirm="true"
                                            confirmMessage="Duplicate this service template?"
                                        />
                                        <x-table.action-button 
                                            href="{{ route('service-templates.destroy', $template) }}"
                                            method="DELETE"
                                            icon="trash"
                                            title="Delete Template"
                                            confirm="true"
                                            confirm-message="Are you sure you want to delete this template?"
                                        />
                                    </x-table.actions>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">No service templates found</div>
                                    <a href="{{ route('service-templates.create') }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Create First Template
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $serviceTemplates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
