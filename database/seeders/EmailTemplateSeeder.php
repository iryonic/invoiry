<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'code' => 'invoice_generated',
                'name' => 'Invoice Generated & Bill Delivery',
                'category' => 'invoices',
                'subject' => 'Tax Invoice #{{invoice_number}} from {{business_name}}',
                'available_placeholders' => [
                    'customer_name', 'invoice_number', 'invoice_date', 'invoice_total',
                    'invoice_paid', 'invoice_balance_due', 'business_name', 'business_phone',
                    'business_email', 'business_address', 'business_upi',
                ],
                'body_html' => self::wrapLayout(
                    'Tax Invoice Generated',
                    'Invoice #{{invoice_number}} is ready. Details and settlement summary are below.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Dear <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">Thank you for your business at <strong>{{business_name}}</strong>. Your invoice has been generated and the official copy is attached to this email.</p>
                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #F7F7F6; border-radius: 8px; overflow: hidden; border: 1px solid #E6E6E6;">
                        <tr><td style="padding: 10px 14px; font-size: 12px; color: #777777; border-bottom: 1px solid #E6E6E6;">Invoice Number:</td><td style="padding: 10px 14px; font-size: 12px; font-weight: bold; font-family: monospace; color: #111111; text-align: right; border-bottom: 1px solid #E6E6E6;">#{{invoice_number}}</td></tr>
                        <tr><td style="padding: 10px 14px; font-size: 12px; color: #777777; border-bottom: 1px solid #E6E6E6;">Billing Date:</td><td style="padding: 10px 14px; font-size: 12px; color: #111111; text-align: right; border-bottom: 1px solid #E6E6E6;">{{invoice_date}}</td></tr>
                        <tr><td style="padding: 10px 14px; font-size: 12px; color: #777777; border-bottom: 1px solid #E6E6E6;">Amount Paid:</td><td style="padding: 10px 14px; font-size: 12px; color: #027A48; font-weight: bold; text-align: right; border-bottom: 1px solid #E6E6E6;">{{invoice_paid}}</td></tr>
                        <tr><td style="padding: 10px 14px; font-size: 12px; color: #777777; border-bottom: 1px solid #E6E6E6;">Balance Due:</td><td style="padding: 10px 14px; font-size: 12px; color: #D97706; font-weight: bold; text-align: right; border-bottom: 1px solid #E6E6E6;">{{invoice_balance_due}}</td></tr>
                        <tr style="background-color: #050505;"><td style="padding: 12px 14px; font-size: 14px; font-weight: bold; color: #FFFFFF;">Grand Total:</td><td style="padding: 12px 14px; font-size: 18px; font-weight: 800; font-family: monospace; color: #B7FF00; text-align: right;">{{invoice_total}}</td></tr>
                    </table>
                    <p style="margin: 0 0 8px; font-size: 13px; line-height: 20px; color: #666666;">Please keep this email and attached PDF receipt for warranty coverage and hardware verification.</p>'
                ),
                'body_text' => "Dear {{customer_name}},\n\nThank you for choosing {{business_name}}.\n\nInvoice Number: #{{invoice_number}}\nDate: {{invoice_date}}\nGrand Total: {{invoice_total}}\nAmount Paid: {{invoice_paid}}\nBalance Due: {{invoice_balance_due}}\n\nPlease find your attached PDF invoice.\n\n{{business_name}}\nPhone: {{business_phone}}",
            ],
            [
                'code' => 'payment_received',
                'name' => 'Payment Confirmation & Receipt',
                'category' => 'payments',
                'subject' => 'Payment Receipt for Invoice #{{invoice_number}} - {{business_name}}',
                'available_placeholders' => [
                    'customer_name', 'invoice_number', 'payment_amount', 'payment_date',
                    'payment_method', 'invoice_balance_due', 'business_name', 'business_phone',
                ],
                'body_html' => self::wrapLayout(
                    'Payment Received',
                    'We have successfully recorded your payment of {{payment_amount}}.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Dear <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">This is to confirm that we have received your payment for Invoice <strong>#{{invoice_number}}</strong>. Your ledger has been updated.</p>
                    <div style="background-color: #ECFDF3; border: 1px solid #A6F4C5; border-radius: 8px; padding: 14px; margin: 16px 0; text-align: center;">
                        <span style="font-size: 12px; color: #027A48; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Payment Successful</span>
                        <div style="font-size: 24px; font-weight: 800; color: #027A48; font-family: monospace; margin: 4px 0;">{{payment_amount}}</div>
                        <span style="font-size: 11px; color: #027A48;">Method: {{payment_method}} &bull; Date: {{payment_date}}</span>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 12px;">
                        <tr><td style="padding: 8px 0; color: #777777;">Invoice Ref:</td><td style="padding: 8px 0; font-weight: bold; text-align: right; color: #111111;">#{{invoice_number}}</td></tr>
                        <tr><td style="padding: 8px 0; color: #777777;">Remaining Balance:</td><td style="padding: 8px 0; font-weight: bold; text-align: right; color: #111111;">{{invoice_balance_due}}</td></tr>
                    </table>'
                ),
                'body_text' => "Dear {{customer_name}},\n\nPayment Received: {{payment_amount}}\nInvoice: #{{invoice_number}}\nDate: {{payment_date}}\nMethod: {{payment_method}}\nRemaining Balance: {{invoice_balance_due}}\n\nThank you,\n{{business_name}}",
            ],
            [
                'code' => 'payment_reminder',
                'name' => 'Friendly Payment Reminder',
                'category' => 'reminders',
                'subject' => 'Payment Reminder: Balance Due on Invoice #{{invoice_number}}',
                'available_placeholders' => [
                    'customer_name', 'invoice_number', 'invoice_total', 'invoice_balance_due',
                    'invoice_due_date', 'business_name', 'business_phone', 'business_upi',
                ],
                'body_html' => self::wrapLayout(
                    'Payment Reminder',
                    'Friendly reminder regarding pending balance of {{invoice_balance_due}}.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Dear <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">We hope you are enjoying your purchase from <strong>{{business_name}}</strong>. This is a gentle reminder that an outstanding balance of <strong>{{invoice_balance_due}}</strong> remains on Invoice <strong>#{{invoice_number}}</strong>.</p>
                    <div style="background-color: #FFFBEB; border: 1px solid #FDE68A; border-radius: 8px; padding: 14px; margin: 16px 0;">
                        <div style="font-size: 12px; color: #92400E; font-weight: 600;">Outstanding Khata Balance</div>
                        <div style="font-size: 22px; font-weight: 800; color: #B45309; font-family: monospace; margin: 4px 0;">{{invoice_balance_due}}</div>
                        <div style="font-size: 11px; color: #92400E;">Invoice Total: {{invoice_total}} &bull; Due Date: {{invoice_due_date}}</div>
                    </div>
                    <p style="margin: 0 0 12px; font-size: 13px; color: #555555;">You can settle this counter balance directly via store UPI or walk in at our store:</p>
                    <div style="background: #F7F7F6; padding: 10px 14px; border-radius: 6px; font-family: monospace; font-size: 12px; color: #111111;">UPI ID: <strong>{{business_upi}}</strong></div>'
                ),
                'body_text' => "Dear {{customer_name}},\n\nFriendly reminder that a balance of {{invoice_balance_due}} is due on Invoice #{{invoice_number}}.\nDue Date: {{invoice_due_date}}\nStore UPI: {{business_upi}}\n\nThank you,\n{{business_name}}",
            ],
            [
                'code' => 'payment_overdue',
                'name' => 'Overdue Payment Notification',
                'category' => 'reminders',
                'subject' => 'URGENT: Overdue Account Balance on Invoice #{{invoice_number}}',
                'available_placeholders' => [
                    'customer_name', 'invoice_number', 'invoice_balance_due', 'invoice_due_date',
                    'business_name', 'business_phone', 'business_upi',
                ],
                'body_html' => self::wrapLayout(
                    'Overdue Notice',
                    'Your invoice payment is past due date. Please arrange settlement.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Dear <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">Our records show that your account has an overdue balance of <strong>{{invoice_balance_due}}</strong> for Invoice <strong>#{{invoice_number}}</strong> which was due on <strong>{{invoice_due_date}}</strong>.</p>
                    <div style="background-color: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 14px; margin: 16px 0;">
                        <span style="font-size: 11px; font-weight: bold; color: #B91C1C; text-transform: uppercase;">Overdue Settlement Required</span>
                        <div style="font-size: 24px; font-weight: 800; color: #B91C1C; font-family: monospace; margin: 4px 0;">{{invoice_balance_due}}</div>
                    </div>
                    <p style="margin: 0 0 12px; font-size: 13px; color: #555555;">Please clear this balance via UPI (<strong>{{business_upi}}</strong>) or reach our counter at <strong>{{business_phone}}</strong> to avoid service interruptions.</p>'
                ),
                'body_text' => "Dear {{customer_name}},\n\nYour payment of {{invoice_balance_due}} on Invoice #{{invoice_number}} is past due.\nPlease clear this balance immediately via UPI: {{business_upi}} or contact us at {{business_phone}}.\n\n{{business_name}}",
            ],
            [
                'code' => 'customer_welcome',
                'name' => 'Customer Registration & Welcome',
                'category' => 'customers',
                'subject' => 'Welcome to {{business_name}}!',
                'available_placeholders' => [
                    'customer_name', 'customer_email', 'customer_phone', 'business_name',
                    'business_phone', 'business_email', 'business_address',
                ],
                'body_html' => self::wrapLayout(
                    'Welcome to Our Store',
                    'We are pleased to have you as a valued customer of {{business_name}}.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Dear <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">Welcome! We have registered your customer profile at <strong>{{business_name}}</strong>. Any purchases, warranty tickets, and digital invoices will be delivered directly to this email address.</p>
                    <div style="background-color: #F7F7F6; border: 1px solid #E6E6E6; border-radius: 8px; padding: 14px; margin: 16px 0; font-size: 13px;">
                        <div><strong>Registered Contact:</strong> {{customer_phone}}</div>
                        <div style="margin-top: 4px;"><strong>Support Email:</strong> {{business_email}}</div>
                        <div style="margin-top: 4px;"><strong>Store Address:</strong> {{business_address}}</div>
                    </div>
                    <p style="margin: 0; font-size: 13px; color: #666666;">If you have any questions about hardware or services, please call us at <strong>{{business_phone}}</strong>.</p>'
                ),
                'body_text' => "Welcome {{customer_name}} to {{business_name}}!\n\nYour customer profile is active. Contact us at {{business_phone}} or visit us at {{business_address}}.\n\nBest regards,\n{{business_name}}",
            ],
            [
                'code' => 'account_notification',
                'name' => 'Account & Staff Security Notification',
                'category' => 'system',
                'subject' => 'Security Notice for your {{business_name}} Account',
                'available_placeholders' => [
                    'customer_name', 'business_name', 'current_year', 'login_url',
                ],
                'body_html' => self::wrapLayout(
                    'Security Notice',
                    'Important security update regarding your account access.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Hello <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">This is an automated notification regarding an activity on your <strong>{{business_name}}</strong> counter account. If this was authorized by you, no further action is required.</p>
                    <p style="margin: 0 0 16px; font-size: 13px; color: #555555;">If you did not initiate this change, please sign in to lock your terminal or notify your store administrator immediately.</p>
                    <div style="text-align: center; margin: 24px 0;">
                        <a href="{{login_url}}" style="display: inline-block; background-color: #050505; color: #FFFFFF; text-decoration: none; padding: 10px 22px; border-radius: 8px; font-weight: 600; font-size: 13px;">Access Account Portal</a>
                    </div>'
                ),
                'body_text' => "Hello {{customer_name}},\n\nSecurity notification regarding your {{business_name}} account. If unauthorized, lock your terminal immediately.\n\n{{business_name}}",
            ],
            [
                'code' => 'auth_password_reset',
                'name' => 'Authentication & Password Reset',
                'category' => 'system',
                'subject' => 'Reset Your Password - {{business_name}}',
                'available_placeholders' => [
                    'customer_name', 'login_url', 'business_name', 'current_year',
                ],
                'body_html' => self::wrapLayout(
                    'Password Reset Request',
                    'Follow the link below to securely reset your credentials.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Hello <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">You are receiving this email because we received a password reset request for your account on <strong>{{business_name}}</strong>.</p>
                    <div style="text-align: center; margin: 24px 0;">
                        <a href="{{login_url}}" style="display: inline-block; background-color: #B7FF00; color: #101010; text-decoration: none; padding: 12px 26px; border-radius: 8px; font-weight: 700; font-size: 14px;">Reset Password</a>
                    </div>
                    <p style="margin: 0; font-size: 12px; color: #888888; line-height: 18px;">This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.</p>'
                ),
                'body_text' => "Hello {{customer_name}},\n\nReset your {{business_name}} password using the following link:\n{{login_url}}\n\nThis link expires in 60 minutes.",
            ],
            [
                'code' => 'general_communication',
                'name' => 'General Business Announcement & Notice',
                'category' => 'general',
                'subject' => 'Update from {{business_name}}',
                'available_placeholders' => [
                    'customer_name', 'business_name', 'business_phone', 'business_email', 'business_address',
                ],
                'body_html' => self::wrapLayout(
                    'Customer Announcement',
                    'News and operational updates from {{business_name}}.',
                    '<p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #333333;">Dear <strong>{{customer_name}}</strong>,</p>
                    <p style="margin: 0 0 16px; font-size: 14px; line-height: 22px; color: #555555;">We would like to share an important business update with you regarding our store hours, new device inventory, and retail services.</p>
                    <div style="background-color: #F7F7F6; border-left: 3px solid #111111; padding: 14px 18px; margin: 18px 0; font-size: 13px; color: #333333; line-height: 20px;">
                        Visit our retail counter for special seasonal pricing on certified pre-owned smartphones and genuine electronic accessories.
                    </div>
                    <p style="margin: 0; font-size: 13px; color: #666666;">Warm regards,<br><strong>{{business_name}} Team</strong><br>{{business_phone}}</p>'
                ),
                'body_text' => "Dear {{customer_name}},\n\nUpdate from {{business_name}}.\n\nWarm regards,\n{{business_name}} Team\n{{business_phone}}",
            ],
        ];

        foreach ($templates as $data) {
            EmailTemplate::updateOrCreate(
                [
                    'tenant_id' => null, // Global default
                    'code' => $data['code'],
                ],
                array_merge($data, [
                    'is_active' => true,
                    'is_default' => true,
                ])
            );
        }
    }

    /**
     * Wrap body HTML in a responsive email container.
     */
    private static function wrapLayout(string $title, string $subtitle, string $content): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>'.e($title).'</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F7F7F6; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #111111;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #F7F7F6; padding: 32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 580px; background-color: #FFFFFF; border-radius: 14px; border: 1px solid #E6E6E6; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <!-- Brand Header -->
                    <tr>
                        <td style="background-color: #050505; padding: 22px 28px; text-align: left;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; font-weight: 700; color: #B7FF00; letter-spacing: 1px; text-transform: uppercase;">{{business_name}}</div>
                                        <div style="font-size: 18px; font-weight: 800; color: #FFFFFF; margin-top: 4px; letter-spacing: -0.3px;">'.e($title).'</div>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="display: inline-block; background-color: rgba(183, 255, 0, 0.15); color: #B7FF00; border: 1px solid rgba(183, 255, 0, 0.3); font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; font-family: monospace;">VERIFIED</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Subtitle Banner -->
                    <tr>
                        <td style="background-color: #F7F7F6; padding: 10px 28px; border-bottom: 1px solid #E6E6E6; font-size: 12px; color: #666666;">
                            '.e($subtitle).'
                        </td>
                    </tr>
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 28px 28px 20px;">
                            '.$content.'
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F7F7F6; padding: 18px 28px; border-top: 1px solid #E6E6E6; font-size: 11px; color: #888888; text-align: center; line-height: 18px;">
                            <div>Sent by <strong>{{business_name}}</strong> &bull; {{business_phone}}</div>
                            <div style="margin-top: 4px; color: #AAAAAA;">This is a system generated communication from INVOIRY Cloud Counter.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
}
