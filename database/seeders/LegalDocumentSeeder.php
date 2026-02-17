<?php

namespace Database\Seeders;

use App\Models\LegalDocument;
use Illuminate\Database\Seeder;

class LegalDocumentSeeder extends Seeder
{
    public function run(): void
    {
        LegalDocument::updateOrCreate(
            ['type' => 'privacy_policy', 'version' => '1.0'],
            [
                'title' => 'Privacy Policy',
                'content' => <<<'HTML'
<h2>Privacy Policy</h2>
<p>Last updated: February 17, 2026</p>

<h3>1. Information We Collect</h3>
<p>We collect information you provide directly to us when using the POS system, including:</p>
<ul>
    <li>Business name and contact details</li>
    <li>User account information (name, email, role)</li>
    <li>Transaction and sales data</li>
    <li>Device and terminal information</li>
</ul>

<h3>2. How We Use Your Information</h3>
<p>We use the information we collect to:</p>
<ul>
    <li>Provide, maintain, and improve the POS system</li>
    <li>Process transactions and send related information</li>
    <li>Generate sales reports and analytics</li>
    <li>Send technical notices and support messages</li>
</ul>

<h3>3. Data Storage and Security</h3>
<p>We implement appropriate security measures to protect your personal information. All transaction data is encrypted in transit and at rest.</p>

<h3>4. Data Sharing</h3>
<p>We do not sell, trade, or otherwise transfer your personal information to third parties. We may share data only with service providers who assist in operating the system.</p>

<h3>5. Data Retention</h3>
<p>We retain your data for as long as your account is active or as needed to provide services. You may request deletion of your data at any time.</p>

<h3>6. Contact Us</h3>
<p>If you have questions about this Privacy Policy, please contact us at support@pos-app.com.</p>
HTML,
                'is_active' => true,
                'effective_date' => now(),
            ]
        );

        LegalDocument::updateOrCreate(
            ['type' => 'terms_and_conditions', 'version' => '1.0'],
            [
                'title' => 'Terms and Conditions',
                'content' => <<<'HTML'
<h2>Terms and Conditions</h2>
<p>Last updated: February 17, 2026</p>

<h3>1. Acceptance of Terms</h3>
<p>By accessing or using the POS system, you agree to be bound by these Terms and Conditions. If you do not agree, you may not use the system.</p>

<h3>2. License</h3>
<p>Upon purchase or activation of a valid license, we grant you a non-exclusive, non-transferable license to use the POS system in accordance with your plan tier.</p>

<h3>3. User Responsibilities</h3>
<ul>
    <li>You are responsible for maintaining the confidentiality of your account credentials</li>
    <li>You agree to use the system only for lawful business purposes</li>
    <li>You are responsible for all activity that occurs under your account</li>
    <li>You must not attempt to reverse engineer or modify the system</li>
</ul>

<h3>4. Payment and Licensing</h3>
<p>License fees are based on the plan selected at the time of purchase. One-time payment plans grant perpetual access to the purchased version. The licensor reserves the right to modify pricing for future versions.</p>

<h3>5. Limitation of Liability</h3>
<p>The POS system is provided "as is" without warranties of any kind. We shall not be liable for any indirect, incidental, or consequential damages arising from the use of the system.</p>

<h3>6. Termination</h3>
<p>We may terminate or suspend your access if you violate these terms. Upon termination, your right to use the system ceases immediately.</p>

<h3>7. Changes to Terms</h3>
<p>We reserve the right to update these terms at any time. Continued use of the system after changes constitutes acceptance of the new terms.</p>

<h3>8. Contact Us</h3>
<p>For questions regarding these Terms and Conditions, please contact us at support@pos-app.com.</p>
HTML,
                'is_active' => true,
                'effective_date' => now(),
            ]
        );
    }
}
