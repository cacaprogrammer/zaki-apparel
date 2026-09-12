<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    public function register(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'accepted',
        ], [
            'name.required' => __('auth.validation.name_required'),
            'email.required' => __('auth.validation.email_required'),
            'email.email' => __('auth.validation.email_invalid'),
            'email.unique' => __('auth.validation.email_unique'),
            'phone.required' => __('auth.validation.phone_required'),
            'password.required' => __('auth.validation.password_required'),
            'password.min' => __('auth.validation.password_min'),
            'password.confirmed' => __('auth.validation.password_confirmed'),
            'terms.accepted' => __('auth.validation.terms_required'),
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        session()->flash('registered', true);

        $this->redirect(route('login'), navigate: false);
    }
}; ?>

<div class="auth-card">
    <a href="{{ url('/') }}" class="back-home">
        <svg viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M11 18l-6-6 6-6"/></svg>
        {{ __('auth.back_home') }}
    </a>

    <div class="form-box">
        <h2>{{ __('auth.register_title') }}</h2>
        <div class="sub">{{ __('auth.have_account') }} <a href="{{ route('login') }}">{{ __('auth.login_link') }}</a></div>

        <form wire:submit="register">
            <div class="field">
                <label>{{ __('auth.name_label') }}</label>
                <div class="input-wrap">
                    <input type="text" wire:model="name" placeholder="{{ __('auth.name_placeholder') }}">
                    <span class="icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c1.6-4 4.4-6 7.5-6s5.9 2 7.5 6"/></svg></span>
                </div>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>{{ __('auth.email_label') }}</label>
                <div class="input-wrap">
                    <input type="email" wire:model="email" placeholder="{{ __('auth.email_placeholder') }}">
                    <span class="icon"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
                </div>
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>{{ __('auth.phone_label') }}</label>
                <div class="input-wrap">
                    <input type="tel" wire:model="phone" placeholder="{{ __('auth.phone_placeholder') }}">
                    <span class="icon"><svg viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .4 2 .6 3 .7a2 2 0 0 1 1.7 2z"/></svg></span>
                </div>
                @error('phone') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field" x-data="{ show: false, val: '' }">
                <label>{{ __('auth.password_label') }}</label>
                <div class="input-wrap">
                    <input :type="show ? 'text' : 'password'" wire:model="password" x-on:input="val = $event.target.value" placeholder="{{ __('auth.password_create_placeholder') }}">
                    <button type="button" class="icon" @click="show = !show">
                        <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="pw-strength">
                    <div class="seg" :class="val.length >= 6 ? 'on' : ''"></div>
                    <div class="seg" :class="(/[A-Z]/.test(val) && /[0-9]/.test(val)) ? 'on' : ''"></div>
                    <div class="seg" :class="(val.length >= 10 && /[^A-Za-z0-9]/.test(val)) ? 'strong' : ''"></div>
                </div>
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field" x-data="{ show: false }">
                <label>{{ __('auth.confirm_password_label') }}</label>
                <div class="input-wrap">
                    <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" placeholder="{{ __('auth.confirm_password_placeholder') }}">
                    <button type="button" class="icon" @click="show = !show">
                        <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <label class="terms-row">
                <input type="checkbox" wire:model="terms" class="native-checkbox">
                <span class="box"><svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span class="text">{{ __('auth.terms_text') }} <a href="#">{{ __('auth.terms_of_service') }}</a> {{ __('auth.and') }} <a href="#">{{ __('auth.privacy_policy') }}</a></span>
            </label>
            @error('terms') <div class="field-error" style="margin-top:-14px; margin-bottom:16px;">{{ $message }}</div> @enderror

            <button class="btn-submit" type="submit">{{ __('auth.register_button') }}</button>
        </form>

        <div class="divider-row"><span class="line"></span><span>{{ __('auth.or_signup_with') }}</span><span class="line"></span></div>

        <div class="social-row">
            <button type="button" class="social-btn">
                <svg viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.4-1.7 4.1-5.5 4.1-3.3 0-6-2.7-6-6.2s2.7-6.2 6-6.2c1.9 0 3.1.8 3.9 1.5l2.6-2.5C16.9 3.1 14.7 2 12 2 6.9 2 2.7 6.1 2.7 11.2S6.9 21 12 21c6.3 0 9-4.4 9-9.6 0-.6-.1-1.1-.2-1.6H12z"/></svg>
                Google
            </button>
            <button type="button" class="social-btn">
                <svg viewBox="0 0 24 24"><path fill="#1877F2" d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg>
                Facebook
            </button>
        </div>
    </div>
</div>