@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Edit Receipt #{{ $receipt->receipt_number }}</h4>
                        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('receipts.update', $receipt->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Receipt Details -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Receipt Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-hashtag text-muted"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        name="receipt_number" 
                                        class="form-control @error('receipt_number') is-invalid @enderror"
                                        value="{{ old('receipt_number', $receipt->receipt_number) }}"
                                        required
                                    >
                                    @error('receipt_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Receipt Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input 
                                        type="date" 
                                        name="receipt_date" 
                                        class="form-control @error('receipt_date') is-invalid @enderror"
                                        value="{{ old('receipt_date', $receipt->receipt_date->format('Y-m-d')) }}"
                                        required
                                    >
                                    @error('receipt_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium">Currency</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-money-bill text-muted"></i>
                                    </span>
                                    <select 
                                        name="currency" 
                                        class="form-select @error('currency') is-invalid @enderror"
                                        required
                                    >
                                        <option value="USD" {{ old('currency', $receipt->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="SAR" {{ old('currency', $receipt->currency) === 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                                        <option value="EGP" {{ old('currency', $receipt->currency) === 'EGP' ? 'selected' : '' }}>EGP - Egyptian Pound</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Customer and Amount -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <select 
                                        name="customer_id" 
                                        class="form-select @error('customer_id') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option 
                                                value="{{ $customer->id }}"
                                                {{ old('customer_id', $receipt->customer_id) == $customer->id ? 'selected' : '' }}
                                            >
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
                            
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-dollar-sign text-muted"></i>
                                    </span>
                                    <input 
                                        type="number" 
                                        name="amount" 
                                        class="form-control @error('amount') is-invalid @enderror"
                                        value="{{ old('amount', $receipt->amount) }}"
                                        step="0.01"
                                        min="0"
                                        required
                                    >
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-medium">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-align-left text-muted"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        name="description" 
                                        class="form-control @error('description') is-invalid @enderror"
                                        value="{{ old('description', $receipt->description) }}"
                                        placeholder="Enter receipt description..."
                                    >
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Receipt
                            </button>
                            <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
</style>
@endsection
