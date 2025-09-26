<x-layouts.app :title="__('Subjects Management')">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Subjects Management</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Manage all subjects in the system</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <!-- Search Form -->
    <flux:card class="mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.subjects') }}" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <flux:input
                    name="search"
                    label="Search Subjects"
                    placeholder="Search by name, code, or description..."
                    value="{{ request('search') }}"
                />
                <div class="flex items-end space-x-3">
                    <flux:button variant="primary" type="submit">
                        
                        Search
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('admin.subjects') }}">
                        Clear
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>

    <!-- Subjects Table -->
    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">Subjects ({{ $subjects->total() }})</flux:heading>
                @if(auth()->user()->hasRole('admin'))
                    <flux:button variant="primary" href="{{ route('admin.subjects.create') }}">
                        <flux:icon.plus class="h-4 w-4 mr-2" />
                        Add Subject
                    </flux:button>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                    <flux:table.column>Teachers</flux:table.column>
                    <flux:table.column>Students</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    @if(auth()->user()->hasRole(['admin', 'teacher']))
                        <flux:table.column>Actions</flux:table.column>
                    @endif
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($subjects as $subject)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="text-sm font-medium">{{ $subject->name }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="primary">{{ $subject->code }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ Str::limit($subject->description, 50) }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($subject->teachers->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($subject->teachers->take(2) as $teacher)
                                            <flux:badge size="sm" variant="blue">{{ $teacher->user->name }}</flux:badge>
                                        @endforeach
                                        @if($subject->teachers->count() > 2)
                                            <flux:badge size="sm" variant="gray">+{{ $subject->teachers->count() - 2 }} more</flux:badge>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No teachers assigned</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $activeStudentsCount = $subject->students()->wherePivot('enrollment_status', 'active')->count();
                                @endphp
                                @if($activeStudentsCount > 0)
                                    <div class="flex items-center">
                                        <flux:badge size="sm" color="green">{{ $activeStudentsCount }} active</flux:badge>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No active students</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $subject->created_at->format('M d, Y') }}</flux:table.cell>
                            @if(auth()->user()->hasRole('admin'))
                                <flux:table.cell>
                                    <div class="flex space-x-2">
                                        <flux:button variant="ghost" size="sm" href="{{ route('admin.subjects.show', $subject) }}" title="View Subject">
                                            <flux:icon.eye class="h-4 w-4" />
                                        </flux:button>
                                        <flux:button variant="ghost" size="sm" href="{{ route('admin.subjects.edit', $subject) }}" title="Edit Subject">
                                            <flux:icon.pencil class="h-4 w-4" />
                                        </flux:button>
                                        <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button variant="ghost" size="sm" type="submit" title="Delete Subject">
                                                <flux:icon.trash class="h-4 w-4" />
                                            </flux:button>
                                        </form>
                                    </div>
                                </flux:table.cell>
                            @elseif(auth()->user()->hasRole('teacher'))
                                <flux:table.cell>
                                    <flux:button variant="ghost" size="sm" href="{{ route('academic.subjects.show', $subject) }}" title="View Subject">
                                        <flux:icon.eye class="h-4 w-4" />
                                    </flux:button>
                                </flux:table.cell>
                            @endif
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="{{ auth()->user()->hasRole(['admin', 'teacher']) ? '7' : '6' }}" class="text-center py-8">
                                <div class="text-gray-500">
                                    <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                    <p class="text-lg font-medium">No subjects found</p>
                                    <p class="text-sm">Get started by adding your first subject</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        @if($subjects->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $subjects->links() }}
            </div>
        @endif
    </flux:card>
</x-layouts.app>
