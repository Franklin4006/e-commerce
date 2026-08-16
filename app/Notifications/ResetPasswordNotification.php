<?php

namespace App\Notifications;

use App\Mail\TemplatedMail;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): Mailable
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new TemplatedMail('reset-password', [
            'name' => $notifiable->name,
            'reset_url' => $url,
        ]))->to($notifiable->email);
    }
}
