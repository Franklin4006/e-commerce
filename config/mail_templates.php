<?php

return [

    'verify-email' => [
        'name' => 'Email Verification',
        'subject' => 'Your verification code is {{ otp }}',
        'description' => 'Sent when a customer registers. Available variables: {{ name }}, {{ otp }}. Also available everywhere: {{ site_name }}, {{ primary_color }}.',
        'body' => <<<'HTML'
            <h2 style="margin:0 0 16px; font-size:20px; color:#101828;">Verify your email address</h2>
            <p style="margin:0 0 16px;">Hi {{ name }},</p>
            <p style="margin:0 0 24px;">Thanks for creating an account with {{ site_name }}! Enter the code below to verify your email address. It expires in 10 minutes.</p>
            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                <tr>
                    <td bgcolor="#101828" style="border-radius:8px; background-color:#101828; padding:20px 28px;">
                        <span style="display:block; font-size:32px; font-weight:800; letter-spacing:10px; color:#ffffff;">{{ otp }}</span>
                    </td>
                </tr>
            </table>
            <p style="margin:0; font-size:13px; color:#667085;">If you did not create an account, no further action is required.</p>
            HTML,
    ],

    'reset-password' => [
        'name' => 'Password Reset',
        'subject' => 'Reset your password',
        'description' => 'Sent when a customer requests a password reset. Available variables: {{ name }}, {{ reset_url }}. Also available everywhere: {{ site_name }}, {{ primary_color }}.',
        'body' => <<<'HTML'
            <h2 style="margin:0 0 16px; font-size:20px; color:#101828;">Reset your password</h2>
            <p style="margin:0 0 16px;">Hi {{ name }},</p>
            <p style="margin:0 0 24px;">We received a request to reset the password for your {{ site_name }} account. Click the button below to choose a new password.</p>
            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                <tr>
                    <td style="border-radius:8px; background-color:{{ primary_color }};">
                        <a href="{{ reset_url }}" style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:8px;">Reset Password</a>
                    </td>
                </tr>
            </table>
            <p style="margin:0; font-size:13px; color:#667085;">This link will expire in 60 minutes. If you did not request a password reset, you can safely ignore this email.</p>
            HTML,
    ],

    'order-confirmation' => [
        'name' => 'Order Confirmation',
        'subject' => 'Your order {{ order_number }} has been placed!',
        'description' => 'Sent when a customer places an order. Available variables: {{ name }}, {{ order_number }}, {{ grand_total }}, {{ order_url }}. Also available everywhere: {{ site_name }}, {{ primary_color }}.',
        'body' => <<<'HTML'
            <h2 style="margin:0 0 16px; font-size:20px; color:#101828;">Thank you for your order!</h2>
            <p style="margin:0 0 16px;">Hi {{ name }},</p>
            <p style="margin:0 0 24px;">We've received your order and it's now being processed. Here's a quick summary:</p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px; background-color:#f8f9fb; border-radius:8px;">
                <tr>
                    <td style="padding:16px 20px; font-size:14px; color:#344054;">Order Number</td>
                    <td style="padding:16px 20px; font-size:14px; color:#101828; font-weight:600; text-align:right;">{{ order_number }}</td>
                </tr>
                <tr>
                    <td style="padding:0 20px 16px; font-size:14px; color:#344054; border-top:1px solid #eef0f3;">Order Total</td>
                    <td style="padding:0 20px 16px; font-size:14px; color:#101828; font-weight:600; text-align:right; border-top:1px solid #eef0f3;">₹{{ grand_total }}</td>
                </tr>
            </table>
            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                <tr>
                    <td style="border-radius:8px; background-color:{{ primary_color }};">
                        <a href="{{ order_url }}" style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:8px;">View Order</a>
                    </td>
                </tr>
            </table>
            <p style="margin:0; font-size:13px; color:#667085;">We'll send you another email as soon as your order ships.</p>
            HTML,
    ],

    'contact-inquiry' => [
        'name' => 'Contact Inquiry Notification',
        'subject' => 'New contact inquiry from {{ name }}',
        'description' => 'Sent to the store\'s contact email when a customer submits the Contact Us form. Available variables: {{ name }}, {{ email }}, {{ phone }}, {{ subject }}, {{ message }}. Also available everywhere: {{ site_name }}, {{ primary_color }}.',
        'body' => <<<'HTML'
            <h2 style="margin:0 0 16px; font-size:20px; color:#101828;">New contact inquiry</h2>
            <p style="margin:0 0 24px;">You've received a new message via the {{ site_name }} Contact Us form.</p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 8px; background-color:#f8f9fb; border-radius:8px;">
                <tr>
                    <td style="padding:16px 20px; font-size:14px; color:#344054; width:120px;">Name</td>
                    <td style="padding:16px 20px; font-size:14px; color:#101828; font-weight:600;">{{ name }}</td>
                </tr>
                <tr>
                    <td style="padding:0 20px 16px; font-size:14px; color:#344054; border-top:1px solid #eef0f3;">Email</td>
                    <td style="padding:0 20px 16px; font-size:14px; color:#101828; font-weight:600; border-top:1px solid #eef0f3;">{{ email }}</td>
                </tr>
                <tr>
                    <td style="padding:0 20px 16px; font-size:14px; color:#344054; border-top:1px solid #eef0f3;">Phone</td>
                    <td style="padding:0 20px 16px; font-size:14px; color:#101828; font-weight:600; border-top:1px solid #eef0f3;">{{ phone }}</td>
                </tr>
                <tr>
                    <td style="padding:0 20px 16px; font-size:14px; color:#344054; border-top:1px solid #eef0f3;">Subject</td>
                    <td style="padding:0 20px 16px; font-size:14px; color:#101828; font-weight:600; border-top:1px solid #eef0f3;">{{ subject }}</td>
                </tr>
            </table>
            <p style="margin:16px 0 0; font-size:14px; color:#344054;"><strong>Message:</strong></p>
            <p style="margin:8px 0 0; font-size:14px; color:#101828; white-space:pre-line;">{{ message }}</p>
            HTML,
    ],

];
