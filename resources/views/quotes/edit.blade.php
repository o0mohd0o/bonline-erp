@extends('layouts.app')

@push('head_scripts')
<script>
// Initialize variables in the global scope
window.itemCount = {{ count($quote->items) }};
window.serviceTemplates = {!! json_encode($serviceTemplates) !!};
window.currentLang = 'en'; // Default language

window.toggleLanguage = function() {
    window.currentLang = window.currentLang === 'en' ? 'ar' : 'en';
    const btn = document.getElementById('langToggleBtn');
    btn.innerHTML = `<i class="fas fa-language me-2"></i>${window.currentLang === 'en' ? 'عربي' : 'English'}`;
    
    // Update template modal content
    const templates = document.querySelectorAll('.template-card');
    templates.forEach(card => {
        const template = window.serviceTemplates.find(t => t.id === parseInt(card.dataset.templateId));
        if (template) {
            card.querySelector('.template-name').textContent = template[`name_${window.currentLang}`];
            card.querySelector('.template-description').textContent = template[`description_${window.currentLang}`];
        }
    });
};

window.addCustomService = function() {
    window.addQuoteItem();
};

window.addTemplateService = function(templateId) {
    const template = window.serviceTemplates.find(t => t.id === templateId);
    if (!template) return;

    // Update the hidden currency input
    document.querySelector('input[name="currency"]').value = template.currency;
    
    window.addQuoteItem({
        service_name: template[`name_${window.currentLang}`],
        description: template[`description_${window.currentLang}`],
        details: template[`details_${window.currentLang}`],
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

window.addQuoteItem = function(data = null) {
    const container = document.getElementById('quote-items');
    if (!container) return;

    const selectedCurrency = document.querySelector('input[name="currency"]').value;
    
    // Helper function to escape HTML
    const escapeHtml = (unsafe) => {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    // Helper function to safely get value
    const safeValue = (value) => {
        if (value === null || value === undefined) return '';
        return escapeHtml(String(value));
    };

    const itemHtml = `
        <div class="card shadow-sm border mb-3 item-card" id="item-${window.itemCount}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Service Details</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuoteItem(${window.itemCount})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Service Name</label>
                        <input type="text" name="items[${window.itemCount}][service_name]" class="form-control" 
                            value="${safeValue(data?.service_name)}" required dir="rtl">
                        ${data?.service_template_id ? `
                            <input type="hidden" name="items[${window.itemCount}][service_template_id]" value="${safeValue(data.service_template_id)}">
                        ` : ''}
                        ${data?.is_vat_free ? `
                            <div class="text-success small mt-1">
                                <i class="fas fa-check-circle"></i> VAT Free Service
                            </div>
                        ` : ''}
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Icon (FontAwesome)</label>
                        <input type="text" name="items[${window.itemCount}][icon]" class="form-control" 
                            value="${safeValue(data?.icon)}" placeholder="fas fa-server" dir="ltr">
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">Description</label>
                        <textarea name="items[${window.itemCount}][description]" class="form-control" rows="2" 
                            dir="rtl">${safeValue(data?.description)}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted">Details (One per line)</label>
                        <textarea name="items[${window.itemCount}][details]" class="form-control" rows="3"
                            dir="rtl" placeholder="Enter details, one per line">${data?.details ? (Array.isArray(data.details) ? data.details.join('\n') : data.details) : ''}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Quantity</label>
                        <input type="number" name="items[${window.itemCount}][quantity]" class="form-control quantity-input" 
                            value="${safeValue(data?.quantity || '1')}" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Unit Price (${selectedCurrency})</label>
                        <input type="number" name="items[${window.itemCount}][unit_price]" class="form-control price-input" 
                            value="${safeValue(data?.unit_price)}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Total (${selectedCurrency})</label>
                        <input type="text" class="form-control total-input" readonly>
                    </div>
                </div>
                ${data?.is_vat_free ? `<input type="hidden" class="is-vat-free" value="1">` : ''}
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    window.itemCount++;
    updateTotals();
};

window.removeQuoteItem = function(itemId) {
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

    // Calculate discount
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
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
    document.getElementById('discount-amount-display').textContent = totalDiscount.toFixed(2);
    document.getElementById('vat-amount').textContent = vatAmount.toFixed(2);
    document.getElementById('total-amount').textContent = total.toFixed(2);

    // Update all currency displays
    document.querySelectorAll('.currency-display').forEach(el => {
        el.textContent = selectedCurrency;
    });
};

// Initialize everything when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for calculations
    document.addEventListener('input', function(e) {
        if (e.target.matches('.quantity-input') || e.target.matches('.price-input')) {
            updateTotals();
        }
    });

    // Handle discount input changes
    const discountAmount = document.getElementById('discount_amount');
    const discountPercentage = document.getElementById('discount_percentage');

    if (discountAmount) {
        discountAmount.addEventListener('input', function() {
            if (this.value && discountPercentage) {
                discountPercentage.value = '';
            }
            updateTotals();
        });
    }

    if (discountPercentage) {
        discountPercentage.addEventListener('input', function() {
            if (this.value && discountAmount) {
                discountAmount.value = '';
            }
            updateTotals();
        });
    }

    // Set initial currency value
    const defaultCurrency = '{{ old('currency', $quote->currency) }}';
    document.querySelector('input[name="currency"]').value = defaultCurrency;

    // Load existing quote items
    const existingItems = {!! json_encode($quote->items) !!};
    existingItems.forEach(item => {
        window.addQuoteItem({
            service_name: item.service_name,
            description: item.description,
            details: item.details,
            icon: item.icon,
            unit_price: item.unit_price,
            quantity: item.quantity,
            service_template_id: item.service_template_id,
            is_vat_free: item.is_vat_free
        });
    });

    updateTotals();
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary mb-0">Edit Quote #{{ $quote->quote_number }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}" class="text-decoration-none">Quotes</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" id="langToggleBtn" class="btn btn-outline-secondary me-2" onclick="toggleLanguage()">
                <i class="fas fa-language me-2"></i>عربي
            </button>
            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Quotes
            </a>
        </div>
    </div>

    <form action="{{ route('quotes.update', $quote) }}" method="POST" id="quoteForm">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Left Column - Main Content -->
            <div class="col-lg-8">
                <!-- Quote Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Quote Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Quote Number -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted">Quote Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-hashtag text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" 
                                        value="{{ $quote->quote_number }}" readonly>
                                </div>
                                <small class="text-muted">Auto-generated quote number</small>
                            </div>

                            <!-- Quote Date -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Quote Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input type="date" name="quote_date" class="form-control border-start-0 ps-0 @error('quote_date') is-invalid @enderror"
                                        value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" required>
                                    @error('quote_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Valid Until -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Valid Until</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                    </span>
                                    <input type="date" name="valid_until" class="form-control border-start-0 ps-0 @error('valid_until') is-invalid @enderror"
                                        value="{{ old('valid_until', optional($quote->valid_until)->format('Y-m-d')) }}">
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Currency -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Currency</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-dollar-sign text-muted"></i>
                                    </span>
                                    <input type="hidden" name="currency" value="{{ old('currency', $quote->currency) }}" required>
                                    <div class="form-control border-start-0 ps-0">
                                        <span class="currency-display">{{ old('currency', $quote->currency) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Discount Amount -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Discount Amount</label>
                                <div class="input-group">
                                    <input type="number" id="discount_amount" name="discount_amount" 
                                        class="form-control @error('discount_amount') is-invalid @enderror" 
                                        min="0" step="0.01" placeholder="Amount"
                                        value="{{ old('discount_amount', $quote->discount_amount) }}">
                                    <span class="input-group-text currency-display">{{ $quote->currency }}</span>
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Discount Percentage -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Discount Percentage</label>
                                <div class="input-group">
                                    <input type="number" id="discount_percentage" name="discount_percentage" 
                                        class="form-control @error('discount_percentage') is-invalid @enderror" 
                                        min="0" max="100" step="0.01" placeholder="Percentage"
                                        value="{{ old('discount_percentage', $quote->discount_percentage) }}">
                                    <span class="input-group-text">%</span>
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote Items Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Quote Items</h5>
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
                    <div class="card-body p-0">
                        <div id="quote-items" class="p-4"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Customer & Summary -->
            <div class="col-lg-4">
                <!-- Customer Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Select Customer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <select name="customer_id" class="form-select border-start-0 ps-0 @error('customer_id') is-invalid @enderror" required>
                                    <option value="">Choose a customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $quote->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->customer_type === 'company' ? $customer->company_name : $customer->first_name . ' ' . $customer->last_name }}
                                            @if($customer->customer_type === 'company' && $customer->contact_person_name)
                                                ({{ $customer->contact_person_name }})
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

                <!-- Quote Summary Card -->
                <div class="card shadow-sm bg-light">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Quote Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <div>
                                <span id="subtotal">0.00</span>
                                <span class="ms-1 text-muted currency-display">{{ $quote->currency }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Discount</span>
                            <div class="text-danger">
                                -<span id="discount-amount-display">0.00</span>
                                <span class="ms-1 text-muted currency-display">{{ $quote->currency }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">VAT (14%)</span>
                            <div>
                                <span id="vat-amount">0.00</span>
                                <span class="ms-1 text-muted currency-display">{{ $quote->currency }}</span>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between">
                            <span class="h6 mb-0">Total</span>
                            <div class="h6 mb-0">
                                <span id="total-amount">0.00</span>
                                <span class="ms-1 currency-display">{{ $quote->currency }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Quote
                            </button>
                            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
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
            <div class="modal-header">
                <h5 class="modal-title">Add Service from Templates</h5>
                <div>
                    <button type="button" id="langToggleBtn" class="btn btn-outline-secondary me-2" onclick="toggleLanguage()">
                        <i class="fas fa-language me-2"></i>عربي
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    @foreach($serviceTemplates as $template)
                    <div class="col-md-6">
                        <div class="card h-100 template-card" data-template-id="{{ $template->id }}">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="{{ $template->icon }} fa-2x me-3 text-primary"></i>
                                    <h3 class="h6 mb-0 template-name">{{ $template->name_en }}</h3>
                                </div>
                                <p class="small text-muted mb-3 template-description">{{ $template->description_en }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ number_format($template->default_price, 2) }} {{ $template->currency }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="addTemplateService({{ $template->id }})">
                                        <i class="fas fa-plus me-2"></i>Add Service
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