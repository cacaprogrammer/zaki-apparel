<?php

use Livewire\Component;

new class extends Component {
    public string $orderId = '';
    public string $submittedOn = '';

    public array $steps = [];
    public array $item = [];
    public array $refundDetails = [];
    public array $summary = [];
    public ?string $reasonLabel = null;
    public ?string $reasonDetail = null;
    public int $photosCount = 0;

    public function mount(string $orderId): void
    {
        $this->orderId = $orderId;

        $refundRequest = session('refund_request_' . $orderId);
        $order = session('last_order_' . $orderId);

        if ($refundRequest) {
            $submittedAt = \Carbon\Carbon::parse($refundRequest['submitted_at']);
            $this->submittedOn = $submittedAt->translatedFormat('j F Y');

            $this->reasonLabel = $refundRequest['reason_label'] ?? null;
            $this->reasonDetail = $refundRequest['detail'] ?: null;
            $this->photosCount = $refundRequest['photos_count'] ?? 0;

            $this->steps = [
                ['key' => 'submitted', 'label' => __('refund-status.request_submitted'), 'time' => $submittedAt->format('j M, g:i A'), 'status' => 'done'],
                ['key' => 'review', 'label' => __('refund-status.under_review'), 'time' => $submittedAt->addHours(2)->format('j M, g:i A'), 'status' => 'current'],
                ['key' => 'approved', 'label' => __('refund-status.approved'), 'time' => __('refund-status.estimated') . ' ' . $submittedAt->copy()->addDays(4)->format('j M'), 'status' => 'pending'],
                ['key' => 'refunded', 'label' => __('refund-status.refunded'), 'time' => __('refund-status.estimated') . ' ' . $submittedAt->copy()->addDays(7)->format('j M'), 'status' => 'pending'],
            ];

            $firstItem = $order['items'][0] ?? null;
            $this->item = $firstItem ? [
                'name' => $firstItem['name'],
                'color' => $firstItem['color'],
                'size' => $firstItem['size'],
                'qty' => $firstItem['qty'],
                'price' => $firstItem['price'] * $firstItem['qty'],
                'image' => $firstItem['image'],
            ] : [
                'name' => '-', 'color' => '-', 'size' => '-', 'qty' => 1, 'price' => $refundRequest['total'], 'image' => '',
            ];

            $this->refundDetails = [
                'method' => $refundRequest['refund_method'] === 'store_credit' ? __('refund-status.store_credit') : __('refund-status.original_payment_method'),
                'submitted_on' => $submittedAt->format('j F Y, H:i'),
                'processing_time' => __('refund-status.processing_time_value'),
            ];

            $this->summary = [
                'item_price' => $refundRequest['total'],
                'shipping' => 0,
                'total' => $refundRequest['total'],
            ];

            return;
        }

        // Fallback dummy — dipakai kalau halaman diakses langsung tanpa lewat pengajuan refund asli
        $this->submittedOn = '2 September 2026';
        $this->reasonLabel = __('refund-status.dummy_reason');
        $this->reasonDetail = __('refund-status.dummy_reason_detail');
        $this->photosCount = 2;

        $this->steps = [
            ['key' => 'submitted', 'label' => __('refund-status.request_submitted'), 'time' => '1 Sep, 10:42 AM', 'status' => 'done'],
            ['key' => 'review', 'label' => __('refund-status.under_review'), 'time' => '2 Sep, 9:15 AM', 'status' => 'current'],
            ['key' => 'approved', 'label' => __('refund-status.approved'), 'time' => __('refund-status.estimated') . ' 4 Sep', 'status' => 'pending'],
            ['key' => 'refunded', 'label' => __('refund-status.refunded'), 'time' => __('refund-status.estimated') . ' 7 Sep', 'status' => 'pending'],
        ];

        $this->item = [
            'name' => 'Kids Flannel Shirt', 'color' => 'Black', 'size' => 'L', 'qty' => 1,
            'price' => 125000, 'image' => '/images/products/flannel-shirt.jpg',
        ];

        $this->refundDetails = [
            'method' => __('refund-status.original_payment_method'),
            'submitted_on' => '2 September 2026, 9:12 AM',
            'processing_time' => __('refund-status.processing_time_value'),
        ];

        $this->summary = ['item_price' => 125000, 'shipping' => 0, 'total' => 125000];
    }

    public function getDoneCountProperty(): int
    {
        return count(array_filter($this->steps, fn($s) => $s['status'] === 'done'));
    }
};
?>

