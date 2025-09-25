<x-layouts.app :title="__('Schedule')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl">Class Schedule</flux:heading>
                <flux:text class="mt-2">View your children's class schedules</flux:text>
            </div>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Schedule Information</flux:heading>
                </div>
                <div class="p-6">
                    <flux:text class="text-gray-500">No schedule information available yet.</flux:text>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
