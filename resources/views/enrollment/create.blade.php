<x-layouts.app :title="__('Enroll Student in Subjects')">
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <flux:button variant="ghost" href="{{ route('enrollment.index') }}" class="mr-4">
                <flux:icon.arrow-left class="h-4 w-4 mr-2" />
                Back to Enrollments
            </flux:button>
        </div>
        <flux:heading size="xl" level="1">Enroll Student in Subjects</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Select subjects for {{ $student->user->name }}</flux:text>
    </div>

    @if ($errors->any())
        <flux:callout class="mb-6" variant="danger" icon="x-mark" heading="Please correct the following errors:">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Enrollment Form -->
        <div class="lg:col-span-2">
            <flux:card>
                <div class="p-6">
                    <form method="POST" action="{{ route('enrollment.store', $student) }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <flux:heading size="lg" class="mb-4">Available Subjects</flux:heading>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select one or more subjects to enroll this student in.</flux:text>
                            
                            @if($availableSubjects->count() > 0)
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    @foreach($availableSubjects as $subject)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                            <label class="flex items-start space-x-3 cursor-pointer">
                                                <flux:checkbox 
                                                    name="subjects[]" 
                                                    value="{{ $subject->id }}"
                                                    class="mt-1"
                                                />
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ $subject->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $subject->code }}</div>
                                                    @if($subject->description)
                                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ Str::limit($subject->description, 100) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <flux:icon.academic-cap class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                    <p class="text-lg font-medium text-gray-500">No available subjects</p>
                                    <p class="text-sm text-gray-400">This student is already enrolled in all subjects</p>
                                </div>
                            @endif
                        </div>

                        <flux:field>
                            <flux:label>Enrollment Notes (Optional)</flux:label>
                            <flux:textarea
                                name="notes"
                                placeholder="Add any notes about this enrollment..."
                                rows="3"
                            >{{ old('notes') }}</flux:textarea>
                        </flux:field>

                        <div class="flex justify-end space-x-3">
                            <flux:button variant="ghost" href="{{ route('enrollment.index') }}">
                                Cancel
                            </flux:button>
                            @if($availableSubjects->count() > 0)
                                <flux:button variant="primary" type="submit">
                                    <flux:icon.plus class="h-4 w-4 mr-2" />
                                    Enroll Student
                                </flux:button>
                            @endif
                        </div>
                    </form>
                </div>
            </flux:card>
        </div>

        <!-- Student Info Sidebar -->
        <div>
            <flux:card>
                <div class="px-6 py-4 border-b border-gray-200">
                    <flux:heading size="lg">Student Information</flux:heading>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <flux:avatar name="{{ $student->user->name }}" size="xl" class="mx-auto mb-3" />
                        <flux:heading size="lg">{{ $student->user->name }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400">{{ $student->user->email }}</flux:text>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <flux:text class="text-sm font-medium">Student ID</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->student_id ?? 'Not assigned' }}
                            </flux:text>
                        </div>
                        
                        <div>
                            <flux:text class="text-sm font-medium">Parent</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->parent->name ?? 'Not assigned' }}
                            </flux:text>
                        </div>
                        
                        <div>
                            <flux:text class="text-sm font-medium">Current Enrollments</flux:text>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->subjects->count() }} subjects
                            </flux:text>
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Current Enrollments -->
            @if($student->subjects->count() > 0)
                <flux:card class="mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <flux:heading size="lg">Current Enrollments</flux:heading>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2">
                            @foreach($student->subjects as $subject)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <flux:text class="text-sm font-medium">{{ $subject->name }}</flux:text>
                                        <flux:text class="text-xs text-gray-500">{{ $subject->code }}</flux:text>
                                    </div>
                                    <flux:badge 
                                        size="sm" 
                                        variant="{{ $subject->pivot->status === 'enrolled' ? 'blue' : ($subject->pivot->status === 'completed' ? 'green' : 'red') }}"
                                    >
                                        {{ ucfirst($subject->pivot->status) }}
                                    </flux:badge>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </flux:card>
            @endif
        </div>
    </div>
</x-layouts.app>
