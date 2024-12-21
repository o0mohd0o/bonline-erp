@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-primary">Create Receipt</h1>
                <x-action-button 
                    href="{{ route('receipts.index') }}"
                    icon="arrow-left"
                    variant="secondary"
                    outline
                >
                    Back to List
                </x-action-button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('receipts.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Receipt Number -->
                        <div class="mb-3">
                            <label for="receipt_number" class="form-label fw-bold">Receipt Number</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-hashtag text-muted"></i>
                                </span>
                                <input 
                                    type="number" 
                                    name="receipt_number" 
                                    class="form-control form-control-lg" 
                                    required 
                                    placeholder="Enter a 6-digit number"
                                >
                            </div>
                        </div>

                        <!-- Customer Selection -->
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-bold">Customer</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <select name="customer_id" class="form-select form-select-lg" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->first_name }} {{ $customer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Receipt Date -->
                        <div class="mb-3">
                            <label for="receipt_date" class="form-label fw-bold">Receipt Date</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar text-muted"></i>
                                </span>
                                <input 
                                    type="date" 
                                    name="receipt_date" 
                                    class="form-control form-control-lg" 
                                    value="{{ old('receipt_date') }}" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Amount -->
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-bold">Amount</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-dollar-sign text-muted"></i>
                                </span>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    class="form-control form-control-lg" 
                                    required 
                                    step="0.01"
                                    placeholder="0.00"
                                >
                            </div>
                        </div>

                        <!-- Currency -->
                        <div class="mb-3">
                            <label for="currency" class="form-label fw-bold">Currency</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-coins text-muted"></i>
                                </span>
                                <select name="currency" class="form-select form-select-lg" required>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EGP">EGP - Egyptian Pound</option>
                                    <option value="SAR">SAR - Saudi Riyal</option>
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-align-left text-muted"></i>
                                </span>
                                <input 
                                    type="text" 
                                    name="description" 
                                    class="form-control form-control-lg"
                                    placeholder="Payment description"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <x-action-button 
                        type="submit"
                        icon="save"
                        size="lg"
                        class="px-5"
                    >
                        Save Receipt
                    </x-action-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
