<x-layouts.app :title="__('Student Enrollment Management')">
    <div class="mb-8">
        <flux:heading size="xl" level="1">Student Enrollment Management</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Manage student enrollments in subjects</flux:text>
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
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:input
                    name="search"
                    label="Search Student"
                    placeholder="Search by name or email"
                    value="{{ request('search') }}"
                />
                
                <flux:field>
                    <flux:label>Enrollment Status</flux:label>
                    <flux:select name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </flux:select>
                </flux:field>
                
                <flux:field>
                    <flux:label>Subject</flux:label>
                    <flux:select name="subject_id">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </flux:select>
                </flux:field>
                
                <div class="flex items-end space-x-2">
                    <flux:button variant="primary" type="submit">
                        <flux:icon.magnifying-glass class="h-4 w-4 mr-2" />
                        Search
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('enrollment.index') }}">
                        Clear
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:card>

    <!-- Students Enrollment Table -->
    <flux:card>
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Student Enrollments</flux:heading>
                <flux:text class="text-sm text-gray-500">{{ $students->count() }} students found</flux:text>
            </div>
        </div>
        
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Student</flux:table.column>
                <flux:table.column>Student ID</flux:table.column>
                <flux:table.column>Parent</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Enrollment Number</flux:table.column>
                <flux:table.column>Enrollment Status</flux:table.column>
                <flux:table.column>Enrollment Date</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @forelse($students as $student)
                    @foreach($student->subjects as $subject)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center">
                                    <flux:avatar name="{{ $student->user->name }}" class="mr-3" />
                                    <div>
                                        <div class="font-medium">{{ $student->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                {{ $student->student_id ?? 'Not assigned' }}
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                @if($student->parent)
                                    <div>
                                        <div class="font-medium">{{ $student->parent->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->parent->email }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No parent assigned</span>
                                @endif
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <div>
                                    <div class="font-medium">{{ $subject->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subject->code }}</div>
                                </div>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <flux:badge variant="blue" size="sm">
                                    {{ $subject->pivot->enrollment_number ?? 'Not assigned' }}
                                </flux:badge>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                @php
                                    $enrollmentStatus = $subject->pivot->enrollment_status ?? 'pending';
                                    $badgeColor = $enrollmentStatus === 'active' ? 'green' : ($enrollmentStatus === 'pending' ? 'amber' : 'red');
                                @endphp
                                <flux:badge size="sm" color="{{ $badgeColor }}">
                                    {{ ucfirst($enrollmentStatus) }}
                                </flux:badge>
                            </flux:table.cell>
                            
                            
                            <flux:table.cell>
                                @php
                                    $enrollmentDate = $subject->pivot->enrollment_date;
                                    $formattedDate = $enrollmentDate ? \Carbon\Carbon::parse($enrollmentDate)->format('M j, Y') : '-';
                                @endphp
                                {{ $formattedDate }}
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <div class="flex space-x-2">
                                    <flux:button variant="ghost" size="sm" href="{{ route('enrollment.edit', [$student, $subject]) }}" title="Edit Enrollment">
                                        <flux:icon.pencil class="h-4 w-4" />
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" href="{{ route('enrollment.show', $student) }}" title="View Student Details">
                                        <flux:icon.eye class="h-4 w-4" />
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8">
                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No enrollments found</p>
                            <p class="text-sm text-gray-400">Enrollments will appear here once students are enrolled in subjects</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        
    </flux:card>
</x-layouts.app>