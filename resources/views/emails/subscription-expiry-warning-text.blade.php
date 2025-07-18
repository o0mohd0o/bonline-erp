SUBSCRIPTION EXPIRY WARNING

Dear Administrator,

ACTION REQUIRED: A subscription is expiring soon and requires your attention.

EXPIRY INFORMATION:
This subscription will expire in {{ $daysUntilExpiry }} day{{ $daysUntilExpiry != 1 ? 's' : '' }} on {{ $subscription->end_date->format('F j, Y') }}.

SUBSCRIPTION DETAILS:
- Subscription Number: {{ $subscription->subscription_number }}
- Customer: {{ $customer->full_name }}
- Email: {{ $customer->email }}
- Service: {{ $serviceTemplate->getName() }}
- Billing Cycle: {{ $subscription->billing_cycle_display }}
- Amount: {{ $subscription->currency }} {{ number_format($subscription->price, 2) }}
- Start Date: {{ $subscription->start_date->format('F j, Y') }}
- End Date: {{ $subscription->end_date->format('F j, Y') }}

RECOMMENDED ACTIONS:
- Contact the customer to discuss renewal options
- Process payment if renewal is confirmed
- Update the subscription dates in the system
@if($subscription->auto_renew)
- Verify auto-renewal settings are properly configured
@endif

Please take action before the expiry date to ensure uninterrupted service for your customer.

Best regards,
{{ config('app.name') }} System

---
This is an automated notification from {{ config('app.name') }}.
Please do not reply to this email.

Sent on {{ now()->format('F j, Y \a\t g:i A') }}
Notification Email: {{ $subscription->notification_email }} 