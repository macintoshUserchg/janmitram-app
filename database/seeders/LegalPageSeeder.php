<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    public function run(): void
    {
        $legalPages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'description' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This policy outlines how we collect, use, and protect your personal information when you use our services.</p><p>We collect information you provide directly, such as your name, email address, phone number, and shipping address when you create an account or place an order.</p><p>We use your information to process orders, provide customer support, and improve our services. We do not share your personal information with third parties except as necessary to fulfill orders or as required by law.</p>',
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-and-conditions',
                'description' => '<h2>Terms of Service</h2><p>By using our platform, you agree to these terms and conditions. Please read them carefully.</p><p>All products and services are provided as described. We reserve the right to modify or discontinue any service without prior notice.</p><p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>',
            ],
            [
                'title' => 'Return policy / Refund Policy',
                'slug' => 'return-and-refund-policy',
                'description' => '<h2>Return & Refund Policy</h2><p>We accept returns within 30 days of delivery. Items must be unused and in original packaging.</p><p>Refunds will be processed within 5-7 business days after we receive the returned item. Shipping costs are non-refundable.</p><p>For digital products, all sales are final unless the product is defective or not as described.</p>',
            ],
            [
                'title' => 'Shipping & Delivery Policy',
                'slug' => 'shipping-and-delivery-policy',
                'description' => '<h2>Shipping & Delivery Policy</h2><p>We offer free shipping on orders over a certain amount. Standard delivery takes 3-7 business days.</p><p>Express delivery is available at an additional cost. Delivery times may vary depending on your location.</p><p>We are not responsible for delays caused by customs, weather conditions, or other factors beyond our control.</p>',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'description' => '<h2>About Us</h2><p>Welcome to our marketplace! We are a leading e-commerce platform connecting buyers with trusted sellers.</p><p>Our mission is to provide a seamless shopping experience with quality products at competitive prices. We support local businesses and entrepreneurs.</p><p>With our multi-vendor platform, you can browse products from multiple shops, compare prices, and enjoy secure payments.</p>',
            ],
        ];

        foreach ($legalPages as $legalPage) {
            LegalPage::create($legalPage);
        }
    }
}
