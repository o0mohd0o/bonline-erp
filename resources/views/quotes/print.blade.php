<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quote #{{ $quote->quote_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        /* Body styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        /* Container styles */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0;
            background: transparent;
        }

        /* Print-specific styles */
        @media print {
            /* Hide print button */
            .no-print { 
                display: none !important; 
            }
            
            /* Reset page margins */
            @page {
                margin: 0.5cm;
                size: A4;
            }
            
            /* Optimize spacing */
            .container {
                margin: 0 !important;
                padding: 10px !important;
            }
            
            .mb-4 {
                margin-bottom: 0.75rem !important;
            }
            
            .p-3 {
                padding: 0.5rem !important;
            }
            
            /* Reduce font sizes */
            body {
                font-size: 14px;
                line-height: 1.4;
            }
            
            .small {
                font-size: 80% !important;
            }
            
            h4 {
                font-size: 1.2rem !important;
            }
            
            h6 {
                font-size: 1rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            /* Optimize table */
            .table {
                margin-bottom: 0.5rem !important;
            }
            
            .table td, .table th {
                padding: 0.25rem !important;
            }
            
            /* Ensure content fits on one page */
            .row {
                break-inside: avoid;
            }
            
            .table-responsive {
                break-inside: avoid;
            }
            
            /* Force background colors */
            .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Ensure text is readable when printed */
            .text-muted { 
                color: #666 !important; 
            }
            
            /* Ensure the page doesn't break in awkward places */
            .row, .table-responsive, .col-6 { 
                break-inside: avoid; 
            }
            
            /* Reduce some spacing for print */
            .mb-4 { 
                margin-bottom: 1rem !important; 
            }
            .p-3 { 
                padding: 0.75rem !important; 
            }
            
            /* Force background colors to print */
            .bg-light { 
                background-color: #f8f9fa !important; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
            .table-light { 
                background-color: #f8f9fa !important; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
            }
        }
    </style>
</head>
<body>
    <!-- Print Button - Will be hidden when printing -->
    <div class="no-print">
        <div class="container py-4">
            <div class="d-flex justify-content-end">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Print Quote
                </button>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Header with Logo and Quote Info -->
        <div class="row align-items-center mb-4">
            <div class="col-6">
                <img src="{{ asset('assets/images/bonline-logo-en.svg') }}" alt="Bonline Logo" style="height: 45px;">
            </div>
            <div class="col-6 text-end">
                <h4 class="text-primary mb-2">Quote #{{ $quote->quote_number }}</h4>
                <div class="text-muted small mb-2">
                    <div>Quote Date: {{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}</div>
                    @if($quote->valid_until)
                        <div>Valid Until: {{ \Carbon\Carbon::parse($quote->valid_until)->format('M d, Y') }}</div>
                    @endif
                </div>
                <div>
                    <span class="badge rounded-3 bg-{{ $quote->status === 'accepted' ? 'success' : ($quote->status === 'sent' ? 'warning' : ($quote->status === 'rejected' ? 'danger' : 'info')) }} bg-opacity-10 text-{{ $quote->status === 'accepted' ? 'success' : ($quote->status === 'sent' ? 'warning' : ($quote->status === 'rejected' ? 'danger' : 'info')) }}">
                        {{ ucfirst($quote->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="row g-4 mb-1">
            <!-- Left Column: Customer Info -->
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 h-100">
                    <h6 class="text-primary mb-3">Customer Information</h6>
                    @if($quote->customer)
                        @if($quote->customer->customer_type === 'individual')
                            <div class="mb-2">
                                <div class="text-muted small">Customer Name</div>
                                <div class="fw-medium">{{ $quote->customer->first_name }} {{ $quote->customer->last_name }}</div>
                            </div>
                        @else
                            <div class="mb-2">
                                <div class="text-muted small">Company</div>
                                <div class="fw-medium">{{ $quote->customer->company_name }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="text-muted small">Contact Person</div>
                                <div class="fw-medium">{{ $quote->customer->contact_person_name }}</div>
                            </div>
                        @endif
                        <div class="mb-2">
                            <div class="text-muted small">Contact</div>
                            <div class="fw-medium">
                                {{ $quote->customer->phone }}<br>
                                {{ $quote->customer->email }}
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Address</div>
                            <div class="fw-medium">{{ $quote->customer->address }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Company Info -->
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 h-100">
                    <h6 class="text-primary mb-3">Company Information</h6>
                    <div class="mb-2">
                        <div class="text-muted small">Company Name</div>
                        <div class="fw-medium">{{ config('company.name') }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Commercial Register</div>
                        <div class="fw-medium">{{ config('company.commercial_register', '397806') }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Contact</div>
                        <div class="fw-medium">
                            {{ config('company.phone', '+20 109 1111 999') }}<br>
                            {{ config('company.email', 'info@bonline.sa') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small">Address</div>
                        <div class="fw-medium">{{ config('company.address', 'Cairo, Egypt') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="mb-4">
            <h6 class="text-primary mb-3">Services</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Service</th>
                            <th class="text-end" style="width: 100px;">Quantity</th>
                            <th class="text-end" style="width: 120px;">Unit Price</th>
                            <th class="text-end" style="width: 120px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
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
                                            @if($item->serviceTemplate && $item->serviceTemplate->subscription_type)
                                                <div class="small mb-1">
                                                    <span @class([
                                                        'badge badge-sm rounded-pill',
                                                        'bg-info bg-opacity-10 text-info' => $item->serviceTemplate->subscription_type === 'one_time',
                                                        'bg-primary bg-opacity-10 text-primary' => $item->serviceTemplate->subscription_type === 'monthly',
                                                        'bg-warning bg-opacity-10 text-warning' => $item->serviceTemplate->subscription_type === 'every_6_months',
                                                        'bg-success bg-opacity-10 text-success' => $item->serviceTemplate->subscription_type === 'yearly'
                                                    ])>
                                                        {{ $item->serviceTemplate->getSubscriptionTypeLabel() }}
                                                    </span>
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
                                <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }} {{ $quote->currency }}</td>
                                <td class="text-end">{{ number_format($item->amount, 2) }} {{ $quote->currency }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end">Subtotal:</td>
                            <td class="text-end fw-medium">{{ number_format($quote->subtotal, 2) }} {{ $quote->currency }}</td>
                        </tr>
                        @if($quote->discount_amount > 0 || $quote->discount_percentage > 0)
                        <tr>
                            <td colspan="4" class="text-end">
                                Discount{{ $quote->discount_percentage > 0 ? ' (' . number_format($quote->discount_percentage, 1) . '%)' : '' }}:
                            </td>
                            <td class="text-end text-danger">-{{ number_format($quote->discount_amount, 2) }} {{ $quote->currency }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end">VAT ({{ number_format($quote->vat_rate, 0) }}%):</td>
                            <td class="text-end">{{ number_format($quote->vat_amount, 2) }} {{ $quote->currency }}</td>
                        </tr>
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-end">{{ number_format($quote->total, 2) }} {{ $quote->currency }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <!-- <div class="row justify-content-end">
            <div class="col-5">
                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-medium">{{ number_format($quote->subtotal, 2) }} {{ $quote->currency }}</span>
                    </div>
                    @if($quote->discount_amount > 0 || $quote->discount_percentage > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">
                            Discount{{ $quote->discount_percentage > 0 ? ' (' . number_format($quote->discount_percentage, 1) . '%)' : '' }}:
                        </span>
                        <span class="text-danger">-{{ number_format($quote->discount_amount, 2) }} {{ $quote->currency }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">VAT ({{ number_format($quote->vat_rate, 0) }}%):</span>
                        <span class="fw-medium">{{ number_format($quote->vat_amount, 2) }} {{ $quote->currency }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-medium">Total:</span>
                        <span class="fw-bold">{{ number_format($quote->total, 2) }} {{ $quote->currency }}</span>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Terms -->
        @if($quote->terms->isNotEmpty())
            <div class="mt-4">
                <h6 class="text-primary mb-3">Terms & Conditions</h6>
                <div class="bg-light rounded-3 p-3">
                    <ul class="mb-0 ps-3">
                        @foreach($quote->terms as $term)
                            <li class="mb-2">{{ $term->content }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Notes -->
        @if($quote->notes)
            <div class="mt-4">
                <h6 class="text-primary mb-3">Notes</h6>
                <div class="bg-light rounded-3 p-3">
                    {!! nl2br(e($quote->notes)) !!}
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="text-center text-muted small">
            <p class="mb-1">Thank you for your business!</p>
            <p class="mb-0">
            {{ config('company.name') }} | 
            {{ config('company.phone') }} | 
            {{ config('company.email') }}
            </p>
        </div>
    </div>
</body>
</html>
