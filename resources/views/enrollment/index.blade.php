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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Students with Enrollments -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Students & Their Enrollments</flux:heading>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($students as $student)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <flux:avatar name="{{ $student->user->name }}" class="mr-3" />
                                    <div>
                                        <div class="font-medium">{{ $student->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <flux:button variant="ghost" size="sm" href="{{ route('enrollment.show', $student) }}" title="View Enrollments">
                                        <flux:icon.eye class="h-4 w-4" />
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" href="{{ route('enrollment.create', $student) }}" title="Enroll in Subjects">
                                        <flux:icon.plus class="h-4 w-4" />
                                    </flux:button>
                                </div>
                            </div>
                            
                            @if($student->subjects->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($student->subjects->take(3) as $subject)
                                        <flux:badge 
                                            size="sm" 
                                            variant="{{ $subject->pivot->status === 'enrolled' ? 'blue' : ($subject->pivot->status === 'completed' ? 'green' : 'red') }}"
                                        >
                                            {{ $subject->name }}
                                        </flux:badge>
                                    @endforeach
                                    @if($student->subjects->count() > 3)
                                        <flux:badge size="sm" variant="gray">+{{ $student->subjects->count() - 3 }} more</flux:badge>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">No enrollments yet</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No students found</p>
                            <p class="text-sm text-gray-400">Students will appear here once they are created</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </flux:card>

        <!-- Subjects with Enrollments -->
        <flux:card>
            <div class="px-6 py-4 border-b border-gray-200">
                <flux:heading size="lg">Subjects & Their Students</flux:heading>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($subjects as $subject)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-medium">{{ $subject->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subject->code }}</div>
                                </div>
                                <flux:badge size="sm" variant="blue">{{ $subject->students->count() }} students</flux:badge>
                            </div>
                            
                            @if($subject->students->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($subject->students->take(3) as $student)
                                        <flux:badge 
                                            size="sm" 
                                            variant="{{ $student->pivot->status === 'enrolled' ? 'blue' : ($student->pivot->status === 'completed' ? 'green' : 'red') }}"
                                        >
                                            {{ $student->user->name }}
                                        </flux:badge>
                                    @endforeach
                                    @if($subject->students->count() > 3)
                                        <flux:badge size="sm" variant="gray">+{{ $subject->students->count() - 3 }} more</flux:badge>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">No students enrolled</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                            <p class="text-lg font-medium text-gray-500">No subjects found</p>
                            <p class="text-sm text-gray-400">Subjects will appear here once they are created</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
