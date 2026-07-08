<?php

namespace Database\Seeders;

use App\Models\Footer;
use App\Models\FooterItem;
use App\Models\GeneraleSetting;
use Illuminate\Database\Seeder;

class FooterItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FooterItem::query()->delete();

        $generaleSetting = GeneraleSetting::first();

        $footerItems = [
            // Footer 1
            [
                [
                    'id' => 1,
                    'footer_id' => 1,
                    'type' => 'logo',
                    'title' => null,
                    'ar_title' => null,
                    'url' => null,
                    'is_active' => 1,
                    'order' => 0,
                    'is_default' => 1,
                ],
                [
                    'id' => 2,
                    'footer_id' => 1,
                    'type' => 'text',
                    'title' => $generaleSetting?->footer_description ?? 'The ultimate all-in-one solution for your eCommerce business worldwide',
                    'ar_title' => 'الحل الأمثل الشامل لأعمال التجارة الإلكترونية الخاصة بك في جميع أنحاء العالم',
                    'url' => null,
                    'is_active' => 1,
                    'order' => 1,
                    'is_default' => 1,
                ],
                [
                    'id' => 3,
                    'footer_id' => 1,
                    'type' => 'phone',
                    'title' => $generaleSetting?->footer_phone ?? '0123456789',
                    'ar_title' => $generaleSetting?->footer_phone ?? '0123456789',
                    'url' => null,
                    'is_active' => 1,
                    'order' => 2,
                    'is_default' => 1,
                ],
                [
                    'id' => 4,
                    'footer_id' => 1,
                    'type' => 'email',
                    'title' => $generaleSetting?->footer_email ?? 'admin@example.com',
                    'ar_title' => $generaleSetting?->footer_email ?? 'admin@example.com',
                    'url' => null,
                    'is_active' => 1,
                    'order' => 3,
                    'is_default' => 1,
                ],
                [
                    'id' => 5,
                    'footer_id' => 1,
                    'type' => 'social_links',
                    'title' => null,
                    'ar_title' => null,
                    'url' => null,
                    'is_active' => 1,
                    'order' => 4,
                    'is_default' => 1,
                ],
            ],

            // Footer 2
            [

                [
                    'id' => 6,
                    'footer_id' => 2,
                    'type' => 'link',
                    'title' => 'Products',
                    'ar_title' => 'المنتجات',
                    'url' => '/products',
                    'is_active' => 1,
                    'order' => 0,
                    'is_default' => 0,
                ],
                [
                    'id' => 7,
                    'footer_id' => 2,
                    'type' => 'link',
                    'title' => 'Most Popular',
                    'ar_title' => 'الاكثر شعبية',
                    'url' => '/most-popular',
                    'is_active' => 1,
                    'order' => 1,
                    'is_default' => 0,
                ],
                [
                    'id' => 8,
                    'footer_id' => 2,
                    'type' => 'link',
                    'title' => 'Best Deal',
                    'ar_title' => 'الافضل العرض',
                    'url' => '/best-deal',
                    'is_active' => 1,
                    'order' => 2,
                    'is_default' => 0,
                ],
                [
                    'id' => 9,
                    'footer_id' => 2,
                    'type' => 'link',
                    'title' => 'Become a Seller',
                    'ar_title' => 'صاحب متجر',
                    'url' => '/shop/register',
                    'shop_type' => 'multi',
                    'target' => '_blank',
                    'is_active' => 1,
                    'order' => 3,
                    'is_default' => true,
                ],
                [
                    'id' => 10,
                    'footer_id' => 2,
                    'type' => 'link',
                    'title' => 'Blogs',
                    'ar_title' => 'المدونات',
                    'url' => '/blogs',
                    'is_active' => 1,
                    'order' => 5,
                    'is_default' => 0,
                ],
                [
                    'id' => 11,
                    'footer_id' => 3,
                    'type' => 'link',
                    'title' => 'About us',
                    'ar_title' => 'من نحن',
                    'url' => '/about-us',
                    'is_active' => 1,
                    'order' => 0,
                    'is_default' => 0,
                ],
            ],

            // Footer 3
            [
                [
                    'id' => 12,
                    'footer_id' => 3,
                    'type' => 'link',
                    'title' => 'Contact',
                    'ar_title' => 'اتصل بنا',
                    'url' => '/contact-us',
                    'is_active' => 1,
                    'order' => 1,
                    'is_default' => 0,
                ],
                [
                    'id' => 13,
                    'footer_id' => 3,
                    'type' => 'link',
                    'title' => 'Terms & Conditions',
                    'ar_title' => 'الشروط والاحكام',
                    'url' => '/terms-and-conditions',
                    'is_active' => 1,
                    'order' => 2,
                    'is_default' => 0,
                ],
                [
                    'id' => 14,
                    'footer_id' => 3,
                    'type' => 'link',
                    'title' => 'Privacy Policy',
                    'ar_title' => 'سياسة الخصوصية',
                    'url' => '/privacy-policy',
                    'is_active' => 1,
                    'order' => 3,
                    'is_default' => 0,
                ],
            ],

            // Footer 4
            [
                [
                    'id' => 15,
                    'footer_id' => 4,
                    'type' => 'app_store',
                    'title' => null,
                    'ar_title' => null,
                    'url' => null,
                    'is_active' => 1,
                    'order' => 0,
                    'is_default' => true,
                ],
            ],
        ];

        $footers = Footer::all();

        foreach ($footers as $key => $footer) {

            $items = $footerItems[$key];

            foreach ($items as $item) {

                $item['footer_id'] = $footer->id;
                FooterItem::create($item);
            }
        }
    }
}
