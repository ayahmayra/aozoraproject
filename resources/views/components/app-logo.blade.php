@php
    $organization = \App\Models\Organization::first();
@endphp

@if($organization && $organization->logo)
    <div class="flex aspect-square size-8 items-center justify-center rounded-md overflow-hidden">
        <img src="{{ Storage::url($organization->logo) }}" 
             alt="{{ $organization->name }}" 
             class="h-full w-full object-contain" />
    </div>
@else
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
    </div>
@endif
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">{{ $organization->name ?? 'Aozora Education' }}</span>
</div>
