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

    window.addQuoteItem({
        service_name: template[`name_${window.currentLang}`],
        description: template[`description_${window.currentLang}`],
        details: template[`details_${window.currentLang}`],
        icon: template.icon,
        unit_price: template.default_price,
        quantity: 1
    });

    // Close the modal
    const modal = document.getElementById('serviceTemplateModal');
    const bootstrapModal = bootstrap.Modal.getInstance(modal);
    if (bootstrapModal) {
        bootstrapModal.hide();
    }
};

window.addQuoteItem = function(data = null) {
    const container = document.getElementById('servicesContainer');
    if (!container) return;

    const itemHtml = `
        <div class="card mb-3 item-card" id="item-${window.itemCount}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 mb-0">Service Details</h3>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" onclick="removeQuoteItem(${window.itemCount})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="items[${window.itemCount}][service_name]" class="form-control" 
                            value="${data ? data.service_name : ''}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon (FontAwesome)</label>
                        <input type="text" name="items[${window.itemCount}][icon]" class="form-control" 
                            value="${data ? data.icon : ''}" placeholder="fas fa-server">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="items[${window.itemCount}][description]" class="form-control" rows="2">${data ? data.description : ''}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Details (One point per line)</label>
                        <textarea name="items[${window.itemCount}][details]" class="form-control" rows="3"
                            placeholder="Enter details, one point per line">${data && data.details ? data.details.join('\n') : ''}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${window.itemCount}][quantity]" class="form-control quantity-input" 
                            value="${data ? data.quantity : '1'}" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="items[${window.itemCount}][unit_price]" class="form-control price-input" 
                            value="${data ? data.unit_price : ''}" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control total-input" readonly>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    window.itemCount++;
    updateTotals();
};

window.removeQuoteItem = function(index) {
    const container = document.getElementById('servicesContainer');
    const items = container.querySelectorAll('.item-card');
    
    if (items.length <= 1) {
        // Show error message using Bootstrap toast or alert
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                At least one service item is required.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        const alertContainer = document.createElement('div');
        alertContainer.innerHTML = alertHtml;
        container.parentElement.insertBefore(alertContainer, container);
        return;
    }

    const itemToRemove = document.getElementById(`item-${index}`);
    if (itemToRemove) {
        itemToRemove.remove();
        updateTotals();
    }
};

window.updateTotals = function() {
    const items = document.querySelectorAll('.item-card');
    let subtotal = 0;

    // Calculate totals for each item
    items.forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(item.querySelector('.price-input').value) || 0;
        const total = quantity * unitPrice;
        
        // Update item total
        const totalInput = item.querySelector('.total-input');
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }
        
        subtotal += total;
    });

    // Update summary totals
    const vat = subtotal * 0.14; // 14% VAT
    const total = subtotal + vat;

    // Update summary display
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('vat').textContent = vat.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);

    // Update hidden inputs for form submission
    document.getElementById('subtotal_input').value = subtotal.toFixed(2);
    document.getElementById('vat_input').value = vat.toFixed(2);
    document.getElementById('total_input').value = total.toFixed(2);
};

// Add event listeners for quantity and price changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('servicesContainer').addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
            updateTotals();
        }
    });
    
    // Initial calculation
    updateTotals();
});
</script>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-primary">Edit Quote</h1>
                    <p class="text-muted small mb-0">Update quote details and services</p>
                </div>
                <div>
                    <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-eye me-2"></i>View Quote
                    </a>
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('quotes.update', $quote->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Customer Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Customer Information</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $quote->customer_id) == $customer->id ? 'selected' : '' }}>
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
                    <div class="col-md-3">
                        <label class="form-label">Quote Date</label>
                        <input type="date" name="quote_date" class="form-control @error('quote_date') is-invalid @enderror" 
                            value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" required>
                        @error('quote_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                            value="{{ old('valid_until', optional($quote->valid_until)->format('Y-m-d')) }}">
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Currency Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Currency Settings</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                            <option value="EGP" {{ old('currency', $quote->currency) === 'EGP' ? 'selected' : '' }}>EGP - Egyptian Pound</option>
                            <option value="USD" {{ old('currency', $quote->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="SAR" {{ old('currency', $quote->currency) === 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Services</h2>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" onclick="addCustomService()">
                            <i class="fas fa-plus me-2"></i>Add Custom Service
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#serviceTemplateModal">
                            <i class="fas fa-list me-2"></i>Add from Templates
                        </button>
                    </div>
                </div>

                <div id="servicesContainer">
                    @foreach($quote->items as $index => $item)
                    <div class="card mb-3 item-card" id="item-{{ $index }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="h6 mb-0">Service Details</h3>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" onclick="removeQuoteItem({{ $index }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Service Name</label>
                                    <input type="text" name="items[{{ $index }}][service_name]" class="form-control" 
                                        value="{{ old("items.$index.service_name", $item->service_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Icon (FontAwesome)</label>
                                    <input type="text" name="items[{{ $index }}][icon]" class="form-control" 
                                        value="{{ old("items.$index.icon", $item->icon) }}" placeholder="fas fa-server">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="items[{{ $index }}][description]" class="form-control" rows="2">{{ old("items.$index.description", $item->description) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Details (One point per line)</label>
                                    <textarea name="items[{{ $index }}][details]" class="form-control" rows="3"
                                        placeholder="Enter details, one point per line">{{ old("items.$index.details", is_array($item->details) ? implode("\n", $item->details) : $item->details) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity-input" 
                                        value="{{ old("items.$index.quantity", $item->quantity) }}" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unit Price</label>
                                    <input type="number" name="items[{{ $index }}][unit_price]" class="form-control price-input" 
                                        value="{{ old("items.$index.unit_price", $item->unit_price) }}" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control total-input" value="{{ number_format($item->quantity * $item->unit_price, 2) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quote Summary -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-4">Quote Summary</h2>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="text-start">Subtotal:</td>
                                <td class="text-end">
                                    <span id="subtotal">{{ number_format($quote->subtotal, 2) }}</span>
                                    <input type="hidden" name="subtotal" id="subtotal_input" value="{{ $quote->subtotal }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-start">VAT (14%):</td>
                                <td class="text-end">
                                    <span id="vat">{{ number_format($quote->vat, 2) }}</span>
                                    <input type="hidden" name="vat" id="vat_input" value="{{ $quote->vat }}">
                                </td>
                            </tr>
                            <tr class="fw-bold">
                                <td class="text-start">Total:</td>
                                <td class="text-end">
                                    <span id="total">{{ number_format($quote->total, 2) }}</span>
                                    <input type="hidden" name="total" id="total_input" value="{{ $quote->total }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notes and Terms -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Additional Information</h2>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                            placeholder="Add any additional notes...">{{ old('notes', $quote->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <h3 class="h6 mb-3">Terms & Conditions</h3>
                        @foreach($defaultTerms as $term)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="terms[]" value="{{ $term->id }}" 
                                    class="form-check-input" id="term{{ $term->id }}"
                                    {{ in_array($term->id, old('terms', $quote->terms->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="term{{ $term->id }}">
                                    {{ $term->title }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Quote
                    </button>
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Service Template Modal -->
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
