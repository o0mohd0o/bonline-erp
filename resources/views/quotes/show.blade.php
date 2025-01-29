@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0 text-primary">Quote Details</h1>
                        <div class="d-flex gap-2">
                            <a href="{{ route('quotes.print', $quote) }}" class="btn btn-outline-dark" target="_blank">
                                <i class="fas fa-print me-2"></i>Print
                            </a>
                            <!-- Status Update -->
                            @if($quote->status === 'draft')
                                <form action="{{ route('quotes.status.update', $quote) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="sent">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>Mark as Sent
                                    </button>
                                </form>
                            @elseif($quote->status === 'sent')
                                <div class="btn-group">
                                    <form action="{{ route('quotes.status.update', $quote) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Accept
                                        </button>
                                    </form>
                                    <form action="{{ route('quotes.status.update', $quote) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times me-2"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="btn-group">
                                <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="{{ route('quotes.print', $quote) }}" class="dropdown-item" target="_blank">
                                            <i class="fas fa-print me-2"></i>Print
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item">
                                            <i class="fas fa-download me-2"></i>Download PDF
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                onclick="return confirm('Are you sure you want to delete this quote?')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Quote Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="h5 mb-3">Quote Information</h2>
                            <table class="table table-sm">
                                <tr>
                                    <th class="ps-0" style="width: 130px;">Quote Number:</th>
                                    <td class="text-muted">{{ $quote->quote_number }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Status:</th>
                                    <td>
                                        <span @class([
                                            'badge rounded-pill',
                                            'bg-secondary bg-opacity-10 text-secondary' => $quote->status === 'draft',
                                            'bg-info bg-opacity-10 text-info' => $quote->status === 'sent',
                                            'bg-success bg-opacity-10 text-success' => $quote->status === 'accepted',
                                            'bg-danger bg-opacity-10 text-danger' => $quote->status === 'rejected',
                                        ])>
                                            {{ ucfirst($quote->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Quote Date:</th>
                                    <td class="text-muted">{{ $quote->quote_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Valid Until:</th>
                                    <td>
                                        @if($quote->valid_until)
                                            <span @class([
                                                'text-danger' => $quote->valid_until->isPast(),
                                                'text-success' => $quote->valid_until->isFuture(),
                                            ])>
                                                {{ $quote->valid_until->format('M d, Y') }}
                                                @if($quote->valid_until->isPast())
                                                    (Expired)
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Currency:</th>
                                    <td class="text-muted">{{ $quote->currency }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h2 class="h5 mb-3">Customer Information</h2>
                            <table class="table table-sm">
                                <tr>
                                    <th class="ps-0" style="width: 130px;">Name:</th>
                                    <td>
                                        <a href="{{ route('customers.show', $quote->customer) }}" class="text-decoration-none">
                                            {{ $quote->customer->customer_type === 'individual' 
                                                ? $quote->customer->first_name . ' ' . $quote->customer->last_name 
                                                : $quote->customer->company_name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Email:</th>
                                    <td class="text-muted">{{ $quote->customer->email }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Phone:</th>
                                    <td class="text-muted">{{ $quote->customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0">Address:</th>
                                    <td class="text-muted">{{ $quote->customer->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Quote Items -->
                    <div class="mb-4">
                        <h2 class="h5 mb-3">Services</h2>
                        <div class="row g-4">
                            @foreach($quote->items as $item)
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
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
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quote Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            @if($quote->notes)
                                <h2 class="h5 mb-3">Notes</h2>
                                <div class="card">
                                    <div class="card-body">
                                        {{ $quote->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h2 class="h5 mb-3">Summary</h2>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal:</span>
                                        <span>{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</span>
                                    </div>
                                    @if($quote->discount_amount > 0 || $quote->discount_percentage > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">
                                            Discount{{ $quote->discount_percentage > 0 ? ' (' . number_format($quote->discount_percentage, 1) . '%)' : '' }}:
                                        </span>
                                        <span class="text-danger">-{{ $quote->currency }} {{ number_format($quote->discount_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">VAT ({{ $quote->vat_rate }}%):</span>
                                        <span>{{ $quote->currency }} {{ number_format($quote->vat_amount, 2) }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-medium">Total:</span>
                                        <span class="fw-bold">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    @if($quote->terms->isNotEmpty())
                        <div>
                            <h2 class="h5 mb-3">Terms & Conditions</h2>
                            <div class="card">
                                <div class="card-body">
                                    @foreach($quote->terms as $term)
                                        <div class="mb-3">
                                            <h3 class="h6 mb-2">{{ $term->title }}</h3>
                                            <p class="mb-0 text-muted">{{ $term->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
