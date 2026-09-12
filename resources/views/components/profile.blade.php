<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $memberSince = '';
    public string $avatarInitial = '';

    public bool $editMode = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name ?? 'Queenza Akleema';
        $this->email = $user->email ?? 'quincaa@email.com';
        $this->phone = $user->phone_number ?? '';
        $this->address = $user->address ?? '';
        $this->memberSince = $user && $user->created_at ? $user->created_at->translatedFormat('F Y') : 'January 2025';
        $this->avatarInitial = strtoupper(substr(trim($this->name), 0, 1)) ?: 'U';
    }

    public function toggleEdit(): void
    {
        $this->editMode = !$this->editMode;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->name = $this->name;
            $user->email = $this->email;
            $user->phone_number = $this->phone;
            $user->address = $this->address;
            $user->save();
        }

        $this->editMode = false;
    }
};
?>

<div class="profile-wrap">
    <div class="profile-layout">

        {{-- SIDEBAR --}}
        <div class="profile-sidebar">
            <div class="profile-sidebar-head">
                <div class="profile-avatar profile-avatar-initial">
                    {{ $avatarInitial }}
                </div>
                <div>
                    <div class="name">{{ $name }}</div>
                    <div class="email">{{ $email }}</div>
                </div>
            </div>

            <div class="profile-nav">
                <a href="{{ route('profile') }}" class="active">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 000 18z" fill="currentColor" stroke="none"/></svg>
                    {{ __('profile.personal_information') }}
                </a>
                <a href="{{ route('my-orders') }}" class="{{ request()->routeIs('my-orders') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v2M16 4v2"/></svg>
                    {{ __('profile.my_orders') }}
                </a>
                <a href="{{ route('address') }}" class="{{ request()->routeIs('address') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M12 21s-7-6.5-7-11a7 7 0 0114 0c0 4.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    {{ __('profile.address') }}
                </a>
                <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">
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
        <div class="profile-main">
            <div class="profile-main-head">
                <div>
                    <h2>{{ __('profile.personal_information') }}</h2>
                    <div class="sub">{{ __('profile.your_account_details') }}</div>
                </div>
                @if(!$editMode)
                    <button type="button" class="edit-profile-btn" wire:click="toggleEdit">{{ __('profile.edit_profile') }}</button>
                @endif
            </div>

            <div class="profile-highlight">
                <div class="profile-avatar profile-avatar-initial">
                    {{ $avatarInitial }}
                </div>
                <div>
                    <div class="name">{{ $name }}</div>
                    <div class="member-since">{{ __('profile.member_since') }} {{ $memberSince }}</div>
                </div>
            </div>

            @if(!$editMode)
                <div class="profile-info-grid">
                    <div class="profile-info-card">
                        <label>{{ __('profile.full_name') }}</label>
                        <div class="val">{{ $name }}</div>
                    </div>
                    <div class="profile-info-card">
                        <label>{{ __('profile.email') }}</label>
                        <div class="val">{{ $email }}</div>
                    </div>
                    <div class="profile-info-card">
                        <label>{{ __('profile.phone_number') }}</label>
                        <div class="val">{{ $phone }}</div>
                    </div>
                    <div class="profile-info-card">
                        <label>{{ __('profile.default_address') }}</label>
                        <div class="val">{{ $address }}</div>
                    </div>
                </div>
            @else
                <div class="profile-info-grid">
                    <div class="profile-info-card">
                        <label>{{ __('profile.full_name') }}</label>
                        <input type="text" wire:model="name">
                    </div>
                    <div class="profile-info-card">
                        <label>{{ __('profile.email') }}</label>
                        <input type="email" wire:model="email">
                    </div>
                    <div class="profile-info-card">
                        <label>{{ __('profile.phone_number') }}</label>
                        <input type="text" wire:model="phone">
                    </div>
                    <div class="profile-info-card">
                        <label>{{ __('profile.default_address') }}</label>
                        <input type="text" wire:model="address">
                    </div>
                </div>

                <div class="profile-edit-actions">
                    <button type="button" class="profile-save-btn" wire:click="saveProfile">{{ __('profile.save_changes') }}</button>
                    <button type="button" class="profile-cancel-btn" wire:click="toggleEdit">{{ __('profile.cancel') }}</button>
                </div>
            @endif
        </div>

    </div>
</div>