<div class="refund-status-wrap">

    <div class="refund-status-head">
        <h1>{{ __('refund-status.page_title') }}</h1>
        <div class="meta">
            {{ __('refund-status.order') }} <strong>#{{ $orderId }}</strong> · {{ __('refund-status.submitted_on') }} {{ $submittedOn }}
        </div>
    </div>

    {{-- STEPPER --}}
    <div class="rs-status-card">
        <div class="rs-stepper">
            <div class="rs-line">
                <div class="rs-line-fill" style="width: {{ $this->doneCount > 0 ? (($this->doneCount - 0.5) / (count($steps) - 1)) * 100 : 0 }}%"></div>
            </div>

            @foreach($steps as $step)
                <div class="rs-step {{ $step['status'] }}">
                    <div class="dot">
                        @if($step['status'] === 'done')
                            <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        @elseif($step['key'] === 'review')
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        @elseif($step['key'] === 'approved')
                            <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                        @else
                            <svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M3 11h18M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        @endif
                    </div>
                    <div class="label">{{ $step['label'] }}</div>
                    <div class="time">{{ $step['time'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="refund-status-layout">

        {{-- LEFT --}}
        <div>
            <div class="rs-card">
                <h3>{{ __('refund-status.item_under_refund') }}</h3>
                <div class="rs-item-row">
                    <div class="rs-item-thumb">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                    </div>
                    <div class="rs-item-body">
                        <div>
                            <h4>{{ $item['name'] }}</h4>
                            <div class="meta">{{ __('refund-status.color') }}: {{ $item['color'] }}</div>
                            <div class="meta">{{ __('refund-status.size') }}: {{ $item['size'] }}</div>
                        </div>
                        <div>
                            <div class="rs-item-qty">{{ __('refund-status.qty') }} {{ $item['qty'] }}</div>
                            <div class="rs-item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                @if($reasonLabel)
                    <div class="rs-reason-box">
                        <div class="reason-title">{{ $reasonLabel }}</div>
                        @if($reasonDetail)
                            <div class="reason-detail">"{{ $reasonDetail }}"</div>
                        @endif
                    </div>
                @endif

                @if($photosCount > 0)
                    <div class="rs-photo-row">
                        @for($i = 0; $i < $photosCount; $i++)
                            <div class="rs-photo-placeholder">
                                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </div>
                        @endfor
                    </div>
                @endif
            </div>

            <div class="rs-card">
                <h3>{{ __('refund-status.refund_details') }}</h3>
                <div class="rs-detail-row">
                    <span class="label">{{ __('refund-status.refund_method') }}</span>
                    <span class="value">{{ $refundDetails['method'] }}</span>
                </div>
                <div class="rs-detail-row">
                    <span class="label">{{ __('refund-status.submitted_on') }}</span>
                    <span class="value">{{ $refundDetails['submitted_on'] }}</span>
                </div>
                <div class="rs-detail-row">
                    <span class="label">{{ __('refund-status.estimated_processing_time') }}</span>
                    <span class="value">{{ $refundDetails['processing_time'] }}</span>
                </div>
            </div>

            <div class="rs-card">
                <h3>{{ __('refund-status.response_from_team') }}</h3>
                <div class="rs-response-box">{{ __('refund-status.no_response_yet') }}</div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div>
            <div class="rs-card">
                <h3>{{ __('refund-status.refund_summary') }}</h3>
                <div class="rs-summary-row">
                    <span class="label">{{ __('refund-status.item_price') }}</span>
                    <span class="value">Rp {{ number_format($summary['item_price'], 0, ',', '.') }}</span>
                </div>
                <div class="rs-summary-row">
                    <span class="label">{{ __('refund-status.shipping') }}</span>
                    <span class="value">{{ __('refund-status.free') }}</span>
                </div>

                <hr class="rs-summary-divider">

                <div class="rs-summary-total-row">
                    <span class="label">{{ __('refund-status.total_paid') }}</span>
                    <span class="value">Rp {{ number_format($summary['total'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="rs-help-card">
                <div class="chat-icon">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                </div>
                <h4>{{ __('refund-status.have_questions') }}</h4>
                <p>{{ __('refund-status.have_questions_desc') }}</p>
                <a href="{{ route('chat') }}" class="chat-btn">{{ __('refund-status.chat_with_us') }}</a>
            </div>
        </div>

    </div>
</div>