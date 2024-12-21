@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Quotes</h1>
                    <p class="text-muted small mb-0">Manage and track customer quotes</p>
                </div>
                <x-action-button 
                    href="{{ route('quotes.create') }}"
                    icon="plus"
                >
                    Create New Quote
                </x-action-button>
            </div>

            <!-- Filters -->
            <x-filters 
                :filters="[
                    [
                        'id' => 'statusFilter',
                        'placeholder' => 'Filter by Status',
                        'options' => [
                            ['value' => 'draft', 'label' => 'Draft'],
                            ['value' => 'sent', 'label' => 'Sent'],
                            ['value' => 'accepted', 'label' => 'Accepted'],
                            ['value' => 'rejected', 'label' => 'Rejected']
                        ]
                    ],
                    [
                        'id' => 'currencyFilter',
                        'placeholder' => 'Filter by Currency',
                        'options' => [
                            ['value' => 'EGP', 'label' => 'EGP - Egyptian Pound']
                        ]
                    ]
                ]"
                search-placeholder="Search by quote number, customer, or amount..."
            />

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    @if($quotes->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                            <p class="h4 text-muted">No quotes found</p>
                            <a href="{{ route('quotes.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Create First Quote
                            </a>
                        </div>
                    @else
                        <x-table :headers="['Quote Number', 'Customer', 'Date', 'Valid Until', 'Total Amount', 'Currency', 'Status', 'Actions']">
                            @foreach($quotes as $quote)
                            <tr>
                                <td class="fw-medium">{{ $quote->quote_number }}</td>
                                <td>
                                    <a href="{{ route('customers.show', $quote->customer) }}" class="text-decoration-none">
                                        {{ $quote->customer->customer_type === 'individual' 
                                            ? $quote->customer->first_name . ' ' . $quote->customer->last_name 
                                            : $quote->customer->company_name }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</td>
                                <td>
                                    @if($quote->valid_until)
                                        <span @class([
                                            'text-danger' => $quote->valid_until->isPast(),
                                            'text-success' => $quote->valid_until->isFuture(),
                                        ])>
                                            {{ $quote->valid_until->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ number_format($quote->total, 2) }}</td>
                                <td data-currencyFilter="{{ $quote->currency }}">{{ $quote->currency }}</td>
                                <td data-statusFilter="{{ $quote->status }}">
                                    <span @class([
                                        'badge rounded-pill',
                                        'bg-secondary bg-opacity-10 text-secondary' => $quote->status === 'draft',
                                        'bg-info bg-opacity-10 text-info' => $quote->status === 'sent',
                                        'bg-success bg-opacity-10 text-success' => $quote->status === 'accepted',
                                        'bg-danger bg-opacity-10 text-danger' => $quote->status === 'rejected'
                                    ])>
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                </td>
                                <td>
                                    <x-table.actions>
                                        <x-table.action-button 
                                            href="{{ route('quotes.show', $quote->id) }}"
                                            icon="eye"
                                            title="View Quote"
                                        />
                                        <x-table.action-button 
                                            href="{{ route('quotes.edit', $quote->id) }}"
                                            icon="edit"
                                            title="Edit Quote"
                                        />
                                        <x-table.action-button 
                                            href="{{ route('quotes.destroy', $quote->id) }}"
                                            method="DELETE"
                                            icon="trash"
                                            title="Delete Quote"
                                            text-color="danger"
                                            confirm="Are you sure you want to delete this quote?"
                                        />
                                    </x-table.actions>
                                </td>
                            </tr>
                            @endforeach
                        </x-table>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($quotes->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $quotes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('.filter');
    const searchInput = document.querySelector('.search-input');
    const tableRows = document.querySelectorAll('tbody tr');

    // Filter function
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeFilters = {};

        filters.forEach(filter => {
            const value = filter.value;
            if (value) {
                activeFilters[filter.id] = value;
            }
        });

        tableRows.forEach(row => {
            let showRow = true;

            // Check filters
            for (const [filterId, filterValue] of Object.entries(activeFilters)) {
                const cell = row.querySelector(`[data-${filterId}="${filterValue}"]`);
                if (!cell) {
                    showRow = false;
                    break;
                }
            }

            // Check search term
            if (showRow && searchTerm) {
                const text = row.textContent.toLowerCase();
                showRow = text.includes(searchTerm);
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Event listeners
    filters.forEach(filter => {
        filter.addEventListener('change', filterTable);
    });

    searchInput.addEventListener('input', filterTable);
});
</script>
@endpush
@endsection
