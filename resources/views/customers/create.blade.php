@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2 text-primary fw-semibold">New Customer</h1>
                    <p class="text-muted mb-0">Add a new customer to your database</p>
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
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
                        @csrf
                        
                        <!-- Customer Type Selection -->
                        <div class="customer-type-selector mb-4">
                            <label class="form-label d-block mb-3 text-secondary fw-medium">Customer Type</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="type-card">
                                        <input 
                                            type="radio" 
                                            name="customer_type" 
                                            id="individualType" 
                                            value="individual"
                                            class="type-input"
                                            checked
                                            onclick="toggleCustomerType('individual')"
                                        >
                                        <label class="type-label" for="individualType">
                                            <div class="d-flex align-items-center">
                                                <div class="type-icon bg-primary-subtle text-primary">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-1">Individual</h6>
                                                    <p class="text-muted small mb-0">Personal customer account</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="type-card">
                                        <input 
                                            type="radio" 
                                            name="customer_type" 
                                            id="companyType" 
                                            value="company"
                                            class="type-input"
                                            onclick="toggleCustomerType('company')"
                                        >
                                        <label class="type-label" for="companyType">
                                            <div class="d-flex align-items-center">
                                                <div class="type-icon bg-info-subtle text-info">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="mb-1">Company</h6>
                                                    <p class="text-muted small mb-0">Business customer account</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Sections -->
                        <div class="form-sections">
                            <!-- Individual Fields -->
                            <div id="individualFields" class="customer-section active">
                                <h5 class="text-secondary mb-3">Personal Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                name="first_name" 
                                                class="form-control @error('first_name') is-invalid @enderror" 
                                                id="firstName"
                                                placeholder="First Name"
                                                required
                                            >
                                            <label for="firstName">First Name</label>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                name="last_name" 
                                                class="form-control @error('last_name') is-invalid @enderror" 
                                                id="lastName"
                                                placeholder="Last Name"
                                                required
                                            >
                                            <label for="lastName">Last Name</label>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Fields -->
                            <div id="companyFields" class="customer-section" style="display: none;">
                                <h5 class="text-secondary mb-3">Company Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                name="company_name" 
                                                class="form-control @error('company_name') is-invalid @enderror" 
                                                id="companyName"
                                                placeholder="Company Name"
                                            >
                                            <label for="companyName">Company Name</label>
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                name="contact_person_name" 
                                                class="form-control @error('contact_person_name') is-invalid @enderror" 
                                                id="contactPerson"
                                                placeholder="Contact Person"
                                            >
                                            <label for="contactPerson">Contact Person</label>
                                            @error('contact_person_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="customer-section mt-4">
                                <h5 class="text-secondary mb-3">Contact Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input 
                                                type="email" 
                                                name="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                id="email"
                                                placeholder="Email Address"
                                                required
                                            >
                                            <label for="email">Email Address</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input 
                                                type="tel" 
                                                name="phone" 
                                                class="form-control @error('phone') is-invalid @enderror" 
                                                id="phone"
                                                placeholder="Phone Number"
                                                required
                                            >
                                            <label for="phone">Phone Number</label>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input 
                                                type="text" 
                                                name="address" 
                                                class="form-control @error('address') is-invalid @enderror" 
                                                id="address"
                                                placeholder="Address"
                                            >
                                            <label for="address">Address</label>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select 
                                                name="status" 
                                                class="form-select @error('status') is-invalid @enderror" 
                                                id="status"
                                                required
                                            >
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                            <label for="status">Status</label>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4 d-flex justify-content-end">
                            <x-action-button
                                type="submit"
                                icon="save"
                                variant="primary"
                                class="px-4"
                            >
                                Save Customer
                            </x-action-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-rgb: 37, 99, 235;
    --info-rgb: 6, 182, 212;
}

.customer-type-selector .type-card {
    position: relative;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.type-card:hover {
    border-color: rgba(var(--primary-rgb), 0.4);
}

.type-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.type-label {
    display: block;
    padding: 1rem;
    cursor: pointer;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}

.type-icon {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    font-size: 1.25rem;
}

.type-input:checked + .type-label {
    background-color: rgba(var(--primary-rgb), 0.04);
    border-color: rgba(var(--primary-rgb), 0.4);
}

.form-floating {
    position: relative;
}

.form-floating > .form-control,
.form-floating > .form-select {
    height: calc(3.5rem + 2px);
    line-height: 1.25;
}

.form-floating > label {
    padding: 1rem 0.75rem;
}

.form-control:focus,
.form-select:focus {
    border-color: rgba(var(--primary-rgb), 0.4);
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.1);
}

.customer-section {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.invalid-feedback {
    font-size: 0.875rem;
}
</style>

<script>
function toggleCustomerType(type) {
    const individualFields = document.getElementById('individualFields');
    const companyFields = document.getElementById('companyFields');
    const firstNameInput = document.querySelector('input[name="first_name"]');
    const lastNameInput = document.querySelector('input[name="last_name"]');
    const companyNameInput = document.querySelector('input[name="company_name"]');
    const contactPersonInput = document.querySelector('input[name="contact_person_name"]');
    
    if (type === 'individual') {
        individualFields.style.display = 'block';
        companyFields.style.display = 'none';
        
        firstNameInput.required = true;
        lastNameInput.required = true;
        companyNameInput.required = false;
        contactPersonInput.required = false;
        
        companyNameInput.value = '';
        contactPersonInput.value = '';
    } else {
        individualFields.style.display = 'none';
        companyFields.style.display = 'block';
        
        firstNameInput.required = false;
        lastNameInput.required = false;
        companyNameInput.required = true;
        contactPersonInput.required = true;
        
        firstNameInput.value = '';
        lastNameInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('customerForm');
    
    form.addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="customer_type"]:checked').value;
        const requiredFields = type === 'individual' 
            ? ['first_name', 'last_name'] 
            : ['company_name', 'contact_person_name'];
            
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = form.querySelector(`input[name="${field}"]`);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
