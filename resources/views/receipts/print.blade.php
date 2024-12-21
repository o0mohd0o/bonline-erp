<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .company-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .company-info img {
            max-width: 150px;
        }
        .company-details {
            text-align: right;
        }
        .company-details h2 {
            font-weight: bold;
            margin: 0;
        }
        .invoice-details, .customer-info {
            margin-bottom: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <!-- Company Information -->
    <div class="company-info">
        <div>
            <img src="https://bonline.cc/invoice/logo.png" alt="Company Logo">
        </div>
        <div class="company-details">
            <h2>Bonline Co.</h2>
            <p>12 Hassan Alshref Nasir City, Cairo, Egypt 11725</p>
            <p>Phone: +201008985681 | Email: info@itc-4u.com</p>
            <p>CR: 397806 | Tax-ID: 450-399-869</p>
        </div>
    </div>
    <h1 style="text-align:center;">Receipt #{{ $receipt->receipt_number }}</h1>
    <!-- Customer Information -->
    <div class="customer-info">
        <h4>Customer Information</h4>
        <p><strong>Name:</strong> {{ $receipt->customer->first_name }} {{ $receipt->customer->last_name }}</p>
        <p><strong>Email:</strong> {{ $receipt->customer->email }}</p>
        <p><strong>Phone:</strong> {{ $receipt->customer->phone }}</p>
        <p><strong>Address:</strong> {{ $receipt->customer->address }}</p>
    </div>

    <!-- Receipt Information -->
    <div class="invoice-details">
        <h4>Receipt Details</h4>
        <p><strong>Date:</strong> {{ $receipt->receipt_date ? \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d') : 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $receipt->description }}</p>
        <p><strong>Amount:</strong> {{ number_format($receipt->amount, 2) }} {{ $receipt->currency }}</p>
        <p><strong>Amount in Words:</strong> {{ $amountInWords }}</p>
    </div>

    <!-- Print and Back Buttons -->
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <a href="{{ route('receipts.index') }}" class="btn btn-secondary">Back to Receipts</a>
    </div>
</div>

</body>
</html>
