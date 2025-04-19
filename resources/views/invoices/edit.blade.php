@extends('layouts.app')

@push('head_scripts')
<script>
// Initialize variables and functions in the global scope
window.itemCount = {{ count($invoice->items) }};
window.serviceTemplates = {!! json_encode($serviceTemplates) !!};

window.addCustomService = function() {
    window.addInvoiceItem();
};

window.addTemplateService = function(templateId) {
    const template = window.serviceTemplates.find(t => t.id === templateId);
    if (!template) return;

    // Update the hidden currency input
    document.querySelector('input[name="currency"]').value = template.currency;
    
    window.addInvoiceItem({
        service_name: template.name_ar,
        description: template.description_ar,
        details: template.details_ar,
        icon: template.icon,
        unit_price: template.default_price,
        quantity: 1,
        service_template_id: template.id,
        is_vat_free: template.is_vat_free
    });

    // Update totals with new currency
    updateTotals();

    // Close the modal
    const modal = document.getElementById('serviceTemplateModal');
    const bootstrapModal = bootstrap.Modal.getInstance(modal);
    if (bootstrapModal) {
        bootstrapModal.hide();
    }
};

window.addInvoiceItem = function(data = null) {
    const container = document.getElementById('invoice-items');
    if (!container) return;

    const selectedCurrency = document.querySelector('input[name="currency"]').value;
    const itemHtml = `
        <div class="card shadow-sm border mb-3 item-card" id="item-${window.itemCount}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Service Details</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceItem(${window.itemCount})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Service Name</label>
                        <input type="text" name="items[${window.itemCount}][service_name]" class="form-control" 
                            value="${data ? data.service_name : ''}" required dir="rtl">
                        ${data && data.service_template_id ? `
                            <input type="hidden" name="items[${window.itemCount}][service_template_id]" value="${data.service_template_id}">
                        ` : ''}
                        ${data && data.is_vat_free ? `
                            <div class="text-success small mt-1">
                                <i class="fas fa-check-circle"></i> VAT Free Service
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Icon (FontAwesome)</label>
                        <input type="text" name="items[${window.itemCount}][icon]" class="form-control" 
                            value="${data ? data.icon : ''}" placeholder="fas fa-server" dir="ltr">
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">Description</label>
                        <textarea name="items[${window.itemCount}][description]" class="form-control" rows="2" 
                            dir="rtl">${data ? data.description : ''}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">Details (One per line)</label>
                        <textarea name="items[${window.itemCount}][details]" class="form-control" rows="3"
                            dir="rtl" placeholder="Enter details, one per line">${data && data.details ? data.details.join('\n') : ''}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Quantity</label>
                        <input type="number" name="items[${window.itemCount}][quantity]" class="form-control quantity-input" 
                            value="${data ? data.quantity : '1'}" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Unit Price (${selectedCurrency})</label>
                        <input type="number" name="items[${window.itemCount}][unit_price]" class="form-control price-input" 
                            value="${data ? data.unit_price : ''}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Total (${selectedCurrency})</label>
                        <input type="text" class="form-control total-input" readonly>
                    </div>
                </div>
                ${data && data.is_vat_free ? `<input type="hidden" class="is-vat-free" value="1">` : ''}
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    window.itemCount++;
    updateTotals();
};

window.removeInvoiceItem = function(itemId) {
    const item = document.getElementById(`item-${itemId}`);
    if (item) {
        item.remove();
        updateTotals();
    }
};

