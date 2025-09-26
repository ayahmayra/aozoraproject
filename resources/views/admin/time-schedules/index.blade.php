<x-layouts.app :title="__('Time Schedules Management')">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Time Schedules Management</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Manage class schedules and time slots for subjects</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <!-- Search and Filter Form -->
    <flux:card class="mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <flux:heading size="lg">Search & Filter</flux:heading>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <flux:input
                    name="search"
                    label="Search Schedules"
                    placeholder="Search by subject, teacher, or room..."
                    value="{{ request('search') }}"
                />
                
                <flux:field>
                    <flux:label>Day of Week</flux:label>
                    <flux:select name="day">
                        <option value="">All Days</option>
                        @foreach($days as $key => $value)
                            <option value="{{ $key }}" {{ request('day') === $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                
                <div class="flex items-end space-x-3">
                    <flux:button variant="primary" type="submit">
                        <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                        Search
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('admin.time-schedules.index') }}">
                        Clear
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>

    <!-- Time Schedules Table -->
    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">Time Schedules ({{ $schedules->count() }})</flux:heading>
                <flux:button variant="primary" href="{{ route('admin.time-schedules.create') }}">
                    <flux:icon.plus class="h-4 w-4 mr-2" />
                    Add Schedule
                </flux:button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Subject</flux:table.column>
                    <flux:table.column>Teacher</flux:table.column>
                    <flux:table.column>Day</flux:table.column>
                    <flux:table.column>Time</flux:table.column>
                    <flux:table.column>Room</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($schedules as $schedule)
                        <flux:table.row>
                            <flux:table.cell>
                                <div>
                                    <div class="font-medium">{{ $schedule->subject->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $schedule->subject->code }}</div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div>
                                    @if($schedule->teacher)
                                        <div class="font-medium">{{ $schedule->teacher->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $schedule->teacher->user->email }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">No teacher assigned</div>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="blue" size="sm">{{ $schedule->day_of_week }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-mono text-sm">
                                    {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm">
                                    {{ $schedule->room ?? 'Not assigned' }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex space-x-2">
                                    <flux:button variant="ghost" size="sm" href="{{ route('admin.time-schedules.show', $schedule) }}" title="View Schedule">
                                        <flux:icon.eye class="h-4 w-4" />
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" href="{{ route('admin.time-schedules.edit', $schedule) }}" title="Edit Schedule">
                                        <flux:icon.pencil class="h-4 w-4" />
                                    </flux:button>
                                    <form method="POST" action="{{ route('admin.time-schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button variant="ghost" size="sm" type="submit" title="Delete Schedule">
                                            <flux:icon.trash class="h-4 w-4" />
                                        </flux:button>
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8">
                                <div class="text-gray-500">
                                    <flux:icon.clock class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                    <p class="text-lg font-medium">No schedules found</p>
                                    <p class="text-sm">Get started by adding your first time schedule</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>
</x-layouts.app>
