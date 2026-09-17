<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public bool $agree = false;
    public bool $showPassword = false;
    public bool $showConfirmPassword = false;

    public function togglePasswordVisibility(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function toggleConfirmPasswordVisibility(): void
    {
        $this->showConfirmPassword = !$this->showConfirmPassword;
    }

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|min:8',
            'passwordConfirmation' => 'required|same:password',
            'agree' => 'accepted',
        ], [
            'agree.accepted' => __('admin-auth.must_agree'),
            'passwordConfirmation.same' => __('admin-auth.password_mismatch'),
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone,
            'password' => Hash::make($this->password),
            'role' => 'admin',
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirect(route('admin.dashboard'), navigate: false);
    }
};
?>

<div class="auth-card">
    <h1>{{ __('admin-auth.register_title') }}</h1>
    <p class="sub">{{ __('admin-auth.register_subtitle') }}</p>

    <div class="auth-field">
        <label>{{ __('admin-auth.full_name') }}</label>
        <div class="auth-input-wrap">
            <input type="text" wire:model="name" placeholder="{{ __('admin-auth.full_name_placeholder') }}">
            <span class="icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c1.6-4 4.4-6 7.5-6s5.9 2 7.5 6"/></svg>
            </span>
        </div>
        @error('name') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
    </div>

    <div class="auth-field">
        <label>{{ __('admin-auth.email') }}</label>
        <div class="auth-input-wrap">
            <input type="email" wire:model="email" placeholder="your@email.com">
            <span class="icon">
                <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
            </span>
        </div>
        @error('email') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
    </div>

    <div class="auth-field">
        <label>{{ __('admin-auth.phone_number') }}</label>
        <div class="auth-input-wrap">
            <input type="text" wire:model="phone" placeholder="+62 xxx xxxx xxxx">
            <span class="icon">
                <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.68 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.32 1.85.55 2.81.68A2 2 0 0122 16.92z"/></svg>
            </span>
        </div>
        @error('phone') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
    </div>

    <div class="auth-field-row">
        <div class="auth-field">
            <label>{{ __('admin-auth.password') }}</label>
            <div class="auth-input-wrap">
                <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" placeholder="{{ __('admin-auth.password_placeholder') }}">
                <span class="icon clickable" wire:click="togglePasswordVisibility">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </span>
            </div>
            @error('password') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
        </div>
        <div class="auth-field">
            <label>{{ __('admin-auth.confirm_password') }}</label>
            <div class="auth-input-wrap">
                <input type="{{ $showConfirmPassword ? 'text' : 'password' }}" wire:model="passwordConfirmation" placeholder="{{ __('admin-auth.repeat_password') }}">
                <span class="icon clickable" wire:click="toggleConfirmPasswordVisibility">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </span>
            </div>
            @error('passwordConfirmation') <div class="auth-error" style="margin-top:6px; margin-bottom:0;">{{ $message }}</div> @enderror
        </div>
    </div>

    <label class="auth-agree">
        <input type="checkbox" wire:model="agree">
        <span>{{ __('admin-auth.agree_prefix') }} <a href="#">{{ __('admin-auth.terms_of_service') }}</a> {{ __('admin-auth.and') }} <a href="#">{{ __('admin-auth.privacy_policy') }}</a></span>
    </label>
    @error('agree') <div class="auth-error">{{ $message }}</div> @enderror

    <button type="button" class="auth-submit-btn" wire:click="register">{{ __('admin-auth.create_account') }}</button>

    <div class="auth-divider">{{ __('admin-auth.or_sign_up_with') }}</div>

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
        {{ __('admin-auth.have_account') }} <a href="{{ route('admin.login') }}">{{ __('admin-auth.sign_in') }}</a>
    </div>
</div>