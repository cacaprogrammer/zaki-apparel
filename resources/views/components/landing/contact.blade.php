<?php

use Livewire\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $message = '';
    public bool $sent = false;

    public function send(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:5',
        ], [
            'name.required' => __('landing.validation.name_required'),
            'email.required' => __('landing.validation.email_required'),
            'email.email' => __('landing.validation.email_invalid'),
            'message.required' => __('landing.validation.message_required'),
            'message.min' => __('landing.validation.message_min'),
        ]);

        // Dummy: belum benar-benar mengirim, hanya simulasi UI
        $this->sent = true;
        $this->reset(['name', 'email', 'message']);
    }
}; ?>

<section id="contact-us" class="contact-section">
    <div class="contact-inner">
        <div class="contact-form-box">
            <div class="contact-eyebrow">{{ __('landing.contact_eyebrow') }}</div>
            <h2>{{ __('landing.contact_heading') }}</h2>
            <div class="sub">{{ __('landing.contact_subtitle') }}</div>

            @if ($sent)
                <div class="contact-success">{{ __('landing.contact_success') }}</div>
            @endif

            <form wire:submit="send">
                <div class="contact-field">
                    <label>{{ __('landing.field_name') }}</label>
                    <input type="text" wire:model="name" placeholder="{{ __('landing.field_name_placeholder') }}">
                    @error('name') <div class="contact-error">{{ $message }}</div> @enderror
                </div>

                <div class="contact-field">
                    <label>{{ __('landing.field_email') }}</label>
                    <input type="email" wire:model="email" placeholder="{{ __('landing.field_email_placeholder') }}">
                    @error('email') <div class="contact-error">{{ $message }}</div> @enderror
                </div>

                <div class="contact-field">
                    <label>{{ __('landing.field_message') }}</label>
                    <textarea wire:model="message" placeholder="{{ __('landing.field_message_placeholder') }}"></textarea>
                    @error('message') <div class="contact-error">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="contact-submit-btn">{{ __('landing.send_message') }}</button>
            </form>
        </div>

        <div class="contact-info-box">
            <div class="brand-title">ZAKI APPAREL</div>

            <div class="contact-info-item">
                <div class="label">{{ __('landing.store_address_label') }}</div>
                <div class="value">Jl. Rungkut Industri No. 12<br>Surabaya, Jawa Timur 60293</div>
            </div>

            <div class="contact-info-item">
                <div class="label">{{ __('landing.email_label') }}</div>
                <div class="value">hello@zakiapparel.id</div>
            </div>

            <div class="contact-info-item">
                <div class="label">{{ __('landing.whatsapp_label') }}</div>
                <div class="value">+62 812-3456-7890</div>
            </div>

            <div class="contact-info-item" style="margin-bottom:0;">
                <div class="label">{{ __('landing.follow_us_label') }}</div>
                <div class="contact-social">
                    <a href="#"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="3.5"/></svg></a>
                    <a href="#"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                    <a href="#"><svg viewBox="0 0 24 24"><path d="M22 4.01c-1 .49-1.98.689-3 .99-1.121-1.265-2.783-1.335-4.38-.737S11.977 6.323 12 8v1c-3.245.083-6.135-1.395-8-4 0 0-4.182 7.433 4 11-1.872 1.247-3.739 2.088-6 2 3.308 1.803 6.913 2.423 10.034 1.517 3.58-1.04 6.522-3.723 7.651-7.742a13.84 13.84 0 0 0 .497-3.753C20.18 7.773 21.692 5.25 22 4.009z"/></svg></a>
                </div>
            </div>
        </div>
    </div>
</section>