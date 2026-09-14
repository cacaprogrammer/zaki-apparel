<?php

use Livewire\Component;

new class extends Component {
    public array $checkoutItems = [];
    public int $cartTotalCount = 0;

    public string $email = '';
    public string $phone = '';

    public array $addresses = [];
    public $selectedAddressId = null;
    public bool $showAddressForm = false;
    public ?int $editingAddressId = null;

    public string $newName = '';
    public string $newPhone = '';
    public string $newFullAddress = '';
    public string $newCity = '';
    public string $newPostalCode = '';

    public string $shippingMethod = 'regular';
    public string $paymentMethod = 'card';

    public string $promoCode = '';

    public bool $showSuccessModal = false;
    public string $orderId = '';
    public ?string $formError = null;

    public function mount(): void
    {
        $cart = session('cart', []);
        $this->cartTotalCount = count($cart);

        $selectedIds = session('checkout_selected', array_column($cart, 'id'));
        $this->checkoutItems = array_values(array_filter($cart, fn($item) => in_array($item['id'], $selectedIds)));

        if (empty($this->checkoutItems)) {
            $this->redirect(route('cart'));
            return;
        }

        $this->addresses = [
            [
                'id' => 1,
                'name' => 'Akleema',
                'phone' => '+62 812 3456 7890',
                'full_address' => 'Jl. Kenanga No. 12, RT 04/RW 02, Kelurahan Sukamaju, Kec. Lowokwaru',
                'city' => 'Malang, Jawa Timur',
                'postal_code' => '65141',
            ],
        ];
        $this->selectedAddressId = 1;
    }

    public function selectAddress(int $id): void
    {
        $this->selectedAddressId = $id;
    }

    public function openAddForm(): void
    {
        $this->editingAddressId = null;
        $this->newName = '';
        $this->newPhone = '';
        $this->newFullAddress = '';
        $this->newCity = '';
        $this->newPostalCode = '';
        $this->showAddressForm = true;
    }

    public function editAddress(int $id): void
    {
        $address = collect($this->addresses)->firstWhere('id', $id);

        if (!$address) {
            return;
        }

        $this->editingAddressId = $id;
        $this->newName = $address['name'];
        $this->newPhone = $address['phone'];
        $this->newFullAddress = $address['full_address'];
        $this->newCity = $address['city'];
        $this->newPostalCode = $address['postal_code'];
        $this->showAddressForm = true;
    }

    public function cancelForm(): void
    {
        $this->showAddressForm = false;
        $this->editingAddressId = null;
    }

    public function saveAddress(): void
    {
        if (trim($this->newName) === '' || trim($this->newPhone) === '' || trim($this->newFullAddress) === '' || trim($this->newCity) === '' || trim($this->newPostalCode) === '') {
            return;
        }

        if ($this->editingAddressId !== null) {
            foreach ($this->addresses as $i => $address) {
                if ($address['id'] === $this->editingAddressId) {
                    $this->addresses[$i] = [
                        'id' => $this->editingAddressId,
                        'name' => $this->newName,
                        'phone' => $this->newPhone,
                        'full_address' => $this->newFullAddress,
                        'city' => $this->newCity,
                        'postal_code' => $this->newPostalCode,
                    ];
                    break;
                }
            }
            $this->selectedAddressId = $this->editingAddressId;
        } else {
            $newId = count($this->addresses) + 1;

            $this->addresses[] = [
                'id' => $newId,
                'name' => $this->newName,
                'phone' => $this->newPhone,
                'full_address' => $this->newFullAddress,
                'city' => $this->newCity,
                'postal_code' => $this->newPostalCode,
            ];

            $this->selectedAddressId = $newId;
        }

        $this->showAddressForm = false;
        $this->editingAddressId = null;

        $this->newName = '';
        $this->newPhone = '';
        $this->newFullAddress = '';
        $this->newCity = '';
        $this->newPostalCode = '';
    }

    public function selectShipping(string $method): void
    {
        $this->shippingMethod = $method;
    }

    public function selectPayment(string $method): void
    {
        $this->paymentMethod = $method;
    }

    public function placeOrder(): void
    {
        if (trim($this->email) === '' || trim($this->phone) === '' || $this->selectedAddressId === null) {
            return;
        }

        $checkoutIds = array_column($this->checkoutItems, 'id');
        $cart = session('cart', []);
        $cart = array_values(array_filter($cart, fn($item) => !in_array($item['id'], $checkoutIds)));

        $this->orderId = '#ZA-' . random_int(10000, 99999);

        $selectedAddress = collect($this->addresses)->firstWhere('id', $this->selectedAddressId);

        session(['last_order_' . ltrim($this->orderId, '#') => [
            'items' => $this->checkoutItems,
            'address' => $selectedAddress,
            'shipping_method' => $this->shippingMethod,
            'payment_method' => $this->paymentMethod,
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shippingCost,
            'tax' => $this->tax,
            'total' => $this->total,
            'placed_at' => now()->toDateTimeString(),
            'status' => 'processing',
        ]]);

        session(['cart' => $cart]);
        session()->forget('checkout_selected');

        $this->dispatch('cart-updated', count: count($cart));

        $this->showSuccessModal = true;
    }

    public function backToHome(): void
    {
        $this->redirect('/');
    }

    public function trackOrder(): void
    {
        $this->redirect(route('order.detail', ltrim($this->orderId, '#')));
    }

    public function getSubtotalProperty(): int
    {
        return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $this->checkoutItems));
    }

    public function getShippingCostProperty(): int
    {
        return $this->shippingMethod === 'express' ? 25000 : 0;
    }

    public function getTaxProperty(): int
    {
        return count($this->checkoutItems) > 0 ? 10000 : 0;
    }

    public function getTotalProperty(): int
    {
        return $this->subtotal + $this->shippingCost + $this->tax;
    }
};
?>

