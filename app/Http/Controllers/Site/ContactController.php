<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\TemplatedMail;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactController extends Controller
{
    public const SUBJECTS = [
        'General Inquiry',
        'Order Support',
        'Returns & Exchanges',
        'Product Question',
        'Feedback',
        'Other',
    ];

    public function index(): View
    {
        return view('site.contact', ['siteSettings' => Setting::allSettings()]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', Rule::in(self::SUBJECTS)],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Always present (even if blank) so the {{ phone }} placeholder in
        // the notification email resolves to an empty string instead of
        // leaking the literal placeholder when a customer leaves it blank.
        $validated['phone'] = $validated['phone'] ?? '';

        $recipient = Setting::get('email') ?: config('mail.from.address');

        Mail::to($recipient)->send(new TemplatedMail('contact-inquiry', $validated));

        return back()->with('status', 'Thanks for reaching out! We\'ll get back to you soon.');
    }
}
