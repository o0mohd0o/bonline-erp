<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceTemplate;

class HostingServiceSeeder extends Seeder
{
    public function run()
    {
        ServiceTemplate::create([
            'name_ar' => 'باقة الاستضافة المتقدمة',
            'name_en' => 'Professional Hosting Package',
            'description_ar' => 'حل استضافة متكامل للمواقع المهنية مع ميزات غير محدودة',
            'description_en' => 'Complete hosting solution for professional websites with unlimited features',
            'details_ar' => [
                'شهادة SSL مجانية',
                'مساحة تخزين 20 جيجابايت',
                'بريد إلكتروني غير محدود',
                'نطاقات فرعية غير محدودة',
                'نقل بيانات غير محدود',
                'لوحة تحكم cPanel'
            ],
            'details_en' => [
                'Free SSL',
                '20GB Disk Space',
                'Unlimited E-Mails',
                'Unlimited Subdomains',
                'Unlimited Bandwidth',
                'cPanel'
            ],
            'icon' => 'fa-server',
            'default_price' => 5000.00,
            'currency' => 'EGP',
            'is_vat_free' => false,
            'is_active' => true,
        ]);
    }
}
