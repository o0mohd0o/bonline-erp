<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
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
            
            /* Ensure text is readable when printed */
            .text-muted { 
                color: #666 !important; 
            }
            .small { 
                font-size: 85% !important; 
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
                    <i class="fas fa-print me-2"></i>Print Invoice
                </button>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Header with Logo and Invoice Info -->
        <div class="row align-items-center mb-4">
            <div class="col-6">
                <img src="{{ asset('assets/images/bonline-logo-en.svg') }}" alt="Bonline Logo" style="height: 45px;">
            </div>
            <div class="col-6 text-end">
                <h4 class="text-primary mb-3">Invoice #{{ $invoice->invoice_number }}</h4>
                <div class="d-flex justify-content-end gap-4 mb-3">
                    <div>
                        <div class="text-muted small">Invoice Date</div>
                        <div class="fw-medium">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Due Date</div>
                        <div class="fw-medium">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Not Set' }}</div>
                    </div>
                </div>
                <div>
                    <span class="badge rounded-3 bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    <span class="badge rounded-3 bg-primary bg-opacity-10 text-primary ms-2">
                        {{ ucfirst($invoice->invoice_type) }} Invoice
                    </span>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="row g-4 mb-4">
            <!-- Left Column: Customer Info -->
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 h-100">
                    <h6 class="text-primary mb-3">Customer Information</h6>
                    @if($invoice->customer)
                        @if($invoice->customer->customer_type === 'individual')
                            <div class="mb-2">
                                <div class="text-muted small">Customer Name</div>
                                <div class="fw-medium">{{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</div>
                            </div>
                        @else
                            <div class="mb-2">
                                <div class="text-muted small">Company</div>
                                <div class="fw-medium">{{ $invoice->customer->company_name }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="text-muted small">Contact Person</div>
                                <div class="fw-medium">{{ $invoice->customer->contact_person_name }}</div>
                            </div>
                        @endif
                        <div class="mb-2">
                            <div class="text-muted small">Contact</div>
                            <div class="fw-medium">
                                {{ $invoice->customer->phone }}<br>
                                {{ $invoice->customer->email }}
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small">Address</div>
                            <div class="fw-medium">{{ $invoice->customer->address }}</div>
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
                        <div class="fw-medium">Bonline Co.</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Address</div>
                        <div class="fw-medium">12 Hassan Alshref Nasir City<br>Cairo, Egypt 11725</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Contact</div>
                        <div class="fw-medium">
                            Phone: +201008985681<br>
                            Email: sales@bonline.cc
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small">Registration</div>
                        <div class="fw-medium">CR: 397806 | Tax-ID: 450-399-869</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="mb-4">
            <h6 class="text-primary mb-2">Invoice Items</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
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
            <div class="col-5">
                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-medium">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">VAT ({{ number_format($invoice->vat_rate, 0) }}%):</span>
                        <span class="fw-medium">{{ $invoice->currency }} {{ number_format($invoice->vat_amount, 2) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-medium">Total:</span>
                        <span class="h5 mb-0">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span>
                    </div>
                    <div class="text-muted small text-end mt-2">
                        {{ \App\Helpers\NumberToWordsHelper::convertToArabicWords($invoice->total, $invoice->currency) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-muted small mt-4">
            <p class="mb-1">Thank you for your business!</p>
            <p class="mb-0">This is a computer-generated document and requires no signature.</p>
        </div>
    </div>
</body>
</html>
