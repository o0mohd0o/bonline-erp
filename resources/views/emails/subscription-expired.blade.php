<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .expired-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background-color: #fee2e2;
            border-left: 4px solid #dc2626;
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
        .expired-highlight {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .expired-date {
            font-size: 20px;
            font-weight: 700;
            color: #dc2626;
        }
        .urgent-actions {
            background-color: #fef2f2;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #dc2626;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
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
            <div class="expired-icon">🔔</div>
            <h1>Service Subscription Status Update</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">{{ $subscription->subscription_number }}</p>
        </div>
        
        <div class="content">
            <p>Dear Administrator,</p>
            
            <div class="alert-box">
                <strong>Service Status Notice:</strong> A subscription has reached its expiration date and requires your attention.
            </div>
            
            <div class="expired-highlight">
                <p style="margin: 0; font-size: 16px; color: #6b7280;">This subscription expired</p>
                <div class="expired-date">{{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }} ago</div>
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
                    <span class="detail-label">Expired Date:</span>
                    <span class="detail-value" style="color: #dc2626; font-weight: 600;">{{ $subscription->end_date->format('F j, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Current Status:</span>
                    <span class="detail-value" style="color: #dc2626; font-weight: 600;">{{ $subscription->status_display }}</span>
                </div>
            </div>
            
            <div class="urgent-actions">
                <h3 style="margin-top: 0; color: #374151;">📋 Recommended Next Steps:</h3>
                <ul>
                    <li><strong>Contact the customer</strong> to discuss service renewal options</li>
                    <li>Confirm if the customer wishes to continue the service</li>
                    <li>Process renewal payment if customer chooses to continue</li>
                    <li>Update the subscription status accordingly</li>
                    @if($subscription->auto_renew)
                    <li>Review why the automatic renewal process was unsuccessful</li>
                    @endif
                </ul>
            </div>
            
            <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h4 style="margin-top: 0; color: #374151;">Customer Service Note:</h4>
                <p style="margin-bottom: 0;">The customer's services may be affected. Please follow up promptly to ensure continuity of service or provide clear communication about the service status.</p>
            </div>
            
            <p><strong>This notification was sent to:</strong> {{ $subscription->notification_email }}</p>
            
            <p>Best regards,<br>
            <strong>{{ config('app.name') }} Team</strong></p>
        </div>
        
        <div class="footer">
            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 30px;">
                <h4 style="margin-top: 0; color: #374151;">Contact Information:</h4>
                <p style="margin: 5px 0;">
                    <strong>{{ config('app.name') }}</strong><br>
                    Email: info@bonlineco.com<br>
                    Website: https://bonlineco.com
                </p>
                
                <p style="margin-top: 15px; font-size: 14px; color: #6b7280;">
                    This is an automated service notification.<br>
                    For support, please contact us through the channels above.
                </p>
                
                <p style="margin-top: 15px;">
                    <small style="color: #9ca3af;">
                        Generated: {{ now()->format('F j, Y \a\t g:i A') }}<br>
                        Reference: {{ $subscription->subscription_number }}
                    </small>
                </p>
            </div>
        </div>
    </div>
</body>
</html> 