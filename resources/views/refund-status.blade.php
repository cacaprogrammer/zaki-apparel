<x-layouts.user :title="__('refund-status.page_title') . ' — Zaki Apparel'">
    @vite(['resources/css/landing.css', 'resources/css/refund-status.css'])

    <livewire:refund-status :order-id="$orderId" />
</x-layouts.user>