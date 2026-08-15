<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class TemplatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $renderedSubject;

    private string $renderedBody;

    /**
     * @param  array<string, string>  $variables
     */
    public function __construct(string $slug, array $variables = [])
    {
        $rendered = EmailTemplate::render($slug, $variables);

        $this->renderedSubject = $rendered['subject'];
        $this->renderedBody = $rendered['body'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->renderedSubject);
    }

    public function content(): Content
    {
        $settings = Setting::allSettings();

        return new Content(view: 'emails.layout', with: [
            'slot' => $this->renderedBody,
            'preheader' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($this->renderedBody))), 100),
            'siteName' => $settings['site_name'] ?? config('app.name'),
            'logoUrl' => ! empty($settings['logo']) ? asset('uploads/'.$settings['logo']) : null,
            'primaryColor' => $settings['primary_color'] ?? '#4f46e5',
            'address' => $settings['address'] ?? null,
            'phone' => $settings['phone'] ?? null,
            'email' => $settings['email'] ?? null,
            'socialFacebook' => $settings['social_facebook'] ?? null,
            'socialInstagram' => $settings['social_instagram'] ?? null,
            'socialTwitter' => $settings['social_twitter'] ?? null,
            'socialYoutube' => $settings['social_youtube'] ?? null,
        ]);
    }
}
