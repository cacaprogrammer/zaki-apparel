<?php

use Livewire\Component;

new class extends Component {
    public array $cartItems = [];
    public array $selected = [];

    public string $promoCode = '';
    public ?string $promoMessage = null;

    public array $suggestedProducts = [];

    public function mount(): void
    {
        $this->cartItems = session('cart', []);

        foreach ($this->cartItems as $item) {
            $this->selected[$item['id']] = true;
        }

        $this->suggestedProducts = [
            ['slug' => 'sky-blue-kids-1', 'name' => 'Kemeja Sky Blue Kids', 'category' => 'Flannel Shirt', 'price' => 149000, 'image' => '/images/products/flannel-shirt.jpg', 'badge' => 'New'],
            ['slug' => 'sky-blue-kids-2', 'name' => 'Kemeja Sky Blue Kids', 'category' => 'Flannel Shirt', 'price' => 149000, 'image' => '/images/products/flannel-1.jpg', 'badge' => 'New'],
            ['slug' => 'sky-blue-kids-3', 'name' => 'Kemeja Sky Blue Kids', 'category' => 'Flannel Shirt', 'price' => 149000, 'image' => '/images/products/flannel-2.jpg', 'badge' => 'New'],
            ['slug' => 'sky-blue-kids-4', 'name' => 'Kemeja Sky Blue Kids', 'category' => 'Flannel Shirt', 'price' => 149000, 'image' => '/images/products/flannel-3.jpg', 'badge' => 'New'],
        ];
    }

    protected function syncSession(): void
    {
        session(['cart' => $this->cartItems]);
        $this->dispatch('cart-updated', count: count($this->cartItems));
    }

    public function toggleSelect(string $id): void
    {
        $this->selected[$id] = !($this->selected[$id] ?? false);
    }

    public function incrementQty(string $id): void
    {
        foreach ($this->cartItems as $i => $item) {
            if ($item['id'] === $id) {
                $this->cartItems[$i]['qty']++;
                break;
            }
        }
        $this->syncSession();
    }

    public function decrementQty(string $id): void
    {
        foreach ($this->cartItems as $i => $item) {
            if ($item['id'] === $id && $item['qty'] > 1) {
                $this->cartItems[$i]['qty']--;
                break;
            }
        }
        $this->syncSession();
    }

    public function removeItem(string $id): void
    {
        $this->cartItems = array_values(array_filter($this->cartItems, fn($item) => $item['id'] !== $id));
        unset($this->selected[$id]);
        $this->syncSession();
    }

    public function applyPromo(): void
    {
        if (trim($this->promoCode) === '') {
            $this->promoMessage = null;
            return;
        }

        $this->promoMessage = __('cart.promo_invalid');
    }

    public function checkout(): void
    {
        if ($this->selectedCount === 0) {
            return;
        }

        session(['checkout_selected' => array_column($this->selectedItems, 'id')]);

        $this->redirect('/checkout');
    }

    public function getSelectedItemsProperty(): array
    {
        return array_values(array_filter(
            $this->cartItems,
            fn($item) => $this->selected[$item['id']] ?? false
        ));
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->selectedItems);
    }

    public function getSubtotalProperty(): int
    {
        return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $this->selectedItems));
    }

    public function getTaxProperty(): int
    {
        return $this->selectedCount > 0 ? 10000 : 0;
    }

    public function getTotalProperty(): int
    {
        return $this->subtotal + $this->tax;
    }
};
?>

