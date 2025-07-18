<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Mail\SubscriptionExpiryWarning;
use App\Mail\SubscriptionExpired;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry {--force : Force send notifications even if already sent} {--test-email= : Send test email to specified address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring and expired subscriptions and send email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testEmail = $this->option('test-email');
        
        // If test email is provided, send test emails
        if ($testEmail) {
            return $this->sendTestEmails($testEmail);
        }
        
        $this->info('Checking subscription expiry...');
        
        $force = $this->option('force');
        
        // Update expired subscriptions status
        $this->updateExpiredSubscriptions();
        
        // Check for expiring subscriptions (warning notifications)
        $this->checkExpiringSubscriptions($force);
        
        // Check for expired subscriptions (expiry notifications)
        $this->checkExpiredSubscriptions($force);
        
        $this->info('Subscription expiry check completed.');
    }

    /**
     * Update status of expired subscriptions
     */
    private function updateExpiredSubscriptions()
    {
        $expiredSubscriptions = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where('end_date', '<', Carbon::now())
            ->get();

        if ($expiredSubscriptions->count() > 0) {
            $this->info("Updating {$expiredSubscriptions->count()} expired subscription(s) status...");
            
            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => Subscription::STATUS_EXPIRED]);
                $this->line("Updated subscription {$subscription->subscription_number} to expired status");
            }
        }
    }

    /**
     * Check for expiring subscriptions and send warning notifications
     */
    private function checkExpiringSubscriptions($force = false)
    {
        $this->info('Checking for expiring subscriptions...');
        
        $subscriptions = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where('notification_enabled', true)
            ->get();

        $warningsSent = 0;

        foreach ($subscriptions as $subscription) {
            if ($subscription->isExpiringSoon()) {
                // Check if warning notification already sent
                if (!$force && $subscription->last_notification_sent) {
                    continue;
                }

                try {
                    Mail::to($subscription->notification_email)->send(new SubscriptionExpiryWarning($subscription));
                    
                    $subscription->update(['last_notification_sent' => Carbon::now()]);
                    
                    $this->line("Warning sent for subscription {$subscription->subscription_number} (expires {$subscription->end_date->format('Y-m-d')})");
                    $warningsSent++;
                    
                } catch (\Exception $e) {
                    $this->error("Failed to send warning for subscription {$subscription->subscription_number}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Sent {$warningsSent} warning notification(s).");
    }

    /**
     * Check for expired subscriptions and send expiry notifications
     */
    private function checkExpiredSubscriptions($force = false)
    {
        $this->info('Checking for expired subscriptions...');
        
        $expiredSubscriptions = Subscription::where('status', Subscription::STATUS_EXPIRED)
            ->where('notification_enabled', true)
            ->get();

        $expiredNotificationsSent = 0;

        foreach ($expiredSubscriptions as $subscription) {
            // Check if expiry notification already sent
            if (!$force && $subscription->expiry_notification_sent) {
                continue;
            }

            try {
                Mail::to($subscription->notification_email)->send(new SubscriptionExpired($subscription));
                
                $subscription->update(['expiry_notification_sent' => Carbon::now()]);
                
                $this->line("Expiry notification sent for subscription {$subscription->subscription_number} (expired {$subscription->end_date->format('Y-m-d')})");
                $expiredNotificationsSent++;
                
            } catch (\Exception $e) {
                $this->error("Failed to send expiry notification for subscription {$subscription->subscription_number}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$expiredNotificationsSent} expiry notification(s).");
    }

    /**
     * Send test emails to specified address
     */
    private function sendTestEmails($email)
    {
        $this->info("Sending test emails to: {$email}");
        
        // Create a dummy subscription for testing
        $subscription = new Subscription([
            'subscription_number' => 'TEST-' . date('Ymd-His'),
            'billing_cycle' => 'monthly',
            'price' => 99.99,
            'currency' => 'EGP',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(5), // 5 days from now for warning test
            'notification_email' => $email,
            'notify_before_days' => 15,
        ]);
        
        // Create dummy relationships
        $customer = new \stdClass();
        $customer->full_name = 'Test Customer';
        $customer->email = 'test@example.com';
        $subscription->setRelation('customer', $customer);
        
        $serviceTemplate = new class {
            public $name_en = 'Test Service';
            public $description_en = 'This is a test service for email testing purposes.';
            public function getName() { return $this->name_en; }
            public function getDescription() { return $this->description_en; }
        };
        $subscription->setRelation('serviceTemplate', $serviceTemplate);
        
        try {
            // Send test warning email
            $this->info('Sending test expiry warning email...');
            Mail::to($email)->send(new SubscriptionExpiryWarning($subscription));
            $this->line("✓ Test expiry warning email sent successfully");
            
            // Send test expired email
            $this->info('Sending test expired notification email...');
            Mail::to($email)->send(new SubscriptionExpired($subscription));
            $this->line("✓ Test expired notification email sent successfully");
            
            $this->info("Test emails sent successfully to: {$email}");
            
        } catch (\Exception $e) {
            $this->error("Failed to send test emails: {$e->getMessage()}");
            return 1;
        }
        
        return 0;
    }
}
