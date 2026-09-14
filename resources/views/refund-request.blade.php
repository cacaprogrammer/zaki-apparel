<x-layouts.user :title="__('refund-request.page_title') . ' — Zaki Apparel'">
    @vite(['resources/css/landing.css', 'resources/css/refund-request.css'])

    <livewire:refund-request :order-id="$orderId" />
</x-layouts.user>