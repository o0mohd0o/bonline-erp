<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Customer;
use App\Models\ServiceTemplate;
use App\Mail\SubscriptionExpiryWarning;
use App\Mail\SubscriptionExpired;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the subscriptions.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['customer', 'serviceTemplate'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create()
    {
        $customers = Customer::where('status', Customer::STATUS_ACTIVE)->get();
        $serviceTemplates = ServiceTemplate::where('is_active', true)->get();

        return view('subscriptions.create', compact('customers', 'serviceTemplates'));
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'service_template_id' => 'required|exists:service_templates,id',
                'billing_cycle' => 'required|in:monthly,every_6_months,yearly',
                'price' => 'required|numeric|min:0',
                'currency' => 'required|in:USD,SAR,EGP',
                'start_date' => 'required|date',
                'notification_email' => 'required|email',
                'notify_before_days' => 'required|integer|min:1|max:90',
                'auto_renew' => 'boolean',
                'notes' => 'nullable|string',
            ]);

            $subscription = new Subscription();
            $subscription->customer_id = $request->customer_id;
            $subscription->service_template_id = $request->service_template_id;
            $subscription->subscription_number = Subscription::generateSubscriptionNumber();
            $subscription->billing_cycle = $request->billing_cycle;
            $subscription->price = $request->price;
            $subscription->currency = $request->currency;
            $subscription->start_date = $request->start_date;
            $subscription->notification_email = $request->notification_email;
            $subscription->notify_before_days = $request->notify_before_days;
            $subscription->auto_renew = $request->boolean('auto_renew');
            $subscription->notes = $request->notes;
            
            // Calculate end date and next billing date
            $subscription->end_date = $subscription->calculateEndDate($request->start_date);
            $subscription->next_billing_date = $subscription->end_date;

            $subscription->save();

            return redirect()->route('subscriptions.index')
                ->with('success', 'Subscription created successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Subscription creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['customer', 'serviceTemplate']);
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(Subscription $subscription)
    {
        $customers = Customer::where('status', Customer::STATUS_ACTIVE)->get();
        $serviceTemplates = ServiceTemplate::where('is_active', true)->get();

        return view('subscriptions.edit', compact('subscription', 'customers', 'serviceTemplates'));
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_template_id' => 'required|exists:service_templates,id',
            'billing_cycle' => 'required|in:monthly,every_6_months,yearly',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,SAR,EGP',
            'start_date' => 'required|date',
            'notification_email' => 'required|email',
            'notify_before_days' => 'required|integer|min:1|max:90',
            'status' => 'required|in:active,inactive,cancelled,expired',
            'auto_renew' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $oldStartDate = $subscription->start_date;
        $oldBillingCycle = $subscription->billing_cycle;

        $subscription->customer_id = $request->customer_id;
        $subscription->service_template_id = $request->service_template_id;
        $subscription->billing_cycle = $request->billing_cycle;
        $subscription->price = $request->price;
        $subscription->currency = $request->currency;
        $subscription->start_date = $request->start_date;
        $subscription->notification_email = $request->notification_email;
        $subscription->notify_before_days = $request->notify_before_days;
        $subscription->status = $request->status;
        $subscription->auto_renew = $request->boolean('auto_renew');
        $subscription->notes = $request->notes;

        // Recalculate end date if start date or billing cycle changed
        if ($oldStartDate != $request->start_date || $oldBillingCycle != $request->billing_cycle) {
            $subscription->end_date = $subscription->calculateEndDate($request->start_date);
            $subscription->next_billing_date = $subscription->end_date;
        }

        $subscription->save();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    /**
     * Remove the specified subscription from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }

    /**
     * Update subscription status
     */
    public function updateStatus(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,cancelled,expired',
        ]);

        $subscription->status = $request->status;
        $subscription->save();

        return redirect()->back()
            ->with('success', 'Subscription status updated successfully.');
    }

    /**
     * Renew a subscription
     */
    public function renew(Subscription $subscription)
    {
        if ($subscription->status !== Subscription::STATUS_ACTIVE) {
            return redirect()->back()
                ->with('error', 'Only active subscriptions can be renewed.');
        }

        $newStartDate = $subscription->end_date;
        $newEndDate = $subscription->calculateEndDate($newStartDate);

        $subscription->start_date = $newStartDate;
        $subscription->end_date = $newEndDate;
        $subscription->next_billing_date = $newEndDate;
        $subscription->last_notification_sent = null;
        $subscription->expiry_notification_sent = null;
        $subscription->save();

        return redirect()->back()
            ->with('success', 'Subscription renewed successfully.');
    }

    /**
     * Get service template details for AJAX
     */
    public function getServiceTemplateDetails(ServiceTemplate $serviceTemplate)
    {
        return response()->json([
            'name' => $serviceTemplate->getName(),
            'description' => $serviceTemplate->getDescription(),
            'default_price' => $serviceTemplate->default_price,
            'currency' => $serviceTemplate->currency,
        ]);
    }

    /**
     * Send test expiry warning email
     */
    public function sendTestWarning(Subscription $subscription)
    {
        try {
            Mail::to($subscription->notification_email)->send(new SubscriptionExpiryWarning($subscription));
            
            return redirect()->back()
                ->with('success', 'Test expiry warning email sent successfully to ' . $subscription->notification_email);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Send test expired notification email
     */
    public function sendTestExpired(Subscription $subscription)
    {
        try {
            Mail::to($subscription->notification_email)->send(new SubscriptionExpired($subscription));
            
            return redirect()->back()
                ->with('success', 'Test expired notification email sent successfully to ' . $subscription->notification_email);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
