<x-layouts.user :title="__('order-detail.page_title') . ' — Zaki Apparel'">
    @vite(['resources/css/landing.css', 'resources/css/order-detail.css'])

    <livewire:order-detail :order-id="$orderId" />
</x-layouts.user>