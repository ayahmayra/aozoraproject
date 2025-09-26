<x-layouts.app :title="__('Time Schedule Details')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('admin.time-schedules.index') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Time Schedules
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Time Schedule Details</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Schedule information for {{ $timeSchedule->subject->name }}</flux:text>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <!-- Schedule Information Card -->
            <flux:card class="mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Schedule Information</flux:heading>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:text class="text-sm font-medium text-gray-500">Subject</flux:text>
                        <div class="mt-1">
                            <flux:badge variant="blue" size="md">{{ $timeSchedule->subject->name }}</flux:badge>
                            <flux:text class="text-sm text-gray-600 mt-1">{{ $timeSchedule->subject->code }}</flux:text>
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-gray-500">Teacher</flux:text>
                        <div class="mt-1">
                            @if($timeSchedule->teacher)
                                <flux:text class="text-lg font-medium">{{ $timeSchedule->teacher->user->name }}</flux:text>
                                <flux:text class="text-sm text-gray-600">{{ $timeSchedule->teacher->user->email }}</flux:text>
                            @else
                                <flux:text class="text-sm text-gray-500">No teacher assigned</flux:text>
                            @endif
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-gray-500">Day of Week</flux:text>
                        <div class="mt-1">
                            <flux:badge variant="green" size="md">{{ $timeSchedule->day_of_week }}</flux:badge>
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-gray-500">Time</flux:text>
                        <div class="mt-1">
                            <flux:text class="text-lg font-mono">{{ $timeSchedule->start_time->format('H:i') }} - {{ $timeSchedule->end_time->format('H:i') }}</flux:text>
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-sm font-medium text-gray-500">Room</flux:text>
                        <div class="mt-1">
                            <flux:text class="text-lg">{{ $timeSchedule->room ?? 'Not assigned' }}</flux:text>
                        </div>
                    </div>
                    @if($timeSchedule->notes)
                        <div class="md:col-span-2">
                            <flux:text class="text-sm font-medium text-gray-500">Notes</flux:text>
                            <div class="mt-1">
                                <flux:text class="text-lg">{{ $timeSchedule->notes }}</flux:text>
                            </div>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Subject Information -->
            <flux:card class="mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Subject Details</flux:heading>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-500">Subject Name</flux:text>
                            <flux:text class="text-lg">{{ $timeSchedule->subject->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-500">Subject Code</flux:text>
                            <flux:badge variant="primary" size="md">{{ $timeSchedule->subject->code }}</flux:badge>
                        </div>
                        @if($timeSchedule->subject->description)
                            <div class="md:col-span-2">
                                <flux:text class="text-sm font-medium text-gray-500">Description</flux:text>
                                <flux:text class="text-lg">{{ $timeSchedule->subject->description }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>
            </flux:card>

            <!-- Teacher Information -->
            @if($timeSchedule->teacher)
                <flux:card>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Teacher Details</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:text class="text-sm font-medium text-gray-500">Teacher Name</flux:text>
                                <flux:text class="text-lg">{{ $timeSchedule->teacher->user->name }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-500">Email</flux:text>
                                <flux:text class="text-lg">{{ $timeSchedule->teacher->user->email }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-500">Employee Number</flux:text>
                                <flux:text class="text-lg">{{ $timeSchedule->teacher->employee_number ?? 'Not assigned' }}</flux:text>
                            </div>
                            <div>
                                <flux:text class="text-sm font-medium text-gray-500">Employment Status</flux:text>
                                <flux:badge 
                                    size="sm" 
                                    color="{{ $timeSchedule->teacher->employment_status === 'active' ? 'green' : 'red' }}"
                                >
                                    {{ ucfirst($timeSchedule->teacher->employment_status ?? 'Active') }}
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </flux:card>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <flux:card class="mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Quick Actions</flux:heading>
                </div>
                <div class="p-6 space-y-3">
                    <flux:button variant="secondary" href="{{ route('admin.time-schedules.edit', $timeSchedule) }}" class="w-full">
                        <flux:icon.pencil class="h-4 w-4 mr-2" />
                        Edit Schedule
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('admin.time-schedules.index') }}" class="w-full">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Schedules
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Schedule Statistics</flux:heading>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <flux:text class="text-sm">Duration</flux:text>
                        <flux:badge size="sm" variant="blue">
                            {{ $timeSchedule->start_time->diffInMinutes($timeSchedule->end_time) }} minutes
                        </flux:badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <flux:text class="text-sm">Created</flux:text>
                        <flux:text class="text-sm">{{ $timeSchedule->created_at->format('M d, Y') }}</flux:text>
                    </div>
                    <div class="flex justify-between items-center">
                        <flux:text class="text-sm">Last Updated</flux:text>
                        <flux:text class="text-sm">{{ $timeSchedule->updated_at->format('M d, Y') }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Danger Zone</flux:heading>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.time-schedules.destroy', $timeSchedule) }}" onsubmit="return confirm('Are you sure you want to delete this schedule? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <flux:button variant="danger" type="submit" class="w-full">
                            <flux:icon.trash class="h-4 w-4 mr-2" />
                            Delete Schedule
                        </flux:button>
                    </form>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
