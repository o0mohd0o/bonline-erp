@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Receipts</h1>
                    <p class="text-muted small mb-0">Manage your payment receipts</p>
                </div>
                <x-action-button 
                    href="{{ route('receipts.create') }}"
                    icon="plus"
                >
                    Create New Receipt
                </x-action-button>
            </div>

            <!-- Filters -->
            <x-filters 
                :filters="[
                    [
                        'id' => 'receiptnumberfilter',
                        'placeholder' => 'Filter by Receipt #',
                        'options' => $receipts->pluck('receipt_number')->unique()->map(function($number) {
                            return ['value' => $number, 'label' => $number];
                        })->toArray()
                    ],
                    [
                        'id' => 'customerfilter',
                        'placeholder' => 'Filter by Customer',
                        'options' => $receipts->map(function($receipt) {
                            $name = $receipt->customer->first_name . ' ' . $receipt->customer->last_name;
                            return ['value' => $name, 'label' => $name];
                        })->unique()->values()->toArray()
                    ],
                    [
                        'id' => 'amountfilter',
                        'placeholder' => 'Filter by Amount',
                        'options' => $receipts->pluck('amount')->unique()->map(function($amount) {
                            return ['value' => number_format($amount, 2), 'label' => number_format($amount, 2)];
                        })->toArray()
                    ],
                    [
                        'id' => 'currencyfilter',
                        'placeholder' => 'Filter by Currency',
                        'options' => [
                            ['value' => 'USD', 'label' => 'USD - US Dollar'],
                            ['value' => 'EGP', 'label' => 'EGP - Egyptian Pound'],
                            ['value' => 'SAR', 'label' => 'SAR - Saudi Riyal'],
                            ['value' => 'AUD', 'label' => 'AUD - Australian Dollar']
                        ]
                    ],
                    [
                        'id' => 'statusfilter',
                        'placeholder' => 'Filter by Status',
                        'options' => [
                            ['value' => 'pending', 'label' => 'Pending'],
                            ['value' => 'completed', 'label' => 'Completed'],
                            ['value' => 'cancelled', 'label' => 'Cancelled']
                        ]
                    ],
                    [
                        'id' => 'datefilter',
                        'placeholder' => 'Filter by Date',
                        'options' => $receipts->pluck('receipt_date')->unique()->map(function($date) {
                            return [
                                'value' => $date ? $date->format('Y-m-d') : '', 
                                'label' => $date ? $date->format('M d, Y') : 'N/A'
                            ];
                        })->toArray()
                    ]
                ]"
                search-placeholder="Search by receipt number, customer, or amount..."
            />

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <x-table :headers="['Receipt #', 'Customer', 'Amount', 'Currency', 'Status', 'Receipt Date', 'Actions']">
                        @forelse($receipts as $receipt)
                            <tr>
                                <td class="fw-medium" 
                                    data-receiptnumberfilter="{{ $receipt->receipt_number }}" 
                                    data-search="{{ $receipt->receipt_number }}">
                                    {{ $receipt->receipt_number }}
                                </td>
                                <td data-customerfilter="{{ $receipt->customer->first_name }} {{ $receipt->customer->last_name }}" 
                                    data-search="{{ $receipt->customer->first_name }} {{ $receipt->customer->last_name }} {{ $receipt->customer->email }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-user fa-sm"></i>
                                        </div>
                                        <div>
                                            <span class="d-block">{{ $receipt->customer->first_name }} {{ $receipt->customer->last_name }}</span>
                                            <small class="text-muted">{{ $receipt->customer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium" 
                                    data-amountfilter="{{ number_format($receipt->amount, 2) }}" 
                                    data-search="{{ number_format($receipt->amount, 2) }}">
                                    {{ number_format($receipt->amount, 2) }}
                                </td>
                                <td data-currencyfilter="{{ $receipt->currency }}" 
                                    data-search="{{ $receipt->currency }}">
                                    {{ $receipt->currency }}
                                </td>
                                <td data-statusfilter="{{ $receipt->status ?? 'pending' }}" 
                                    data-search="{{ $receipt->status ?? 'pending' }}">
                                    <span @class([
                                        'badge rounded-pill',
                                        'bg-success bg-opacity-10 text-success' => ($receipt->status ?? 'pending') === 'completed',
                                        'bg-warning bg-opacity-10 text-warning' => ($receipt->status ?? 'pending') === 'pending',
                                        'bg-danger bg-opacity-10 text-danger' => ($receipt->status ?? 'pending') === 'cancelled'
                                    ])>
                                        {{ ucfirst($receipt->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td data-datefilter="{{ $receipt->receipt_date ? $receipt->receipt_date->format('Y-m-d') : '' }}" 
                                    data-raw-date="{{ $receipt->receipt_date }}"
                                    data-search="{{ $receipt->receipt_date ? $receipt->receipt_date->format('M d, Y') : 'N/A' }}">
                                    {{ $receipt->receipt_date ? $receipt->receipt_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td>
                                    <x-table.actions>
                                        <x-table.action-button
                                            href="{{ route('receipts.show', $receipt) }}"
                                            icon="eye"
                                            title="View Receipt"
                                        />
                                        <x-table.action-button
                                            href="{{ route('receipts.edit', $receipt) }}"
                                            icon="edit"
                                            title="Edit Receipt"
                                        />
                                    </x-table.actions>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">No receipts found</div>
                                </td>
                            </tr>
                        @endforelse
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('select[id$="filter"]');
    const searchInput = document.querySelector('#searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput?.value?.toLowerCase() || '';
        const activeFilters = {};

        filters.forEach(filter => {
            const value = filter.value;
            if (value) {
                activeFilters[filter.id] = value.toLowerCase();
            }
        });

        tableRows.forEach(row => {
            let show = true;

            // Check filters
            for (const [filterId, filterValue] of Object.entries(activeFilters)) {
                const cell = row.querySelector(`[data-${filterId}]`);
                const cellValue = cell?.dataset[filterId]?.toLowerCase();
                if (cell && cellValue && !cellValue.includes(filterValue)) {
                    show = false;
                    break;
                }
            }

            // Check search term
            if (show && searchTerm) {
                let found = false;
                const searchCells = row.querySelectorAll('[data-search]');
                searchCells.forEach(cell => {
                    const searchValue = cell.dataset.search?.toLowerCase() || '';
                    if (searchValue.includes(searchTerm)) {
                        found = true;
                    }
                });
                show = found;
            }

            row.style.display = show ? '' : 'none';
        });
    }

    filters.forEach(filter => {
        filter.addEventListener('change', filterTable);
    });
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
});
</script>
@endsection