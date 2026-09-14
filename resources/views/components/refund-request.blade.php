<?php

use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $orderId = '';
    public bool $eligible = false;

    public array $items = [];
    public string $placedOn = '';

    public string $reason = 'damaged';
    public string $additionalDetail = '';
    public array $photos = [];

    public string $refundMethod = 'original';

    public array $reasons = [];
    public array $refundMethods = [];

    public function mount(string $orderId): void
    {
        $this->orderId = $orderId;

        $this->reasons = [
            'damaged' => __('refund-request.reason_damaged'),
            'not_as_described' => __('refund-request.reason_not_as_described'),
            'wrong_item' => __('refund-request.reason_wrong_item'),
            'changed_mind' => __('refund-request.reason_changed_mind'),
            'wrong_size' => __('refund-request.reason_wrong_size'),
            'other' => __('refund-request.reason_other'),
        ];

        $realOrder = session('last_order_' . $orderId);

        if ($realOrder) {
            $status = $realOrder['status'] ?? 'processing';
            $this->eligible = in_array($status, ['completed', 'delivered']);
            $this->placedOn = \Carbon\Carbon::parse($realOrder['placed_at'])->translatedFormat('j M Y');
            $this->items = $realOrder['items'];
            return;
        }

        // Fallback dummy — dipakai kalau halaman diakses langsung tanpa lewat order asli
        $this->eligible = true;
        $this->placedOn = '1 Sep 2026';
        $this->items = [
            ['id' => 1, 'name' => 'Kids Short-Sleeve School Shirt', 'color' => 'Black', 'size' => 'L', 'qty' => 1, 'price' => 125000, 'image' => '/images/products/school-shirt.jpg'],
        ];
    }

    public function getRefundTotalProperty(): int
    {
        return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $this->items));
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function updatedPhotos(): void
    {
        if (count($this->photos) > 5) {
            $this->photos = array_slice($this->photos, 0, 5);
        }
    }

    public bool $showSuccessModal = false;

    public function submitRequest(): void
    {
        $this->validate([
            'reason' => 'required',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:4096',
        ]);

        // NOTE: belum ada tabel refund_requests di database.
        // Untuk sekarang cuma disimpan sementara ke session sebagai bukti alur sudah jalan.
        session(['refund_request_' . $this->orderId => [
            'reason' => $this->reason,
            'reason_label' => $this->reasons[$this->reason] ?? $this->reason,
            'detail' => $this->additionalDetail,
            'refund_method' => $this->refundMethod,
            'photos_count' => count($this->photos),
            'total' => $this->refundTotal,
            'submitted_at' => now()->toDateTimeString(),
        ]]);

        $this->showSuccessModal = true;
    }

    public function backToOrders(): void
    {
        $this->redirect(route('my-orders'));
    }

    public function trackRefundStatus(): void
    {
        $this->redirect(route('refund.status', $this->orderId));
    }
};
?>

