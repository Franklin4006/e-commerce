<x-layouts.admin title="Settings">
    @php
        $settingsTabFields = [
            'general' => ['site_name', 'logo', 'meta_title', 'meta_description', 'meta_keywords', 'primary_color', 'secondary_color'],
            'pages' => ['about_us_title', 'about_us_content', 'terms_title', 'terms_content', 'privacy_title', 'privacy_content'],
            'contact' => ['email', 'phone', 'address', 'whatsapp_number', 'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube'],
            'mail' => ['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'],
            'commerce' => ['shipping_charge', 'gst_percentage', 'low_stock_threshold', 'cod_enabled'],
            'payments' => ['razorpay_mode', 'razorpay_test_key_id', 'razorpay_test_key_secret', 'razorpay_live_key_id', 'razorpay_live_key_secret'],
            'social-login' => ['google_client_id', 'google_client_secret'],
        ];
        $activeSettingsTab = 'general';
        foreach ($settingsTabFields as $tab => $fields) {
            if ($errors->hasAny($fields)) {
                $activeSettingsTab = $tab;
                break;
            }
        }
    @endphp

    <h1 class="page-title">Settings</h1>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="settings-wrap">
        <nav class="profile-tabs profile-tabs-inline" data-tabs="admin-settings">
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'general' ? 'active' : '' }}" data-tab-btn="general">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>General</span>
            </button>
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'pages' ? 'active' : '' }}" data-tab-btn="pages">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                <span>Pages</span>
            </button>
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'contact' ? 'active' : '' }}" data-tab-btn="contact">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span>Contact &amp; Social</span>
            </button>
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'mail' ? 'active' : '' }}" data-tab-btn="mail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 6 12 13 2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                <span>Mail</span>
            </button>
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'commerce' ? 'active' : '' }}" data-tab-btn="commerce">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span>Commerce</span>
            </button>
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'payments' ? 'active' : '' }}" data-tab-btn="payments">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <span>Payments</span>
            </button>
            <button type="button" class="profile-tab-btn {{ $activeSettingsTab === 'social-login' ? 'active' : '' }}" data-tab-btn="social-login">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                <span>Social Login</span>
            </button>
        </nav>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="stack">
        @csrf
        @method('PUT')

        <div class="profile-tab-panel {{ $activeSettingsTab === 'general' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="general">
        <section class="card">
            <h2 class="section-title">Website Information</h2>

            <div class="form-group">
                <label for="site_name" class="form-label">Website Name</label>
                <input id="site_name" type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required class="form-control">
                @error('site_name') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="logo" class="form-label">Logo</label>
                <input id="logo" type="file" name="logo" accept="image/*" class="form-control">
                <small style="color: var(--text-muted);">PNG, JPG up to 2MB</small>
                @error('logo') <span class="field-error">{{ $message }}</span> @enderror
                <img id="logo-preview" src="{{ ! empty($settings['logo']) ? asset('storage/'.$settings['logo']) : '' }}"
                     alt="" class="thumb-lg form-preview" style="{{ ! empty($settings['logo']) ? '' : 'display: none;' }}">
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">SEO</h2>

            <div class="form-group">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input id="meta_title" type="text" name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}" class="form-control">
                @error('meta_title') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3" class="form-control">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                @error('meta_description') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <input id="meta_keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}" placeholder="comma, separated, keywords" class="form-control">
                @error('meta_keywords') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">Theme Colors</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="primary_color" class="form-label">Primary Color</label>
                    <input id="primary_color" type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#4f46e5') }}" class="form-control" style="padding: 0.25rem; height: 42px;">
                    @error('primary_color') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="secondary_color" class="form-label">Secondary Color</label>
                    <input id="secondary_color" type="color" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#64748b') }}" class="form-control" style="padding: 0.25rem; height: 42px;">
                    @error('secondary_color') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>
        </div>

        <div class="profile-tab-panel {{ $activeSettingsTab === 'pages' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="pages">
        <section class="card">
            <h2 class="section-title">About Us</h2>

            <div class="form-group">
                <label for="about_us_title" class="form-label">Page Title</label>
                <input id="about_us_title" type="text" name="about_us_title" value="{{ old('about_us_title', $settings['about_us_title'] ?? '') }}" placeholder="About Us" class="form-control">
                @error('about_us_title') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="about_us_content" class="form-label">Page Content</label>
                <textarea id="about_us_content" name="about_us_content" rows="8" class="form-control" data-rich-text>{{ old('about_us_content', $settings['about_us_content'] ?? '') }}</textarea>
                @error('about_us_content') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">Terms of Service</h2>

            <div class="form-group">
                <label for="terms_title" class="form-label">Page Title</label>
                <input id="terms_title" type="text" name="terms_title" value="{{ old('terms_title', $settings['terms_title'] ?? '') }}" placeholder="Terms of Service" class="form-control">
                @error('terms_title') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="terms_content" class="form-label">Page Content</label>
                <textarea id="terms_content" name="terms_content" rows="8" class="form-control" data-rich-text>{{ old('terms_content', $settings['terms_content'] ?? '') }}</textarea>
                @error('terms_content') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">Privacy Policy</h2>

            <div class="form-group">
                <label for="privacy_title" class="form-label">Page Title</label>
                <input id="privacy_title" type="text" name="privacy_title" value="{{ old('privacy_title', $settings['privacy_title'] ?? '') }}" placeholder="Privacy Policy" class="form-control">
                @error('privacy_title') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="privacy_content" class="form-label">Page Content</label>
                <textarea id="privacy_content" name="privacy_content" rows="8" class="form-control" data-rich-text>{{ old('privacy_content', $settings['privacy_content'] ?? '') }}</textarea>
                @error('privacy_content') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </section>
        </div>

        <div class="profile-tab-panel {{ $activeSettingsTab === 'contact' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="contact">
        <section class="card">
            <h2 class="section-title">Contact Information</h2>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" class="form-control">
                @error('email') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Contact Number</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" class="form-control">
                @error('phone') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <textarea id="address" name="address" rows="2" class="form-control">{{ old('address', $settings['address'] ?? '') }}</textarea>
                @error('address') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">Social Media Links</h2>

            <div class="form-group">
                <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                <input id="whatsapp_number" type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}" placeholder="+91XXXXXXXXXX" class="form-control">
                @error('whatsapp_number') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Include the country code. Powers the WhatsApp support button on checkout and the WhatsApp link shown across the site.</small>
            </div>

            <div class="form-group">
                <label for="social_facebook" class="form-label">Facebook URL</label>
                <input id="social_facebook" type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/yourpage" class="form-control">
                @error('social_facebook') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="social_instagram" class="form-label">Instagram URL</label>
                <input id="social_instagram" type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/yourpage" class="form-control">
                @error('social_instagram') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="social_twitter" class="form-label">Twitter / X URL</label>
                <input id="social_twitter" type="url" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" placeholder="https://x.com/yourpage" class="form-control">
                @error('social_twitter') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="social_youtube" class="form-label">YouTube URL</label>
                <input id="social_youtube" type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/@yourchannel" class="form-control">
                @error('social_youtube') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </section>
        </div>

        <div class="profile-tab-panel {{ $activeSettingsTab === 'mail' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="mail">
        <section class="card">
            <h2 class="section-title">Mail (SMTP)</h2>

            <div class="form-group">
                <label for="mail_mailer" class="form-label">Mailer</label>
                <select id="mail_mailer" name="mail_mailer" class="form-control">
                    <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer'] ?? 'log') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="log" {{ old('mail_mailer', $settings['mail_mailer'] ?? 'log') === 'log' ? 'selected' : '' }}>Log (development only)</option>
                </select>
                @error('mail_mailer') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mail_host" class="form-label">SMTP Host</label>
                    <input id="mail_host" type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" placeholder="smtp.example.com" class="form-control">
                    @error('mail_host') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="mail_port" class="form-label">SMTP Port</label>
                    <input id="mail_port" type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? 587) }}" class="form-control">
                    @error('mail_port') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="mail_username" class="form-label">SMTP Username</label>
                <input id="mail_username" type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="form-control">
                @error('mail_username') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="mail_password" class="form-label">SMTP Password</label>
                <input id="mail_password" type="password" name="mail_password" value="" placeholder="{{ ! empty($settings['mail_password']) ? '••••••••' : '' }}" autocomplete="new-password" class="form-control">
                @error('mail_password') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Leave blank to keep the current password.</small>
            </div>

            <div class="form-group">
                <label for="mail_encryption" class="form-label">Encryption</label>
                <select id="mail_encryption" name="mail_encryption" class="form-control">
                    <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === '' ? 'selected' : '' }}>None</option>
                </select>
                @error('mail_encryption') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mail_from_address" class="form-label">From Address</label>
                    <input id="mail_from_address" type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="form-control">
                    @error('mail_from_address') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="mail_from_name" class="form-label">From Name</label>
                    <input id="mail_from_name" type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" class="form-control">
                    @error('mail_from_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>
        </div>

        <div class="profile-tab-panel {{ $activeSettingsTab === 'commerce' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="commerce">
        <section class="card">
            <h2 class="section-title">Payment Methods</h2>

            <div class="form-group form-check">
                <input type="hidden" name="cod_enabled" value="0">
                <input id="cod_enabled" type="checkbox" name="cod_enabled" value="1" {{ old('cod_enabled', $settings['cod_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                <label for="cod_enabled">Enable Cash on Delivery (COD)</label>
            </div>
            @error('cod_enabled') <span class="field-error">{{ $message }}</span> @enderror
            <small style="color: var(--text-muted);">When disabled, customers won't see Cash on Delivery as a payment option at checkout.</small>
        </section>

        <section class="card">
            <h2 class="section-title">Shipping</h2>

            <div class="form-group">
                <label for="shipping_charge" class="form-label">Shipping Charge (₹)</label>
                <input id="shipping_charge" type="number" name="shipping_charge" value="{{ old('shipping_charge', $settings['shipping_charge'] ?? '') }}" min="0" step="0.01" class="form-control">
                @error('shipping_charge') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Leave blank or 0 for free shipping. Shown to customers on the cart page.</small>
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">Tax</h2>

            <div class="form-group">
                <label for="gst_percentage" class="form-label">GST (%)</label>
                <input id="gst_percentage" type="number" name="gst_percentage" value="{{ old('gst_percentage', $settings['gst_percentage'] ?? '') }}" min="0" max="100" step="0.01" class="form-control">
                @error('gst_percentage') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Product prices are treated as GST-inclusive. Leave blank to hide the GST breakup on invoices. Shown as a tax breakdown on the invoice only — the total charged to customers does not change.</small>
            </div>
        </section>

        <section class="card">
            <h2 class="section-title">Inventory</h2>

            <div class="form-group">
                <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                <input id="low_stock_threshold" type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? 5) }}" min="0" class="form-control">
                @error('low_stock_threshold') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Products at or below this stock level are flagged as low stock in the Inventory list.</small>
            </div>
        </section>
        </div>

        <div class="profile-tab-panel {{ $activeSettingsTab === 'payments' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="payments">
        <section class="card">
            <h2 class="section-title">Razorpay</h2>

            <div class="form-group">
                <label for="razorpay_mode" class="form-label">Active Mode</label>
                <select id="razorpay_mode" name="razorpay_mode" class="form-control">
                    <option value="test" {{ old('razorpay_mode', $settings['razorpay_mode'] ?? 'test') === 'test' ? 'selected' : '' }}>Test</option>
                    <option value="live" {{ old('razorpay_mode', $settings['razorpay_mode'] ?? 'test') === 'live' ? 'selected' : '' }}>Live</option>
                </select>
                @error('razorpay_mode') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Only the key pair for the active mode is used to process payments.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="razorpay_test_key_id" class="form-label">Test Key ID</label>
                    <input id="razorpay_test_key_id" type="text" name="razorpay_test_key_id" value="{{ old('razorpay_test_key_id', $settings['razorpay_test_key_id'] ?? '') }}" placeholder="rzp_test_xxxxxxxxxxxx" class="form-control">
                    @error('razorpay_test_key_id') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="razorpay_test_key_secret" class="form-label">Test Key Secret</label>
                    <input id="razorpay_test_key_secret" type="password" name="razorpay_test_key_secret" value="" placeholder="{{ ! empty($settings['razorpay_test_key_secret']) ? '••••••••' : '' }}" autocomplete="new-password" class="form-control">
                    @error('razorpay_test_key_secret') <span class="field-error">{{ $message }}</span> @enderror
                    <small style="color: var(--text-muted);">Leave blank to keep the current secret.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="razorpay_live_key_id" class="form-label">Live Key ID</label>
                    <input id="razorpay_live_key_id" type="text" name="razorpay_live_key_id" value="{{ old('razorpay_live_key_id', $settings['razorpay_live_key_id'] ?? '') }}" placeholder="rzp_live_xxxxxxxxxxxx" class="form-control">
                    @error('razorpay_live_key_id') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="razorpay_live_key_secret" class="form-label">Live Key Secret</label>
                    <input id="razorpay_live_key_secret" type="password" name="razorpay_live_key_secret" value="" placeholder="{{ ! empty($settings['razorpay_live_key_secret']) ? '••••••••' : '' }}" autocomplete="new-password" class="form-control">
                    @error('razorpay_live_key_secret') <span class="field-error">{{ $message }}</span> @enderror
                    <small style="color: var(--text-muted);">Leave blank to keep the current secret.</small>
                </div>
            </div>
        </section>
        </div>

        <div class="profile-tab-panel {{ $activeSettingsTab === 'social-login' ? 'active' : '' }}" data-tab-group="admin-settings" data-tab-panel="social-login">
        <section class="card">
            <h2 class="section-title">Google Login</h2>
            <p class="muted">Lets customers sign in or register with their Google account. Create OAuth credentials in the <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener">Google Cloud Console</a> and paste them below.</p>

            <div class="form-group">
                <label class="form-label">Authorized redirect URI</label>
                <input type="text" value="{{ route('auth.google.callback') }}" class="form-control" readonly onclick="this.select()">
                <small style="color: var(--text-muted);">Add this exact URL as an "Authorized redirect URI" for your Google OAuth client.</small>
            </div>

            <div class="form-group">
                <label for="google_client_id" class="form-label">Client ID</label>
                <input id="google_client_id" type="text" name="google_client_id" value="{{ old('google_client_id', $settings['google_client_id'] ?? '') }}" placeholder="xxxxxxxxxx.apps.googleusercontent.com" class="form-control">
                @error('google_client_id') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="google_client_secret" class="form-label">Client Secret</label>
                <input id="google_client_secret" type="password" name="google_client_secret" value="" placeholder="{{ ! empty($settings['google_client_secret']) ? '••••••••' : '' }}" autocomplete="new-password" class="form-control">
                @error('google_client_secret') <span class="field-error">{{ $message }}</span> @enderror
                <small style="color: var(--text-muted);">Leave blank to keep the current secret.</small>
            </div>
        </section>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
    </div>

    <script src="{{ asset('js/rich-text-editor.js') }}"></script>
    <script src="{{ asset('js/settings-form.js') }}"></script>
    <script src="{{ asset('js/tabs.js') }}"></script>
</x-layouts.admin>
