<?php

namespace App\Notifications;

use App\Mail\TemplatedMail;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): Mailable
    {
        $otp = $notifiable->generateEmailVerificationOtp();

        return (new TemplatedMail('verify-email', [
            'name' => $notifiable->name,
            'otp' => $otp,
        ]))->to($notifiable->email);
    }
}
