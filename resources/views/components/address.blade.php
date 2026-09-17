<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public bool $showLogoutModal = false;

    public string $name = '';
    public string $email = '';
    public string $avatarInitial = '';

    public array $addresses = [];

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $formLabel = 'Home';
    public string $formName = '';
    public string $formPhone = '';
    public string $formFullAddress = '';
    public string $formCity = '';
    public string $formPostalCode = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? 'Queenza';
        $this->email = $user->email ?? 'quincaa@email.com';
        $this->avatarInitial = strtoupper(substr(trim($this->name), 0, 1)) ?: 'U';

        // Dummy data — nanti diganti dengan data alamat asli dari database
        $this->addresses = [
            [
                'id' => 1,
                'label' => 'Home',
                'name' => 'Queenza',
                'phone' => '+62 812 3456 7890',
                'full_address' => 'Jl. Kenanga No. 12, RT 04/RW 02, Kelurahan Sukamaju, Kec. Lowokwaru',
                'city' => 'Malang, Jawa Timur',
                'postal_code' => '65141',
            ],
            [
                'id' => 2,
                'label' => 'Office',
                'name' => 'Queenza',
                'phone' => '+62 812 3456 7890',
                'full_address' => 'Jl. Ahmad Yani No. 88, Gedung Graha Pena Lt. 5',
                'city' => 'Surabaya, Jawa Timur',
                'postal_code' => '60234',
            ],
        ];
    }

    public function openAddForm(): void
    {
        $this->editingId = null;
        $this->formLabel = 'Home';
        $this->formName = '';
        $this->formPhone = '';
        $this->formFullAddress = '';
        $this->formCity = '';
        $this->formPostalCode = '';
        $this->showForm = true;
    }

    public function editAddress(int $id): void
    {
        $address = collect($this->addresses)->firstWhere('id', $id);
        if (!$address) {
            return;
        }

        $this->editingId = $id;
        $this->formLabel = $address['label'];
        $this->formName = $address['name'];
        $this->formPhone = $address['phone'];
        $this->formFullAddress = $address['full_address'];
        $this->formCity = $address['city'];
        $this->formPostalCode = $address['postal_code'];
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
    }

    public function saveAddress(): void
    {
        if (trim($this->formName) === '' || trim($this->formPhone) === '' || trim($this->formFullAddress) === '' || trim($this->formCity) === '' || trim($this->formPostalCode) === '') {
            return;
        }

        $data = [
            'label' => $this->formLabel,
            'name' => $this->formName,
            'phone' => $this->formPhone,
            'full_address' => $this->formFullAddress,
            'city' => $this->formCity,
            'postal_code' => $this->formPostalCode,
        ];

        if ($this->editingId !== null) {
            foreach ($this->addresses as $i => $address) {
                if ($address['id'] === $this->editingId) {
                    $this->addresses[$i] = ['id' => $this->editingId, ...$data];
                    break;
                }
            }
        } else {
            $newId = count($this->addresses) > 0 ? max(array_column($this->addresses, 'id')) + 1 : 1;
            $this->addresses[] = ['id' => $newId, ...$data];
        }

        $this->showForm = false;
        $this->editingId = null;
    }

    public function deleteAddress(int $id): void
    {
        $this->addresses = array_values(array_filter($this->addresses, fn($a) => $a['id'] !== $id));
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
                <a href="{{ route('address') }}" class="active">
                    <svg viewBox="0 0 24 24"><path d="M12 21s-7-6.5-7-11a7 7 0 0114 0c0 4.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    {{ __('profile.address') }}
                </a>
                <a href="{{ route('settings') }}">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 01-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.55-1H3a2 2 0 010-4h.09a1.7 1.7 0 001.55-1 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H9a1.7 1.7 0 001-1.55V3a2 2 0 014 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V9a1.7 1.7 0 001.55 1H21a2 2 0 010 4h-.09a1.7 1.7 0 00-1.55 1z"/></svg>
                    {{ __('profile.settings') }}
                </a>
                <a href="{{ route('chat') }}">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                    {{ __('profile.chat') }}
                </a>
                <a href="{{ route('notifications') }}">
                    <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 5-2 6-2 6h16s-2-1-2-6"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>
                    {{ __('profile.notifications') }}
                    <span class="nav-badge-count">5</span>
                </a>

                <button type="button" class="logout" wire:click="$set('showLogoutModal', true)">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    {{ __('profile.logout') }}
                </button>
            </div>
        </div>

        @if($showLogoutModal)
            <div class="logout-modal-overlay">
                <div class="logout-modal-box">
                    <div class="logout-modal-icon">
                        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    </div>
                    <h3>{{ __('profile.logout_confirm_title') }}</h3>
                    <p>{{ __('profile.logout_confirm_desc') }}</p>
                    <div class="logout-modal-actions">
                        <button type="button" class="logout-cancel-btn" wire:click="$set('showLogoutModal', false)">{{ __('profile.cancel') }}</button>
                        <form method="POST" action="{{ route('logout') }}" style="flex:1;">
                            @csrf
                            <button type="submit" class="logout-confirm-btn" style="width:100%;">{{ __('profile.confirm_logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- MAIN --}}
        <div class="address-main">
            <div class="address-main-head">
                <h2>{{ __('address.page_title') }}</h2>
                <div class="sub">{{ __('address.subtitle') }}</div>
            </div>

            <div class="address-list-scroll">
                @forelse($addresses as $address)
                    <div class="address-card">
                        <span class="address-tag">{{ $address['label'] }}</span>
                        <div class="name">{{ $address['name'] }}</div>
                        <div class="text">{{ $address['full_address'] }}, {{ $address['city'] }} {{ $address['postal_code'] }} · {{ $address['phone'] }}</div>

                        <button type="button" class="address-edit-btn" wire:click="editAddress({{ $address['id'] }})">{{ __('address.edit') }}</button>
                        <button type="button" class="address-delete-btn" wire:click="deleteAddress({{ $address['id'] }})">{{ __('address.delete') }}</button>
                    </div>
                @empty
                    <div class="address-empty">{{ __('address.no_addresses') }}</div>
                @endforelse
            </div>

            @if($showForm)
                <div class="address-form">
                    <div class="address-field-row">
                        <div class="address-field">
                            <label>{{ __('address.label') }}</label>
                            <select wire:model="formLabel">
                                <option value="Home">{{ __('address.home') }}</option>
                                <option value="Office">{{ __('address.office') }}</option>
                                <option value="Other">{{ __('address.other') }}</option>
                            </select>
                        </div>
                        <div class="address-field">
                            <label>{{ __('address.full_name') }}</label>
                            <input type="text" wire:model="formName" placeholder="{{ __('address.full_name_placeholder') }}">
                        </div>
                    </div>
                    <div class="address-field-row">
                        <div class="address-field">
                            <label>{{ __('address.phone_number') }}</label>
                            <input type="text" wire:model="formPhone" placeholder="+62 xxx xxxx xxxx">
                        </div>
                        <div class="address-field">
                            <label>{{ __('address.city') }}</label>
                            <input type="text" wire:model="formCity" placeholder="Malang">
                        </div>
                    </div>
                    <div class="address-field-row" style="grid-template-columns: 1fr;">
                        <div class="address-field">
                            <label>{{ __('address.full_address') }}</label>
                            <textarea wire:model="formFullAddress" placeholder="{{ __('address.full_address_placeholder') }}"></textarea>
                        </div>
                    </div>
                    <div class="address-field-row" style="grid-template-columns: 1fr;">
                        <div class="address-field">
                            <label>{{ __('address.postal_code') }}</label>
                            <input type="text" wire:model="formPostalCode" placeholder="65141">
                        </div>
                    </div>

                    <div class="address-form-actions">
                        <button type="button" class="address-save-btn" wire:click="saveAddress">
                            {{ $editingId ? __('address.update_address') : __('address.save_address') }}
                        </button>
                        <button type="button" class="address-cancel-btn" wire:click="cancelForm">{{ __('address.cancel') }}</button>
                    </div>
                </div>
            @else
                <button type="button" class="add-address-btn" wire:click="openAddForm">
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    {{ __('address.add_new_address') }}
                </button>
            @endif
        </div>

    </div>
</div>