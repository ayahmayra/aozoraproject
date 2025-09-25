<x-layouts.app :title="__('Student Management')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <flux:heading size="xl" level="1">Student Management</flux:heading>
                <flux:text class="mb-6 mt-2 text-base">Manage all students in the system</flux:text>
            </div>

            @if (session()->has('success'))
                <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
            @endif

            @if (session()->has('error'))
                <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
            @endif

            <!-- Search and Filter Form -->
            <flux:card class="mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.students') }}" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <flux:input
                            name="search"
                            label="Search Students"
                            placeholder="Search by name or email..."
                            value="{{ request('search') }}"
                        />
                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </flux:select>
                        </flux:field>
                        <div class="flex items-end space-x-3">
                            <flux:button variant="primary" type="submit">
                                <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                                Search
                            </flux:button>
                            <flux:button variant="ghost" href="{{ route('admin.students') }}">
                                Clear
                            </flux:button>
                        </div>
                    </form>
                </div>
            </flux:card>

            <!-- Students Table -->
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Students ({{ $students->total() }})</flux:heading>
                        <flux:button variant="primary" href="{{ route('admin.students.create') }}">
                            <flux:icon.plus class="h-4 w-4 mr-2" />
                            Add Student
                        </flux:button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Student</flux:table.column>
                            <flux:table.column>Student ID</flux:table.column>
                            <flux:table.column>Parent</flux:table.column>
                            <flux:table.column>Status</flux:table.column>
                            <flux:table.column>Created</flux:table.column>
                            <flux:table.column>Actions</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @forelse($students as $student)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <flux:avatar name="{{ $student->user->name }}" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium">{{ $student->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($student->student_id)
                                            <flux:badge variant="primary">{{ $student->student_id }}</flux:badge>
                                        @else
                                            <span class="text-gray-400">Not assigned</span>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="text-sm">{{ $student->parent->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->parent->email }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        @if($student->user->status === 'active')
                                            <flux:badge variant="success">Active</flux:badge>
                                        @elseif($student->user->status === 'pending')
                                            <flux:badge variant="blue">Pending</flux:badge>
                                        @else
                                            <flux:badge variant="danger">Inactive</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $student->created_at->format('M d, Y') }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex space-x-2">
                                            <flux:button variant="ghost" size="sm" href="{{ route('admin.students.edit', $student) }}" title="Edit Student">
                                                <flux:icon.pencil class="h-4 w-4" />
                                            </flux:button>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:button variant="ghost" size="sm" type="submit" title="Delete Student">
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
                                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                            <p class="text-lg font-medium">No students found</p>
                                            <p class="text-sm">Get started by adding your first student</p>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
                @if($students->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $students->links() }}
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</x-layouts.app>