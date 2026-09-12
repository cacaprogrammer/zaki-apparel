<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => __('auth.validation.email_required'),
            'email.email' => __('auth.validation.email_invalid'),
            'password.required' => __('auth.validation.password_required'),
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', __('auth.validation.failed'));
            return;
        }

        session()->regenerate();

        $this->redirect('/', navigate: false);
    }
}; ?>

<div class="auth-card">
    <a href="{{ url('/') }}" class="back-home">
        <svg viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M11 18l-6-6 6-6"/></svg>
        {{ __('auth.back_home') }}
    </a>

    <div class="form-box">
        <h2>{{ __('auth.login_title') }}</h2>
        <div class="sub">{{ __('auth.login_subtitle') }}</div>

        <form wire:submit="login">
            <div class="field">
                <label>{{ __('auth.email_label') }}</label>
                <div class="input-wrap">
                    <input type="email" wire:model="email" placeholder="{{ __('auth.email_placeholder') }}">
                    <span class="icon"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
                </div>
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="field" x-data="{ show: false }">
                <label>{{ __('auth.password_label') }}</label>
                <div class="input-wrap">
                    <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="{{ __('auth.password_placeholder') }}">
                    <button type="button" class="icon" @click="show = !show">
                        <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="row-between">
                <label class="remember">
                    <input type="checkbox" wire:model="remember" class="native-checkbox">
                    <span class="box"><svg viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    {{ __('auth.remember_me') }}
                </label>
                <a href="#" class="forgot-link">{{ __('auth.forgot_password') }}</a>
            </div>

            <button class="btn-submit" type="submit">{{ __('auth.login_button') }}</button>
        </form>

        <div class="divider-row"><span class="line"></span><span>{{ __('auth.or_continue_with') }}</span><span class="line"></span></div>

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

        <div class="sub" style="margin-top:24px; margin-bottom:0;">{{ __('auth.no_account') }} <a href="{{ route('register') }}">{{ __('auth.register_link') }}</a></div>
        <div class="admin-link">{{ __('auth.admin_question') }} <a href="#">{{ __('auth.admin_link') }}</a></div>
    </div>
</div>