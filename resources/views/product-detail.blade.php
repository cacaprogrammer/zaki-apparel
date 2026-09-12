<x-layouts.user :title="__('product.page_title')">
    @vite('resources/css/product.css')

    <livewire:product.detail :slug="$slug" />
</x-layouts.user>