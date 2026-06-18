@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Smart Coffee CRM" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md border-2 border-black bg-amber-400 shadow-[2px_2px_0px_#000]">
            <span class="text-lg">☕</span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Smart Coffee CRM" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md border-2 border-black bg-amber-400 shadow-[2px_2px_0px_#000]">
            <span class="text-lg">☕</span>
        </x-slot>
    </flux:brand>
@endif
