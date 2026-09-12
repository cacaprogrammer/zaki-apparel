<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $avatarInitial = '';

    public array $toggles = [
        'order_notifications' => true,
        'promotions' => true,
        'sms_notifications' => false,
        'two_factor' => false,
    ];

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? 'Queenza';
        $this->email = $user->email ?? 'quincaa@email.com';
        $this->avatarInitial = strtoupper(substr(trim($this->name), 0, 1)) ?: 'U';
    }

    public function toggle(string $key): void
    {
        if (array_key_exists($key, $this->toggles)) {
            $this->toggles[$key] = !$this->toggles[$key];
        }
    }
};
?>

<div class="profile-wrap">
    <div class="profile-layout">

        {{-- SIDEBAR (sama seperti Profile) --}}
        <div class="profile-sidebar">
            <div class="profile-sidebar-head">
                <div class="profile-avatar profile-avatar-initial">{{ $avatarInitial }}</div>
                <div>
                    <div class="name">{{ $name }}</div>
                    <div class="email">{{ $email }}</div>
                </div>
            </div>

            <div class="profile-nav">
                <a href="{{ route('profile') }}">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 000 18z" fill="currentColor" stroke="none"/></svg>
                    {{ __('profile.personal_information') }}
                </a>
                <a href="{{ route('my-orders') }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v2M16 4v2"/></svg>
                    {{ __('profile.my_orders') }}
                </a>
                <a href="{{ route('address') }}">
                    <svg viewBox="0 0 24 24"><path d="M12 21s-7-6.5-7-11a7 7 0 0114 0c0 4.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    {{ __('profile.address') }}
                </a>
                <a href="{{ route('settings') }}" class="active">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 01-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.55-1H3a2 2 0 010-4h.09a1.7 1.7 0 001.55-1 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H9a1.7 1.7 0 001-1.55V3a2 2 0 014 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V9a1.7 1.7 0 001.55 1H21a2 2 0 010 4h-.09a1.7 1.7 0 00-1.55 1z"/></svg>
                    {{ __('profile.settings') }}
                </a>
                <a href="#">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                    {{ __('profile.chat') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout">
                        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        {{ __('profile.logout') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN --}}
        <div class="settings-main">
            <div class="settings-main-head">
                <h2>{{ __('settings.page_title') }}</h2>
                <div class="sub">{{ __('settings.subtitle') }}</div>
            </div>

            <div class="settings-row">
                <div class="settings-row-text">
                    <div class="title">{{ __('settings.order_notifications') }}</div>
                    <div class="desc">{{ __('settings.order_notifications_desc') }}</div>
                </div>
                <button type="button" class="toggle-switch {{ $toggles['order_notifications'] ? 'on' : '' }}" wire:click="toggle('order_notifications')"></button>
            </div>

            <div class="settings-row">
                <div class="settings-row-text">
                    <div class="title">{{ __('settings.promotions') }}</div>
                    <div class="desc">{{ __('settings.promotions_desc') }}</div>
                </div>
                <button type="button" class="toggle-switch {{ $toggles['promotions'] ? 'on' : '' }}" wire:click="toggle('promotions')"></button>
            </div>

            <div class="settings-row">
                <div class="settings-row-text">
                    <div class="title">{{ __('settings.sms_notifications') }}</div>
                    <div class="desc">{{ __('settings.sms_notifications_desc') }}</div>
                </div>
                <button type="button" class="toggle-switch {{ $toggles['sms_notifications'] ? 'on' : '' }}" wire:click="toggle('sms_notifications')"></button>
            </div>

            <div class="settings-row">
                <div class="settings-row-text">
                    <div class="title">{{ __('settings.two_factor') }}</div>
                    <div class="desc">{{ __('settings.two_factor_desc') }}</div>
                </div>
                <button type="button" class="toggle-switch {{ $toggles['two_factor'] ? 'on' : '' }}" wire:click="toggle('two_factor')"></button>
            </div>
        </div>

    </div>
</div>