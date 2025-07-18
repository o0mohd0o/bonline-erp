<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expiry Warning</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .warning-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .subscription-details {
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
        }
        .detail-value {
            color: #111827;
        }
        .expiry-highlight {
            background-color: #fef2f2;
            border: 2px solid #fca5a5;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .expiry-date {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
        }
        .cta-button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .content { padding: 20px; }
            .header { padding: 20px; }
            .detail-row { 
                flex-direction: column; 
                gap: 5px; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="warning-icon">⚠️</div>
            <h1>Subscription Expiry Warning</h1>
        </div>
        
        <div class="content">
            <p>Dear Administrator,</p>
            
            <div class="alert-box">
                <strong>Action Required:</strong> A subscription is expiring soon and requires your attention.
            </div>
            
            <div class="expiry-highlight">
                <p style="margin: 0; font-size: 16px; color: #6b7280;">This subscription will expire in</p>
                <div class="expiry-date">{{ $daysUntilExpiry }} day{{ $daysUntilExpiry != 1 ? 's' : '' }}</div>
                <p style="margin: 0; font-size: 14px; color: #6b7280;">on {{ $subscription->end_date->format('F j, Y') }}</p>
            </div>
            
            <div class="subscription-details">
                <h3 style="margin-top: 0; color: #111827;">Subscription Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Subscription Number:</span>
                    <span class="detail-value">{{ $subscription->subscription_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">{{ $customer->full_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $customer->email }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value">{{ $serviceTemplate->getName() }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Billing Cycle:</span>
                    <span class="detail-value">{{ $subscription->billing_cycle_display }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">{{ $subscription->currency }} {{ number_format($subscription->price, 2) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Start Date:</span>
                    <span class="detail-value">{{ $subscription->start_date->format('F j, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">End Date:</span>
                    <span class="detail-value" style="color: #dc2626; font-weight: 600;">{{ $subscription->end_date->format('F j, Y') }}</span>
                </div>
            </div>
            
            <h3>Recommended Actions:</h3>
            <ul>
                <li>Contact the customer to discuss renewal options</li>
                <li>Process payment if renewal is confirmed</li>
                <li>Update the subscription dates in the system</li>
                @if($subscription->auto_renew)
                <li>Verify auto-renewal settings are properly configured</li>
                @endif
            </ul>
            
            <p>Please take action before the expiry date to ensure uninterrupted service for your customer.</p>
            
            <p>Best regards,<br>
            <strong>{{ config('app.name') }} System</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.<br>
            Please do not reply to this email.</p>
            
            <p style="margin-top: 15px;">
                <small>
                    Sent on {{ now()->format('F j, Y \a\t g:i A') }}<br>
                    Notification Email: {{ $subscription->notification_email }}
                </small>
            </p>
        </div>
    </div>
</body>
</html> 