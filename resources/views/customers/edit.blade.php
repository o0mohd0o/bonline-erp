@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">Edit Customer</h1>
                    <p class="text-muted small mb-0">Update customer information</p>
                </div>
                <x-action-button 
                    href="{{ route('customers.index') }}"
                    icon="arrow-left"
                    variant="secondary"
                    outline
                >
                    Back to List
                </x-action-button>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Customer Type Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-medium mb-3">Customer Type</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input @error('customer_type') is-invalid @enderror" 
                                            type="radio" 
                                            name="customer_type" 
                                            id="individualType" 
                                            value="individual"
                                            {{ old('customer_type', $customer->customer_type) === 'individual' ? 'checked' : '' }}
                                            onclick="toggleCustomerType('individual')"
                                        >
                                        <label class="form-check-label" for="individualType">
                                            <i class="fas fa-user text-info me-2"></i>Individual
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input @error('customer_type') is-invalid @enderror" 
                                            type="radio" 
                                            name="customer_type" 
                                            id="companyType" 
                                            value="company"
                                            {{ old('customer_type', $customer->customer_type) === 'company' ? 'checked' : '' }}
                                            onclick="toggleCustomerType('company')"
                                        >
                                        <label class="form-check-label" for="companyType">
                                            <i class="fas fa-building text-primary me-2"></i>Company
                                        </label>
                                    </div>
                                </div>
                                @error('customer_type')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Individual Fields -->
                        <div id="individualFields" class="mb-4" style="{{ old('customer_type', $customer->customer_type) === 'individual' ? 'display: block;' : 'display: none;' }}">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h6 class="fw-medium text-primary mb-0">
                                        <i class="fas fa-user me-2"></i>Personal Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-user text-muted"></i>
                                                </span>
                                                <input 
                                                    type="text" 
                                                    name="first_name" 
                                                    class="form-control @error('first_name') is-invalid @enderror" 
                                                    value="{{ old('first_name', $customer->first_name) }}"
                                                    placeholder="Enter first name"
                                                >
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-user text-muted"></i>
                                                </span>
                                                <input 
                                                    type="text" 
                                                    name="last_name" 
                                                    class="form-control @error('last_name') is-invalid @enderror" 
                                                    value="{{ old('last_name', $customer->last_name) }}"
                                                    placeholder="Enter last name"
                                                >
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Fields -->
                        <div id="companyFields" class="mb-4" style="{{ old('customer_type', $customer->customer_type) === 'company' ? 'display: block;' : 'display: none;' }}">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h6 class="fw-medium text-primary mb-0">
                                        <i class="fas fa-building me-2"></i>Company Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Company Name</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-building text-muted"></i>
                                                </span>
                                                <input 
                                                    type="text" 
                                                    name="company_name" 
                                                    class="form-control @error('company_name') is-invalid @enderror" 
                                                    value="{{ old('company_name', $customer->company_name) }}"
                                                    placeholder="Enter company name"
                                                >
                                                @error('company_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Contact Person Name</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-user-tie text-muted"></i>
                                                </span>
                                                <input 
                                                    type="text" 
                                                    name="contact_person_name" 
                                                    class="form-control @error('contact_person_name') is-invalid @enderror" 
                                                    value="{{ old('contact_person_name', $customer->contact_person_name) }}"
                                                    placeholder="Enter contact person name"
                                                >
                                                @error('contact_person_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Contact Person Phone</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-phone text-muted"></i>
                                                </span>
                                                <input 
                                                    type="text" 
                                                    name="contact_person_phone" 
                                                    class="form-control @error('contact_person_phone') is-invalid @enderror" 
                                                    value="{{ old('contact_person_phone', $customer->contact_person_phone) }}"
                                                    placeholder="Enter contact person phone"
                                                >
                                                @error('contact_person_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h6 class="fw-medium text-primary mb-0">
                                        <i class="fas fa-address-card me-2"></i>Contact Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                </span>
                                                <input 
                                                    type="email" 
                                                    name="email" 
                                                    class="form-control @error('email') is-invalid @enderror" 
                                                    value="{{ old('email', $customer->email) }}"
                                                    placeholder="Enter email address"
                                                >
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-phone text-muted"></i>
                                                </span>
                                                <input 
                                                    type="text" 
                                                    name="phone" 
                                                    class="form-control @error('phone') is-invalid @enderror" 
                                                    value="{{ old('phone', $customer->phone) }}"
                                                    placeholder="Enter phone number"
                                                >
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Address</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-map-marker-alt text-muted"></i>
                                                </span>
                                                <textarea 
                                                    name="address" 
                                                    class="form-control @error('address') is-invalid @enderror" 
                                                    rows="2"
                                                    placeholder="Enter full address"
                                                >{{ old('address', $customer->address) }}</textarea>
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h6 class="fw-medium text-primary mb-0">
                                        <i class="fas fa-toggle-on me-2"></i>Account Status
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-toggle-on text-muted"></i>
                                                </span>
                                                <select 
                                                    name="status" 
                                                    class="form-select @error('status') is-invalid @enderror"
                                                >
                                                    <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end">
                            <x-action-button
                                type="submit"
                                icon="save"
                                variant="primary"
                            >
                                Update Customer
                            </x-action-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.input-group {
    border-radius: 0.375rem;
    overflow: hidden;
    background-color: #fff;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}
.input-group:focus-within {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.15);
}
.input-group .input-group-text {
    background-color: #fff !important;
    border: none;
    border-right: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem 1rem;
    min-width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.input-group .form-control,
.input-group .form-select {
    border: none;
    padding: 0.5rem 1rem;
    background-color: #fff;
}
.input-group .form-control:focus,
.input-group .form-select:focus {
    box-shadow: none;
}
.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}
.card {
    border: none;
}
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #edf2f9;
}
.invalid-feedback {
    font-size: 0.8125rem;
    margin-top: 0.25rem;
}
</style>

<script>
function toggleCustomerType(type) {
    const individualFields = document.getElementById('individualFields');
    const companyFields = document.getElementById('companyFields');
    
    if (type === 'individual') {
        individualFields.style.display = 'block';
        companyFields.style.display = 'none';
        
        // Make individual fields required and company fields optional
        document.querySelector('input[name="first_name"]').required = true;
        document.querySelector('input[name="last_name"]').required = true;
        document.querySelector('input[name="company_name"]').required = false;
        document.querySelector('input[name="contact_person_name"]').required = false;
        
    } else {
        individualFields.style.display = 'none';
        companyFields.style.display = 'block';
        
        // Make company fields required and individual fields optional
        document.querySelector('input[name="first_name"]').required = false;
        document.querySelector('input[name="last_name"]').required = false;
        document.querySelector('input[name="company_name"]').required = true;
        document.querySelector('input[name="contact_person_name"]').required = true;
    }
}

// Initialize form validation state based on old input
document.addEventListener('DOMContentLoaded', function() {
    const customerType = document.querySelector('input[name="customer_type"]:checked')?.value;
    if (customerType) {
        toggleCustomerType(customerType);
    }
});
</script>
@endsection
