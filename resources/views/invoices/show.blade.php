@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <x-action-button 
            href="{{ route('invoices.print', $invoice->id) }}"
            icon="print"
            target="_blank"
        >
            Print
        </x-action-button>

        <x-action-button 
            href="{{ route('invoices.edit', $invoice->id) }}"
            icon="edit"
            variant="warning"
        >
            Edit
        </x-action-button>

        <x-action-button 
            href="{{ route('invoices.index') }}"
            icon="arrow-left"
            variant="secondary"
            outline
        >
            Back to List
        </x-action-button>
    </div>

    <!-- Main Invoice Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">
            <!-- Header with Logo and Company Info -->
            <div class="row align-items-start mb-5">
                <div class="col-md-6">
                    <img src="{{ asset('assets/images/bonline-logo-en.svg') }}" alt="Bonline Logo" style="height: 45px;">
                </div>
                <div class="col-md-6 text-md-end">
                    <h4 class="text-primary mb-2">Invoice #{{ $invoice->invoice_number }}</h4>
                    <p class="text-muted mb-1">Created: {{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}</p>
                    <span class="badge rounded-3 bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }} mb-2">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    <span class="badge rounded-3 bg-primary bg-opacity-10 text-primary ms-2">
                        {{ ucfirst($invoice->invoice_type) }} Invoice
                    </span>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-light rounded-3 p-4 mb-4">
                <h5 class="text-primary mb-4">Customer Information</h5>
                @if($invoice->customer)
                    <div class="row g-3">
                        @if($invoice->customer->customer_type === 'individual')
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Customer Name</label>
                                <div class="fw-medium">{{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Company Name</label>
                                <div class="fw-medium">{{ $invoice->customer->company_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Contact Person</label>
                                <div class="fw-medium">{{ $invoice->customer->contact_person_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Contact Phone</label>
                                <div class="fw-medium">{{ $invoice->customer->contact_person_phone }}</div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Email</label>
                            <div class="fw-medium">{{ $invoice->customer->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Phone</label>
                            <div class="fw-medium">{{ $invoice->customer->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Address</label>
                            <div class="fw-medium">{{ $invoice->customer->address }}</div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Customer information is not available.
                    </div>
                @endif
            </div>

            <!-- Invoice Items -->
            <div class="mb-4">
                <h5 class="text-primary mb-4">Invoice Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th style="width: 45%">Service</th>
                                <th class="text-center" style="width: 10%">Qty</th>
                                <th class="text-end" style="width: 20%">Unit Price</th>
                                <th class="text-end" style="width: 20%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            @if($item->icon)
                                                <i class="{{ $item->icon }} me-3 mt-1 text-primary"></i>
                                            @endif
                                            <div class="flex-grow-1">
                                                <div class="fw-medium">{{ $item->service_name }}</div>
                                                @if($item->serviceTemplate && $item->serviceTemplate->is_vat_free)
                                                    <div class="text-success small mb-1">
                                                        <i class="fas fa-check-circle me-1"></i>VAT Free Service
                                                    </div>
                                                @endif
                                                @if($item->description)
                                                    <div class="text-muted mb-1">{{ $item->description }}</div>
                                                @endif
                                                @if(!empty($item->details))
                                                    <div class="small text-muted">
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($item->details as $detail)
                                                                <li>
                                                                    <i class="fas fa-check text-success me-1"></i>
                                                                    {{ $detail }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->unit_price, 2) }} {{ $invoice->currency }}</td>
                                    <td class="text-end fw-medium">{{ number_format($item->total, 2) }} {{ $invoice->currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-medium">Subtotal:</td>
                                <td class="text-end fw-medium">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">VAT ({{ $invoice->vat_rate }}%):</td>
                                <td class="text-end">{{ number_format($invoice->vat_amount, 2) }} {{ $invoice->currency }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="text-end fw-bold">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Total Section -->
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="bg-light rounded-3 p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-medium">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">VAT ({{ number_format($invoice->vat_rate, 0) }}%):</span>
                            <span class="fw-medium">{{ $invoice->currency }} {{ number_format($invoice->vat_amount, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-medium">Total:</span>
                            <span class="h4 mb-0">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <div class="text-muted small">Invoice Date</div>
                        <div class="fw-medium">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Due Date</div>
                        <div class="fw-medium">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Not Set' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection