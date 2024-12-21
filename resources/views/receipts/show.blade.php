@extends('layouts.app')

@section('content')
<h1>Receipt #{{ $receipt->receipt_number }}</h1>

<div class="mb-3">
    <h4>Customer Information</h4>
    <p><strong>Name:</strong> {{ $receipt->customer->first_name }} {{ $receipt->customer->last_name }}</p>
</div>

<div class="mb-3">
    <h4>Receipt Details</h4>
    <p><strong>Amount:</strong> {{ number_format($receipt->amount, 2) }} {{ $receipt->currency }}</p>
    <p><strong>Description:</strong> {{ $receipt->description }}</p>
    <p><strong>Date:</strong> {{ $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d') : 'N/A' }}</p>
</div>

<div class="mt-4">
    <a href="{{ route('receipts.print', $receipt->id) }}" class="btn btn-primary">Print Receipt</a>
    <a href="{{ route('receipts.index') }}" class="btn btn-secondary">Back to Receipts</a>
</div>
@endsection
