<?php

namespace App\Notifications;

use App\Mail\TemplatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
