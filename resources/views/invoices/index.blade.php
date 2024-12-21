@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Invoices</h1>
                    <p class="text-muted small mb-0">Manage and track all your invoices</p>
                </div>
                <x-action-button 
                    href="{{ route('invoices.create') }}"
                    icon="plus"
                >
                    Create New Invoice
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
                            ['value' => 'pending', 'label' => 'Pending'],
                            ['value' => 'paid', 'label' => 'Paid'],
                            ['value' => 'cancelled', 'label' => 'Cancelled']
                        ]
                    ],
                    [
                        'id' => 'typeFilter',
                        'placeholder' => 'Filter by Type',
                        'options' => [
                            ['value' => 'credit', 'label' => 'Credit Invoice'],
                            ['value' => 'sales', 'label' => 'Sales Invoice']
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
                search-placeholder="Search by invoice number, customer, or amount..."
            />

            <!-- Table Card -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <x-table :headers="['Invoice Number', 'Date', 'Total Amount', 'Currency', 'Status', 'Type', 'Actions']">
                        @foreach($invoices as $invoice)
                        <tr>
                            <td class="fw-medium">{{ $invoice->invoice_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                            <td class="fw-medium">{{ number_format($invoice->total, 2) }}</td>
                            <td data-currencyFilter="{{ $invoice->currency }}">{{ $invoice->currency }}</td>
                            <td data-statusFilter="{{ $invoice->status }}">
                                <span @class([
                                    'badge rounded-pill',
                                    'bg-success bg-opacity-10 text-success' => $invoice->status === 'paid',
                                    'bg-warning bg-opacity-10 text-warning' => $invoice->status === 'pending',
                                    'bg-info bg-opacity-10 text-info' => $invoice->status === 'draft',
                                    'bg-danger bg-opacity-10 text-danger' => $invoice->status === 'cancelled'
                                ])>
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td data-typeFilter="{{ $invoice->invoice_type }}">
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ ucfirst($invoice->invoice_type) }}
                                </span>
                            </td>
                            <td>
                                <x-table.actions>
                                    <x-table.action-button 
                                        href="{{ route('invoices.show', $invoice->id) }}"
                                        icon="eye"
                                        title="View Invoice"
                                    />
                                    <x-table.action-button 
                                        href="{{ route('invoices.edit', $invoice->id) }}"
                                        icon="edit"
                                        title="Edit Invoice"
                                    />
                                    <x-table.action-button 
                                        href="{{ route('invoices.destroy', $invoice->id) }}"
                                        method="DELETE"
                                        icon="trash"
                                        title="Delete Invoice"
                                        text-color="danger"
                                        confirm
                                        confirm-message="Are you sure you want to delete this invoice?"
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('.filter');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = document.querySelector('#searchInput').value.toLowerCase();
        const statusValue = document.querySelector('#statusFilter').value.toLowerCase();
        const typeValue = document.querySelector('#typeFilter').value.toLowerCase();
        const currencyValue = document.querySelector('#currencyFilter').value.toLowerCase();

        tableRows.forEach(row => {
            const invoiceNumber = row.cells[0].textContent.toLowerCase();
            const status = row.cells[4].getAttribute('data-statusFilter').toLowerCase();
            const type = row.cells[5].getAttribute('data-typeFilter').toLowerCase();
            const currency = row.cells[3].getAttribute('data-currencyFilter').toLowerCase();

            const matchesSearch = invoiceNumber.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesType = !typeValue || type.includes(typeValue);
            const matchesCurrency = !currencyValue || currency.includes(currencyValue);

            row.style.display = matchesSearch && matchesStatus && matchesType && matchesCurrency ? '' : 'none';
        });
    }

    document.querySelector('#searchInput').addEventListener('input', filterTable);
    document.querySelector('#statusFilter').addEventListener('change', filterTable);
    document.querySelector('#typeFilter').addEventListener('change', filterTable);
    document.querySelector('#currencyFilter').addEventListener('change', filterTable);
});
</script>
@endsection
