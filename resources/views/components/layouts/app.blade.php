<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
           <!-- Footer -->
    <x-footer />
    </flux:main>
    
 
</x-layouts.app.sidebar>