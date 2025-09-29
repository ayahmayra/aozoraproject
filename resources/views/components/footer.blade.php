<footer class=" ">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{--  --}}

        <!-- Bottom Section -->
        <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    © {{ date('Y') }} {{ \App\Models\Organization::first()->name ?? 'Aozora Education' }}. All rights reserved.
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">
                        Developed by <span class="font-semibold text-blue-600 dark:text-blue-400">hermanspace.id</span>
                    </span>
                    <div class="flex items-center text-sm text-zinc-600 dark:text-zinc-400">
                        <flux:icon.heart class="h-4 w-4 text-red-500 mr-1" />
                        Made with love
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