<div class="refund-wrap">

    @if(!$eligible)
        <div class="refund-not-eligible">
            <h3>{{ __('refund-request.not_eligible_title') }}</h3>
            <p>{{ __('refund-request.not_eligible_desc') }}</p>
            <a href="{{ route('order.detail', $orderId) }}">{{ __('refund-request.back_to_order') }}</a>
        </div>
    @else
        <div class="refund-head">
            <div class="refund-eyebrow">{{ __('refund-request.eyebrow') }}</div>
            <h1>{{ __('refund-request.page_title') }}</h1>
            <p>{{ __('refund-request.subtitle') }}</p>
        </div>

        @foreach($items as $item)
            <div class="refund-order-card">
                <div class="refund-order-thumb">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                </div>
                <div class="refund-order-info">
                    <div class="meta-top">{{ __('refund-request.order') }} #{{ $orderId }} · {{ $placedOn }}</div>
                    <h4>{{ $item['name'] }}</h4>
                    <div class="meta">{{ $item['color'] }} · {{ __('refund-request.size') }} {{ $item['size'] }} · {{ __('refund-request.qty') }} {{ $item['qty'] }}</div>
                </div>
                <div class="refund-order-price">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</div>
            </div>
        @endforeach

        <div class="refund-card">
            <h3>{{ __('refund-request.reason_heading') }}</h3>
            <div class="sub">{{ __('refund-request.reason_subtitle') }}</div>

            @foreach($reasons as $key => $label)
                <label class="refund-reason-option {{ $reason === $key ? 'active' : '' }}">
                    <input type="radio" wire:model.live="reason" value="{{ $key }}">
                    <span>{{ $label }}</span>
                </label>
            @endforeach

            <div class="refund-field">
                <label>{{ __('refund-request.additional_detail') }}</label>
                <textarea wire:model="additionalDetail" placeholder="{{ __('refund-request.additional_detail_placeholder') }}"></textarea>
            </div>

            <div class="refund-field">
                <label>{{ __('refund-request.upload_photos') }}</label>
                <label class="refund-upload-box">
                    <svg viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                    <div class="title">{{ __('refund-request.upload_title') }}</div>
                    <div class="desc">{{ __('refund-request.upload_desc') }}</div>
                    <input type="file" wire:model="photos" multiple accept="image/png, image/jpeg">
                </label>

                @if(count($photos) > 0)
                    <div class="refund-photo-previews">
                        @foreach($photos as $index => $photo)
                            <div class="refund-photo-thumb">
                                <img src="{{ $photo->temporaryUrl() }}" alt="">
                                <button type="button" wire:click="removePhoto({{ $index }})">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('photos.*') <div class="promo-message" style="color:#B3261E; font-size:11px; margin-top:6px;">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="refund-card">
            <h3>{{ __('refund-request.refund_method_heading') }}</h3>

            <label class="refund-method-option {{ $refundMethod === 'original' ? 'active' : '' }}">
                <input type="radio" wire:model.live="refundMethod" value="original">
                <div class="refund-method-icon">
                    <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <div class="refund-method-text">
                    <div class="title">{{ __('refund-request.refund_original') }}</div>
                    <div class="desc">{{ __('refund-request.refund_original_desc') }}</div>
                </div>
            </label>

            <label class="refund-method-option {{ $refundMethod === 'store_credit' ? 'active' : '' }}">
                <input type="radio" wire:model.live="refundMethod" value="store_credit">
                <div class="refund-method-icon">
                    <svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
                </div>
                <div class="refund-method-text">
                    <div class="title">{{ __('refund-request.refund_store_credit') }}</div>
                    <div class="desc">{{ __('refund-request.refund_store_credit_desc') }}</div>
                </div>
            </label>

            <hr class="refund-summary-divider">

            <div class="refund-summary-row">
                <span>{{ __('refund-request.item_price') }}</span>
                <span class="value">Rp {{ number_format($this->refundTotal, 0, ',', '.') }}</span>
            </div>
            <div class="refund-summary-row">
                <span>{{ __('refund-request.shipping_non_refundable') }}</span>
                <span class="value">Rp 0</span>
            </div>

            <div class="refund-summary-total">
                <span class="label">{{ __('refund-request.total_refund') }}</span>
                <span class="value">Rp {{ number_format($this->refundTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="refund-actions">
            <a href="{{ route('order.detail', $orderId) }}" class="refund-cancel-btn">{{ __('refund-request.cancel') }}</a>
            <button type="button" class="refund-submit-btn" wire:click="submitRequest">{{ __('refund-request.submit_request') }}</button>
        </div>
    @endif

    {{-- REFUND SUBMITTED SUCCESS MODAL --}}
    @if($showSuccessModal)
        <div class="refund-success-overlay">
            <div class="refund-success-box">
                <div class="refund-success-icon">
                    <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                </div>

                <h2>{{ __('refund-request.submitted_title') }}</h2>
                <p>{{ __('refund-request.submitted_desc', ['orderId' => $orderId]) }}</p>

                <div class="refund-order-id-box">
                    <span class="refund-order-id-label">{{ __('refund-request.your_order_id') }}</span>
                    <div class="refund-order-id-row">
                        <span class="refund-order-id-value">#{{ $orderId }}</span>
                        <button type="button" class="refund-copy-btn" onclick="navigator.clipboard.writeText('{{ $orderId }}')" title="{{ __('refund-request.copy') }}">
                            <svg viewBox="0 0 24 24"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
                        </button>
                    </div>
                </div>

                <div class="refund-success-actions">
                    <button type="button" class="refund-cancel-btn" wire:click="backToOrders">{{ __('refund-request.back_to_orders') }}</button>
                    <button type="button" class="refund-submit-btn" wire:click="trackRefundStatus">{{ __('refund-request.track_refund_status') }}</button>
                </div>
            </div>
        </div>
    @endif

</div>