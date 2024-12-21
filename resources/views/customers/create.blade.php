@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 text-primary">New Customer</h1>
                    <p class="text-muted small mb-0">Add a new customer to your database</p>
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
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        
                        <!-- Customer Type Selection -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="customer_type" 
                                        id="individualType" 
                                        value="individual"
                                        checked
                                        onclick="toggleCustomerType('individual')"
                                    >
                                    <label class="form-check-label" for="individualType">
                                        <i class="fas fa-user text-info me-2"></i>Individual
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="customer_type" 
                                        id="companyType" 
                                        value="company"
                                        onclick="toggleCustomerType('company')"
                                    >
                                    <label class="form-check-label" for="companyType">
                                        <i class="fas fa-building text-primary me-2"></i>Company
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Fields -->
                        <div id="individualFields">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">First Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Last Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Fields -->
                        <div id="companyFields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Company Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-building text-muted"></i>
                                        </span>
                                        <input type="text" name="company_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Contact Person</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user-tie text-muted"></i>
                                        </span>
                                        <input type="text" name="contact_person_name" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-medium">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                    </span>
                                    <input type="text" name="address" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-toggle-on text-muted"></i>
                                    </span>
                                    <select name="status" class="form-select" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4 text-end">
                            <x-action-button
                                type="submit"
                                icon="save"
                                variant="primary"
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
.input-group {
    transition: all 0.2s ease;
}
.input-group:focus-within {
    transform: translateY(-1px);
    box-shadow: 0 .25rem .5rem rgba(0,0,0,.05);
}
.form-check-input:checked {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}
.form-check-label {
    cursor: pointer;
    user-select: none;
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
        
    } else {
        individualFields.style.display = 'none';
        companyFields.style.display = 'block';
        
        // Make company fields required and individual fields optional
        document.querySelector('input[name="first_name"]').required = false;
        document.querySelector('input[name="last_name"]').required = false;
        document.querySelector('input[name="company_name"]').required = true;
    }
}
</script>
@endsection
