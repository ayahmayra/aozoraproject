<x-layouts.app :title="__('Subject Details')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('admin.subjects') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Subjects
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">{{ $subject->name }}</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">{{ $subject->code }} - {{ $subject->description ?? 'No description available' }}</flux:text>
    </div>

    @if (session()->has('success'))
        <flux:callout class="mb-6" variant="success" icon="check-circle" :heading="session('success')" />
    @endif

    @if (session()->has('error'))
        <flux:callout class="mb-6" variant="danger" icon="x-mark" :heading="session('error')" />
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subject Information -->
        <div class="lg:col-span-2">
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Subject Information</flux:heading>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Subject Name</flux:text>
                            <flux:text class="text-lg font-semibold">{{ $subject->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Subject Code</flux:text>
                            <flux:badge variant="primary" size="lg">{{ $subject->code }}</flux:badge>
                        </div>
                        <div class="md:col-span-2">
                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Description</flux:text>
                            <flux:text class="text-lg">{{ $subject->description ?? 'No description provided' }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Created Date</flux:text>
                            <flux:text class="text-lg">{{ $subject->created_at->format('F j, Y') }}</flux:text>
                        </div>
                        <div>
                            <flux:text class="text-sm font-medium text-gray-500 mb-1">Last Updated</flux:text>
                            <flux:text class="text-lg">{{ $subject->updated_at->format('F j, Y') }}</flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Assigned Teachers -->
            <flux:card class="mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Assigned Teachers</flux:heading>
                        <flux:badge size="sm" color="blue">{{ $subject->teachers->count() }} teachers</flux:badge>
                    </div>
                </div>
                <div class="p-6">
                    @if($subject->teachers->count() > 0)
                        <div class="space-y-4">
                            @foreach($subject->teachers as $teacher)
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <flux:avatar name="{{ $teacher->user->name }}" size="md" />
                                        <div>
                                            <flux:text class="font-medium">{{ $teacher->user->name }}</flux:text>
                                            <flux:text class="text-sm text-gray-500">{{ $teacher->user->email }}</flux:text>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <flux:badge size="sm" color="green">{{ $teacher->employment_status ?? 'Active' }}</flux:badge>
                                        <flux:button variant="ghost" size="sm" href="{{ route('profile.show') }}?user={{ $teacher->user->id }}" title="View Teacher Profile">
                                            <flux:icon.eye class="h-4 w-4" />
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon.user-group class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No teachers assigned</p>
                            <p class="text-sm text-gray-400">This subject doesn't have any assigned teachers yet</p>
                            <flux:button variant="primary" href="{{ route('admin.subjects.edit', $subject) }}" class="mt-4">
                                <flux:icon.pencil class="h-4 w-4 mr-2" />
                                Assign Teachers
                            </flux:button>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Enrolled Students -->
            <flux:card class="mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <flux:heading size="lg">Enrolled Students</flux:heading>
                        <flux:badge size="sm" color="green">{{ $subject->students->count() }} active students</flux:badge>
                    </div>
                </div>
                <div class="p-6">
                    @if($subject->students->count() > 0)
                        <div class="space-y-4">
                            @foreach($subject->students as $student)
                                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <flux:avatar name="{{ $student->user->name }}" size="md" />
                                        <div>
                                            <flux:text class="font-medium">{{ $student->user->name }}</flux:text>
                                            <flux:text class="text-sm text-gray-500">{{ $student->user->email }}</flux:text>
                                            @if($student->student_id)
                                                <flux:text class="text-xs text-gray-400">ID: {{ $student->student_id }}</flux:text>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <flux:badge size="sm" color="green">{{ ucfirst($student->pivot->enrollment_status ?? 'active') }}</flux:badge>
                                        <flux:badge size="sm" color="blue">{{ $student->pivot->enrollment_number ?? 'N/A' }}</flux:badge>
                                        <flux:button variant="ghost" size="sm" href="{{ route('profile.show') }}?user={{ $student->user->id }}" title="View Student Profile">
                                            <flux:icon.eye class="h-4 w-4" />
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No active students</p>
                            <p class="text-sm text-gray-400">No students are currently enrolled in this subject</p>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Actions -->
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Quick Actions</flux:heading>
                </div>
                <div class="p-6 space-y-3">
                    <flux:button variant="primary" href="{{ route('admin.subjects.edit', $subject) }}" class="w-full">
                        <flux:icon.pencil class="h-4 w-4 mr-2" />
                        Edit Subject
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('admin.subjects') }}" class="w-full">
                        <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                        Back to Subjects
                    </flux:button>
                </div>
            </flux:card>

            <!-- Subject Statistics -->
            <flux:card class="mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Statistics</flux:heading>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <flux:text class="text-sm font-medium">Total Teachers</flux:text>
                            <flux:badge size="sm" color="blue">{{ $subject->teachers->count() }}</flux:badge>
                        </div>
                        <div class="flex justify-between items-center">
                            <flux:text class="text-sm font-medium">Active Students</flux:text>
                            <flux:badge size="sm" color="green">{{ $subject->students->count() }}</flux:badge>
                        </div>
                        <div class="flex justify-between items-center">
                            <flux:text class="text-sm font-medium">Created</flux:text>
                            <flux:text class="text-sm text-gray-500">{{ $subject->created_at->format('M j, Y') }}</flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
