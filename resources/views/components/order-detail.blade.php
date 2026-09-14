<?php

use Livewire\Component;

new class extends Component {
    public string $orderId = '';
    public string $placedOn = '';

    public array $steps = [];

    public array $shippingInfo = [];
    public array $items = [];
    public array $paymentSummary = [];
    public array $address = [];
    public array $payment = [];
    public bool $isCompleted = false;

    public function mount(string $orderId = 'ZA-84719'): void
    {
        $this->orderId = $orderId;

        $realOrder = session('last_order_' . $orderId);

        if ($realOrder) {
            $this->placedOn = \Carbon\Carbon::parse($realOrder['placed_at'])->translatedFormat('j F Y');

            $this->items = array_map(fn($item) => [
                'id' => $item['id'],
                'name' => $item['name'],
                'color' => $item['color'],
                'size' => $item['size'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'image' => $item['image'],
            ], $realOrder['items']);

            $this->paymentSummary = [
                'subtotal' => $realOrder['subtotal'],
                'shipping' => $realOrder['shipping_cost'],
                'tax' => $realOrder['tax'],
                'total_paid' => $realOrder['total'],
            ];

            $address = $realOrder['address'];
            $this->address = [
                'name' => $address['name'] ?? '-',
                'full_address' => ($address['full_address'] ?? '') . ', ' . ($address['city'] ?? ''),
                'phone' => $address['phone'] ?? '-',
                'shipping_method' => $realOrder['shipping_method'] === 'express' ? __('order-detail.express_shipping') : __('order-detail.regular_shipping'),
                'shipping_desc' => $realOrder['shipping_method'] === 'express' ? __('order-detail.express_shipping_desc') : __('order-detail.regular_shipping_desc'),
            ];

            $paymentLabels = [
                'card' => __('order-detail.credit_debit_card'),
                'bank_transfer' => __('order-detail.bank_transfer'),
                'qris' => __('order-detail.qris'),
                'gopay' => __('order-detail.gopay'),
                'ovo' => __('order-detail.ovo'),
                'shopeepay' => __('order-detail.shopeepay'),
            ];

            $this->payment = [
                'method' => $paymentLabels[$realOrder['payment_method']] ?? $realOrder['payment_method'],
                'note' => __('order-detail.paid_via_midtrans'),
            ];

            $this->shippingInfo = [
                'courier' => 'JNE Reguler',
                'resi' => strtoupper(substr(md5($orderId), 0, 14)),
                'status_text' => __('order-detail.shipping_status_text'),
            ];

            $this->steps = [
                ['key' => 'placed', 'label' => __('order-detail.order_placed'), 'time' => \Carbon\Carbon::parse($realOrder['placed_at'])->format('j M, g:i A'), 'status' => 'done'],
                ['key' => 'processing', 'label' => __('order-detail.processing'), 'time' => __('order-detail.processing'), 'status' => 'current'],
                ['key' => 'shipped', 'label' => __('order-detail.shipped'), 'time' => '-', 'status' => 'pending'],
                ['key' => 'delivered', 'label' => __('order-detail.delivered'), 'time' => '-', 'status' => 'pending'],
            ];

            $this->isCompleted = collect($this->steps)->firstWhere('key', 'delivered')['status'] === 'done';

            return;
        }

        // Fallback dummy — dipakai kalau halaman diakses langsung tanpa lewat checkout
        $this->placedOn = '1 September 2026';

        // Dummy data — nanti diganti dengan data order asli dari database
        $this->steps = [
            ['key' => 'placed', 'label' => __('order-detail.order_placed'), 'time' => '1 Sep, 10:42 AM', 'status' => 'done'],
            ['key' => 'processing', 'label' => __('order-detail.processing'), 'time' => '1 Sep, 11:15 AM', 'status' => 'done'],
            ['key' => 'shipped', 'label' => __('order-detail.shipped'), 'time' => '2 Sep, 2:30 PM', 'status' => 'current'],
            ['key' => 'delivered', 'label' => __('order-detail.delivered'), 'time' => __('order-detail.estimated') . ' 5 Sep', 'status' => 'pending'],
        ];

        $this->isCompleted = collect($this->steps)->firstWhere('key', 'delivered')['status'] === 'done';

        $this->shippingInfo = [
            'courier' => 'JNE Reguler',
            'resi' => 'JX02946172538D',
            'status_text' => __('order-detail.shipping_status_text'),
        ];

        $this->items = [
            ['id' => 1, 'name' => 'Kids Flannel Shirt', 'color' => 'Black', 'size' => 'L', 'qty' => 1, 'price' => 125000, 'image' => '/images/products/flannel-shirt.jpg'],
            ['id' => 2, 'name' => 'Kids Flannel Shirt', 'color' => 'Black', 'size' => 'L', 'qty' => 1, 'price' => 125000, 'image' => '/images/products/flannel-1.jpg'],
            ['id' => 3, 'name' => 'Kids Flannel Shirt', 'color' => 'Black', 'size' => 'L', 'qty' => 1, 'price' => 125000, 'image' => '/images/products/flannel-2.jpg'],
            ['id' => 4, 'name' => 'Navy Classic Shirt', 'color' => 'Navy', 'size' => 'L', 'qty' => 1, 'price' => 217000, 'image' => '/images/products/navy-classic.jpg'],
        ];

        $this->paymentSummary = [
            'subtotal' => 592000,
            'shipping' => 0,
            'tax' => 11840,
            'total_paid' => 453000,
        ];

        $this->address = [
            'name' => 'Akleema',
            'full_address' => 'Jl. Kenanga No. 12, RT 04/RW 02, Kelurahan Sukamaju, Kec. Lowokwaru, Malang, Jawa Timur 65141',
            'phone' => '+62 812 3456 7890',
            'shipping_method' => __('order-detail.regular_shipping'),
            'shipping_desc' => __('order-detail.regular_shipping_desc'),
        ];

        $this->payment = [
            'method' => __('order-detail.credit_debit_card'),
            'note' => __('order-detail.paid_via_midtrans'),
        ];
    }

    public function getDoneCountProperty(): int
    {
        return count(array_filter($this->steps, fn($s) => $s['status'] === 'done'));
    }
};
?>

<div class="order-detail-wrap">

    <div class="order-detail-head">
        <h1>{{ __('order-detail.page_title') }}</h1>
        <div class="meta">
            {{ __('order-detail.order') }} <strong>#{{ $orderId }}</strong> · {{ __('order-detail.placed_on') }} {{ $placedOn }}
        </div>
    </div>

    {{-- STATUS STEPPER --}}
    <div class="status-card">
        <div class="status-stepper">
            <div class="status-line">
                <div class="status-line-fill" style="width: {{ $this->doneCount > 0 ? (($this->doneCount - 0.5) / (count($steps) - 1)) * 100 : 0 }}%"></div>
            </div>

            @foreach($steps as $step)
                <div class="status-step {{ $step['status'] }}">
                    <div class="dot">
                        @if($step['status'] === 'done')
                            <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        @elseif($step['key'] === 'shipped')
                            <svg viewBox="0 0 24 24"><rect x="1" y="7" width="15" height="10" rx="1"/><path d="M16 10h3l3 3v4h-6z"/><circle cx="6" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>
                        @else
                            <svg viewBox="0 0 24 24"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                        @endif
                    </div>
                    <div class="label">{{ $step['label'] }}</div>
                    <div class="time">{{ $step['time'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- SHIPPING INFO --}}
    <div class="shipping-info-card">
        <div class="shipping-info-icon">
            <svg viewBox="0 0 24 24"><rect x="1" y="7" width="15" height="10" rx="1"/><path d="M16 10h3l3 3v4h-6z"/><circle cx="6" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>
        </div>
        <div class="shipping-info-body">
            <div class="courier">{{ $shippingInfo['courier'] }}</div>
            <div class="resi">{{ __('order-detail.tracking_no') }}: <span class="val">{{ $shippingInfo['resi'] }}</span></div>
            <div class="status-line-text">
                <span class="green-dot"></span>
                {{ $shippingInfo['status_text'] }}
            </div>
        </div>
    </div>

    <div class="order-detail-layout">

        {{-- LEFT --}}
        <div>
            <div class="order-card">
                <h3>{{ __('order-detail.items_ordered') }}</h3>
                @foreach($items as $item)
                    <div class="order-item-row">
                        <input type="checkbox" checked style="pointer-events:none;">
                        <div class="order-item-thumb">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                        </div>
                        <div class="order-item-body">
                            <div>
                                <h4>{{ $item['name'] }}</h4>
                                <div class="meta">{{ __('order-detail.color') }}: {{ $item['color'] }}</div>
                                <div class="meta">{{ __('order-detail.size') }}: {{ $item['size'] }}</div>
                            </div>
                            <div>
                                <div class="order-item-qty">{{ __('order-detail.qty') }} {{ $item['qty'] }}</div>
                                <div class="order-item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- SHOPPING ADDRESS --}}
            <div class="order-card">
                <h3>{{ __('order-detail.shopping_address') }}</h3>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 21s-7-6.5-7-11a7 7 0 0114 0c0 4.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </div>
                    <div>
                        <div class="title">{{ $address['name'] }}</div>
                        <div class="desc">{{ $address['full_address'] }} · {{ $address['phone'] }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24"><rect x="1" y="7" width="15" height="10" rx="1"/><path d="M16 10h3l3 3v4h-6z"/><circle cx="6" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>
                    </div>
                    <div>
                        <div class="title">{{ $address['shipping_method'] }}</div>
                        <div class="desc">{{ $address['shipping_desc'] }}</div>
                    </div>
                </div>
            </div>

            {{-- PAYMENT --}}
            <div class="order-card">
                <h3>{{ __('order-detail.payment') }}</h3>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div>
                        <div class="title">{{ $payment['method'] }}</div>
                        <div class="desc">{{ $payment['note'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div>
            <div class="order-card payment-summary-card">
                <h3>{{ __('order-detail.payment_summary') }}</h3>
                <div class="summary-row">
                    <span class="label">{{ __('order-detail.subtotal') }}</span>
                    <span class="value">Rp {{ number_format($paymentSummary['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="label">{{ __('order-detail.shipping') }}</span>
                    <span class="value">{{ $paymentSummary['shipping'] > 0 ? 'Rp ' . number_format($paymentSummary['shipping'], 0, ',', '.') : __('order-detail.free') }}</span>
                </div>
                <div class="summary-row">
                    <span class="label">{{ __('order-detail.estimated_taxes') }}</span>
                    <span class="value">Rp {{ number_format($paymentSummary['tax'], 0, ',', '.') }}</span>
                </div>

                <hr class="summary-divider">

                <div class="summary-total-row">
                    <span class="label">{{ __('order-detail.total_paid') }}</span>
                    <span class="value">Rp {{ number_format($paymentSummary['total_paid'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="help-card">
                <div class="chat-icon">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                </div>
                <h4>{{ __('order-detail.trouble_heading') }}</h4>
                <p>{{ __('order-detail.trouble_desc') }}</p>
                <a href="#" class="chat-btn">{{ __('order-detail.chat_with_us') }}</a>
            </div>

            @if($isCompleted)
                <div class="order-card">
                    <h3>{{ __('order-detail.refund_heading') }}</h3>
                    <p class="refund-desc">{{ __('order-detail.refund_desc') }}</p>
                    <a href="{{ route('refund.request', $orderId) }}" class="refund-btn">{{ __('order-detail.request_refund') }}</a>
                </div>
            @endif
        </div>

    </div>
</div>