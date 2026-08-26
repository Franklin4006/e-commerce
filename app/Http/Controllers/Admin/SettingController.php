<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::allSettings();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'mail_mailer' => ['nullable', 'in:smtp,log'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'cod_enabled' => ['nullable', 'boolean'],
            'razorpay_mode' => ['nullable', 'in:test,live'],
            'razorpay_test_key_id' => ['nullable', 'string', 'max:255'],
            'razorpay_test_key_secret' => ['nullable', 'string', 'max:255'],
            'razorpay_live_key_id' => ['nullable', 'string', 'max:255'],
            'razorpay_live_key_secret' => ['nullable', 'string', 'max:255'],
            'google_client_id' => ['nullable', 'string', 'max:255'],
            'google_client_secret' => ['nullable', 'string', 'max:255'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'gst_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'about_us_title' => ['nullable', 'string', 'max:255'],
            'about_us_content' => ['nullable', 'string'],
            'terms_title' => ['nullable', 'string', 'max:255'],
            'terms_content' => ['nullable', 'string'],
            'privacy_title' => ['nullable', 'string', 'max:255'],
            'privacy_content' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('favicon')) {
            $oldFavicon = Setting::get('favicon');
            if ($oldFavicon) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $validated['favicon'] = $request->file('favicon')->store('settings', 'public');
        } else {
            unset($validated['favicon']);
        }

        if (! $request->filled('mail_password')) {
            unset($validated['mail_password']);
        }

        if (! $request->filled('razorpay_test_key_secret')) {
            unset($validated['razorpay_test_key_secret']);
        }

        if (! $request->filled('razorpay_live_key_secret')) {
            unset($validated['razorpay_live_key_secret']);
        }

        if (! $request->filled('google_client_secret')) {
            unset($validated['google_client_secret']);
        }

        $validated['cod_enabled'] = $request->boolean('cod_enabled') ? '1' : '0';

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Settings updated successfully.');
    }
}