window.updateTotals = function() {
    let subtotal = 0;
    let vatableAmount = 0;
    const vatRate = 14; // 14% VAT
    const selectedCurrency = document.querySelector('input[name="currency"]').value;

    // Calculate item totals
    document.querySelectorAll('.item-card').forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(item.querySelector('.price-input').value) || 0;
        const total = quantity * price;
        const isVatFree = item.querySelector('.is-vat-free') !== null;
        
        item.querySelector('.total-input').value = total.toFixed(2);
        subtotal += total;
        
        if (!isVatFree) {
            vatableAmount += total;
        }
    });

    // Discount logic
    const discountAmountInput = document.getElementById('discount_amount');
    const discountPercentageInput = document.getElementById('discount_percentage');
    let discountAmount = parseFloat(discountAmountInput ? discountAmountInput.value : 0) || 0;
    let discountPercentage = parseFloat(discountPercentageInput ? discountPercentageInput.value : 0) || 0;
    let totalDiscount = discountAmount;
    if (discountPercentage > 0) {
        totalDiscount = subtotal * (discountPercentage / 100);
    }
    // Apply discount to vatable amount proportionally
    if (subtotal > 0) {
        vatableAmount = vatableAmount * ((subtotal - totalDiscount) / subtotal);
    }

    // Update summary
    const vatAmount = vatableAmount * (vatRate / 100);
    const total = subtotal - totalDiscount + vatAmount;

    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    if (document.getElementById('discount-amount-display')) {
        document.getElementById('discount-amount-display').textContent = totalDiscount.toFixed(2);
    }
    document.getElementById('vat-amount').textContent = vatAmount.toFixed(2);
    document.getElementById('total-amount').textContent = total.toFixed(2);

    // Update all currency displays
    document.querySelectorAll('.currency-display').forEach(el => {
        el.textContent = selectedCurrency;
    });
};

// Handle discount input changes
if (document.getElementById('discount_amount')) {
    document.getElementById('discount_amount').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discount_percentage').value = '';
        }
        updateTotals();
    });
}
if (document.getElementById('discount_percentage')) {
    document.getElementById('discount_percentage').addEventListener('input', function() {
        if (this.value) {
            document.getElementById('discount_amount').value = '';
        }
        updateTotals();
    });
}