<div class="checkout-wrap">
    <div class="checkout-layout">

        {{-- LEFT COLUMN --}}
        <div class="checkout-col-left">

            {{-- CONTACT INFORMATION --}}
            <div class="checkout-card">
                <h3>{{ __('checkout.contact_information') }}</h3>
                <div class="field-row">
                    <div class="field">
                        <label>{{ __('checkout.email') }}</label>
                        <input type="email" wire:model="email" placeholder="you@email.com">
                    </div>
                    <div class="field">
                        <label>{{ __('checkout.phone_number') }}</label>
                        <input type="text" wire:model="phone" placeholder="+62 xxx xxxx xxxx">
                    </div>
                </div>
            </div>

            {{-- SHOPPING ADDRESS --}}
            <div class="checkout-card">
                <h3>{{ __('checkout.shopping_address') }}</h3>

                @foreach($addresses as $address)
                    <div class="address-card {{ $selectedAddressId === $address['id'] ? 'active' : '' }}" wire:click="selectAddress({{ $address['id'] }})">
                        <input type="radio" @checked($selectedAddressId === $address['id'])>
                        <div>
                            <div class="name">{{ $address['name'] }}</div>
                            <div class="addr-text">{{ $address['full_address'] }}, {{ $address['city'] }} {{ $address['postal_code'] }} · {{ $address['phone'] }}</div>
                            <button type="button" class="edit-link" wire:click.stop="editAddress({{ $address['id'] }})">{{ __('checkout.edit_address') }}</button>
                        </div>
                    </div>
                @endforeach

                <button type="button" class="add-address-btn" wire:click="openAddForm">
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    {{ __('checkout.add_new_address') }}
                </button>

                @if($showAddressForm)
                    <div class="field-row">
                        <div class="field">
                            <label>{{ __('checkout.full_name') }}</label>
                            <input type="text" wire:model="newName" placeholder="{{ __('checkout.full_name_placeholder') }}">
                        </div>
                        <div class="field">
                            <label>{{ __('checkout.phone_number') }}</label>
                            <input type="text" wire:model="newPhone" placeholder="+62 xxx xxxx xxxx">
                        </div>
                    </div>
                    <div class="field-row" style="grid-template-columns: 1fr;">
                        <div class="field">
                            <label>{{ __('checkout.full_address') }}</label>
                            <textarea wire:model="newFullAddress" placeholder="{{ __('checkout.full_address_placeholder') }}"></textarea>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label>{{ __('checkout.city') }}</label>
                            <input type="text" wire:model="newCity" placeholder="Malang">
                        </div>
                        <div class="field">
                            <label>{{ __('checkout.postal_code') }}</label>
                            <input type="text" wire:model="newPostalCode" placeholder="65141">
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; align-items:center;">
                        <button type="button" class="save-address-btn" wire:click="saveAddress">
                            {{ $editingAddressId ? __('checkout.update_address') : __('checkout.save_address') }}
                        </button>
                        <button type="button" class="cancel-address-btn" wire:click="cancelForm">{{ __('checkout.cancel') }}</button>
                    </div>
                @endif
            </div>

            {{-- PAYMENT METHOD (SHIPPING) --}}
            <div class="checkout-card">
                <h3>{{ __('checkout.payment_method') }}</h3>

                <div class="shipping-option {{ $shippingMethod === 'regular' ? 'active' : '' }}" wire:click="selectShipping('regular')">
                    <input type="radio" @checked($shippingMethod === 'regular')>
                    <div class="shipping-icon">
                        <svg viewBox="0 0 24 24"><rect x="1" y="7" width="15" height="10" rx="1"/><path d="M16 10h3l3 3v4h-6z"/><circle cx="6" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>
                    </div>
                    <div class="shipping-text">
                        <div class="title">{{ __('checkout.regular_shipping') }}</div>
                        <div class="desc">{{ __('checkout.regular_shipping_desc') }}</div>
                    </div>
                    <div class="shipping-price free">{{ __('checkout.free') }}</div>
                </div>

                <div class="shipping-option {{ $shippingMethod === 'express' ? 'active' : '' }}" wire:click="selectShipping('express')">
                    <input type="radio" @checked($shippingMethod === 'express')>
                    <div class="shipping-icon">
                        <svg viewBox="0 0 24 24"><path d="M13 2 3 14h7l-1 8 10-12h-7z"/></svg>
                    </div>
                    <div class="shipping-text">
                        <div class="title">{{ __('checkout.express_shipping') }}</div>
                        <div class="desc">{{ __('checkout.express_shipping_desc') }}</div>
                    </div>
                    <div class="shipping-price">Rp 25.000</div>
                </div>
            </div>

            {{-- PAYMENT --}}
            <div class="checkout-card">
                <h3>{{ __('checkout.payment') }}</h3>
                <div class="payment-note">
                    <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                    {{ __('checkout.payment_note') }}
                </div>

                <div class="payment-grid">
                    @foreach([
                        'card' => ['label' => __('checkout.credit_debit_card'), 'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>'],
                        'bank_transfer' => ['label' => __('checkout.bank_transfer'), 'icon' => '<path d="M3 10h18M5 10v9M9 10v9M15 10v9M19 10v9M2 10l10-6 10 6M3 19h18"/>'],
                        'qris' => ['label' => __('checkout.qris'), 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>'],
                        'gopay' => ['label' => __('checkout.gopay'), 'icon' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>'],
                        'ovo' => ['label' => __('checkout.ovo'), 'icon' => '<circle cx="12" cy="12" r="9"/>'],
                        'shopeepay' => ['label' => __('checkout.shopeepay'), 'icon' => '<path d="M6 2 3 7v13a1 1 0 001 1h16a1 1 0 001-1V7l-3-5z"/><path d="M3 7h18"/>'],
                    ] as $key => $method)
                        <div class="payment-option {{ $paymentMethod === $key ? 'active' : '' }}" wire:click="selectPayment('{{ $key }}')">
                            <div class="payment-option-icon">
                                <svg viewBox="0 0 24 24">{!! $method['icon'] !!}</svg>
                            </div>
                            <span>{{ $method['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ORDER SUMMARY --}}
        <div class="order-summary">
            <h3>{{ __('checkout.order_summary') }}</h3>
            <div class="sub">{{ __('checkout.items_selected', ['selected' => count($checkoutItems), 'total' => $cartTotalCount]) }}</div>

            <hr class="summary-divider">

            <div class="summary-row">
                <span class="label">{{ __('checkout.subtotal') }}</span>
                <span class="value">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">{{ __('checkout.shipping') }}</span>
                <span class="value">{{ $this->shippingCost > 0 ? 'Rp ' . number_format($this->shippingCost, 0, ',', '.') : __('checkout.free') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">{{ __('checkout.estimated_taxes') }}</span>
                <span class="value">Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
            </div>

            <div class="promo-row">
                <input type="text" wire:model="promoCode" placeholder="{{ __('checkout.promo_placeholder') }}">
                <button type="button">{{ __('checkout.apply') }}</button>
            </div>

            <hr class="summary-divider">

            <div class="summary-total-row">
                <span class="label">{{ __('checkout.total') }}</span>
                <span class="value">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </div>

            <button class="checkout-btn" wire:click="placeOrder">
                {{ __('checkout.checkout') }} ({{ count($checkoutItems) }})
            </button>

            <div class="secure-note">
                <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                {{ __('checkout.secure_payment') }}
            </div>
        </div>

    </div>

    {{-- PAYMENT SUCCESS MODAL --}}
    @if($showSuccessModal)
        <div class="success-modal-overlay">
            <div class="success-modal-box">
                <div class="success-illustration">
                    <svg viewBox="0 0 120 100" width="120" height="100">
                        <rect x="30" y="10" width="46" height="72" rx="10" fill="var(--ink)"/>
                        <rect x="37" y="18" width="32" height="48" rx="3" fill="#fff" opacity="0.15"/>
                        <circle cx="53" cy="70" r="3" fill="#fff" opacity="0.4"/>
                        <rect x="50" y="24" width="52" height="36" rx="8" fill="#fff" stroke="var(--line)" stroke-width="1.5"/>
                        <line x1="58" y1="34" x2="86" y2="34" stroke="var(--line)" stroke-width="2"/>
                        <line x1="58" y1="42" x2="78" y2="42" stroke="var(--line)" stroke-width="2"/>
                        <circle cx="90" cy="50" r="11" fill="var(--gold)"/>
                        <path d="M85 50l3.2 3.5L96 45" stroke="#fff" stroke-width="2.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 24l2 5 5 2-5 2-2 5-2-5-5-2 5-2z" fill="var(--gold)"/>
                        <path d="M104 70l1.4 3.4 3.4 1.4-3.4 1.4-1.4 3.4-1.4-3.4-3.4-1.4 3.4-1.4z" fill="var(--gold)"/>
                    </svg>
                </div>

                <h2>{{ __('checkout.payment_successful') }}</h2>
                <p class="success-desc">{{ __('checkout.payment_success_desc') }}</p>

                <div class="order-id-box">
                    <span class="order-id-label">{{ __('checkout.your_order_id') }}</span>
                    <div class="order-id-row">
                        <span class="order-id-value">{{ $orderId }}</span>
                        <button type="button" class="copy-btn" onclick="navigator.clipboard.writeText('{{ $orderId }}')" title="{{ __('checkout.copy') }}">
                            <svg viewBox="0 0 24 24"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
                        </button>
                    </div>
                </div>

                <div class="success-actions">
                    <button type="button" class="btn-back-home" wire:click="backToHome">{{ __('checkout.back_main_page') }}</button>
                    <button type="button" class="btn-track-order" wire:click="trackOrder">{{ __('checkout.track_your_order') }}</button>
                </div>
            </div>
        </div>
    @endif

</div>