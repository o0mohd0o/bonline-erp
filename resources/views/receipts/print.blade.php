<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --border-color: #e2e8f0;
            --heading-color: #0f172a;
            --text-color: #334155;
            --light-bg: #f8fafc;
            --card-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            --hover-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            --container-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --transition: all 0.2s ease;
        }

        @media print {
            .no-print { display: none !important; }
            body { 
                padding: 0;
                margin: 0;
                background: white !important;
                width: 100%;
                height: 100vh;
            }
            .container { max-width: 100% !important; }
            .print-container { 
                box-shadow: none !important;
                margin: 0 !important;
                padding: 1.5rem !important;
                max-width: 100% !important;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
                page-break-inside: avoid;
            }
            .receipt-header {
                padding: 1.5rem !important;
            }
            .receipt-title {
                padding: 1rem !important;
            }
            .receipt-body {
                padding: 1.5rem !important;
                flex: 1;
            }
            .signature-section {
                padding: 1.5rem !important;
                margin-top: 1rem !important;
            }
            .card-header {
                padding: 0.75rem 1rem !important;
            }
            .card-body {
                padding: 1rem !important;
            }
            .detail-row {
                margin-bottom: 0.5rem !important;
            }
            .amount-in-words {
                margin-top: 0.75rem !important;
                padding: 0.75rem !important;
            }
            .signature-line {
                margin-top: 2rem !important;
            }
            * {
                font-size: 12px !important;
            }
            h2 {
                font-size: 16px !important;
            }
            h4 {
                font-size: 14px !important;
            }
            h5 {
                font-size: 13px !important;
            }
            .receipt-status {
                font-size: 11px !important;
                padding: 0.25rem 1rem !important;
            }
            .watermark {
                font-size: 6rem !important;
            }
            .badge-outline {
                padding: 0.25rem 0.5rem !important;
                font-size: 11px !important;
            }
        }

        [dir="rtl"] {
            text-align: right;
        }

        [dir="rtl"] .text-start {
            text-align: right !important;
        }

        [dir="rtl"] .text-end {
            text-align: left !important;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: var(--text-color);
            background-color: var(--light-bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .print-container {
            max-width: 850px;
            margin: 2rem auto;
            background: white;
            box-shadow: var(--container-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .company-logo img {
            max-width: 160px;
            height: auto;
        }

        .receipt-title {
            position: relative;
            padding: 1.5rem 2rem;
            background: white;
            border-bottom: 1px solid var(--border-color);
        }

        .receipt-body {
            padding: 1.5rem 2rem;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            background: white;
            height: 100%;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.25rem;
        }

        .card-body {
            padding: 1rem 1.25rem;
        }

        .info-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            white-space: nowrap;
        }

        .info-value {
            color: var(--heading-color);
            font-weight: 500;
            font-size: 1rem;
            word-break: break-word;
        }

        .amount-in-words {
            background: var(--light-bg);
            padding: 0.75rem;
            border-radius: var(--border-radius-sm);
            margin-top: 0.75rem;
            font-style: italic;
            color: var(--secondary-color);
        }

        .signature-section {
            padding: 1.5rem 2rem;
            margin-top: 1.5rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-top: 1px solid var(--border-color);
        }

        .signature-line {
            border-top: 2px solid var(--border-color);
            margin-top: 2rem;
            padding-top: 0.5rem;
            text-align: center;
            color: var(--secondary-color);
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem;
            color: rgba(0, 0, 0, 0.02);
            pointer-events: none;
            z-index: 1000;
            font-weight: 800;
            letter-spacing: 0.5rem;
        }

        .receipt-status {
            position: absolute;
            top: 1.5rem;
            right: 2rem;
            background: var(--success-color);
            color: white;
            padding: 0.35rem 1.25rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .print-header {
            background: white;
            padding: 1.25rem;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-outline-secondary {
            color: var(--secondary-color);
            border-color: var(--border-color);
        }

        .btn-outline-secondary:hover {
            background: var(--light-bg);
            border-color: var(--border-color);
            color: var(--heading-color);
        }

        .company-details {
            font-size: 0.90rem;
            color: var(--secondary-color);
        }

        .company-details h4 {
            color: var(--heading-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .detail-row {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-row .info-label {
            min-width: 120px;
            margin-bottom: 0;
            flex-shrink: 0;
        }

        .detail-row .info-value {
            flex: 1;
            min-width: 0;
        }

        .badge-outline {
            border: 1px solid var(--border-color);
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.85rem;
            background: var(--light-bg);
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            justify-content: flex-end;
        }

        .contact-info span {
            white-space: nowrap;
        }

        .contact-info .divider {
            color: var(--border-color);
        }

        @media (max-width: 768px) {
            .receipt-header {
                padding: 1.5rem;
            }
            
            .receipt-body {
                padding: 1.5rem;
            }

            .company-logo img {
                max-width: 150px;
            }

            .detail-row {
                flex-direction: column;
                gap: 0.25rem;
            }

            .detail-row .info-label {
                min-width: auto;
            }

            .receipt-status {
                position: static;
                display: inline-block;
                margin-top: 1rem;
            }

            .contact-info {
                flex-direction: column;
            }

            .contact-info .divider {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="container-fluid mb-4 no-print">
        <div class="row justify-content-center">
            <div class="col-auto">
                <div class="print-header">
                    <div class="btn-group">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </button>
                        <button onclick="toggleLanguage()" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-language me-2"></i>
                            <span data-lang data-lang-en="عربي" data-lang-ar="English" data-lang-current="en">عربي</span>
                        </button>
                        <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="watermark">PAID</div>

    <div class="print-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="row align-items-center">
                <div class="col-6 company-logo">
                    <img src="{{ asset('assets/images/bonline-logo-en.svg') }}" alt="Bonline Logo" class="img-fluid">
                </div>
                <div class="col-6 text-end company-details">
                    <h4 data-lang data-lang-en="Bonline Co." data-lang-ar="بونلاين">Bonline Co.</h4>
                    <p class="mb-1" data-lang data-lang-en="A Khabeertech Company" data-lang-ar="شركة خبير تك">Khabeertech Software</p>
                    <p class="mb-2" data-lang data-lang-en="12 Hassan Alshref Nasir City, Cairo, Egypt 11725" data-lang-ar="12 شارع حسن الشريف، مدينة نصر، القاهرة، مصر 11725">
                        12 Hassan Alshref Nasir City, Cairo, Egypt 11725
                    </p>
                    <div class="contact-info">
                        <span>
                            <span data-lang data-lang-en="Phone" data-lang-ar="هاتف">Phone</span>: +201008985681
                        </span>
                        <span class="divider">|</span>
                        <span>
                            <span data-lang data-lang-en="Email" data-lang-ar="بريد إلكتروني">Email</span>: info@bonlineco.com
                        </span>
                    </div>
                    <div>
                        <span class="badge-outline me-2">CR: 397806</span>
                        <span class="badge-outline">Tax-ID: 450-399-869</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            <h2 data-lang data-lang-en="Receipt #{{ $receipt->receipt_number }}" data-lang-ar="إيصال #{{ $receipt->receipt_number }}">
                Receipt #{{ $receipt->receipt_number }}
            </h2>
            <div class="receipt-status">PAID</div>
        </div>

        <!-- Receipt Body -->
        <div class="receipt-body">
            <div class="row g-4">
                <!-- Receipt Details -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 data-lang data-lang-en="Receipt Details" data-lang-ar="تفاصيل الإيصال">Receipt Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="info-label" data-lang data-lang-en="Date" data-lang-ar="التاريخ">Date:</span>
                                <span class="info-value">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d') }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="info-label" data-lang data-lang-en="Amount" data-lang-ar="المبلغ">Amount:</span>
                                <span class="info-value">{{ number_format($receipt->amount, 2) }} {{ $receipt->currency }}</span>
                            </div>
                            <div class="amount-in-words">
                                <span class="info-label" data-lang data-lang-en="Amount in Words" data-lang-ar="المبلغ بالحروف">Amount in Words:</span><br>
                                <span class="info-value">{{ $amountInWords }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 data-lang data-lang-en="Customer Information" data-lang-ar="معلومات العميل">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="info-label" data-lang data-lang-en="Name" data-lang-ar="الاسم">Name:</span>
                                <span class="info-value">{{ $receipt->customer->display_name }}</span>
                            </div>
                            @if($receipt->customer->customer_type === 'company')
                            <div class="detail-row">
                                <span class="info-label" data-lang data-lang-en="Contact Person" data-lang-ar="الشخص المسؤول">Contact Person:</span>
                                <span class="info-value">{{ $receipt->customer->contact_person_name }}</span>
                            </div>
                            @endif
                            <div class="detail-row">
                                <span class="info-label" data-lang data-lang-en="Phone" data-lang-ar="الهاتف">Phone:</span>
                                <span class="info-value">{{ $receipt->customer->phone }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="info-label" data-lang data-lang-en="Email" data-lang-ar="البريد الإلكتروني">Email:</span>
                                <span class="info-value">{{ $receipt->customer->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($receipt->description)
            <div class="mt-4">
                <div class="card">
                    <div class="card-header">
                        <h5 data-lang data-lang-en="Description" data-lang-ar="الوصف">Description</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $receipt->description }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="row">
                <div class="col-6">
                    <div class="text-center">
                        <span class="info-label" data-lang data-lang-en="Received By" data-lang-ar="استلم بواسطة">Received By</span>
                        <div class="signature-line">
                            <span data-lang data-lang-en="Customer Signature" data-lang-ar="توقيع العميل">Customer Signature</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <span class="info-label" data-lang data-lang-en="Issued By" data-lang-ar="صدر بواسطة">Issued By</span>
                        <div class="signature-line">
                            <span data-lang data-lang-en="Company Signature" data-lang-ar="توقيع الشركة">Company Signature</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleLanguage() {
        const printArea = document.querySelector('.print-container');
        const elements = printArea.querySelectorAll('[data-lang]');
        const currentDir = document.documentElement.dir;
        
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
    }
    </script>
</body>
</html>