<div class="cart-wrap">

    <div class="cart-page-head">
        <h1>{{ __('cart.page_title') }}</h1>
        <span class="cart-count">{{ __('cart.items_in_cart', ['count' => count($cartItems)]) }}</span>
    </div>

    <div class="cart-layout">

        {{-- CART ITEMS --}}
        <div>
            @if(count($cartItems) > 0)
                <div class="cart-list-head">
                    <span></span>
                    <span></span>
                    <span>{{ __('cart.product') }}</span>
                    <span>{{ __('cart.price') }}</span>
                    <span>{{ __('cart.quantity') }}</span>
                    <span>{{ __('cart.subtotal') }}</span>
                    <span></span>
                </div>

                @foreach($cartItems as $item)
                    <div class="cart-item-row" wire:key="cart-{{ $item['id'] }}">
                        <input type="checkbox" wire:click="toggleSelect('{{ $item['id'] }}')" @checked($selected[$item['id']] ?? false)>

                        <div class="cart-item-thumb">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                        </div>

                        <div class="cart-item-info">
                            <h4>{{ $item['name'] }}</h4>
                            <div class="meta">{{ __('cart.color') }}: {{ $item['color'] }}</div>
                            <div class="meta">{{ __('cart.size') }}: {{ $item['size'] }}</div>
                        </div>

                        <div class="cart-item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>

                        <div class="qty-stepper">
                            <button wire:click="decrementQty('{{ $item['id'] }}')">−</button>
                            <span>{{ $item['qty'] }}</span>
                            <button wire:click="incrementQty('{{ $item['id'] }}')">+</button>
                        </div>

                        <div class="cart-item-subtotal">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</div>

                        <button class="cart-item-remove" wire:click="removeItem('{{ $item['id'] }}')" title="{{ __('cart.remove') }}">
                            <svg viewBox="0 0 24 24"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14z"/></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <div class="cart-empty">{{ __('cart.empty') }}</div>
            @endif

            <a href="{{ route('category') }}" class="continue-shopping">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                {{ __('cart.continue_shopping') }}
            </a>
        </div>

        {{-- ORDER SUMMARY --}}
        <div class="order-summary">
            <h3>{{ __('cart.order_summary') }}</h3>
            <div class="sub">{{ __('cart.items_selected', ['selected' => $this->selectedCount, 'total' => count($cartItems)]) }}</div>

            <hr class="summary-divider">

            <div class="summary-row">
                <span class="label">{{ __('cart.subtotal') }}</span>
                <span class="value">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">{{ __('cart.shipping') }}</span>
                <span class="value">{{ __('cart.free') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">{{ __('cart.estimated_taxes') }}</span>
                <span class="value">Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
            </div>

            <div class="promo-row">
                <input type="text" wire:model="promoCode" placeholder="{{ __('cart.promo_placeholder') }}">
                <button wire:click="applyPromo">{{ __('cart.apply') }}</button>
            </div>
            @if($promoMessage)
                <div class="promo-message">{{ $promoMessage }}</div>
            @endif

            <hr class="summary-divider">

            <div class="summary-total-row">
                <span class="label">{{ __('cart.total') }}</span>
                <span class="value">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </div>

            <button class="checkout-btn" wire:click="checkout" @disabled($this->selectedCount === 0)>
                {{ __('cart.checkout') }} ({{ count($cartItems) }})
            </button>

            <div class="secure-note">
                <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/></svg>
                {{ __('cart.secure_payment') }}
            </div>
        </div>

    </div>

    {{-- YOU MAY ALSO LIKE --}}
    <div class="also-like-section">
        <div class="also-like-head">
            <h2>{{ __('cart.you_may_also_like') }}</h2>
            <a href="{{ route('category') }}" class="view-all-link">
                {{ __('cart.view_all') }}
                <svg viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="product-grid">
            @foreach($suggestedProducts as $product)
                <div class="product-card">
                    <div class="img-wrap">
                        @if($product['badge'])
                            <span class="product-badge">{{ $product['badge'] }}</span>
                        @endif
                        <button class="product-fav" title="{{ __('cart.add_to_wishlist') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.8 8.6c0-3.1-2.5-5.4-5.4-5.4-1.7 0-3.3.9-4.3 2.3-1-1.4-2.6-2.3-4.3-2.3-2.9 0-5.4 2.3-5.4 5.4 0 6 9.7 11.4 9.7 11.4s9.7-5.4 9.7-11.4z"/>
                            </svg>
                        </button>
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                    </div>
                    <div class="product-info">
                        <h4>{{ $product['name'] }}</h4>
                        <div class="price">Rp {{ number_format($product['price'], 0, ',', '.') }}</div>
                        <a href="/product/{{ $product['slug'] }}" class="product-view-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                            {{ __('cart.view_product') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>