// Initialize everything when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for calculations
    document.addEventListener('input', function(e) {
        if (e.target.matches('.quantity-input') || e.target.matches('.price-input')) {
            updateTotals();
        }
    });

    // Add existing items
    @foreach($invoice->items as $item)
        window.addInvoiceItem({
            service_name: @json($item->service_name),
            description: @json($item->description),
            details: @json($item->details),
            icon: @json($item->icon),
            quantity: {{ $item->quantity }},
            unit_price: {{ $item->unit_price }},
            service_template_id: {{ $item->service_template_id ?? 'null' }},
            is_vat_free: {{ $item->serviceTemplate && $item->serviceTemplate->is_vat_free ? 'true' : 'false' }}
        });
    @endforeach

    // Set initial currency value
    const defaultCurrency = '{{ old('currency', $invoice->currency) }}';
    document.querySelector('input[name="currency"]').value = defaultCurrency;
    // Set initial discount values
    if (document.getElementById('discount_amount')) {
        document.getElementById('discount_amount').value = '{{ old('discount_amount', $invoice->discount_amount) }}';
    }
    if (document.getElementById('discount_percentage')) {
        document.getElementById('discount_percentage').value = '{{ old('discount_percentage', $invoice->discount_percentage) }}';
    }
    updateTotals();
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary mb-0">Edit Invoice</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}" class="text-decoration-none">Invoices</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Invoice
        </a>
    </div>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Left Column - Main Content -->
            <div class="col-lg-8">
                <!-- Invoice Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Invoice Number -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Invoice Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-hashtag text-muted"></i>
                                    </span>
                                    <input type="text" name="invoice_number" class="form-control border-start-0 ps-0 @error('invoice_number') is-invalid @enderror"
                                        value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Invoice Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input type="date" name="invoice_date" class="form-control border-start-0 ps-0 @error('invoice_date') is-invalid @enderror"
                                        value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                                    @error('invoice_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Due Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                    </span>
                                    <input type="date" name="due_date" class="form-control border-start-0 ps-0 @error('due_date') is-invalid @enderror"
                                        value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Invoice Type -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Invoice Type</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-file-invoice text-muted"></i>
                                    </span>
                                    <select name="invoice_type" class="form-select border-start-0 ps-0 @error('invoice_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="credit" {{ old('invoice_type', $invoice->invoice_type) == 'credit' ? 'selected' : '' }}>Credit</option>
                                        <option value="sales" {{ old('invoice_type', $invoice->invoice_type) == 'sales' ? 'selected' : '' }}>Sales</option>
                                    </select>
                                    @error('invoice_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-check-circle text-muted"></i>
                                    </span>
                                    <select name="status" class="form-select border-start-0 ps-0 @error('status') is-invalid @enderror" required>
                                        <option value="">Select Status</option>
                                        <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending" {{ old('status', $invoice->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Currency -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Currency</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-dollar-sign text-muted"></i>
                                    </span>
                                    <input type="hidden" name="currency" value="{{ old('currency', $invoice->currency) }}" class="form-control border-start-0 ps-0 @error('currency') is-invalid @enderror" required>
                                    <div class="form-control border-start-0 ps-0">
                                        <span class="currency-display">{{ old('currency', $invoice->currency) }}</span>
                                    </div>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Invoice Items</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm" onclick="addCustomService()">
                                    <i class="fas fa-plus me-2"></i>Add Custom Service
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#serviceTemplateModal">
                                    <i class="fas fa-list me-2"></i>From Templates
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="invoice-items"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Customer & Summary -->
            <div class="col-lg-4">
                <!-- Customer Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Customer</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Select Customer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <select name="customer_id" class="form-select border-start-0 ps-0 @error('customer_id') is-invalid @enderror" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                            {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                            @if($customer->company_name)
                                                ({{ $customer->company_name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <div>
                                <span id="subtotal">0.00</span>
                                <span class="ms-1 text-muted currency-display">{{ old('currency', $invoice->currency) }}</span>
                            </div>
                        </div>
                        <!-- Discount Amount -->
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <span class="text-muted">Discount</span>
                            <div class="input-group" style="max-width: 250px;">
                                <input type="number" id="discount_amount" name="discount_amount" class="form-control form-control-sm" min="0" step="0.01" placeholder="Amount" value="{{ old('discount_amount', $invoice->discount_amount) }}">
                                <span class="input-group-text currency-display">{{ old('currency', $invoice->currency) }}</span>
                                <input type="number" id="discount_percentage" name="discount_percentage" class="form-control form-control-sm ms-2" min="0" max="100" step="0.01" placeholder="%" value="{{ old('discount_percentage', $invoice->discount_percentage) }}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Discount Applied</span>
                            <div class="text-danger">
                                -<span id="discount-amount-display">0.00</span>
                                <span class="ms-1 currency-display">{{ old('currency', $invoice->currency) }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">VAT (14%)</span>
                            <div>
                                <span id="vat-amount">0.00</span>
                                <span class="ms-1 text-muted currency-display">{{ old('currency', $invoice->currency) }}</span>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between">
                            <span class="h6 mb-0">Total</span>
                            <div class="h6 mb-0">
                                <span id="total-amount">0.00</span>
                                <span class="ms-1 currency-display">{{ old('currency', $invoice->currency) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Service Templates Modal -->
<div class="modal fade" id="serviceTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Add Service from Templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    @foreach($serviceTemplates as $template)
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-light rounded p-3 me-3">
                                            <i class="{{ $template->icon }} text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-1">{{ $template->name_ar }}</h6>
                                            <div class="small text-muted">{{ $template->description_ar }}</div>
                                        </div>
                                    </div>
                                    @if(!empty($template->details_ar))
                                        <ul class="small mb-3">
                                            @foreach($template->details_ar as $detail)
                                                <li>{{ $detail }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-primary">
                                            {{ number_format($template->default_price, 2) }} {{ $template->currency }}
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTemplateService({{ $template->id }})">
                                            <i class="fas fa-plus me-1"></i>Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection