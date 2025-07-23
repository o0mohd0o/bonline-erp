@extends('layouts.app')

@push('head_scripts')
<script>
window.updateAmountDisplay = function() {
    const amount = document.getElementById('amount').value;
    const currency = document.getElementById('currency').value;
    const formattedAmount = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
    document.getElementById('amount-display').textContent = formattedAmount;
};

window.toggleLanguage = function() {
    const elements = document.querySelectorAll('[data-lang]');
    elements.forEach(el => {
        const currentLang = el.getAttribute('data-lang-current');
        const arText = el.getAttribute('data-lang-ar');
        const enText = el.getAttribute('data-lang-en');
        
        if (currentLang === 'en') {
            el.textContent = arText;
            el.setAttribute('data-lang-current', 'ar');
            document.documentElement.dir = 'rtl';
        } else {
            el.textContent = enText;
            el.setAttribute('data-lang-current', 'en');
            document.documentElement.dir = 'ltr';
        }
    });
};
</script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary mb-0" data-lang data-lang-en="Create New Receipt" data-lang-ar="إنشاء إيصال جديد" data-lang-current="en">Create New Receipt</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Receipts</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary" onclick="toggleLanguage()">
                <i class="fas fa-language me-2"></i>
                <span data-lang data-lang-en="عربي" data-lang-ar="English" data-lang-current="en">عربي</span>
            </button>
        </div>
    </div>

    <form action="{{ route('receipts.store') }}" method="POST" id="receiptForm">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column - Main Content -->
            <div class="col-lg-8">
                <!-- Receipt Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0" data-lang data-lang-en="Receipt Details" data-lang-ar="تفاصيل الإيصال" data-lang-current="en">Receipt Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Receipt Number -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted" data-lang data-lang-en="Receipt Number" data-lang-ar="رقم الإيصال" data-lang-current="en">Receipt Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-hashtag text-muted"></i>
                                    </span>
                                    <input type="text" 
                                        name="receipt_number" 
                                        class="form-control border-start-0 @error('receipt_number') is-invalid @enderror" 
                                        value="{{ old('receipt_number') }}"
                                        required
                                        placeholder="R-YYYY-XXX">
                                    @error('receipt_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Receipt Date -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted" data-lang data-lang-en="Receipt Date" data-lang-ar="تاريخ الإيصال" data-lang-current="en">Receipt Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                    </span>
                                    <input type="date" 
                                        name="receipt_date" 
                                        class="form-control border-start-0 @error('receipt_date') is-invalid @enderror"
                                        value="{{ old('receipt_date', date('Y-m-d')) }}"
                                        required>
                                    @error('receipt_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Currency Selection -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted" data-lang data-lang-en="Currency" data-lang-ar="العملة" data-lang-current="en">Currency</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-coins text-muted"></i>
                                    </span>
                                    <select name="currency" 
                                        id="currency"
                                        class="form-select border-start-0 @error('currency') is-invalid @enderror"
                                        required
                                        onchange="updateAmountDisplay()">
                                        <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>EGP - Egyptian Pound</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                                        <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label small text-muted" data-lang data-lang-en="Amount" data-lang-ar="المبلغ" data-lang-current="en">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-dollar-sign text-muted"></i>
                                    </span>
                                    <input type="number" 
                                        id="amount"
                                        name="amount" 
                                        class="form-control border-start-0 @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}"
                                        step="0.01"
                                        required
                                        placeholder="0.00"
                                        onchange="updateAmountDisplay()">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label small text-muted" data-lang data-lang-en="Description" data-lang-ar="الوصف" data-lang-current="en">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-align-left text-muted"></i>
                                    </span>
                                    <textarea 
                                        name="description" 
                                        class="form-control border-start-0 @error('description') is-invalid @enderror"
                                        rows="3"
                                        placeholder="Payment description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Customer & Summary -->
            <div class="col-lg-4">
                <!-- Customer Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0" data-lang data-lang-en="Customer Information" data-lang-ar="معلومات العميل" data-lang-current="en">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted" data-lang data-lang-en="Select Customer" data-lang-ar="اختر العميل" data-lang-current="en">Select Customer</label>
                            <select name="customer_id" 
                                class="form-select @error('customer_id') is-invalid @enderror"
                                required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0" data-lang data-lang-en="Receipt Summary" data-lang-ar="ملخص الإيصال" data-lang-current="en">Receipt Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <span class="text-muted" data-lang data-lang-en="Total Amount" data-lang-ar="المبلغ الإجمالي" data-lang-current="en">Total Amount</span>
                            <span class="h4 mb-0" id="amount-display">0.00</span>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                <span data-lang data-lang-en="Save Receipt" data-lang-ar="حفظ الإيصال" data-lang-current="en">Save Receipt</span>
                            </button>
                            <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                <span data-lang data-lang-en="Cancel" data-lang-ar="إلغاء" data-lang-current="en">Cancel</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
