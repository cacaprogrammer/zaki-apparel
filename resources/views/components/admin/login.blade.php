<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $showPassword = false;

    public ?string $error = null;

    public function togglePasswordVisibility(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login(): void
    {
        $this->error = null;

        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->error = __('admin-auth.invalid_credentials');
            return;
        }

        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            $this->error = __('admin-auth.not_an_admin');
            return;
        }

        request()->session()->regenerate();

        $this->redirect(route('admin.dashboard'), navigate: false);
    }
};
?>

<div class="auth-card">
    <h1>{{ __('admin-auth.login_title') }}</h1>
    <p class="sub">{{ __('admin-auth.login_subtitle') }}</p>

    @if($error)
        <div class="auth-error">{{ $error }}</div>
    @endif

    <div class="auth-field">
        <label>{{ __('admin-auth.email_address') }}</label>
        <div class="auth-input-wrap">
            <input type="email" wire:model="email" placeholder="your@email.com">
            <span class="icon">
                <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
            </span>
        </div>
        @error('email') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
    </div>

    <div class="auth-field">
        <label>{{ __('admin-auth.password') }}</label>
        <div class="auth-input-wrap">
            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" placeholder="{{ __('admin-auth.password_placeholder') }}">
            <span class="icon clickable" wire:click="togglePasswordVisibility">
                @if($showPassword)
                    <svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.6 21.6 0 015.06-6.06M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a21.6 21.6 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                @else
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                @endif
            </span>
        </div>
        @error('password') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
    </div>

    <div class="auth-row-between">
        <label class="auth-remember">
            <input type="checkbox" wire:model="remember">
            {{ __('admin-auth.remember_me') }}
        </label>
        <a href="#" class="auth-forgot">{{ __('admin-auth.forgot_password') }}</a>
    </div>

    <button type="button" class="auth-submit-btn" wire:click="login">{{ __('admin-auth.log_in') }}</button>

    <div class="auth-divider">{{ __('admin-auth.or_continue_with') }}</div>

    <div class="auth-social-row">
        <button type="button" class="auth-social-btn" disabled title="{{ __('admin-auth.not_yet_available') }}">
            <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.99.66-2.25 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.85A11 11 0 0012 23z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 010-4.2V7.05H2.18a11 11 0 000 9.9l3.66-2.85z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1a11 11 0 00-9.82 6.05l3.66 2.85C6.71 7.31 9.14 5.38 12 5.38z"/></svg>
            Google
        </button>
        <button type="button" class="auth-social-btn" disabled title="{{ __('admin-auth.not_yet_available') }}">
            <svg viewBox="0 0 24 24"><path fill="#1877F2" d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.7 4.53-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.1 24 18.1 24 12.07z"/></svg>
            Facebook
        </button>
    </div>

    <div class="auth-switch">
        {{ __('admin-auth.no_account') }} <a href="{{ route('admin.register') }}">{{ __('admin-auth.create_one') }}</a>
    </div>
</div>