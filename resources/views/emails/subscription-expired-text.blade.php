SUBSCRIPTION EXPIRED - URGENT NOTICE

Dear Administrator,

URGENT NOTICE: A subscription has expired and requires immediate attention.

EXPIRY INFORMATION:
This subscription expired {{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }} ago on {{ $subscription->end_date->format('F j, Y') }}.

SUBSCRIPTION DETAILS:
- Subscription Number: {{ $subscription->subscription_number }}
- Customer: {{ $customer->full_name }}
- Email: {{ $customer->email }}
- Service: {{ $serviceTemplate->getName() }}
- Billing Cycle: {{ $subscription->billing_cycle_display }}
- Amount: {{ $subscription->currency }} {{ number_format($subscription->price, 2) }}
- Start Date: {{ $subscription->start_date->format('F j, Y') }}
- Expired Date: {{ $subscription->end_date->format('F j, Y') }}
- Current Status: {{ $subscription->status_display }}

IMMEDIATE ACTIONS REQUIRED:
- Contact the customer immediately to discuss service continuation
- Determine if the customer wants to renew the subscription
- Process payment and renew if customer agrees
- Consider suspending or terminating services if no response
- Update the subscription status in the system
@if($subscription->auto_renew)
- Investigate why auto-renewal failed and resolve the issue
@endif

SERVICE IMPACT:
The customer may be experiencing service interruptions. Please take immediate action to restore services or communicate the status to the customer.

This notification was sent to: {{ $subscription->notification_email }}

Best regards,
{{ config('app.name') }} System

---
This is an automated notification from {{ config('app.name') }}.
Please do not reply to this email.

Sent on {{ now()->format('F j, Y \a\t g:i A') }}
Notification Email: {{ $subscription->notification_email }} 