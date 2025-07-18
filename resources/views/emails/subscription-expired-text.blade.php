Service Subscription Status Update

Dear Administrator,

We are writing to inform you that a subscription has reached its expiration date and requires your attention.

SUBSCRIPTION INFORMATION:
This subscription expired {{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }} ago on {{ $subscription->end_date->format('F j, Y') }}.

ACCOUNT DETAILS:
- Subscription Number: {{ $subscription->subscription_number }}
- Customer: {{ $customer->full_name }}
- Contact Email: {{ $customer->email }}
- Service Plan: {{ $serviceTemplate->getName() }}
- Billing Cycle: {{ $subscription->billing_cycle_display }}
- Monthly Amount: {{ $subscription->currency }} {{ number_format($subscription->price, 2) }}
- Service Start Date: {{ $subscription->start_date->format('F j, Y') }}
- Expiration Date: {{ $subscription->end_date->format('F j, Y') }}
- Current Status: {{ $subscription->status_display }}

RECOMMENDED NEXT STEPS:
- Contact the customer to discuss service renewal options
- Confirm if the customer wishes to continue the service
- Process renewal payment if customer chooses to continue
- Update the subscription status accordingly
@if($subscription->auto_renew)
- Review why the automatic renewal process was unsuccessful
@endif

CUSTOMER SERVICE NOTE:
The customer's services may be affected. Please follow up promptly to ensure continuity of service or provide clear communication about the service status.

This notification was sent to: {{ $subscription->notification_email }}

Best regards,
{{ config('app.name') }} Team

---
CONTACT INFORMATION:
{{ config('app.name') }}
Email: info@bonlineco.com
Website: https://bonlineco.com

This is an automated service notification. 
For support, please contact us through the channels above.

Generated: {{ now()->format('F j, Y \a\t g:i A') }}
Reference: {{ $subscription->subscription_number }} 