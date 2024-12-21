@extends('layouts.app')

@push('head_scripts')
<script>
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
            <h1 class="h3 text-primary mb-0" data-lang data-lang-en="Receipt Details" data-lang-ar="تفاصيل الإيصال" data-lang-current="en">Receipt Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">Receipts</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="toggleLanguage()">
                <i class="fas fa-language me-2"></i>
                <span data-lang data-lang-en="عربي" data-lang-ar="English" data-lang-current="en">عربي</span>
            </button>
            <a href="{{ route('receipts.print', $receipt->id) }}" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>
                <span data-lang data-lang-en="Print Receipt" data-lang-ar="طباعة الإيصال" data-lang-current="en">Print Receipt</span>
            </a>
            <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                <span data-lang data-lang-en="Edit" data-lang-ar="تعديل" data-lang-current="en">Edit</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Receipt Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0" data-lang data-lang-en="Receipt Information" data-lang-ar="معلومات الإيصال" data-lang-current="en">Receipt Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted" data-lang data-lang-en="Receipt Number" data-lang-ar="رقم الإيصال" data-lang-current="en">Receipt Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-hashtag text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ $receipt->receipt_number }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted" data-lang data-lang-en="Receipt Date" data-lang-ar="تاريخ الإيصال" data-lang-current="en">Receipt Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted" data-lang data-lang-en="Amount" data-lang-ar="المبلغ" data-lang-current="en">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-dollar-sign text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ number_format($receipt->amount, 2) }} {{ $receipt->currency }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label small text-muted" data-lang data-lang-en="Description" data-lang-ar="الوصف" data-lang-current="en">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-align-left text-muted"></i>
                                    </span>
                                    <textarea class="form-control" rows="3" readonly>{{ $receipt->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Customer Information -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0" data-lang data-lang-en="Customer Information" data-lang-ar="معلومات العميل" data-lang-current="en">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted" data-lang data-lang-en="Customer Name" data-lang-ar="اسم العميل" data-lang-current="en">Customer Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control" value="{{ $receipt->customer->display_name }}" readonly>
                        </div>
                    </div>

                    @if($receipt->customer->customer_type === 'company')
                        <div class="mb-3">
                            <label class="form-label small text-muted" data-lang data-lang-en="Contact Person" data-lang-ar="الشخص المسؤول" data-lang-current="en">Contact Person</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user-tie text-muted"></i>
                                </span>
                                <input type="text" class="form-control" value="{{ $receipt->customer->contact_person_name }}" readonly>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small text-muted" data-lang data-lang-en="Phone" data-lang-ar="رقم الهاتف" data-lang-current="en">Phone</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-phone text-muted"></i>
                            </span>
                            <input type="text" class="form-control" value="{{ $receipt->customer->phone }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted" data-lang data-lang-en="Email" data-lang-ar="البريد الإلكتروني" data-lang-current="en">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="text" class="form-control" value="{{ $receipt->customer->email }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted" data-lang data-lang-en="Address" data-lang-ar="العنوان" data-lang-current="en">Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-map-marker-alt text-muted"></i>
                            </span>
                            <textarea class="form-control" rows="2" readonly>{{ $receipt->customer->address }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
