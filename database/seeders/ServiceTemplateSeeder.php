<?php

namespace Database\Seeders;

use App\Models\ServiceTemplate;
use Illuminate\Database\Seeder;

class ServiceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name_ar' => 'نقل الهوست لموقعين',
                'name_en' => 'Host Migration for Two Websites',
                'description_ar' => 'نقل وإعداد الهوست لموقعين مع ضمان سلامة البيانات وضبط الإعدادات',
                'description_en' => 'Host migration and setup for two websites with data integrity and configuration',
                'details_ar' => [
                    'إعداد نقل البيانات من الهوست الحالي إلى الجديد',
                    'التأكد من سلامة البيانات وملفاتهما',
                    'ضبط DNS بشكل صحيح بعد النقل'
                ],
                'details_en' => [
                    'Setup data migration from current to new host',
                    'Ensure data and files integrity',
                    'Configure DNS settings after migration'
                ],
                'default_price' => 2000.00,
                'currency' => 'EGP',
                'is_active' => true,
                'icon' => 'fas fa-server'
            ],
            [
                'name_ar' => 'فحص الهوست من الفيروسات',
                'name_en' => 'Host Virus Scan',
                'description_ar' => 'فحص شامل للفيروسات والبرمجيات الخبيثة مع تقرير مفصل وتنظيف',
                'description_en' => 'Comprehensive virus and malware scan with detailed report and cleaning',
                'details_ar' => [
                    'فحص شامل للفيروسات والبرمجيات الخبيثة',
                    'تقديم تقرير بالحالة',
                    'تنظيف الملفات المصابة (إن وجدت)'
                ],
                'details_en' => [
                    'Complete virus and malware scan',
                    'Status report generation',
                    'Clean infected files (if found)'
                ],
                'default_price' => 1500.00,
                'currency' => 'EGP',
                'is_active' => true,
                'icon' => 'fas fa-shield-virus'
            ],
            [
                'name_ar' => 'تأمين البريد الإلكتروني وتفعيل Two-Factor Authentication',
                'name_en' => 'Email Security and 2FA Setup',
                'description_ar' => 'تعزيز أمان البريد الإلكتروني وإعداد المصادقة الثنائية لجميع الحسابات',
                'description_en' => 'Email security enhancement and Two-Factor Authentication setup for all accounts',
                'details_ar' => [
                    'ضبط إعدادات الأمان على حسابات البريد الإلكتروني',
                    'تفعيل خاصية 2FA على جميع حسابات البريد',
                    'تقديم دليل للمستخدمين لاستخدام 2FA والتحقق بخطوتين'
                ],
                'details_en' => [
                    'Configure email security settings',
                    'Enable 2FA on all email accounts',
                    'Provide user guide for 2FA and two-step verification'
                ],
                'default_price' => 1000.00,
                'currency' => 'EGP',
                'is_active' => true,
                'icon' => 'fas fa-lock'
            ],
            [
                'name_ar' => 'تجديد الاستضافة لمدة سنة',
                'name_en' => 'Hosting Renewal for 1 year',
                'description_ar' => 'باقة استضافة متكاملة تشمل جميع الخدمات الأساسية مع دعم SSL مجاني',
                'description_en' => 'Comprehensive hosting package including all essential services with free SSL support',
                'details_ar' => [
                    'شهادة SSL مجانية',
                    'مساحة تخزين 10 جيجابايت',
                    'بريد إلكتروني غير محدود',
                    'نطاقات فرعية غير محدودة',
                    'نقل بيانات غير محدود',
                    'لوحة تحكم Cpanel'
                ],
                'details_en' => [
                    'Free SSL',
                    '10GB Disk Space',
                    'Unlimited E-Mails',
                    'Unlimited Subdomains',
                    'Unlimited Bandwidth',
                    'Cpanel'
                ],
                'icon' => 'fas fa-server',
                'default_price' => 52.62,
                'currency' => 'USD',
                'is_vat_free' => true,
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            ServiceTemplate::create($service);
        }
    }
}
