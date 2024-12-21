@extends('layouts.app')

@push('head_scripts')
<script>
// Initialize variables and functions in the global scope
window.itemCount = 0;
window.serviceTemplates = {!! json_encode($serviceTemplates) !!};

window.addCustomService = function() {
    window.addQuoteItem();
};

window.addTemplateService = function(templateId) {
    const template = window.serviceTemplates.find(t => t.id === templateId);
    if (!template) return;

    // Update the hidden currency input
    document.querySelector('input[name="currency"]').value = template.currency;
    
    window.addQuoteItem({
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

window.addQuoteItem = function(data = null) {
    const container = document.getElementById('quote-items');
    if (!container) return;

    const selectedCurrency = document.querySelector('input[name="currency"]').value;
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

    // Update summary
    const vatAmount = vatableAmount * (vatRate / 100);
    const total = subtotal + vatAmount;

    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
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

    // Set initial currency value
    const defaultCurrency = '{{ old('currency', 'EGP') }}';
    document.querySelector('input[name="currency"]').value = defaultCurrency;
    updateTotals();
});
</script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0 text-primary">Create Quote</h1>
                        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('quotes.store') }}" method="POST" id="quoteForm">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Basic Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->customer_type === 'individual' 
                                                    ? $customer->first_name . ' ' . $customer->last_name 
                                                    : $customer->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label fw-medium">Quote Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input type="date" name="quote_date" class="form-control @error('quote_date') is-invalid @enderror" 
                                        value="{{ old('quote_date', date('Y-m-d')) }}" required>
                                    @error('quote_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-medium">Valid Until</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-hourglass-end text-muted"></i>
                                    </span>
                                    <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                        value="{{ old('valid_until') }}">
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <input type="hidden" name="currency" value="EGP">
                        </div>

                        <!-- Items -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">الخدمات</h2>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="add-custom-service">
                                        <i class="fas fa-plus me-1"></i>خدمة مخصصة
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#serviceTemplateModal">
                                        <i class="fas fa-list me-1"></i>إضافة من القوالب
                                    </button>
                                </div>
                            </div>

                            <div id="quote-items">
                                <!-- Items will be added here -->
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">الملاحظات</h2>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-default-notes">
                                    <i class="fas fa-plus me-1"></i>إضافة الملاحظات الافتراضية
                                </button>
                            </div>
                            <textarea name="notes" id="notes" class="form-control" rows="5" dir="rtl"
                                placeholder="أدخل الملاحظات هنا..."></textarea>
                        </div>

                        <!-- Service Template Modal -->
                        <div class="modal fade" id="serviceTemplateModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">إضافة خدمة من القوالب</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-4">
                                            @foreach($serviceTemplates as $template)
                                                <div class="col-md-6">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                @if($template->icon)
                                                                    <i class="{{ $template->icon }} fa-2x text-primary me-3"></i>
                                                                @endif
                                                                <div>
                                                                    <h3 class="h6 mb-1">{{ $template->name_ar }}</h3>
                                                                    <div class="small text-muted">
                                                                        {{ $template->currency }} {{ number_format($template->default_price, 2) }}
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if($template->description_ar)
                                                                <p class="mb-3">{{ $template->description_ar }}</p>
                                                            @endif

                                                            @if(!empty($template->details_ar))
                                                                <ul class="mb-3">
                                                                    @foreach($template->details_ar as $detail)
                                                                        <li>{{ $detail }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif

                                                            <button type="button" class="btn btn-sm btn-outline-primary w-100 add-template"
                                                                data-template-id="{{ $template->id }}">
                                                                <i class="fas fa-plus me-1"></i>إضافة
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="mb-4">
                            <h2 class="h5 mb-3">Terms & Conditions</h2>
                            @foreach($defaultTerms as $term)
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="terms[]" value="{{ $term->id }}" 
                                        class="form-check-input" id="term{{ $term->id }}"
                                        {{ in_array($term->id, old('terms', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="term{{ $term->id }}">
                                        {{ $term->title }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Summary -->
                        <div class="mb-4">
                            <h2 class="h5 mb-3">Summary</h2>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Subtotal</label>
                                    <input type="text" id="subtotal" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">VAT Amount (14%)</label>
                                    <input type="text" id="vat-amount" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Total</label>
                                    <input type="text" id="total-amount" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Quote
                            </button>
                            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
